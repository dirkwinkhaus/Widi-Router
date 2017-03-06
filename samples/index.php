<?php
use Widi\Components\Router\Route\Method\Get;
use Widi\Components\Router\Route\Route;
use Widi\Components\Router\Router;

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
            'key2' => [
                'validators' => [
                    [
                        'class'     => \Widi\Components\Router\Route\Validator\RegEx::class,
                        'parameter' => '/^\d{1,3}$/',
                    ],
                ],
            ],
            'options'    => [
                'method'     => \Widi\Components\Router\Route\Method\Get::class,
                'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
                'controller' => 'ParameterController',
                'action'     => 'DetailAction',
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
                    $route->setParameterValue('myParameter', 'value');
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


$request = new \GuzzleHttp\Psr7\Request(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['PATH_INFO']
);

$router = new Router(
    $request,
    $routes
);

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
