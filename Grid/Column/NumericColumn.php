<?php

namespace ArneGroskurth\Symgrid\Grid\Column;

use ArneGroskurth\Symgrid\Grid\AbstractColumn;
use ArneGroskurth\Symgrid\Grid\Constants;


class NumericColumn extends AbstractColumn {
    
    /**
     * @var int
     */
    protected $decimalPlaces;

    /**
     * @var string
     */
    protected $decimalPoint;

    /**
     * @var string
     */
    protected $thousandsSeparator;


    /**
     * NumericColumn constructor.
     *
     * @param string $title
     * @param string $dataPath
     * @param int $decimalPlaces
     * @param string $decimalPoint
     * @param string $thousandsSeparator
     */
    public function __construct($title, $dataPath, $decimalPlaces = 2, $decimalPoint = ',', $thousandsSeparator = ' ') {

        parent::__construct($title, $dataPath);

        $this->decimalPlaces = $decimalPlaces;
        $this->decimalPoint = $decimalPoint;
        $this->thousandsSeparator = $thousandsSeparator;
        $this->filter = Constants::FILTER_NUMERIC_RANGE;
    }


    /**
     * {@inheritdoc}
     */
    public function renderValue($value, $target = Constants::TARGET_HTML) {
        
        return is_null($value) ? null : number_format($value, $this->decimalPlaces, $this->decimalPoint, $this->thousandsSeparator);
    }
}