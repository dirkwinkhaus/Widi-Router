<?php

namespace Widi\Components\Test\ServiceLocator;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Widi\Components\Router\Exception\RequestMethodNotCreatedException;
use Widi\Components\Router\Exception\RouteComparatorNotCreatedException;
use Widi\Components\Router\Exception\ValidatorNotCreatedException;
use Widi\Components\Router\Route\Route;
use Widi\Components\Router\Router;

require_once(__DIR__ . '/../../vendor/autoload.php');

/**
 * Class RouterTest
 *
 * @package Widi\Components\Test\ServiceLocator
 */
class RouterTest extends TestCase
{

    public function testRootEqualRouting()
    {

        $router = new Router(
            $this->getRequestForPath('/'),
            $this->getDefaultRouteConfig()
        );

        $route = $router->route();

        $this->assertFalse($router->isRouteNotFound());
        $this->assertEquals($route->getRouteKey(), 'root_route');
    }


    public function testEqualRouting()
    {

        $router = new Router(
            $this->getRequestForPath('/top/sub'),
            $this->getDefaultRouteConfig()
        );

        $route = $router->route();

        $this->assertFalse($router->isRouteNotFound());
        $this->assertEquals($route->getRouteKey(), 'sub_route');

    }

    public function testCallbackRouting()
    {
        $this->markTestSkipped('Paramter setting error in test case.');

        $router = new Router(
            $this->getRequestForPath('/callback'),
            [
                'callback_route' => [
                    'route'   => '/callback',
                    'callback'        => function (Route $route) {
                        $route->setParameterValue('callbackParameter', true);
                    },
                    'options' => [
                        'method'     => \Widi\Components\Router\Route\Method\Get::class,
                        'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
                        'controller' => 'ParameterController',
                        'action'     => 'listAction',
                    ],
                ],
            ]
        );

        $router->setEnableRouteCallbacks(true);
        $route = $router->route();

        $this->assertFalse($router->isRouteNotFound());
        $this->assertEquals($route->getRouteKey(), 'callback_route');
        //$this->assertEquals($route->getParameter('callbackParameter'), true);
    }


    public function testRegexSubEqualRouting()
    {

        $router = new Router(
            $this->getRequestForPath('/top/sub/11'),
            $this->getDefaultRouteConfig()
        );

        $route = $router->route();

        $this->assertFalse($router->isRouteNotFound());
        $this->assertEquals($route->getRouteKey(), 'regex_sub_route');
    }


    public function testFailRegexSubEqualRouting()
    {

        $router = new Router(
            $this->getRequestForPath('/top/sub/1a'),
            $this->getDefaultRouteConfig()
        );

        $route = $router->route();

        $this->assertTrue($router->isRouteNotFound());
        $this->assertNull($route);
    }


    public function testParameterMandatoryRouting()
    {

        $router = new Router(
            $this->getRequestForPath('/parameters/aString'),
            $this->getDefaultRouteConfig()
        );

        $route = $router->route();

        $this->assertFalse($router->isRouteNotFound());
        $this->assertEquals($route->getRouteKey(), 'my_parameters');
        $this->assertEquals($route->getParameter('key1'), 'aString');
        $this->assertNull($route->getParameter('key2'));
    }


    public function testParameterExtRouting()
    {

        $router = new Router(
            $this->getRequestForPath('/parameters/aString/123'),
            $this->getDefaultRouteConfig()
        );

        $route = $router->route();

        $this->assertFalse($router->isRouteNotFound());
        $this->assertEquals($route->getRouteKey(), 'my_parameters');
        $this->assertEquals($route->getParameter('key1'), 'aString');
        $this->assertEquals($route->getParameter('key2'), '123');
    }


    public function testCaseSensitiveRouting()
    {

        $router = new Router(
            $this->getRequestForPath('/PARAMeters/aString'),
            $this->getDefaultRouteConfig()
        );

        $route = $router->route();
        $this->assertEquals($route->getRouteKey(), 'my_parameters');

        $router->setCaseSensitive(true);
        $route = $router->route();
        $this->assertNull($route);
    }


    public function testFailParameterExtRouting()
    {

        $router = new Router(
            $this->getRequestForPath('/parameters/aString/1234'),
            $this->getDefaultRouteConfig()
        );

        $route = $router->route();

        $this->assertTrue($router->isRouteNotFound());
        $this->assertNull($route);
    }


    public function testFailParameterMandatoryRouting()
    {

        $router = new Router(
            $this->getRequestForPath('/parameters'),
            $this->getDefaultRouteConfig()
        );

        $route = $router->route();

        $this->assertTrue($router->isRouteNotFound());
        $this->assertNull($route);
    }


    public function testValidatorFails()
    {

        $router = new Router(
            $this->getRequestForPath('/error_validator'),
            $this->getDefaultRouteConfig()
        );

        $this->expectException(ValidatorNotCreatedException::class);

        $router->route();
    }


    public function testComparatorFails()
    {

        $router = new Router(
            $this->getRequestForPath('/error_comparator'),
            $this->getComparatorErrorRouteConfig()
        );

        $this->expectException(RouteComparatorNotCreatedException::class);

        $router->route();
    }


    public function testMethodFails()
    {

        $router = new Router(
            $this->getRequestForPath('/error_method'),
            $this->getMethodErrorRouteConfig()
        );

        $this->expectException(RequestMethodNotCreatedException::class);

        $router->route();
    }


    /**
     * @param $path
     *
     * @return Request
     */
    private function getRequestForPath($path)
    {

        return new Request(
                'get',
                $path
        );
    }


    /**
     * @return array
     */
    private function getDefaultRouteConfig()
    {

        return [
            'root_route'      => [
                'route'   => '/',
                'options' => [
                    'method'     => \Widi\Components\Router\Route\Method\Get::class,
                    'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
                    'controller' => 'RouteRouteController',
                    'action'     => 'indexAction',
                ],
            ],
            'my_parameters'   => [
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
            'top_route'       => [
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
            'error_validator' => [
                'route'      => '/error_validator',
                'parameters' => [
                    'key1' => [
                        'mandatory'  => true,
                        'validators' => [
                            [
                                'class' => 'ERROR',
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
        ];
    }


    /**
     * @return array
     */
    private function getComparatorErrorRouteConfig()
    {

        return [
            'error_comparator' => [
                'route'   => '/error_comparator',
                'options' => [
                    'method'     => \Widi\Components\Router\Route\Method\Get::class,
                    'comparator' => 'ERROR',
                    'controller' => 'ParameterController',
                    'action'     => 'listAction',
                ],
            ],
        ];
    }


    /**
     * @return array
     */
    private function getMethodErrorRouteConfig()
    {

        return [
            'error_comparator' => [
                'route'   => '/error_method',
                'options' => [
                    'method'     => 'ERROR',
                    'comparator' => \Widi\Components\Router\Route\Comparator\Equal::class,
                    'controller' => 'ParameterController',
                    'action'     => 'listAction',
                ],
            ],
        ];
    }
}