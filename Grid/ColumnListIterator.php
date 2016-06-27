<?php

namespace ArneGroskurth\Symgrid\Grid;


class ColumnListIterator implements \Countable, \Iterator {

    /**
     * @var AbstractColumn[]
     */
    protected $columns;

    /**
     * @var int[]
     */
    protected $offsets;

    /**
     * @var int
     */
    protected $position = 0;


    /**
     * ColumnListIterator constructor.
     *
     * @param AbstractColumn[] $columns
     * @param bool $includeHidden
     */
    public function __construct(array $columns, $includeHidden = false) {

        $this->columns = $columns;

        $this->saveOffsets($includeHidden);
    }


    /**
     * Sorts columns by data path.
     *
     * @param bool $includeHidden
     * 
     * @return ColumnListIterator
     */
    public function sortByDataPath($includeHidden = false) {

        usort($this->columns, function(AbstractColumn $a, AbstractColumn $b) {

            return strcmp($a->getDataPath(), $b->getDataPath());
        });

        $this->saveOffsets($includeHidden);

        return $this;
    }


    /**
     * @return int
     */
    public function count() {

        return count($this->offsets);
    }

    /**
     * @return AbstractColumn
     */
    public function current() {

        return $this->columns[$this->offsets[$this->position]];
    }

    /**
     * @return int
     */
    public function key() {

        return $this->offsets[$this->position];
    }

    /**
     * @return void
     */
    public function rewind() {

        $this->position = 0;
    }


    /**
     * @return void
     */
    public function next() {

        ++$this->position;
    }


    /**
     * @return bool
     */
    public function valid() {

        return $this->position < $this->count();
    }


    /**
     * Saves indizes of columns that match filter criteria for iteration.
     *
     * @param bool $includeHidden
     */
    protected function saveOffsets($includeHidden) {

        foreach($this->columns as $key => $column) {

            if($includeHidden || $column->isDisplayed()) {

                $this->offsets[] = $key;
            }
        }
    }
}