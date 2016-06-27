<?php

namespace ArneGroskurth\Symgrid\Grid\Column;

use ArneGroskurth\Symgrid\Grid\AbstractColumn;
use ArneGroskurth\Symgrid\Grid\Constants;
use ArneGroskurth\Symgrid\Grid\DataRecord;


class CallbackColumn extends AbstractColumn {

    /**
     * @var \Closure
     */
    protected $callback;


    /**
     * CallbackColumn constructor.
     *
     * @param string $title
     * @param \Closure $callback
     */
    public function __construct($title, \Closure $callback) {

        parent::__construct($title);

        $this->callback = $callback->bindTo($this);
    }

    
    /**
     * {@inheritdoc}
     */
    public function render(DataRecord $dataRecord, $target = Constants::TARGET_HTML) {

        return call_user_func($this->callback, $dataRecord, $target);
    }
}