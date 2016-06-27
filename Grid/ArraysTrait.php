<?php

namespace ArneGroskurth\Symgrid\Grid;


trait ArraysTrait {

    /**
     * @param array $array
     *
     * @return bool
     */
    private function isAssociativeArray(array $array) {

        return array_filter(array_keys($array), 'is_string') === array_keys($array);
    }

    /**
     * Checks whether the given array has any string keys.
     *
     * @param array $array
     *
     * @return bool
     */
    private function arrayHasStringKeys(array $array) {

        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    private function isStringArray(array $array) {

        return array_filter($array, 'is_string') === $array;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    private function isIntegerArray(array $array) {

        return array_filter($array, 'is_int') === $array;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    private function isFloatArray(array $array) {

        return array_filter($array, 'is_float') === $array;
    }
}