<?php

namespace ArneGroskurth\Symgrid\Grid;


class Grid implements \IteratorAggregate, \Serializable {

    /**
     * @var string
     */
    protected $title;

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
    protected $exportFileName = 'SymgridExport';

    /**
     * @var bool
     */
    protected $pageable = true;

    /**
     * @var int
     */
    protected $pageSize = 30;

    /**
     * @var bool
     */
    protected $aggregatable = true;

    /**
     * @var GroupAction[]
     */
    protected $groupActions = array();

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
     * @return AbstractDataSource
     * @throws Exception
     */
    public function getIterator() {
        
        if(is_null($this->dataSource)) {
            
            throw new Exception("Trying to iterate over grid without data source.");
        }

        return $this->dataSource;
    }


    /**
     * @return string
     */
    public function serialize() {

        // TODO: Implement serialize() method.
    }


    /**
     * @param string $serialized
     */
    public function unserialize($serialized) {

        // TODO: Implement unserialize() method.
    }
}