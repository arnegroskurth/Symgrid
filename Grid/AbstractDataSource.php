<?php

namespace ArneGroskurth\Symgrid\Grid;


abstract class AbstractDataSource {

    /**
     * @param ColumnList $columnList
     *
     * @return int Number of records loaded.
     */
    abstract public function load(ColumnList $columnList = null);


    /**
     * @param int $page
     * @param int $recordsPerPage
     * @param ColumnList $columnList
     *
     * @return int Number of records loaded.
     */
    abstract public function loadPage($page, $recordsPerPage, ColumnList $columnList = null);


    /**
     * @param ColumnList $columnList
     *
     * @return int Total number of records
     */
    abstract public function getTotalCount(ColumnList $columnList = null);


    /**
     * @return int Number of records loaded.
     */
    abstract public function getLoadedCount();


    /**
     * @return ColumnList List of columns deduced from data source.
     */
    abstract public function generateColumnList();


    /**
     * @return bool Whether this data source can be ordered.
     */
    abstract public function isSortable();


    /**
     * @param DataOrder $dataOrder
     *
     * @return $this
     */
    abstract public function applyOrder(DataOrder $dataOrder = null);


    /**
     * @return DataOrder
     */
    abstract public function getAppliedOrder();


    /**
     * @return bool Whether this data source can be applied filters on.
     */
    abstract public function isFilterable();


    /**
     * @param DataFilter $dataFilter
     *
     * @return $this
     */
    abstract public function applyFilter(DataFilter $dataFilter);


    /**
     * @return DataFilter[]
     */
    abstract public function getAppliedFilters();
}