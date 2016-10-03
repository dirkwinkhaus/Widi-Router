<?php

namespace Widi\Components\Router;

use Widi\Components\Router\Exception\RequestMethodNotCreatedException;
use Widi\Components\Router\Exception\RouteComparatorNotCreatedException;
use Widi\Components\Router\Exception\RouteKeyAlreadyExistsException;
use Widi\Components\Router\Exception\RouteNotCreatedException;
use Widi\Components\Router\Exception\ValidatorNotCreatedException;
use Widi\Components\Router\Route\Comparator\ComparatorInterface;
use Widi\Components\Router\Route\Comparator\Equal;
use Widi\Components\Router\Route\Method\Get;
use Widi\Components\Router\Route\Method\MethodInterface;
use Widi\Components\Router\Route\Route;
use Widi\Components\Router\Route\Validator\ValidatorInterface;

/**
 * Class Router
 *
 * @package Widi\Components\Router
 */
class Router
{

    /**
     * @var array
     */
    protected $routes;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var bool
     */
    protected $routeNotFound = false;

    /**
     * @var bool
     */
    protected $caseSensitive = false;

    /**
     * @var bool
     */
    protected $enableRouteCallbacks = false;


    /**
     * Router constructor.
     *
     * @param array   $routes
     * @param Request $request
     */
    public function __construct(Request $request, array $routes = [])
    {

        $this->routes  = $routes;
        $this->request = $request;
    }


    /**
     * @param array $routes
     *
     * @return $this
     */
    public function setRoutes(array $routes)
    {

        $this->routes = $routes;

        return $this;
    }


    /**
     * @param array $routes
     */
    public function addRoutes(array $routes)
    {

        $this->routes = $this->routes + $routes;
    }


    /**
     * @param string        $routeKey
     * @param string        $route
     * @param callable|null $callBack
     * @param null          $controllerString
     * @param null          $actionString
     * @param array         $parameters
     * @param array         $subRoutes
     * @param string        $methodString
     * @param string        $comparatorString
     * @param array         $extraData
     *
     * @return array
     * @throws RouteKeyAlreadyExistsException
     */
    public function buildRouteArray(
        $routeKey,
        $route,
        callable $callBack = null,
        $controllerString = null,
        $actionString = null,
        array $parameters = [],
        array $subRoutes = [],
        $methodString = Get::class,
        $comparatorString = Equal::class,
        array $extraData = []
    ) {

        if (isset($this->routes[$routeKey])) {
            throw new RouteKeyAlreadyExistsException($routeKey);
        }

        return [
            $routeKey => [
                'route'      => $route,
                'options'    => [
                    'method'     => $methodString,
                    'comparator' => $comparatorString,
                    'controller' => $controllerString,
                    'action'     => $actionString,
                    'callback'   => $callBack,
                ],
                'parameters' => $parameters,
                'sub_routes' => $subRoutes,
                'extra'      => $extraData,
            ],
        ];
    }


    /**
     * @return Route
     */
    public function route()
    {

        $serverUri    = $this->getServerUri($this->request);
        $serverMethod = $this->request->getRequestMethod();
        $routes       = $this->routes;

        do {
            $matchingRoute = $this->hitRoute(
                $routes,
                $serverMethod,
                $serverUri
            );

            if ($matchingRoute === null) {
                break;
            }

            $routes    = $matchingRoute->getSubRoutes();
            $serverUri = (string)substr(
                $serverUri,
                strlen($matchingRoute->getRouteStringMatch())
            );

        } while (
            $matchingRoute->hasSubRoutes()
            && !$this->validateLeftUri($matchingRoute, $serverUri)
        );

        if (
            ($matchingRoute !== null)
            && (!$this->validateLeftUri($matchingRoute, $serverUri))
        ) {
            $matchingRoute = null;
        }

        if ($matchingRoute === null) {
            $this->routeNotFound = true;
        } else {
            if ($this->enableRouteCallbacks) {
                $callBack = $matchingRoute->getCallBack();
                if (is_callable($callBack)) {
                    $callBack($matchingRoute);
                }
            }
        }

        return $matchingRoute;
    }


