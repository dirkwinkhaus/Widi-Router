<?php
use Widi\Components\Router\Route\Route;

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
        'callback' => function(Route $route) {
            echo 'callback of ' . $route->getRouteKey() . ' executed';
        },
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

//$router->setCaseSensitive(true);
$router->setEnableRouteCallbacks(true);

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