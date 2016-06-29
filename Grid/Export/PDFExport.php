<?php

namespace ArneGroskurth\Symgrid\Grid\Export;

use ArneGroskurth\Symgrid\Grid\AbstractExport;
use ArneGroskurth\Symgrid\Grid\Exception;


class PDFExport extends AbstractExport{

    public function render($locale = null) {

        throw new Exception("PDF export is not implemented yet.");
    }
}