    /**
     * @return boolean
     */
    public function isRouteNotFound()
    {

        return $this->routeNotFound;
    }


    /**
     * @return boolean
     */
    public function isCaseSensitive()
    {

        return $this->caseSensitive;
    }


    /**
     * @param boolean $caseSensitive
     */
    public function setCaseSensitive($caseSensitive)
    {

        $this->caseSensitive = $caseSensitive;
    }


    /**
     * @param boolean $enableRouteCallbacks
     *
     * @return $this
     */
    public function setEnableRouteCallbacks($enableRouteCallbacks)
    {

        $this->enableRouteCallbacks = $enableRouteCallbacks;

        return $this;
    }


    /**
     * @return boolean
     */
    public function isEnableRouteCallbacks()
    {

        return $this->enableRouteCallbacks;
    }


    /**
     * @param Route  $matchingRoute
     * @param string $leftUri
     *
     * @return bool
     */
    protected function validateLeftUri(Route $matchingRoute, $leftUri)
    {

        if ($matchingRoute->hasParameter()) {
            $parametersFoundInUri = $this->getParametersFromLeftUri(
                $leftUri
            );
            $parametersFoundInRouteConfiguration
                                  = $matchingRoute->getParameter();

            $parameterIndex = 0;
            foreach (
                $parametersFoundInRouteConfiguration as
                $parameterKey => $routeParameter
            ) {
                $routeParameter = $this->prepareDefaultParameterArray(
                    $routeParameter
                );

                $parameterIsMandatoryButMissing
                    = (($routeParameter['mandatory'] === true)
                    && (!isset($parametersFoundInUri[$parameterIndex])));

                if ($parameterIsMandatoryButMissing) {
                    return false;
                }

                $continueBecauseParameterNotFound
                    = !isset($parametersFoundInUri[$parameterIndex]);

                if ($continueBecauseParameterNotFound) {
                    continue;
                }

                $result = $this->validateParameter(
                    $parametersFoundInUri, $parameterIndex, $routeParameter
                );

                if ($result === true) {
                    $matchingRoute->setParameterValue(
                        $parameterKey,
                        $parametersFoundInUri[$parameterIndex]
                    );
                } else {
                    return $result;
                }

                $parameterIndex++;
            }

            return true;
        } else {

            if ($leftUri === '') {

                return true;
            }

        }

        return false;
    }


    /**
     * @param array  $routes
     * @param string $serverMethod
     * @param string $serverUri
     *
     * @return null|Route
     * @throws RequestMethodNotCreatedException
     * @throws RouteComparatorNotCreatedException
     * @throws RouteNotCreatedException
     */
    protected function hitRoute(
        array $routes,
        $serverMethod,
        $serverUri
    ) {

        $serverUri = $this->getServerUriPartToMatch($serverUri);

        foreach ($routes as $routeKey => $routeConfiguration) {

            $routeConfiguration = $this->prepareDefaultArray(
                $routeConfiguration
            );

            try {
                /** @var MethodInterface $method */
                $methodClassString = $routeConfiguration['options']['method'];
            } catch (\Exception $exception) {
                throw new RequestMethodNotCreatedException($exception);
            }

            if (!class_exists($methodClassString)) {
                throw new RequestMethodNotCreatedException();
            }

            $method = new $methodClassString();

            if ($method->isSame($serverMethod)) {

                try {
                    /** @var ComparatorInterface $comparator */
                    $comparatorClassString
                        = $routeConfiguration['options']['comparator'];
                } catch (\Exception $exception) {
                    throw new RouteComparatorNotCreatedException($exception);
                }

                if (!class_exists($comparatorClassString)) {
                    throw new RouteComparatorNotCreatedException();
                }

                $comparator = new $comparatorClassString();

                $successfullyCompared = $comparator->compare(
                    $this->prepareComparatorField($routeConfiguration['route']),
                    $this->prepareComparatorField($serverUri)
                );

                if ($successfullyCompared) {
                    try {
                        return new Route(
                            $method,
                            $comparator,
                            $routeKey,
                            $routeConfiguration['route'],
                            $comparator->getMatch(),
                            $routeConfiguration['options']['controller'],
                            $routeConfiguration['options']['action'],
                            $routeConfiguration['parameters'],
                            $routeConfiguration['sub_routes'],
                            $routeConfiguration['extra'],
                            $routeConfiguration['options']['callback']
                        );
                    } catch (\Exception $exception) {
                        throw new RouteNotCreatedException($exception);
                    }
                }
            }
        }

        return null;
    }


