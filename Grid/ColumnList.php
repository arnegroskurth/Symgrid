<?php

namespace ArneGroskurth\Symgrid\Grid;


class ColumnList implements \ArrayAccess, \Countable, \IteratorAggregate {

    /**
     * @var AbstractColumn[]
     */
    protected $columns = array();


    /**
     * @param bool $includeHidden
     *
     * @return int
     */
    public function count($includeHidden = false) {

        return $this->getIterator($includeHidden)->count();
    }


    /**
     * @param bool $includeHidden
     *
     * @return \ArrayIterator
     */
    public function getIterator($includeHidden = false) {

        return new ColumnListIterator($this->columns, $includeHidden);
    }


    /**
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset) {

        return isset($this->columns[$offset]);
    }


    /**
     * @param int $offset
     *
     * @return AbstractColumn
     * @throws Exception
     */
    public function offsetGet($offset) {

        if(!isset($this->columns[$offset])) {

            throw new Exception("Trying to get column by non-existing offset.");
        }

        return $this->columns[$offset];
    }


    /**
     * @param int $offset
     * @param AbstractColumn $value
     *
     * @throws Exception
     */
    public function offsetSet($offset, $value) {

        if(!($value instanceof AbstractColumn)) {

            throw new Exception("Trying to add invalid column.");
        }

        $this->columns[$offset] = $value;
    }


    /**
     * @param int $offset
     *
     * @throws Exception
     */
    public function offsetUnset($offset) {

        if(!isset($this->columns[$offset])) {

            throw new Exception("Trying to remove column by non-existing index.");
        }

        unset($this->columns[$offset]);
    }
}