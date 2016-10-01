<?php

namespace Widi\Components\Router;

/**
 * Class RouterFactory
 *
 * If you are using a service locator you must replace this with your
 * own factory.
 *
 * @package Widi\Components\Router
 * @author  Dirk Winkhaus <dirk.winkhaus@check24.de>
 */
class RouterFactory
{

    /**
     * @param array $routes
     *
     * @return Router
     */
    public function __invoke(array $routes = [])
    {

        return new Router(
            new Request(
                $_SERVER,
                $_GET,
                $_POST,
                $_COOKIE
            ),
            $routes
        );
    }
}