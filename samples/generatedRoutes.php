
<?php

use Widi\Components\Router\Route\Route;

$routerFactory = new \Widi\Components\Router\RouterFactory();
$router        = $routerFactory->__invoke();

//$router->setCaseSensitive(true);
$router->setEnableRouteCallbacks(true);

$router->addRoutes(
    $router->buildRouteArray(
        'my_route',
        '/myRoute',
        function (Route $route) {

            echo $route->getRouteKey();
        },
        null,
        null,
        [],
        [
            $router->buildRouteArray(
                'my_sub_route',
                '/mySubRoute',
                function (Route $route) {

                    echo $route->getRouteKey();
                }
            ),
        ]
    )
);

$route = $router->route();

if ($router->isRouteNotFound()) {
    echo '404';
}
