<?php

namespace Widi\Components\Router\Route\Comparator;

/**
 * Class ComparatorInterface
 *
 * @package Widi\Components\Router\Route\Comparator
 */
interface ComparatorInterface
{

    /**
     * @param string $routeUri
     * @param string $serverUri
     *
     * @return mixed
     */
    public function compare($routeUri, $serverUri);


    /**
     * @return string
     */
    public function getMatch();
}