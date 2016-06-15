<?php

namespace ArneGroskurth\Symgrid\Grid;


abstract class AbstractColumn {

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $dataPath;

    /**
     * @var bool
     */
    protected $displayed = true;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var string
     */
    protected $aggregation;

    /**
     * @var bool
     */
    protected $sortable = true;

    /**
     * @var string
     */
    protected $filter;

    /**
     * @var bool
     */
    protected $filterNullable = true;

    /**
     * @var string
     */
    protected $customElementClass;

    /**
     * @var string
     */
    protected $headerTooltip;


    /**
     * AbstractColumn constructor.
     *
     * @param string $title
     * @param string $dataPath
     */
    public function __construct($title, $dataPath = null) {

        $this->title = $title;
        $this->dataPath = $dataPath;
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
     * @return AbstractColumn
     */
    public function setTitle($title) {

        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDataPath() {

        return $this->dataPath;
    }

    /**
     * @param string $dataPath
     *
     * @return AbstractColumn
     */
    public function setDataPath($dataPath) {

        $this->dataPath = $dataPath;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDisplayed() {

        return $this->displayed;
    }

    /**
     * @param boolean $displayed
     *
     * @return AbstractColumn
     */
    public function setDisplayed($displayed) {

        $this->displayed = $displayed;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth() {

        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return AbstractColumn
     */
    public function setWidth($width) {

        $this->width = $width;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregation() {

        return $this->aggregation;
    }

    /**
     * @param string $aggregation
     *
     * @return AbstractColumn
     */
    public function setAggregation($aggregation) {

        $this->aggregation = $aggregation;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilter() {

        return $this->filter;
    }

    /**
     * @param string $filter
     *
     * @return AbstractColumn
     */
    public function setFilter($filter) {

        $this->filter = $filter;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFilterNullable() {

        return $this->filterNullable;
    }

    /**
     * @param boolean $filterNullable
     *
     * @return AbstractColumn
     */
    public function setFilterNullable($filterNullable) {

        $this->filterNullable = $filterNullable;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomElementClass() {

        return $this->customElementClass;
    }

    /**
     * @param string $customElementClass
     *
     * @return AbstractColumn
     */
    public function setCustomElementClass($customElementClass) {

        $this->customElementClass = $customElementClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderTooltip() {

        return $this->headerTooltip;
    }

    /**
     * @param string $headerTooltip
     *
     * @return AbstractColumn
     */
    public function setHeaderTooltip($headerTooltip) {

        $this->headerTooltip = $headerTooltip;

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
     * @return AbstractColumn
     */
    public function setSortable($sortable) {

        $this->sortable = $sortable;

        return $this;
    }


    /**
     * @param AbstractDataSource $data
     * @param string $target
     *
     * @return string
     */
    public function render(AbstractDataSource $data, $target = \ArneGroskurth\Symgrid\TARGET_HTML) {

        return $data->current()->getValueByPath($this->dataPath);
    }
}