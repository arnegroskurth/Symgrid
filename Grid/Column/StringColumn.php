<?php

namespace ArneGroskurth\Symgrid\Grid\Column;

use ArneGroskurth\Symgrid\Grid\AbstractColumn;
use ArneGroskurth\Symgrid\Grid\Constants;


class StringColumn extends AbstractColumn {

    /**
     * {@inheritdoc}
     */
    public function __construct($title, $dataPath) {

        parent::__construct($title, $dataPath);

        $this->filter = Constants::FILTER_STRING_CONTAINS;
    }
}