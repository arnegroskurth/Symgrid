<?php

namespace ArneGroskurth\Symgrid\Grid;


class ColumnList implements \Countable, \IteratorAggregate {

    /**
     * @var AbstractColumn[]
     */
    protected $columns = array();


    /**
     * @param AbstractColumn $column Column to add to grid.
     * @param int $position Zero-based position as integer to add the column at. Zero is the leftmost column. Column is appended to the end if omitted.
     * @param bool $replaceOnCollision Whether to replace a potential existing column with the same identifier.
     *
     * @return $this
     * @throws Exception
     */
    public function addColumn(AbstractColumn $column, $position = null, $replaceOnCollision = false) {
        
        foreach($this->columns as $key => $existingColumn) {
            
            if($column->getIdentifier() == $existingColumn->getIdentifier()) {

                if($replaceOnCollision) {

                    $this->removeColumnByIdentifier($column->getIdentifier());

                    return $this->addColumn($column, is_null($position) ? $key : $position);
                }

                else throw new Exception("Trying to add column with identifier that already exists.");
            }
        }

        if(is_int($position)) {
            
            array_splice($this->columns, $position, 0, array($column));
        }
        else {

            $this->columns[] = $column;
        }

        return $this;
    }


    /**
     * Shortcut for column replacement using addColumn.
     *
     * @param AbstractColumn $newColumn Column to replace existing column with identical identifier with.
     *
     * @return $this
     * @throws Exception
     */
    public function replaceColumn(AbstractColumn $newColumn) {

        if(empty($oldColumn = $this->getByIdentifier($newColumn->getIdentifier()))) {

            throw new Exception("Trying to replace non-existing column.");
        }

        return $this->addColumn($newColumn, null, true);
    }


    /**
     * @param string $identifier
     * @param bool $throwExceptionOnFailure
     *
     * @throws Exception
     * @return $this
     */
    public function removeColumnByIdentifier($identifier, $throwExceptionOnFailure = true) {
        
        foreach($this->columns as $key => $column) {

            if($column->getIdentifier() == $identifier) {

                unset($this->columns[$key]);

                $this->columns = array_values($this->columns);

                return $this;
            }
        }

        if($throwExceptionOnFailure) {

            throw new Exception(sprintf("Trying to remove non-existing column with identifier '%s'.", $identifier));
        }

        return $this;
    }


    /**
     * Appends another columnList to the end of this columnList.
     *
     * @param ColumnList $columnList
     *
     * @return $this
     * @throws Exception
     */
    public function append(ColumnList $columnList) {

        foreach($columnList->getIterator(true) as $column) {

            $this->addColumn($column);
        }

        return $this;
    }


    /**
     * @param bool $includeHidden
     *
     * @return bool
     */
    public function hasFilterableColumns($includeHidden = false) {

        foreach($this->getIterator($includeHidden) as $column) {

            if($column->getFilter()) {

                return true;
            }
        }

        return false;
    }


    /**
     * @param bool $includeHidden
     *
     * @return bool
     */
    public function hasAggregatableColumns($includeHidden = false) {

        foreach($this->getIterator($includeHidden) as $column) {

            if($column->getAggregation()) {

                return true;
            }
        }

        return false;
    }


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
     * @return ColumnListIterator
     */
    public function getIterator($includeHidden = false) {

        return new ColumnListIterator($this->columns, $includeHidden);
    }


    /**
     * @param string $identifier
     *
     * @return AbstractColumn
     */
    public function getByIdentifier($identifier) {

        foreach($this->columns as $column) {

            if($column->getIdentifier() == $identifier) {

                return $column;
            }
        }

        return null;
    }
}