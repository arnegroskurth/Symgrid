<?php

namespace ArneGroskurth\Symgrid\Grid;


abstract class AbstractDataSource implements \Traversable {

    /**
     * @return DataRecord
     */
    abstract public function current();

    /**
     * @return scalar
     */
    abstract public function key();

    /**
     * @return void
     */
    abstract public function next();

    /**
     * @return void
     */
    abstract public function rewind();

    /**
     * @return bool
     */
    abstract public function valid();
}