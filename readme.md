# Widi\Components\ServiceLocator
Create a new router before you decide which controller to load. Use a 
service locator to place the route within and/or give the route to the 
dedicated controller to transmit route parameters.

### Code Sample
```
<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$routes = [
    'root_route'    => [
        'route'   => '/',
        'options' => [
            'method'     => \Widi\Components\Router\Route\Method\Get::class,
            'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
            'controller' => 'RouteRootController',
            'action'     => 'indexAction',
        ],
    ],
    'post_route'    => [
        'route'   => '/post',
        'options' => [
            'method'     => \Widi\Components\Router\Route\Method\Post::class,
            'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
            'controller' => 'PostController',
            'action'     => 'updateAction',
        ],
    ],
    'my_parameters' => [
        'route'      => '/parameters',
        'parameters' => [
            'key1' => [
                'mandatory'  => true,
                'validators' => [
                    [
                        'class' => \Widi\Components\Router\Route\Validator\NotEmpty::class,
                    ],
                    [
                        'class' => \Widi\Components\Router\Route\Validator\IsString::class,
                    ],
                ],
            ],
            'key2' => [
                'validators' => [
                    [
                        'class'     => \Widi\Components\Router\Route\Validator\RegEx::class,
                        'parameter' => '/^\d{1,3}$/',
                    ],
                ],
            ],
        ],
        'options'    => [
            'method'     => \Widi\Components\Router\Route\Method\Get::class,
            'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
            'controller' => 'ParameterController',
            'action'     => 'listAction',
        ],
    ],
    'top_route'     => [
        'route'      => '/top',
        'options'    => [
            'method'     => \Widi\Components\Router\Route\Method\Get::class,
            'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
            'controller' => 'TopRouteController',
            'action'     => 'topAction',
        ],
        'extra'      => [
            'some_config_settings' => [
                'option' => 'value',
            ],
        ],
        'sub_routes' => [
            'sub_route' => [
                'route'      => '/sub',
                'options'    => [
                    'method'     => \Widi\Components\Router\Route\Method\Get::class,
                    'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
                    'controller' => 'SubController',
                    'action'     => 'subAction',
                ],
                'sub_routes' => [
                    'regex_sub_route' => [
                        'route'      => '/\/\d\d/',
                        'options'    => [
                            'method'     => \Widi\Components\Router\Route\Method\Get::class,
                            'comparator' => \Widi\Components\Router\Route\Comparator\Regex::class,
                            'controller' => 'regexController',
                            'action'     => 'regexAction',
                        ],
                        'sub_routes' => [
                            'last' => [
                                'route'   => '/last',
                                'options' => [
                                    'method'     => \Widi\Components\Router\Route\Method\Get::class,
                                    'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
                                    'controller' => 'LastController',
                                    'action'     => 'lastAction',
                                ],
                                'extra'   => [
                                    'myKey' => 'myValue',
                                ],
                            ],

                        ],
                    ],
                ],
            ],
        ],
    ],
];

$routerFactory = new \Widi\Components\Router\RouterFactory();
$router        = $routerFactory->__invoke($routes);

$route = $router->route();

?>
<!doctype html>
<html>
<body>
<h1>Router Demo</h1>
<?php
if ($router->isRouteNotFound()) {
    ?>
    <h2>404 Page not found!</h2>
    <?php
} else {
    ?>
    <h2>route key: "<?php echo $route->getRouteKey(); ?>"</h2>
    <h3>controller: "<?php echo $route->getController(); ?>"</h3>
    <h4>action: "<?php echo $route->getAction(); ?>"</h4>
    <h5>route parameter</h5>
    <textarea style="width:100%; min-height:200px;"><?php print_r(
            $route->getParameter()
        ); ?></textarea>
    <h5>route extra data</h5>
    <textarea style="width:100%; min-height:200px;"><?php print_r(
            $route->getExtraData()
        ); ?></textarea>
    <?php
}
?>
</body>
</html>
```

## Files
src/Exception
src/Exception/NoMethodRequestException.php
src/Exception/RequestException.php
src/Exception/RequestMethodNotCreatedException.php
src/Exception/RouteComparatorNotCreatedException.php
src/Exception/RouteNotCreatedException.php
src/Exception/RouterException.php
src/Exception/ValidatorNotCreatedException.php
src/LICENSE
src/Request.php
src/Route
src/Route/Comparator
src/Route/Comparator/ComparatorInterface.php
src/Route/Comparator/Equal.php
src/Route/Comparator/Regex.php
src/Route/Method
src/Route/Method/AbstractMethod.php
src/Route/Method/Delete.php
src/Route/Method/Get.php
src/Route/Method/MethodInterface.php
src/Route/Method/Post.php
src/Route/Method/Put.php
src/Route/Route.php
src/Route/Validator
src/Route/Validator/IsInt.php
src/Route/Validator/IsString.php
src/Route/Validator/NotEmpty.php
src/Route/Validator/RegEx.php
src/Route/Validator/ValidatorInterface.php
src/Router.php
src/RouterFactory.php
