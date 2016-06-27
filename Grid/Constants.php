<?php

namespace ArneGroskurth\Symgrid\Grid;


class Constants {

    const AGGREGATE_AVERAGE = 'avg';
    const AGGREGATE_MAX = 'max';
    const AGGREGATE_MIN = 'min';
    const AGGREGATE_SUM = 'sum';

    const FILTER_DATE_EXACT = 'dateExact';
    const FILTER_DATE_RANGE = 'dateRange';
    const FILTER_NUMERIC_EXACT = 'numericExact';
    const FILTER_NUMERIC_RANGE = 'numericRange';
    const FILTER_SELECT = 'select';
    const FILTER_STRING_CONTAINS = 'stringContains';
    const FILTER_STRING_EXACT = 'stringExact';

    const FILTER_KEYWORD_AFTER = 'after';
    const FILTER_KEYWORD_BEFORE = 'before';
    const FILTER_KEYWORD_CONTAINS = 'contains';
    const FILTER_KEYWORD_EQUALS = 'equals';
    const FILTER_KEYWORD_IN = 'in';
    const FILTER_KEYWORD_MAX = 'max';
    const FILTER_KEYWORD_MIN = 'min';
    const FILTER_KEYWORD_NULL = 'null';

    const ORDER_ASCENDING = 'asc';
    const ORDER_DESCENDING = 'desc';

    const TARGET_CSV = 'csv';
    const TARGET_EXCEL = 'excel';
    const TARGET_HTML = 'html';
    const TARGET_HTML_TOOLTIP = 'tooltip';
    const TARGET_PDF = 'pdf';


    /**
     * @return array
     */
    public static function getAllFilterKeywords() {

        return array(
            self::FILTER_KEYWORD_AFTER,
            self::FILTER_KEYWORD_BEFORE,
            self::FILTER_KEYWORD_CONTAINS,
            self::FILTER_KEYWORD_EQUALS,
            self::FILTER_KEYWORD_IN,
            self::FILTER_KEYWORD_MAX,
            self::FILTER_KEYWORD_MIN,
            self::FILTER_KEYWORD_NULL
        );
    }


    /**
     * @param string $filterKeyword
     *
     * @return bool
     */
    public static function isValidFilterKeyword($filterKeyword) {

        return in_array($filterKeyword, self::getAllFilterKeywords());
    }


    /**
     * @param string $filterKeyword
     *
     * @return string
     * @throws Exception
     */
    public static function validateFilterKeyword($filterKeyword) {

        if(!self::isValidFilterKeyword($filterKeyword)) {

            throw new Exception("Invalid filter keyword.");
        }

        return $filterKeyword;
    }


    /**
     * @return array
     */
    public static function getAllDisplayTargets() {

        return array(
            self::TARGET_CSV,
            self::TARGET_HTML,
            self::TARGET_EXCEL,
            self::TARGET_HTML_TOOLTIP,
            self::TARGET_PDF
        );
    }


    /**
     * @param $target
     *
     * @return bool
     */
    public static function isValidDisplayTarget($target) {

        return in_array($target, self::getAllDisplayTargets());
    }


    /**
     * @param $target
     *
     * @return mixed
     * @throws Exception
     */
    public static function validateTarget($target) {

        if(!self::isValidDisplayTarget($target)) {

            throw new Exception("Invalid display target.");
        }

        return $target;
    }


    /**
     * @return array
     */
    public static function getAllAggregationTypes() {

        return array(
            self::AGGREGATE_SUM,
            self::AGGREGATE_AVERAGE,
            self::AGGREGATE_MAX,
            self::AGGREGATE_MIN
        );
    }


    /**
     * @param $aggregationType
     *
     * @return bool
     */
    public static function isValidAggregationType($aggregationType) {

        return in_array($aggregationType, self::getAllAggregationTypes());
    }


    /**
     * @param $aggregationType
     *
     * @return mixed
     * @throws Exception
     */
    public static function validateAggregationType($aggregationType) {

        if(!self::isValidAggregationType($aggregationType)) {

            throw new Exception("Invalid aggregation type.");
        }

        return $aggregationType;
    }


    /**
     * @return array
     */
    public static function getAllFilterTypes() {

        return array(
            self::FILTER_DATE_EXACT,
            self::FILTER_DATE_RANGE,
            self::FILTER_NUMERIC_EXACT,
            self::FILTER_NUMERIC_RANGE,
            self::FILTER_SELECT,
            self::FILTER_STRING_CONTAINS,
            self::FILTER_STRING_EXACT
        );
    }


    /**
     * @param $filterType
     *
     * @return bool
     */
    public static function isValidFilterType($filterType) {

        return in_array($filterType, self::getAllFilterTypes());
    }


    /**
     * @param $filterType
     *
     * @return mixed
     * @throws Exception
     */
    public static function validateFilterType($filterType) {

        if(!self::isValidFilterType($filterType)) {

            throw new Exception("Invalid filter type.");
        }

        return $filterType;
    }


    /**
     * @return array
     */
    public static function getAllOrderDirections() {

        return array(
            self::ORDER_ASCENDING,
            self::ORDER_DESCENDING
        );
    }


    /**
     * @param $orderDirection
     *
     * @return bool
     */
    public static function isValidOrderDirection($orderDirection) {

        return in_array($orderDirection, self::getAllOrderDirections());
    }


    /**
     * @param $orderDirection
     *
     * @return mixed
     * @throws Exception
     */
    public static function validateOrderDirection($orderDirection) {

        if(!self::isValidOrderDirection($orderDirection)) {

            throw new Exception("Invalid order direction");
        }

        return $orderDirection;
    }
}
