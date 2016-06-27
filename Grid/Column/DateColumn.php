<?php

namespace ArneGroskurth\Symgrid\Grid\Column;

use ArneGroskurth\Symgrid\Grid\AbstractColumn;
use ArneGroskurth\Symgrid\Grid\Constants;
use ArneGroskurth\Symgrid\Grid\Exception;


class DateColumn extends AbstractColumn {

    /**
     * @var string
     */
    protected $format;


    /**
     * DateColumn constructor.
     *
     * @param string $title
     * @param string $dataPath
     * @param string $format
     */
    public function __construct($title, $dataPath, $format = 'd.m.Y') {

        parent::__construct($title, $dataPath);

        $this->format = $format;
        $this->filter = Constants::FILTER_DATE_RANGE;
    }


    /**
     * {@inheritdoc}
     */
    public function renderValue($value, $target = Constants::TARGET_HTML) {

        if(is_null($value)) {

            return null;
        }

        elseif(is_object($value) && $value instanceof \DateTime) {

            return $value->format($this->format);
        }

        elseif(is_string($value)) {

            try {

                return (new \DateTime($value))->format($this->format);
            }
            catch(\Exception $e) {

                throw new Exception("Record value is malformed and cannot be displayed as date.", 0, $e);
            }
        }

        else throw new Exception("Record value cannot be displayed as date.");
    }
}