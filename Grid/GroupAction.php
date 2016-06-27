<?php

namespace ArneGroskurth\Symgrid\Grid;


class GroupAction {

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $targetAction;

    /**
     * @var string
     */
    protected $targetUrl;

    /**
     * @var string
     */
    protected $parameterName = 'id';

    /**
     * @var string Target frame to use for (virtual) form submission.
     */
    protected $target = '_self';

    /**
     * @var string Http method to use.
     */
    protected $method = 'get';

    /**
     * @var string
     */
    protected $confirmationMessage;


    /**
     * GroupAction constructor.
     *
     * @param string $title
     * @param string $targetAction
     */
    public function __construct($title, $targetAction = null) {

        $this->title = $title;
        $this->targetAction = $targetAction;
    }


    /**
     * @return string
     */
    public function getTitle() {

        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return GroupAction
     */
    public function setTitle($title) {

        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetAction() {

        return $this->targetAction;
    }

    /**
     * @param string $targetAction
     *
     * @return GroupAction
     */
    public function setTargetAction($targetAction) {

        $this->targetAction = $targetAction;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetUrl() {

        return $this->targetUrl;
    }

    /**
     * @param string $targetUrl
     *
     * @return GroupAction
     */
    public function setTargetUrl($targetUrl) {

        $this->targetUrl = $targetUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getParameterName() {

        return $this->parameterName;
    }

    /**
     * @param string $parameterName
     *
     * @return GroupAction
     */
    public function setParameterName($parameterName) {

        $this->parameterName = $parameterName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTarget() {

        return $this->target;
    }

    /**
     * @param string $target
     *
     * @return GroupAction
     */
    public function setTarget($target) {

        $this->target = $target;

        return $this;
    }

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