<?php

namespace ArneGroskurth\Symgrid\Grid\DataSource;

use ArneGroskurth\Symgrid\Grid\AbstractDataSource;
use ArneGroskurth\Symgrid\Grid\ColumnList;
use ArneGroskurth\Symgrid\Grid\Constants;
use ArneGroskurth\Symgrid\Grid\DataFilter;
use ArneGroskurth\Symgrid\Grid\DataOrder;
use ArneGroskurth\Symgrid\Grid\DataPathTrait;
use ArneGroskurth\Symgrid\Grid\Exception;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;


class QueryBuilderDataSource extends AbstractDataSource implements \IteratorAggregate {

    use DataPathTrait;
    

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;
    
    /**
     * @var string
     */
    protected $idPath;

    /**
     * @var DataOrder
     */
    protected $order;

    /**
     * @var DataFilter[]
     */
    protected $filters = array();

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var DataRecordWrappingIterator
     */
    protected $iterator;

    /**
     * @var int
     */
    protected $totalCount;

    /**
     * @var int
     */
    protected $loadedCount;


    /**
     * QueryBuilderDataSource constructor.
     *
     * @param QueryBuilder $queryBuilder
     * @param string $idPath
     * @throws Exception
     */
    public function __construct(QueryBuilder $queryBuilder, $idPath) {

        if(count($queryBuilder->getRootAliases()) > 1) {

            throw new Exception("Symgrid does not support QueryBuilders with multiple root aliases yet.");
        }

        $this->queryBuilder = $queryBuilder;
        $this->idPath = $idPath;
    }


    /**
     * {@inheritdoc}
     */
    public function load(ColumnList $columnList = null) {

        $this->paginator = new Paginator($this->getQueryBuilder());

        return $this->getTotalCount();
    }


    /**
     * {@inheritdoc}
     */
    public function getTotalCount(ColumnList $columnList = null) {

        if(is_null($this->totalCount)) {

            if(is_null($this->paginator)) {

                throw new Exception();
            }

            $this->totalCount = count($this->paginator);
        }

        return $this->totalCount;
    }


    /**
     * {@inheritdoc}
     */
    public function loadPage($page, $recordsPerPage, ColumnList $columnList = null) {

        $queryBuilder = $this->getQueryBuilder()
            ->setFirstResult($recordsPerPage * ($page - 1))
            ->setMaxResults($recordsPerPage);

        $this->paginator = new Paginator($queryBuilder);

        return $this->getLoadedCount();
    }


    /**
     * {@inheritdoc}
     */
    public function getLoadedCount() {

        if(is_null($this->loadedCount)) {

            if(is_null($this->paginator)) throw new Exception();

            $this->loadedCount = $this->paginator->getIterator()->count();
        }

        return $this->loadedCount;
    }


    /**
     * {@inheritdoc}
     */
    public function getColumnList() {

        throw new Exception("Column generation from queryBuilder is not implemented yet.");
    }


    /**
     * @return \Traversable
     */
    public function getIterator() {

        if(is_null($this->iterator)) {

            $this->iterator = new DataRecordWrappingIterator($this->paginator->getIterator(), $this->idPath);
        }

        return $this->iterator;
    }


    /**
     * {@inheritdoc}
     */
    public function applyOrder(DataOrder $dataOrder = null) {

        $this->order = $dataOrder;

        $this->invalidateCaches();

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getAppliedOrder() {

        return $this->order;
    }


    /**
     * {@inheritdoc}
     */
    public function applyFilter(DataFilter $dataFilter) {

        $this->filters[] = $dataFilter;

        $this->invalidateCaches();

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getAppliedFilters() {

        return $this->filters;
    }


    /**
     * @return $this
     */
    protected function invalidateCaches() {

        $this->iterator = null;
        $this->totalCount = null;
        $this->loadedCount = null;

        return $this;
    }


    /**
     * @return QueryBuilder
     * @throws Exception
     */
    protected function getQueryBuilder() {

        $queryBuilder = clone $this->queryBuilder;

        if($this->order) {

            $queryBuilder->orderBy($this->getDirectAccessAlias($this->order->getPath()), $this->order->getDirection());
        }

        foreach($this->filters as $filterIndex => $filter) {

            $path = $this->getDirectAccessAlias($filter->getPath());
            $parameterName = sprintf(":filterParameter%d", $filterIndex);

            $value = $filter->getValue();
            $setParameterValue = true;

            switch($filter->getKeyword()) {

                case Constants::FILTER_KEYWORD_AFTER: $expression = "{$path} >= {$parameterName}"; $value = (new \DateTime($value))->format("Y-m-d"); break;
                case Constants::FILTER_KEYWORD_BEFORE: $expression = "{$path} <= {$parameterName}"; $value = (new \DateTime($value))->format("Y-m-d"); break;
                case Constants::FILTER_KEYWORD_CONTAINS: $expression = "{$path} LIKE {$parameterName}"; $value = "%{$value}%"; break;
                case Constants::FILTER_KEYWORD_EQUALS: $expression = "{$path} = {$parameterName}"; break;
                case Constants::FILTER_KEYWORD_NULL: $expression = $path . ' ' . ($value == 'yes' ? 'IS NULL' : 'IS NOT NULL'); $setParameterValue = false; break;
                case Constants::FILTER_KEYWORD_MIN: $expression = "{$path} >= {$parameterName}"; break;
                case Constants::FILTER_KEYWORD_MAX: $expression = "{$path} <= {$parameterName}"; break;

                case Constants::FILTER_KEYWORD_IN:

                    $values = explode(',', $value);
                    $includesNull = in_array('null', $values);
                    $setParameterValue = false;

                    if($includesNull) {

                        $values = array_diff($values, array('null'));
                    }

                    $expressions = array();

                    if(!empty($values)) {

                        $expressions[] = sprintf("{$path} IN (%s)", implode(',', $values));
                    }

                    if($includesNull) {

                        $expressions[] = "{$path} IS NULL";
                    }

                    $expression = implode(' OR ', $expressions);

                    break;

                default: throw new Exception("Unknown filter keyword.");
            }

            $queryBuilder->andWhere($expression);

            if($setParameterValue) {
                $queryBuilder->setParameter($parameterName, $value);
            }
        }

        return $queryBuilder;
    }


    /**
     * @return string
     */
    protected function getRootAlias() {

        return $this->queryBuilder->getRootAliases()[0];
    }


    /**
     * @param string $path
     *
     * @return string
     */
    protected function getDirectAccessAlias($path) {

        return trim($this->getPathFromParts($this->getLastPathParts(array_merge(array($this->getRootAlias()), $this->getPathParts($path)))), '*');
    }
}