<?php

namespace ArneGroskurth\Symgrid\Grid;


abstract class AbstractExport {

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var AbstractDataSource
     */
    protected $dataSource;


    /**
     * AbstractExport constructor.
     *
     * @param Grid $grid
     * @param AbstractDataSource $dataSource
     */
    public function __construct(Grid $grid, AbstractDataSource $dataSource) {

        $this->grid = $grid;
        $this->dataSource = $dataSource;
    }


    /**
     * @return Response
     */
    abstract public function render();
}