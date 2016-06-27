<?php

namespace ArneGroskurth\Symgrid\Grid\Column;

use ArneGroskurth\Symgrid\Grid\AbstractColumn;
use ArneGroskurth\Symgrid\Grid\AbstractDataSource;
use ArneGroskurth\Symgrid\Grid\Constants;
use ArneGroskurth\Symgrid\Grid\Exception;


class AliasColumn extends AbstractColumn {

    /**
     * @var array
     */
    protected $aliases;


    /**
     * @return array
     */
    public function getAliases() {

        return $this->aliases;
    }


    /**
     * AliasColumn constructor.
     *
     * @param string $title
     * @param string $dataPath
     * @param array $aliases
     */
    public function __construct($title, $dataPath, array $aliases) {

        parent::__construct($title, $dataPath);

        $this->aliases = $aliases;
        $this->filter = Constants::FILTER_SELECT;
    }


    /**
     * {@inheritdoc}
     */
    public function renderValue($value, $target = Constants::TARGET_HTML) {

        if(is_scalar($value)) {

            return isset($this->aliases[$value]) ? $this->aliases[$value] : $value;
        }

        elseif(is_null($value)) {

            return null;
        }

        else {

            throw new Exception("Trying to render non-scalar value as alias.");
        }
    }
}