<?php

namespace ArneGroskurth\Symgrid\Twig;

use ArneGroskurth\Symgrid\Grid\Exception;
use ArneGroskurth\Symgrid\Grid\Grid;


class SymgridExtension extends \Twig_Extension {

    /**
     * @return string
     */
    public function getName() {

        return 'symgrid_extension';
    }


    /**
     * @return \Twig_Function[]
     */
    public function getFunctions() {

        return array(
            new \Twig_SimpleFunction('symgrid', array($this, 'renderGrid'), array(
                'needs_environment' => true,
                'is_safe' => array('html')
            ))
        );
    }


    /**
     * @param \Twig_Environment $twig
     * @param Grid $grid
     *
     * @return string
     * @throws Exception
     */
    public function renderGrid(\Twig_Environment $twig, Grid $grid) {

        $grid->validate();

        return $twig->render('ArneGroskurthSymgridBundle::grid.html.twig', array(
            'grid' => $grid
        ));
    }
}