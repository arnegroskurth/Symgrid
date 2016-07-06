<?php

namespace ArneGroskurth\Symgrid\Grid\DataSource;

use ArneGroskurth\Symgrid\Grid\Column\BoolColumn;
use ArneGroskurth\Symgrid\Grid\Column\DateColumn;
use ArneGroskurth\Symgrid\Grid\Column\DateTimeColumn;
use ArneGroskurth\Symgrid\Grid\Column\IntegerColumn;
use ArneGroskurth\Symgrid\Grid\Column\NumericColumn;
use ArneGroskurth\Symgrid\Grid\Column\StringColumn;
use ArneGroskurth\Symgrid\Grid\ColumnList;
use ArneGroskurth\Symgrid\Grid\DataPathTrait;
use ArneGroskurth\Symgrid\Grid\Exception;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Pagination\Paginator;


class EntityDataSource extends QueryBuilderDataSource {

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
     * @var ColumnList
     */
    protected $columnList;

    /**
     * @var string[]
     */
    protected $classNames = array();


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


        parent::__construct($this->createQueryBuilder($this->getColumnList()), $rootClassMetadata->identifier[0]);
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
    public function getColumnList() {

        if(is_null($this->columnList)) {

            $this->classNames = array();
            $this->columnList = $this->getColumnsByClassName($this->rootClassName);
        }

        return $this->columnList;
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

            $isId = isset($fieldMapping['id']);
            $nullable = $parentNullable || $fieldMapping['nullable'];

            $column = null;

            if($fieldMapping['type'] == Type::INTEGER) {

                $column = new IntegerColumn($title, $path);
            }

            elseif($fieldMapping['type'] == Type::FLOAT) {

                $column = new NumericColumn($title, $path);
            }

            elseif(in_array($fieldMapping['type'], array(Type::STRING, Type::TEXT, Type::SIMPLE_ARRAY))) {

                $column = new StringColumn($title, $path);
            }

            elseif($fieldMapping['type'] == Type::DATE) {

                $column = new DateColumn($title, $path);
            }

            elseif($fieldMapping['type'] == Type::DATETIME) {

                $column = new DateTimeColumn($title, $path);
            }

            elseif($fieldMapping['type'] == Type::BOOLEAN) {

                $column = new BoolColumn($title, $path);
            }

            elseif($fieldMapping['type'] == Type::TARRAY) {

                $column = new StringColumn($title, $path . '*');
            }

            
            if($column) {

                $column->setFilterNullable($nullable);

                if($isId) {

                    $column->setAggregation(null);
                }
                
                $columnList->addColumn($column);
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
     * @param ColumnList $columnList
     *
     * @throws Exception
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder(ColumnList $columnList) {

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

        return $queryBuilder;
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
     * {@inheritdoc}
     */
    protected function getRootAlias() {

        $parts = explode('\\', $this->rootClassName);

        return lcfirst($parts[count($parts) - 1]);
    }
}