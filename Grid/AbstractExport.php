<?php

namespace ArneGroskurth\Symgrid\Grid;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\IdentityTranslator;


abstract class AbstractExport {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Grid
     */
    protected $grid;


    /**
     * AbstractExport constructor.
     *
     * @param ContainerInterface $container
     * @param Grid $grid
     */
    public function __construct(ContainerInterface $container, Grid $grid) {

        $this->container = $container;
        $this->grid = $grid;
    }


    /**
     * @param string $locale Target locale to translate content to.
     * @return Response
     */
    abstract public function render($locale = null);


    /**
     * @param string $string
     * @param string $locale
     *
     * @return string
     */
    protected function translate($string, $locale = null) {

        return $this->getTranslator()->trans($string, array(), null, $locale ?: $this->getUserLocale());
    }


    /**
     * @return IdentityTranslator
     */
    protected function getTranslator() {

        return $this->container->get('translator');
    }


    /**
     * @return string
     */
    protected function getUserLocale() {

        return $this->container->get('request')->getLocale();
    }


    /**
     * Disables Symfony profiler as it appends the Symfony toolbar to any html-response breaking a html-export.
     */
    protected function disableSymfonyProfiler() {

        $this->container->get('profiler')->disable();
    }
}