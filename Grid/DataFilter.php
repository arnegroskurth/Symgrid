<?php

namespace ArneGroskurth\Symgrid\Grid;


class DataFilter {

    use DataPathTrait;


    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $keyword;

    /**
     * @var string
     */
    protected $value;


    /**
     * DataFilter constructor.
     *
     * @param string $path
     * @param string $keyword
     * @param string $value
     * @throws Exception
     */
    public function __construct($path, $keyword, $value) {

        if(!is_string($value)) {

            throw new Exception("Filter value has to be a string.");
        }

        $this->path = $this->validatePath($path);
        $this->keyword = Constants::validateFilterKeyword($keyword);
        $this->value = $value;
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
    public function getKeyword() {

        return $this->keyword;
    }

    /**
     * @return string
     */
    public function getValue() {

        return $this->value;
    }
}