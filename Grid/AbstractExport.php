<?php

namespace ArneGroskurth\Symgrid\Grid;

use Symfony\Component\HttpFoundation\Response;


abstract class AbstractExport {

    /**
     * @var Grid
     */
    protected $grid;


    /**
     * AbstractExport constructor.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid) {

        $this->grid = $grid;
    }


    /**
     * @return Response
     */
    abstract public function render();
}