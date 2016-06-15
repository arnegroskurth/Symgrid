<?php

namespace ArneGroskurth\Symgrid\Grid;


class DataRecord {

    /**
     * @var mixed
     */
    protected $record;


    /**
     * DataRecord constructor.
     *
     * @param $record
     */
    public function __construct($record) {

        $this->record = $record;
    }


    /**
     * @param string $path
     * @return mixed
     */
    public function getValueByPath($path) {

        $value = $this->record;

        foreach(explode('.', $path) as $pathPart) {

            if(is_null($value)) {

                break;
            }

            if(is_array($value) && isset($value[$pathPart])) {

                $value = $value[$pathPart];
            }

            elseif(is_object($value)) {

                $getterFunctionName = sprintf("get%s", ucfirst($pathPart));
                $isserFunctionName = sprintf("is%s", ucfirst($pathPart));

                if(isset($value->$pathPart)) {

                    $value = $value->$pathPart;
                }
                elseif(is_callable(array($value, $getterFunctionName))) {

                    $value = $value->$getterFunctionName();
                }
                elseif(is_callable(array($value, $isserFunctionName))) {

                    $value = $value->$isserFunctionName();
                }
                elseif(is_callable(array($value, $pathPart))) {

                    $value = $value->$pathPart();
                }
                else {

                    $value = null;
                }
            }

            else {

                $value = null;
            }
        }

        return $value;
    }
}