<?php

namespace ArneGroskurth\Symgrid\Grid;


trait DataPathTrait {

    /**
     * @param string $path
     *
     * @return string[]
     */
    private function getPathParts($path) {

        return explode('.', $path);
    }

    /**
     * @param string[] $pathParts
     *
     * @return string
     */
    private function getPathFromParts(array $pathParts) {

        return implode('.', $pathParts);
    }

    /**
     * @param string[] $pathParts
     * @param string $part
     *
     * @return string[]
     */
    protected function appendPathPart(array $pathParts, $part) {

        $pathParts[] = $part;

        return $pathParts;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function isValidPath($path) {

        return preg_match('/^([a-z0-9]+\*?\.)*([a-z0-9]+\*?)$/i', $path) > 0;
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws Exception
     */
    private function validatePath($path) {

        if(!$this->isValidPath($path)) {

            throw new Exception("Invalid path.");
        }

        return $path;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function isNestedPath($path) {

        return strpos($path, '*') !== false;
    }

    /**
     * @param string[] $parts
     *
     * @return string
     */
    private function getLastPathPart(array $parts) {

        return count($parts) ? $parts[count($parts) - 1] : null;
    }

    /**
     * @param string[] $parts
     * @param int $partCount
     *
     * @return array
     */
    private function getLastPathParts(array $parts, $partCount = 2) {

        return array_slice($parts, -1 * min($partCount, count($parts)));
    }

    /**
     * @param string[] $parts
     * @param bool $onlyLastPart
     *
     * @return string
     */
    private function getTitleByPath(array $parts, $onlyLastPart = false) {

        if($onlyLastPart) {

            return $this->getLastPathPart($parts) ? ucfirst($this->getLastPathPart($parts)) : null;
        }

        else {

            return implode(' - ', array_map('ucfirst', $parts));
        }
    }

    /**
     * @param string $rootAlias
     * @param string[] $pathParts
     *
     * @return string[]
     */
    private function getCompletePathParts($rootAlias, array $pathParts) {

        return ($pathParts[0] == $rootAlias) ? $pathParts : array_merge(array($rootAlias), $pathParts);
    }

    /**
     * @param string[] $pathParts
     * @param string $rootAlias
     *
     * @return string[]
     */
    private function getEntityPathParts(array $pathParts, $rootAlias = null) {

        if($rootAlias) {

            $pathParts = $this->getCompletePathParts($rootAlias, $pathParts);
        }

        return array_slice($pathParts, 0, count($pathParts) - 1);
    }

    /**
     * @param string $rootAlias
     * @param string[] $pathParts
     *
     * @return string[]
     */
    private function getJoinPathParts($rootAlias, array $pathParts) {

        return array_slice($this->getCompletePathParts($rootAlias, $pathParts), -3, 2);
    }

    /**
     * @param string[] $pathParts
     * @param string $rootAlias
     *
     * @return string
     */
    private function getEntityPathPart(array $pathParts, $rootAlias = null) {

        if($rootAlias) {

            $pathParts = $this->getCompletePathParts($rootAlias, $pathParts);
        }

        return $pathParts[count($pathParts) - 2];
    }

    /**
     * @param string[] $pathParts
     *
     * @return string[]
     */
    private function getEntityFieldPathParts(array $pathParts) {

        return array_slice($pathParts, -2);
    }
}