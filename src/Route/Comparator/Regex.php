<?php

namespace Widi\Components\Router\Route\Comparator;

/**
 * Class Regex
 *
 * @package Widi\Components\Router\Route\Comparator
 */
class Regex implements ComparatorInterface
{

    /**
     * @var string
     */
    protected $match;


    /**
     * @param string $routeUri
     * @param string $serverUri
     *
     * @return mixed
     */
    public function compare($routeUri, $serverUri)
    {

        preg_match($routeUri, $serverUri, $matches);

        if (count($matches) > 0) {
            $this->match = $matches[0];

            return strpos($serverUri, $matches[0]) === 0;
        }

        return false;
    }


    /**
     * @return string
     */
    public function getMatch()
    {

        return $this->match;
    }
}