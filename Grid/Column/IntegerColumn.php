<?php

namespace ArneGroskurth\Symgrid\Grid\Column;


class IntegerColumn extends NumericColumn {

    /**
     * IntegerColumn constructor.
     *
     * @param string $title
     * @param string $dataPath
     * @param string $thousandsSeparator
     */
    public function __construct($title, $dataPath, $thousandsSeparator = ' ') {

        parent::__construct($title, $dataPath, 0, ',', $thousandsSeparator);
    }
}