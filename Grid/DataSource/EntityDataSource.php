<?php

namespace ArneGroskurth\Symgrid\Grid\DataSource;

use ArneGroskurth\Symgrid\Grid\AbstractDataSource;
use ArneGroskurth\Symgrid\Grid\Column\BoolColumn;
use ArneGroskurth\Symgrid\Grid\Column\DateColumn;
use ArneGroskurth\Symgrid\Grid\Column\DateTimeColumn;
use ArneGroskurth\Symgrid\Grid\Column\NumericColumn;
use ArneGroskurth\Symgrid\Grid\Column\StringColumn;
use ArneGroskurth\Symgrid\Grid\ColumnList;
use ArneGroskurth\Symgrid\Grid\Constants;
use ArneGroskurth\Symgrid\Grid\DataFilter;
use ArneGroskurth\Symgrid\Grid\DataOrder;
use ArneGroskurth\Symgrid\Grid\DataPathTrait;
use ArneGroskurth\Symgrid\Grid\Exception;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Pagination\Paginator;


class EntityDataSource extends AbstractDataSource implements \IteratorAggregate {

    use DataPathTrait;


    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $rootClassName;

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
     * @var string[]
     */
    protected $classNames = array();

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var EntityDataSourceIterator
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
     * EntityDataSource constructor.
     *
     * @param EntityManager $entityManager
     * @param string$rootClassName
     *
     * @throws Exception
     */
    public function __construct(EntityManager $entityManager, $rootClassName) {

        if(strpos($rootClassName, ':') !== false) {

            throw new Exception("Root class name has to be given as full qualified class name.");
        }

        $this->entityManager = $entityManager;
        $this->rootClassName = $rootClassName;


        $rootClassMetadata = $this->getClassMetadata($this->rootClassName);

        if(count($rootClassMetadata->identifier) > 1) {

            throw new Exception("Symgrid does not support multi-column keys.");
        }

        $this->idPath = $rootClassMetadata->identifier[0];
    }


    /**
     * {@inheritdoc}
     */
    public function isFilterable() {

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function isSortable() {

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function generateColumnList() {

        $this->classNames = array();

        return $this->getColumnsByClassName($this->rootClassName);
    }


    /**
     * Generates grid columns from tree of doctrine class metadata.
     *
     * @param string $className
     * @param array $pathParts
     * @param bool $parentNullable
     *
     * @return ColumnList
     */
    protected function getColumnsByClassName($className, array $pathParts = array(), $parentNullable=false) {

        $this->classNames[] = $className;

        $classMetadata = $this->getClassMetadata($className);

        $columnList = new ColumnList();

        foreach($classMetadata->fieldMappings as $fieldMapping) {

            $subPathParts = $this->appendPathPart($pathParts, $fieldMapping['fieldName']);
            $title = $this->getTitleByPath($subPathParts);
            $path = $this->getPathFromParts($subPathParts);

            $nullable = $parentNullable || $fieldMapping['nullable'];

            if($fieldMapping['type'] == Type::INTEGER) {

                $columnList->addColumn((new NumericColumn($title, $path, 0))->setFilterNullable($nullable));
            }

            elseif($fieldMapping['type'] == Type::FLOAT) {

                $columnList->addColumn((new NumericColumn($title, $path))->setFilterNullable($nullable));
            }

            elseif(in_array($fieldMapping['type'], array(Type::STRING, Type::TEXT, Type::SIMPLE_ARRAY))) {

                $columnList->addColumn((new StringColumn($title, $path))->setFilterNullable($nullable));
            }

            elseif($fieldMapping['type'] == Type::DATE) {

                $columnList->addColumn((new DateColumn($title, $path))->setFilterNullable($nullable));
            }

            elseif($fieldMapping['type'] == Type::DATETIME) {

                $columnList->addColumn((new DateTimeColumn($title, $path))->setFilterNullable($nullable));
            }

            elseif($fieldMapping['type'] == Type::BOOLEAN) {

                $columnList->addColumn((new BoolColumn($title, $path))->setFilterNullable($nullable));
            }

            elseif($fieldMapping['type'] == Type::TARRAY) {

                $columnList->addColumn((new StringColumn($title, $path . '*'))->setFilterNullable($nullable));
            }
        }

        foreach($classMetadata->associationMappings as $associationMapping) {

            $nullable = $parentNullable || !$associationMapping['isOwningSide'] || $associationMapping['nullable'];

            // only add column if targetEntity is not already part of this column list
            if(!in_array($associationMapping['targetEntity'], $this->classNames)) {

                if($associationMapping['type'] & ClassMetadataInfo::TO_ONE) {

                    $columnList->append($this->getColumnsByClassName($associationMapping['targetEntity'], $this->appendPathPart($pathParts, $associationMapping['fieldName']), $nullable));
                }
            }
        }

        return $columnList;
    }


    /**
     * {@inheritdoc}
     */
    public function load(ColumnList $columnList = null) {

        if(is_null($columnList)) {

            throw new Exception("Trying to load data without column list.");
        }

        $this->paginator = new Paginator($this->createQueryBuilder($columnList));

        return $this->getTotalCount();
    }


    /**
     * {@inheritdoc}
     */
    public function loadPage($page, $recordsPerPage, ColumnList $columnList = null) {

        if(is_null($columnList)) {

            throw new Exception("Trying to load data without column list.");
        }

        $queryBuilder = $this->createQueryBuilder($columnList)
            ->setFirstResult($recordsPerPage * ($page - 1))
            ->setMaxResults($recordsPerPage);

        $this->paginator = new Paginator($queryBuilder);

        return $this->getLoadedCount();
    }


    /**
     * @param ColumnList $columnList
     *
     * @throws Exception
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilder(ColumnList $columnList) {

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select($this->getRootAlias())
            ->from($this->rootClassName, $this->getRootAlias());

        $joinedEntities = array($this->getRootAlias());
        foreach($columnList->getIterator()->sortByDataPath() as $column) {

            $pathParts = $this->getPathParts($column->getDataPath());

            $completePathParts = $this->getCompletePathParts($this->getRootAlias(), $pathParts);
            $entityPath = $this->getPathFromParts($this->getEntityPathParts($completePathParts));

            // join and select entity if not already in list
            if(count($pathParts) > 1) {

                $join = $this->getJoinPathParts($this->getRootAlias(), $pathParts);

                if(!in_array($entityPath, $joinedEntities)) {

                    $joinedEntities[] = $entityPath;

                    $queryBuilder->leftJoin($this->getPathFromParts($join), $join[1]);
                    $queryBuilder->addSelect($this->getEntityPathPart($pathParts));
                }
            }
        }

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
    public function getLoadedCount() {

        if(is_null($this->loadedCount)) {

            if(is_null($this->paginator)) throw new Exception();

            $this->loadedCount = $this->paginator->getIterator()->count();
        }

        return $this->loadedCount;
    }


    /**
     * @return \Traversable
     */
    public function getIterator() {

        if(is_null($this->iterator)) {

            $this->iterator = new EntityDataSourceIterator($this->paginator->getIterator(), $this->idPath);
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
    public function invalidateCaches() {

        $this->iterator = null;
        $this->totalCount = null;
        $this->loadedCount = null;

        return $this;
    }


    /**
     * @param string $rootClassName
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadataInfo
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    protected function getClassMetadata($rootClassName) {

        return $this->entityManager->getMetadataFactory()->getMetadataFor($rootClassName);
    }


    /**
     * @return string
     */
    protected function getRootAlias() {

        $parts = explode('\\', $this->rootClassName);

        return lcfirst($parts[count($parts) - 1]);
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