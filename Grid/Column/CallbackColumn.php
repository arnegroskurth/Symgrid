<?php

namespace ArneGroskurth\Symgrid\Column;

use ArneGroskurth\Symgrid\Grid\AbstractColumn;
use ArneGroskurth\Symgrid\Grid\AbstractDataSource;


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

        $this->callback = $callback;
    }


    /**
     * @param AbstractDataSource $data
     * @param string $target
     *
     * @return mixed
     */
    public function render(AbstractDataSource $data, $target = \ArneGroskurth\Symgrid\TARGET_HTML) {

        return call_user_func($this->callback, $data, $target);
    }
}