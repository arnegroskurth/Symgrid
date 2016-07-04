<?php

namespace ArneGroskurth\Symgrid\Grid;


class GroupAction extends AbstractAction {

    /**
     * @var string Http method to use.
     */
    protected $method = 'get';

    /**
     * @var string
     */
    protected $confirmationMessage;


    /**
     * @return string
     */
    public function getMethod() {

        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return GroupAction
     */
    public function setMethod($method) {

        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationMessage() {

        return $this->confirmationMessage;
    }

    /**
     * @param string $confirmationMessage
     *
     * @return GroupAction
     */
    public function setConfirmationMessage($confirmationMessage) {

        $this->confirmationMessage = $confirmationMessage;

        return $this;
    }
}