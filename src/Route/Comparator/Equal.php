<?php

namespace Widi\Components\Router\Route\Comparator;

/**
 * Class Equal
 *
 * @package Widi\Components\Router\Route\Comparator
 */
class Equal implements ComparatorInterface
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

        $this->match = $routeUri;

        return $serverUri === $routeUri;
    }


    /**
     * @return string
     */
    public function getMatch()
    {

        return $this->match;
    }
}