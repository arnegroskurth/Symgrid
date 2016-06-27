<?php

namespace ArneGroskurth\Symgrid\Grid;


class DataOrder {

    use DataPathTrait;


    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $direction;


    /**
     * DataOrder constructor.
     *
     * @param string $path
     * @param string $direction
     *
     * @throws Exception
     */
    public function __construct($path, $direction) {

        $this->path = $this->validatePath($path);
        $this->direction = Constants::validateOrderDirection($direction);
    }

    /**
     * @return string
     */
    public function getPath() {

        return $this->path;
    }

    /**
     * @return string
     */
    public function getDirection() {

        return $this->direction;
    }
}