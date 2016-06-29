<?php

namespace ArneGroskurth\Symgrid\Grid\Column;

use ArneGroskurth\Symgrid\Grid\AbstractColumn;
use ArneGroskurth\Symgrid\Grid\AbstractDataSource;
use ArneGroskurth\Symgrid\Grid\Constants;


class BoolColumn extends AliasColumn {

    /**
     * {@inheritdoc}
     */
    public function __construct($title, $dataPath) {

        parent::__construct($title, $dataPath, array(
            true => 'Yes',
            false => 'No'
        ));
    }
}