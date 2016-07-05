<?php

namespace ArneGroskurth\Symgrid\Grid\Column;

use ArneGroskurth\Symgrid\Grid\AbstractColumn;
use ArneGroskurth\Symgrid\Grid\Constants;
use ArneGroskurth\Symgrid\Grid\Exception;


class CurrencyColumn extends AbstractColumn {

    const CURRENCY_EURO = 'euro';
    const CURRENCY_POUND_STERLING = 'gbp';
    const CURRENCY_US_DOLLAR = 'usd';
    const CURRENCY_YEN = 'yen';


    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $decimalPoint;

    /**
     * @var string
     */
    protected $thousandsSeparator;

    /**
     * @var int
     */
    protected $decimalPlaces;


    /**
     * CurrencyColumn constructor.
     *
     * @param string $title
     * @param string $dataPath
     * @param string $currency
     * @param string $decimalPoint
     * @param string $thousandsSeparator
     * @param int $decimalPlaces
     */
    public function __construct($title, $dataPath, $currency = self::CURRENCY_EURO, $decimalPoint = ',', $thousandsSeparator = ' ', $decimalPlaces = 2) {

        parent::__construct($title, $dataPath);

        $this->currency = $currency;
        $this->decimalPoint = $decimalPoint;
        $this->thousandsSeparator = $thousandsSeparator;
        $this->decimalPlaces = $decimalPlaces;
        $this->aggregation = Constants::AGGREGATE_SUM;
    }


    /**
     * {@inheritdoc}
     */
    public function renderValue($value, $target = Constants::TARGET_HTML) {

        return is_null($value) ? null : sprintf("%s %s", number_format($value, $this->decimalPlaces, $this->decimalPoint, $this->thousandsSeparator), self::getSymbol($this->currency));
    }


    /**
     * @param $currency
     *
     * @return string
     * @throws Exception
     */
    public static function getSymbol($currency) {

        static $symbols = array(
            self::CURRENCY_EURO => '€',
            self::CURRENCY_POUND_STERLING => '£',
            self::CURRENCY_US_DOLLAR => '$',
            self::CURRENCY_YEN => '¥'
        );

        if(!isset($symbols[$currency])) {

            throw new Exception("Invalid currency symbold requested.");
        }

        return $symbols[$currency];
    }
}