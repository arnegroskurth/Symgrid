<?php

namespace ArneGroskurth\Symgrid\Twig;


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
            'symgrid' => new \Twig_Function_Function()
        );
    }
}