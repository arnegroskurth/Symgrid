<?php

namespace ArneGroskurth\Symgrid\Grid;


abstract class AbstractAction {

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
     * AbstractAction constructor.
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
     * @return AbstractAction
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
     * @return AbstractAction
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
     * @return AbstractAction
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
     * @return AbstractAction
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
     * @return AbstractAction
     */
    public function setTarget($target) {

        $this->target = $target;

        return $this;
    }
}