    /**
     * @param $comparatorField
     *
     * @return string
     */
    protected function prepareComparatorField($comparatorField)
    {

        if (!$this->isCaseSensitive()) {
            $comparatorField = strtolower($comparatorField);
        }

        return $comparatorField;
    }


    /**
     * @param array $routeParameter
     *
     * @return mixed
     */
    protected function prepareDefaultParameterArray(array $routeParameter)
    {

        $routeParameter = $routeParameter + [
                'mandatory'  => false,
                'validators' => [
                ],
            ];

        return $routeParameter;
    }


    /**
     * @param array $routeConfiguration
     *
     * @return array
     */
    protected function prepareDefaultArray(array $routeConfiguration)
    {

        return $routeConfiguration +
        [
            'parameters' => [
            ],
            'options'    => [
                'controller' => '',
                'action'     => '',
            ],
            'sub_routes' => [
            ],
            'extra'      => [
            ],
            'callback'   => null,
        ];
    }


    /**
     * @param $serverUri
     *
     * @return string
     */
    protected function getServerUriPartToMatch($serverUri)
    {

        $serverUri = $this->removeBeginningSlashes($serverUri);
        $serverUri = '/' . explode('/', $serverUri)[0];

        return $serverUri;
    }


    /**
     * @param $serverUri
     *
     * @return string
     */
    protected function removeBeginningSlashes($serverUri)
    {

        $serverUri = ltrim($serverUri, '/');

        return $serverUri;
    }


    /**
     * @param $serverUri
     *
     * @return string
     */
    protected function removeTrailingSlashes($serverUri)
    {

        $serverUri = rtrim($serverUri, '/');

        return $serverUri;
    }


    /**
     * @param Request $request
     *
     * @return string
     */
    protected function getServerUri(Request $request)
    {

        if ($request->hasGetMethod('getPathInfo')) {
            $serverUri = $request->getPathInfo();
        } else {
            $serverUri = '';
        }

        $serverUri = $this->removeTrailingSlashes($serverUri);

        return $serverUri;
    }


    /**
     * @param string $leftUri
     *
     * @return array
     */
    protected function getParametersFromLeftUri($leftUri)
    {

        $leftUri    = $this->removeBeginningSlashes($leftUri);
        $parameters = explode('/', $leftUri);

        return $parameters;
    }


    /**
     * @param string $parametersFoundInUri
     * @param int    $parameterIndex
     * @param string $routeParameter
     *
     * @return bool
     * @throws ValidatorNotCreatedException
     */
    protected function validateParameter(
        $parametersFoundInUri,
        $parameterIndex,
        $routeParameter
    ) {

        if (isset($parametersFoundInUri[$parameterIndex])) {
            foreach (
                $routeParameter['validators'] as $validatorConfigArray
            ) {

                try {
                    $validatorClassString = $validatorConfigArray['class'];
                    $validatorParameter
                                          = (isset($validatorConfigArray['parameter']))
                        ? $validatorConfigArray['parameter']
                        : null;
                } catch (\Exception $exception) {
                    throw new ValidatorNotCreatedException($exception);
                }

                if (!class_exists($validatorClassString)) {
                    throw new ValidatorNotCreatedException();
                }

                /** @var ValidatorInterface $validator */
                $validator = new $validatorClassString();

                $isValid = $validator->isValid(
                    $parametersFoundInUri[$parameterIndex],
                    $validatorParameter
                );
                if (!$isValid) {
                    return false;
                }
            }

            return true;
        } else {
            if ($routeParameter['mandatory'] === false) {

                return true;
            }
        }

        return false;
    }
}