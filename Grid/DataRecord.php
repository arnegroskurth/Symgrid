<?php

namespace ArneGroskurth\Symgrid\Grid;


class DataRecord {

    use DataPathTrait;


    /**
     * @var mixed
     */
    protected $record;

    /**
     * @var scalar
     */
    protected $id;


    /**
     * DataRecord constructor.
     *
     * @param $record
     * @param $id
     * @throws Exception
     */
    public function __construct($record, $id) {

        if(!is_scalar($id)) {

            throw new Exception("Trying to construct DataRecord with non-scalar id.");
        }

        $this->record = $record;
        $this->id = $id;
    }


    /**
     * @return mixed
     */
    public function getRecord() {

        return $this->record;
    }


    /**
     * @return scalar
     */
    public function getId() {

        return $this->id;
    }


    /**
     * @param string $path
     *
     * @return mixed
     * @throws Exception
     */
    public function getValueByPath($path) {

        return static::resolvePath($this->record, $path);
    }


    /**
     * @param mixed $record
     * @param string $idPath
     *
     * @return DataRecord
     * @throws Exception
     */
    public static function createByIdPath($record, $idPath) {

        $instance = new self($record, 0);

        if(!is_scalar($instance->id = $instance->getValueByPath($idPath))) {

            throw new Exception("Given idPath results in non-scalar id.");
        }

        return $instance;
    }


    /**
     * @param mixed $object Root object to apply given path on.
     * @param string $path Path leading to final value to be displayed.
     *
     * @return mixed
     * @throws Exception
     */
    public static function resolvePath($object, $path) {

        $pathParts = empty($path) ? array() : explode('.', $path);

        foreach($pathParts as $pathPartIndex => $pathPart) {

            $nested = preg_match('/^([a-z0-9]+)\*$/i', $pathPart, $match) > 0;
            $pathPart = $nested ? $match[1] : $pathPart;


            if(is_null($object)) {

                break;
            }

            if(is_array($object) && isset($object[$pathPart])) {

                $object = $object[$pathPart];
            }

            elseif(is_object($object)) {

                $getterFunctionName = sprintf("get%s", ucfirst($pathPart));
                $isserFunctionName = sprintf("is%s", ucfirst($pathPart));

                if(isset($object->$pathPart)) {

                    $object = $object->$pathPart;
                }
                elseif(is_callable(array($object, $getterFunctionName))) {

                    $object = $object->$getterFunctionName();
                }
                elseif(is_callable(array($object, $isserFunctionName))) {

                    $object = $object->$isserFunctionName();
                }
                elseif(is_callable(array($object, $pathPart))) {

                    $object = $object->$pathPart();
                }
                else {

                    $object = null;
                }
            }

            else {

                $object = null;
            }


            if($nested) {

                if(is_null($object)) {

                    return array();
                }

                if(!is_array($object) && !($object instanceof \Traversable)) {

                    throw new Exception("Traversable path part results in non-traversable object.");
                }


                $deepPath = implode('.', array_slice($pathParts, $pathPartIndex + 1));

                $return = array();
                foreach($object as $subObject) {

                    $value = self::resolvePath($subObject, $deepPath);

                    if(is_array($value)) {

                        $return = array_merge($return, $value);
                    }

                    elseif(!is_null($value)) {

                        $return[] = $value;
                    }
                }

                return $return;
            }
        }

        return $object;
    }
}