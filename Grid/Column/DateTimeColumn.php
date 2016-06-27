<?php

namespace ArneGroskurth\Symgrid\Grid\Column;


class DateTimeColumn extends DateColumn {

    /**
     * {@inheritdoc}
     */
    public function __construct($title, $dataPath, $format = 'd.m.Y H:i:s') {

        parent::__construct($title, $dataPath, $format);
    }
}