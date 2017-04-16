<?php

namespace Widi\Components\Router;

use Psr\Http\Message\RequestInterface;
use Widi\Components\Router\Exception\RequestMethodNotCreatedException;
use Widi\Components\Router\Exception\RouteComparatorNotCreatedException;
use Widi\Components\Router\Exception\RouteNotCreatedException;
use Widi\Components\Router\Exception\ValidatorNotCreatedException;
use Widi\Components\Router\Route\Comparator\ComparatorInterface;
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
     * @var RequestInterface
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
     * @var string
     */
    protected $routeKeyPath = '';

    /**
     * Router constructor.
     *
     * @param RequestInterface $request
     * @param array            $routes
     */
    public function __construct(
        RequestInterface $request,
        array $routes = []
    ) {

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
     * @param string $serverUri
     * @param string $serverMethod
     *
     * @return null|Route
     */
    public function route(
        $serverUri = null,
        $serverMethod = null
    ) {

        if ($serverUri === null) {
            $serverUri = $this->getServerUri($this->request);
        }

        if ($serverMethod === null) {
            $serverMethod = strtolower($this->request->getMethod());
        }

        $routes = $this->routes;

        do {
            $matchingRoute = $this->hitRoute(
                $routes,
                $serverMethod,
                $serverUri
            );

            if ($matchingRoute === null) {
                break;
            }

            $this->routeKeyPath = $matchingRoute->getRouteKeyPath();
            $routes             = $matchingRoute->getSubRoutes();
            $serverUri          = (string)substr(
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
     * @return RequestInterface
     */
    public function getRequest()
    {

        return $this->request;
    }

    /**
     * @param Route  $matchingRoute
     * @param string $leftUri
     *
     * @return bool
     */
    protected function validateLeftUri(
        Route $matchingRoute,
        $leftUri
    ) {

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

            /** @var MethodInterface $method */
            $methodClassString = $routeConfiguration['options']['method'];

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
                            $routeConfiguration['options']['callback'],
                            $this->routeKeyPath
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

        $routeConfiguration = $routeConfiguration +
                              [
                                  'parameters' => [
                                  ],
                                  'sub_routes' => [
                                  ],
                                  'extra'      => [
                                  ],
                              ];

        $routeConfiguration['options'] = $routeConfiguration['options'] +
                                         [
                                             'controller' => null,
                                             'action'     => null,
                                             'callback'   => null,
                                         ];

        return $routeConfiguration;
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
     * @param RequestInterface $request
     *
     * @return string
     */
    protected function getServerUri(RequestInterface $request)
    {

        $serverUri = $this->removeTrailingSlashes($request->getUri()->getPath());

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