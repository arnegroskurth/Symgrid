<?php

namespace ArneGroskurth\Symgrid\Grid;


abstract class AbstractColumn {

    use DataPathTrait;


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
     * @var bool
     */
    protected $filterable = true;

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

        $this->setTitle($title);
        $this->setDataPath($dataPath);
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
    public function getIdentifier() {

        return preg_replace('/[^a-z0-9_\-]/i', '_', $this->title);
    }

    /**
     * @return string
     */
    public function getDataPath() {

        return $this->dataPath;
    }

    /**
     * @return bool
     */
    public function isNestedDataPath() {

        return $this->isNestedPath($this->dataPath);
    }

    /**
     * @param string $dataPath
     *
     * @return AbstractColumn
     * @throws Exception
     */
    public function setDataPath($dataPath) {

        if($dataPath && !$this->isValidPath($dataPath)) {

            throw new Exception("Invalid data path given.");
        }

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
     * @return bool
     */
    public function isFilterDateExact() {

        return $this->filter == Constants::FILTER_DATE_EXACT;
    }

    /**
     * @return bool
     */
    public function isFilterDateRange() {

        return $this->filter == Constants::FILTER_DATE_RANGE;
    }

    /**
     * @return bool
     */
    public function isFilterNumericExact() {

        return $this->filter == Constants::FILTER_NUMERIC_EXACT;
    }

    /**
     * @return bool
     */
    public function isFilterNumericRange() {

        return $this->filter == Constants::FILTER_NUMERIC_RANGE;
    }

    /**
     * @return bool
     */
    public function isFilterSelect() {

        return $this->filter == Constants::FILTER_SELECT;
    }

    /**
     * @return bool
     */
    public function isFilterStringContains() {

        return $this->filter == Constants::FILTER_STRING_CONTAINS;
    }

    /**
     * @return bool
     */
    public function isFilterStringExact() {

        return $this->filter == Constants::FILTER_STRING_EXACT;
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
     * @return boolean
     */
    public function isFilterable() {

        return $this->filterable;
    }

    /**
     * @param boolean $filterable
     *
     * @return AbstractColumn
     */
    public function setFilterable($filterable) {

        $this->filterable = $filterable;

        return $this;
    }


    /**
     * @return string
     */
    public function getTypeName() {

        return lcfirst(preg_replace('/.*\\\\([a-z]+)Column/i', '$1', get_class($this)));
    }


    /**
     * @return string[]
     */
    public function getClasses() {

        $classes = array();

        if($type = $this->getTypeName()) {

            $classes[] = $type;
        }

        if($this->isSortable()) {

            $classes[] = 'sortable';
        }

        if($this->getFilter()) {

            $classes[] = 'has-filter';
            $classes[] = sprintf('filter-%s', $this->getFilter());
        }

        if($this->getCustomElementClass()) {

            $classes[] = $this->getCustomElementClass();
        }

        return $classes;
    }


    /**
     * @param DataRecord $dataRecord
     * @param string $target
     *
     * @return string
     * @throws Exception
     */
    public function render(DataRecord $dataRecord, $target = Constants::TARGET_HTML) {

        $value = $dataRecord->getValueByPath($this->dataPath);

        if($this->isNestedDataPath()) {

            return $this->renderValues(is_null($value) ? array() : $value, $target);
        }

        return $this->renderValue($value, $target);
    }


    /**
     * @param $value
     * @param string $target
     *
     * @throws Exception
     * @return string
     */
    public function renderValue($value, $target = Constants::TARGET_HTML) {

        if(!is_null($value) && !is_scalar($value)) {

            throw new Exception(sprintf("Cannot render value of type '%s'.", gettype($value)));
        }

        return is_null($value) ? null : strval($value);
    }


    /**
     * @param array $values
     * @param string $target
     *
     * @return string
     */
    public function renderValues(array $values, $target = Constants::TARGET_HTML) {

        $return = array();

        foreach($values as $value) {

            $rendered = $this->renderValue($value, $target);

            if(is_string($rendered) && !empty($rendered)) {

                $return[] = $rendered;
            }
        }

        return empty($return) ? null : implode(', ', $return);
    }
}