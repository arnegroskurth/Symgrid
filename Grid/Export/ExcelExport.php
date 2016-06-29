<?php

namespace ArneGroskurth\Symgrid\Grid\Export;

use ArneGroskurth\Symgrid\Grid\AbstractExport;
use ArneGroskurth\Symgrid\Grid\Exception;


class ExcelExport extends AbstractExport{

    public function render($locale = null) {

        throw new Exception("Excel export is not implemented yet.");
    }
}