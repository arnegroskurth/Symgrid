<?php

namespace ArneGroskurth\Symgrid\Grid\DataSource;

use ArneGroskurth\Symgrid\Grid\DataRecord;


class DataRecordWrappingIterator extends \IteratorIterator {

    /**
     * @var string
     */
    protected $idPath;


    /**
     * DataRecordWrappingIterator constructor.
     *
     * @param \Traversable $iterator
     * @param string $idPath
     */
    public function __construct(\Traversable $iterator, $idPath) {

        parent::__construct($iterator);

        $this->idPath = $idPath;
    }


    /**
     * @return DataRecord
     * @throws \ArneGroskurth\Symgrid\Grid\Exception
     */
    public function current() {

        $record = parent::current();

        return DataRecord::createByIdPath($record, $this->idPath);
    }


    /**
     * @return scalar
     */
    public function key() {

        return $this->current()->getId();
    }
}