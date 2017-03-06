# Widi\Components\Router

## Change log

2.0.1   
        + updated readme and test path

2.0.0  
        + refactored to use psr7 request interface

1.4.1   
        + fix readme file list

1.4.0   
        + added feature get route key path for accessmanagement
        + remove build route array

1.3.1   
        + added php version to composer

1.3.0   
        + added feature to route manually and override 
          uri and method

1.2.0   
        + fix default routing array
        + added get request feature

1.1.2   
        + fixed generated sub route handling
        + created route add function

1.1.1   
        + readme updated
        + added description to package

1.1.0   
        + added callback function

1.0.2   
        + fixed package name
        
1.0.1   
        + 1st release

## Code Sample

### Array routes
```
<?php
use Widi\Components\Router\Route\Method\Get;
use Widi\Components\Router\Route\Route;

require_once(__DIR__ . '/../vendor/autoload.php');

$routes = [
    '404'           => [
        'route'   => '/404',
        'options' => [
            'method'     => \Widi\Components\Router\Route\Method\Get::class,
            'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
            'controller' => 'ErrorController404',
            'action'     => 'indexAction',
        ],
    ],
    '403'           => [
        'route'   => '/403',
        'options' => [
            'method'     => \Widi\Components\Router\Route\Method\Get::class,
            'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
            'controller' => 'ErrorController403',
            'action'     => 'indexAction',
        ],
    ],
    '500'           => [
        'route'   => '/500',
        'options' => [
            'method'     => \Widi\Components\Router\Route\Method\Get::class,
            'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
            'controller' => 'ErrorController500',
            'action'     => 'indexAction',
        ],
    ],
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
            'callback'   =>
                function (Route $route) {

                    echo 'callback of ' . $route->getRouteKey() . ' executed';
                },
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

//$router->setCaseSensitive(true);
$router->setEnableRouteCallbacks(true);

$route = $router->route();
if ($router->isRouteNotFound()) {
    $route = $router->route('/404', Get::METHOD_STRING);
}

?>
<!doctype html>
<html>
<body>
<h1>Router Demo</h1>
<h2>route key: "<?php echo $route->getRouteKey(); ?>"</h2>
<h3>controller: "<?php echo $route->getController(); ?>"</h3>
<h3>action: "<?php echo $route->getAction(); ?>"</h3>
<h3>route key path: "<?php echo $route->getRouteKeyPath(); ?>"</h3>
<h3>route parameter</h3>
<textarea style="width:100%; min-height:200px;"><?php print_r(
        $route->getParameter()
    ); ?></textarea>
<h3>route extra data</h3>
<textarea style="width:100%; min-height:200px;"><?php print_r(
        $route->getExtraData()
    ); ?></textarea>
</body>
</html>
```

## Files
+ src/Exception
+ src/Exception/NoMethodRequestException.php
+ src/Exception/RequestException.php
+ src/Exception/RequestMethodNotCreatedException.php
+ src/Exception/RouteComparatorNotCreatedException.php
+ src/Exception/RouteNotCreatedException.php
+ src/Exception/RouterException.php
+ src/Exception/ValidatorNotCreatedException.php
+ src/LICENSE
+ src/Request.php
+ src/Route
+ src/Route/Comparator
+ src/Route/Comparator/ComparatorInterface.php
+ src/Route/Comparator/Equal.php
+ src/Route/Comparator/Regex.php
+ src/Route/Method
+ src/Route/Method/AbstractMethod.php
+ src/Route/Method/Delete.php
+ src/Route/Method/Get.php
+ src/Route/Method/MethodInterface.php
+ src/Route/Method/Post.php
+ src/Route/Method/Put.php
+ src/Route/Route.php
+ src/Route/Validator
+ src/Route/Validator/IsInt.php
+ src/Route/Validator/IsString.php
+ src/Route/Validator/NotEmpty.php
+ src/Route/Validator/RegEx.php
+ src/Route/Validator/ValidatorInterface.php
+ src/Router.php
+ src/RouterFactory.php
