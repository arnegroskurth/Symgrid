<?php

namespace ArneGroskurth\Symgrid\Grid;

use ArneGroskurth\Symgrid\Grid\DataSource\ArrayDataSource;
use ArneGroskurth\Symgrid\Grid\DataSource\EntityDataSource;
use ArneGroskurth\Symgrid\Grid\DataSource\QueryBuilderDataSource;
use ArneGroskurth\Symgrid\Grid\Export\CSVExport;
use ArneGroskurth\Symgrid\Grid\Export\ExcelExport;
use ArneGroskurth\Symgrid\Grid\Export\HTMLExport;
use ArneGroskurth\Symgrid\Grid\Export\PDFExport;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class Grid {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $title = 'Symgrid';

    /**
     * @var AbstractDataSource
     */
    protected $dataSource;

    /**
     * @var string
     */
    protected $identificationPath = 'id';

    /**
     * @var ColumnList
     */
    protected $columnList;

    /**
     * @var bool
     */
    protected $sortable = true;

    /**
     * @var bool
     */
    protected $filterable = true;

    /**
     * @var bool
     */
    protected $exportable = true;

    /**
     * @var string
     */
    protected $exportFileName = 'Export';

    /**
     * @var bool
     */
    protected $pageable = true;

    /**
     * @var int
     */
    protected $pageSize = 30;

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var bool
     */
    protected $aggregatable = true;

    /**
     * @var bool
     */
    protected $liveUpdateable = true;

    /**
     * @var GroupAction[]
     */
    protected $groupActions = array();

    /**
     * @var RowAction[]
     */
    protected $rowActions = array();

    /**
     * @var string
     */
    protected $jsCallbackOnLoad;

    /**
     * @var string
     */
    protected $jsCallbackRowOnClick;

    /**
     * @var \Closure
     */
    protected $rowClassCallback;

    /**
     * @var string[]
     */
    protected $customClasses = array();

    /**
     * @var string[]
     */
    protected static $takenTitles = array();


    /**
     * Grid constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {

        $this->container = $container;
        $this->columnList = new ColumnList();
    }


    /**
     * @return string
     */
    public function getTitle() {

        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Grid
     * @throws Exception
     */
    public function setTitle($title) {

        if($this->title && ($key = array_search($title, self::$takenTitles)) !== false) {

            unset(self::$takenTitles[$key]);
        }

        if(in_array($title, self::$takenTitles)) {

            throw new Exception("Title is already in use.");
        }

        self::$takenTitles[] = $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier() {

        return preg_replace('/[^a-z0-9_\-]/i', '_', $this->title);
    }

    /**
     * @return AbstractDataSource
     */
    public function getDataSource() {

        return $this->dataSource;
    }

    /**
     * @param AbstractDataSource $dataSource
     * @param bool $retrieveColumns
     *
     * @return Grid
     * @throws Exception
     */
    public function setDataSource(AbstractDataSource $dataSource, $retrieveColumns = true) {

        if(!$dataSource instanceof \Traversable) {

            throw new Exception("Non-traversable data source given.");
        }

        $this->dataSource = $dataSource;

        if($retrieveColumns) {

            try {

                $this->columnList = $dataSource->getColumnList();
            }

            // ignore grid exceptions as columns are normally configured explicitly
            catch(Exception $e) {}
        }

        return $this;
    }

    /**
     * Wraps the creation and usage of an ArrayDataSource.
     *
     * @param array $data
     * @param string $idPath
     *
     * @return Grid
     */
    public function fromArray(array $data, $idPath) {

        $this->setDataSource(new ArrayDataSource($data, $idPath));

        return $this;
    }

    /**
     * Wraps the creation and usage of an EntityDataSource.
     *
     * @param string $rootClassName
     *
     * @return Grid
     */
    public function fromEntity($rootClassName) {

        $entityManager = $this->container->get('doctrine')->getManager();

        $this->setDataSource(new EntityDataSource($entityManager, $rootClassName));

        return $this;
    }

    /**
     * Wraps the creation and usage of a QueryBuilderDataSource.
     *
     * @param QueryBuilder $queryBuilder
     * @param string $idPath
     *
     * @return Grid
     */
    public function fromQueryBuilder(QueryBuilder $queryBuilder, $idPath) {

        $this->setDataSource(new QueryBuilderDataSource($queryBuilder, $idPath));

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentificationPath() {

        return $this->identificationPath;
    }

    /**
     * @param string $identificationPath
     *
     * @return Grid
     */
    public function setIdentificationPath($identificationPath) {

        $this->identificationPath = $identificationPath;

        return $this;
    }

    /**
     * @return ColumnList
     */
    public function getColumnList() {

        return $this->columnList;
    }

    /**
     * @param ColumnList $columnList
     *
     * @return Grid
     */
    public function setColumnList(ColumnList $columnList) {

        $this->columnList = $columnList;

        return $this;
    }

    /**
     * @param AbstractColumn $column
     *
     * @return Grid
     * @throws Exception
     */
    public function addColumn(AbstractColumn $column) {

        $this->columnList->addColumn($column);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSortable() {

        return $this->sortable;
    }

    /**
     * @param boolean $sortable
     *
     * @return Grid
     */
    public function setSortable($sortable) {

        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFilterable() {

        return $this->filterable;
    }

    /**
     * @param boolean $filterable
     *
     * @return Grid
     */
    public function setFilterable($filterable) {

        $this->filterable = $filterable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isExportable() {

        return $this->exportable;
    }

    /**
     * @param boolean $exportable
     *
     * @return Grid
     */
    public function setExportable($exportable) {

        $this->exportable = $exportable;

        return $this;
    }

    /**
     * @return string
     */
    public function getExportFileName() {

        return $this->exportFileName;
    }

    /**
     * @param string $exportFileName
     *
     * @return Grid
     */
    public function setExportFileName($exportFileName) {

        $this->exportFileName = $exportFileName;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPageable() {

        return $this->pageable;
    }

    /**
     * @param boolean $pageable
     *
     * @return Grid
     */
    public function setPageable($pageable) {

        $this->pageable = $pageable;

        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize() {

        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     *
     * @return Grid
     */
    public function setPageSize($pageSize) {

        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage() {

        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     *
     * @return Grid
     */
    public function setCurrentPage($currentPage) {

        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAggregatable() {

        return $this->aggregatable;
    }

    /**
     * @param boolean $aggregatable
     *
     * @return Grid
     */
    public function setAggregatable($aggregatable) {

        $this->aggregatable = $aggregatable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isLiveUpdateable() {

        return $this->liveUpdateable;
    }

    /**
     * @param boolean $liveUpdateable
     *
     * @return Grid
     */
    public function setLiveUpdateable($liveUpdateable) {

        $this->liveUpdateable = $liveUpdateable;

        return $this;
    }

    /**
     * @return GroupAction[]
     */
    public function getGroupActions() {

        return $this->groupActions;
    }

    /**
     * @return bool Whether this grid has any group actions.
     */
    public function hasGroupActions() {

        return !empty($this->groupActions);
    }

    /**
     * @param string $title
     *
     * @return GroupAction
     */
    public function getGroupActionByTitle($title) {

        foreach($this->groupActions as $groupAction) {

            if($groupAction->getTitle() == $title) {

                return $groupAction;
            }
        }

        return null;
    }

    /**
     * @param GroupAction $groupAction
     *
     * @return Grid
     */
    public function addGroupAction(GroupAction $groupAction) {

        $this->groupActions[] = $groupAction;

        usort($this->groupActions, function(GroupAction $lhs, GroupAction $rhs) {

            return $lhs->getTitle() > $rhs->getTitle();
        });

        return $this;
    }

    /**
     * @return RowAction[]
     */
    public function getRowActions() {

        return $this->rowActions;
    }

    /**
     * @return bool Whether this grid has any row actions.
     */
    public function hasRowActions() {

        return !empty($this->rowActions);
    }

    /**
     * @param RowAction $rowAction
     *
     * @return Grid
     */
    public function addRowAction(RowAction $rowAction) {

        $this->rowActions[] = $rowAction;

        usort($this->rowActions, function(RowAction $lhs, RowAction $rhs) {

            return $lhs->getTitle() > $rhs->getTitle();
        });

        return $this;
    }

    /**
     * @return string
     */
    public function getJsCallbackOnLoad() {

        return $this->jsCallbackOnLoad;
    }

    /**
     * @param string $jsCallbackOnLoad
     *
     * @return Grid
     */
    public function setJsCallbackOnLoad($jsCallbackOnLoad) {

        $this->jsCallbackOnLoad = $jsCallbackOnLoad;

        return $this;
    }

    /**
     * @return string
     */
    public function getJsCallbackRowOnClick() {

        return $this->jsCallbackRowOnClick;
    }

    /**
     * @param string $jsCallbackRowOnClick
     *
     * @return Grid
     */
    public function setJsCallbackRowOnClick($jsCallbackRowOnClick) {

        $this->jsCallbackRowOnClick = $jsCallbackRowOnClick;

        return $this;
    }

    /**
     * @return \Closure
     */
    public function getRowClassCallback() {

        return $this->rowClassCallback;
    }

    /**
     * @param \Closure $rowClassCallback
     *
     * @return Grid
     */
    public function setRowClassCallback(\Closure $rowClassCallback) {

        $this->rowClassCallback = $rowClassCallback;

        return $this;
    }

    /**
     * @return \string[]
     */
    public function getCustomClasses() {

        return $this->customClasses;
    }

    /**
     * @param \string[] $customClasses
     *
     * @return Grid
     */
    public function setCustomClasses(array $customClasses) {

        $this->customClasses = $customClasses;

        return $this;
    }

    /**
     * @param string $customClass
     *
     * @return Grid
     */
    public function addCustomClass($customClass) {

        if(!in_array($customClass, $this->customClasses)) {

            $this->customClasses[] = $customClass;
        }

        return $this;
    }

    /**
     * @param string $customClass
     *
     * @return Grid
     */
    public function removeCustomClass($customClass) {

        if(($key = array_search($customClass, $this->customClasses)) !== false) {

            unset($this->customClasses[$key]);
        }

        return $this;
    }

    /**
     * @return string[] Classes to be applied to the symgrid container.
     */
    public function getClasses() {

        $return = $this->getCustomClasses();
        $return[] = 'symgrid';

        if($this->hasGroupActions()) $return[] = 'with-group-actions';
        if($this->hasRowActions()) $return[] = 'with-row-actions';
        if($this->isAggregatable()) $return[] = 'aggregatable';
        if($this->isExportable()) $return[] = 'exportable';
        if($this->isFilterable()) $return[] = 'filterable';
        if($this->isLiveUpdateable()) $return[] = 'liveupdateable';
        if($this->isPageable()) $return[] = 'pageable';
        if($this->isSortable()) $return[] = 'sortable';

        if($this->hasGroupActions()) $return[] = 'selectable';

        return $return;
    }


    /**
     * Activates the bundled default style.
     *
     * @return $this
     */
    public function useDefaultStyle() {

        $this->addCustomClass('styled');

        return $this;
    }


    /**
     * @throws Exception
     */
    public function validate() {

        if(empty($this->getDataSource())) {

            throw new Exception("Grid doesn't have a data source.");
        }

        if(empty($this->getColumnList()->count())) {

            throw new Exception("Grid hasn't any columns.");
        }
    }


    /**
     * @return int
     * @throws Exception
     */
    public function loadDataSource() {

        return $this->dataSource->loadPage($this->currentPage, $this->pageSize, $this->columnList);
    }


    /**
     * @return int
     * @throws Exception
     */
    public function getTotalDataCount() {

        if(is_null($this->dataSource)) {

            throw new Exception("No data source given.");
        }

        return $this->dataSource->getTotalCount($this->columnList);
    }


    /**
     * @return int Page count.
     * @throws Exception
     */
    public function getPageCount() {

        return intval($this->getTotalDataCount() / $this->pageSize) + intval($this->getTotalDataCount() % $this->pageSize != 0);
    }


    /**
     * @return int
     * @throws Exception
     */
    public function getLoadedDataCount() {

        if(is_null($this->dataSource)) {

            throw new Exception("No data source given.");
        }

        return $this->dataSource->getLoadedCount();
    }


    /**
     * @return int
     * @throws Exception
     */
    public function getDisplayedDataCount() {

        return min($this->getTotalDataCount(), $this->pageSize);
    }


    /**
     * @return Response
     */
    public function getGridResponse() {

        $queryBag = $this->getRequest()->query;

        if($queryBag->get('_symgrid') == $this->getIdentifier()) {

            // handle requested filters
            if($this->isFilterable()) {

                $filterParameters = $queryBag->get('_filter', array());

                if(!is_array($filterParameters)) {
                    throw new BadRequestHttpException('Malformed filter request.');
                }

                foreach($filterParameters as $columnIdentifier => $filter) {

                    $column = $this->columnList->getByIdentifier($columnIdentifier);

                    if(is_null($column)) {
                        throw new BadRequestHttpException('Request to filter unknown column.');
                    }

                    if(!$column->isFilterable()) {
                        throw new BadRequestHttpException('Request to filter not filterable column.');
                    }

                    if(!is_array($filter)) {
                        throw new BadRequestHttpException('Malformed filter request.');
                    }

                    foreach($filter as $keyword => $value) {

                        if($value === '') continue;

                        try {
                            $dataFilter = new DataFilter($column->getDataPath(), $keyword, $value);
                        }
                        catch(Exception $e) {
                            throw new BadRequestHttpException('Malformed date filter value.', $e);
                        }

                        $this->dataSource->applyFilter($dataFilter);
                    }
                }
            }

            // handle requested order
            if($this->isSortable()) {

                if($queryBag->get('_orderPath') && $queryBag->get('_orderDirection')) {

                    $this->dataSource->applyOrder(new DataOrder($queryBag->get('_orderPath'), $queryBag->get('_orderDirection')));
                }
            }


            // handle export
            if($this->isExportable()) {

                if($format = $queryBag->get('_export')) {

                    switch(strtolower($format)) {

                        case Constants::TARGET_CSV: $export = new CSVExport($this->container, $this); break;
                        case Constants::TARGET_EXCEL: $export = new ExcelExport($this->container, $this); break;
                        case Constants::TARGET_HTML: $export = new HTMLExport($this->container, $this); break;
                        case Constants::TARGET_PDF: $export = new PDFExport($this->container, $this); break;

                        default: throw new BadRequestHttpException('Unknown export format requested.');
                    }

                    return $export->render();
                }
            }


            // handle pagination
            if($this->isPageable() && $queryBag->getInt('_page')) {

                $this->setCurrentPage($queryBag->getInt('_page'));
            }


            $this->loadDataSource();


            $responseData = array();

            foreach($this->getRequestedParts() as $requestedPart) {

                if($requestedPart == 'tbody') {

                    $responseData['tbody'] = $this->getTwig()->render('ArneGroskurthSymgridBundle::tbody.html.twig', array('grid' => $this));
                }

                elseif($requestedPart == 'tfoot') {

                    $responseData['tfoot'] = $this->getTwig()->render('ArneGroskurthSymgridBundle::tfoot.html.twig', array('grid' => $this));
                }

                elseif($requestedPart == 'table') {

                    $responseData['table'] = $this->getTwig()->render('ArneGroskurthSymgridBundle::table.html.twig', array('grid' => $this));
                }

                else throw new BadRequestHttpException('Unknown grid part requested.');
            }

            return new JsonResponse($responseData);
        }

        return null;
    }


    /**
     * @param string $view
     * @param array $parameters
     * @param Response $response
     *
     * @return Response
     * @throws Exception
     */
    public function getResponse($view, $parameters = array(), Response $response = null) {

        if($gridResponse = $this->getGridResponse()) {

            return $gridResponse;
        }

        return $this->getTwig()->renderResponse($view, $parameters, $response);
    }


    /**
     * @return array
     * @throws Exception
     */
    public function getSelectedRecordIds() {

        return $this->getRequest()->query->get('_rows', array());
    }


    /**
     * @return string[]
     * @throws Exception
     */
    public function getRequestedParts() {

        return explode(',', $this->getRequest()->query->get('_parts', ''));
    }


    /**
     * @return \Symfony\Bundle\TwigBundle\TwigEngine
     * @throws Exception
     */
    protected function getTwig() {

        if(!$this->container->has('templating')) {

            throw new Exception("Unable to find templating engine in service container.");
        }

        return $this->container->get('templating');
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Request
     * @throws Exception
     */
    protected function getRequest() {

        if(!$this->container->has('request_stack')) {

            throw new Exception("Unable to find request stack in service container.");
        }

        return $this->container->get('request_stack')->getCurrentRequest();
    }
}