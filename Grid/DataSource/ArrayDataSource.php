<?php

namespace ArneGroskurth\Symgrid\Grid\DataSource;

use ArneGroskurth\Symgrid\Grid\AbstractDataSource;
use ArneGroskurth\Symgrid\Grid\ArraysTrait;
use ArneGroskurth\Symgrid\Grid\ColumnList;
use ArneGroskurth\Symgrid\Grid\Column\BoolColumn;
use ArneGroskurth\Symgrid\Grid\Column\DateColumn;
use ArneGroskurth\Symgrid\Grid\Column\DateTimeColumn;
use ArneGroskurth\Symgrid\Grid\Column\NumericColumn;
use ArneGroskurth\Symgrid\Grid\Column\StringColumn;
use ArneGroskurth\Symgrid\Grid\DataFilter;
use ArneGroskurth\Symgrid\Grid\DataOrder;
use ArneGroskurth\Symgrid\Grid\DataPathTrait;
use ArneGroskurth\Symgrid\Grid\DataRecord;
use ArneGroskurth\Symgrid\Grid\Exception;


class ArrayDataSource extends AbstractDataSource implements \Iterator {

    use ArraysTrait;
    use DataPathTrait;


    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $idPath;

    /**
     * @var int
     */
    protected $startPosition;

    /**
     * @var int
     */
    protected $currentPosition;

    /**
     * @var int
     */
    protected $endPosition;


    /**
     * ArraySource constructor.
     *
     * @param array $data
     * @param string $idPath
     */
    public function __construct(array $data, $idPath) {

        $this->data = $data;
        $this->idPath = $idPath;
    }


    /**
     * {@inheritdoc}
     */
    public function load(ColumnList $columnList = null) {

        $this->startPosition = 0;
        $this->endPosition = count($this->data) - 1;

        return $this->getTotalCount();
    }


    /**
     * {@inheritdoc}
     */
    public function loadPage($page, $recordsPerPage, ColumnList $columnList = null) {

        $this->startPosition = $recordsPerPage * ($page - 1);
        $this->endPosition = min($recordsPerPage * $page - 1, $this->getTotalCount() - $this->startPosition) + 1;

        $this->rewind();

        return $this->getLoadedCount();
    }

    /**
    * Generates a list of columns from the data structure.
    *
    * @return ColumnList
    * @throws Exception
    */
    public function generateColumnList() {

        if(empty($this->loadPage(1, 1))) {

            throw new Exception("Cannot generate column list from empty array data source.");
        }

        return $this->getColumnsByRecord($this->current()->getRecord());
    }


    /**
     * @param $record
     * @param string[] $pathParts
     *
     * @return ColumnList
     */
    protected function getColumnsByRecord($record, array $pathParts = array()) {

        $path = implode('.', $pathParts);
        $title = $this->getTitleByPath($pathParts);


        $columnList = new ColumnList();

        if(is_string($record)) {

            $columnList->addColumn(new StringColumn($title, $path));
        }

        elseif(is_int($record)) {

            $columnList->addColumn(new NumericColumn($title, $path, 0));
        }

        elseif(is_float($record)) {

            $columnList->addColumn(new NumericColumn($title, $path, 2));
        }

        elseif(is_bool($record)) {

            $columnList->addColumn(new BoolColumn($title, $path));
        }

        elseif(is_object($record) && $record instanceof \DateTime) {

            // probably a date
            if($record->format('H:i:s') == '00:00:00') {

                $columnList->addColumn(new DateColumn($title, $path));
            }

            // probably a date with time
            else {

                $columnList->addColumn(new DateTimeColumn($title, $path));
            }
        }

        elseif(is_object($record) && $record instanceof \stdClass) {

            // use all fields as columns
            $columnList->append($this->getColumnsByRecord((array)$record, $pathParts));
        }

        elseif(is_array($record) && !empty($record)) {

            // associative array given
            if($this->isAssociativeArray($record)) {

                foreach(array_keys($record) as $key) {

                    $columnList->append($this->getColumnsByRecord($record[$key], $this->appendPathPart($pathParts, $key)));
                }
            }

            // check for consistent content
            elseif(!$this->arrayHasStringKeys($record)) {

                $nestedPath = $path . '*';

                if($this->isFloatArray($record)) {

                    $columnList->addColumn(new NumericColumn($title, $nestedPath));
                }

                elseif($this->isIntegerArray($record)) {

                    $columnList->addColumn(new NumericColumn($title, $nestedPath, 0));
                }

                elseif($this->isStringArray($record)) {

                    $columnList->addColumn(new StringColumn($title, $nestedPath));
                }
            }
        }

        return $columnList;
    }


    /**
     * {@inheritdoc}
     */
    public function getTotalCount(ColumnList $columnList = null) {

        return count($this->data);
    }


    /**
     * {@inheritdoc}
     */
    public function getLoadedCount() {

        return $this->endPosition - $this->startPosition;
    }


    /**
     * {@inheritdoc}
     */
    public function isSortable() {

        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function applyOrder(DataOrder $dataOrder = null) {

        throw new Exception("Data ordering not implemented for this data source.");
    }


    /**
     * {@inheritdoc}
     */
    public function getAppliedOrder() {

        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function isFilterable() {

        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function applyFilter(DataFilter $dataFilter) {

        throw new Exception("Data filtering not implemented for this data source.");
    }


    /**
     * {@inheritdoc}
     */
    public function getAppliedFilters() {

        return array();
    }


    /**
     * {@inheritdoc}
     */
    public function current() {

        return DataRecord::createByIdPath($this->data[$this->currentPosition], $this->idPath);
    }


    /**
     * {@inheritdoc}
     */
    public function next() {

        ++$this->currentPosition;
    }


    /**
     * {@inheritdoc}
     */
    public function key() {

        return $this->currentPosition;
    }


    /**
     * {@inheritdoc}
     */
    public function valid() {

        return $this->currentPosition < $this->endPosition;
    }


    /**
     * {@inheritdoc}
     */
    public function rewind() {

        $this->currentPosition = $this->startPosition;
    }
}