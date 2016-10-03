<?php

namespace Widi\Components\Router\Route;

use Widi\Components\Router\Route\Comparator\ComparatorInterface;
use Widi\Components\Router\Route\Method\MethodInterface;

/**
 * Class Route
 *
 * @package Widi\Components\Router\Route
 */
class Route
{

    /**
     * @var array
     */
    protected $subRoutes;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var MethodInterface
     */
    protected $method;

    /**
     * @var ComparatorInterface
     */
    protected $comparator;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var string
     */
    protected $routeKey;

    /**
     * @var string
     */
    protected $routeStringMatch;

    /**
     * @var array
     */
    protected $extraData;

    /**
     * @var array
     */
    protected $parameter;

    /**
     * @var callable
     */
    protected $callBack;


    /**
     * Route constructor.
     *
     * @param MethodInterface     $method
     * @param ComparatorInterface $comparator
     * @param string              $routeKey
     * @param string              $route
     * @param string              $routeStringMatch
     * @param string              $controller
     * @param string              $action
     * @param array               $parameter
     * @param array               $subRoutes
     * @param array               $extraData
     * @param callable            $callback
     */
    public function __construct(
        MethodInterface $method,
        ComparatorInterface $comparator,
        $routeKey,
        $route,
        $routeStringMatch,
        $controller,
        $action,
        array $parameter = [],
        array $subRoutes = [],
        array $extraData = [],
        callable $callback = null
    ) {

        $this->subRoutes        = $subRoutes;
        $this->controller       = $controller;
        $this->action           = $action;
        $this->route            = $route;
        $this->method           = $method;
        $this->comparator       = $comparator;
        $this->routeKey         = $routeKey;
        $this->routeStringMatch = $routeStringMatch;
        $this->extraData        = $extraData;
        $this->parameter        = $parameter;
        $this->callBack         = $callback;
    }


    /**
     * @return ComparatorInterface
     */
    public function getComparator()
    {

        return $this->comparator;
    }


    /**
     * @return MethodInterface
     */
    public function getMethod()
    {

        return $this->method;
    }


    /**
     * @return string
     */
    public function getAction()
    {

        return $this->action;
    }


    /**
     * @return string
     */
    public
    function getController()
    {

        return $this->controller;
    }


    /**
     * @return array
     */
    public
    function getSubRoutes()
    {

        return $this->subRoutes;
    }


    /**
     * @return bool
     */
    public
    function hasSubRoutes()
    {

        return (count($this->subRoutes) > 0);
    }


    /**
     * @return string
     */
    public
    function getRoute()
    {

        return $this->route;
    }


    /**
     * @return string
     */
    public
    function getRouteKey()
    {

        return $this->routeKey;
    }


    /**
     * @return string
     */
    public
    function getRouteStringMatch()
    {

        return $this->routeStringMatch;
    }


    /**
     * @return array
     */
    public function getExtraData()
    {

        return $this->extraData;
    }


    /**
     * @return bool
     */
    public function hasExtraData()
    {

        return count($this->extraData) > 0;
    }


    /**
     * @param null $key
     *
     * @return array|mixed|null
     */
    public function getParameter($key = null)
    {

        if ($key === null) {
            return $this->parameter;
        } else {
            if (isset($this->parameter[$key])) {
                if (isset($this->parameter[$key]['value'])) {
                    return $this->parameter[$key]['value'];
                }
            }

            return null;
        }
    }


    /**
     * @return bool
     */
    public
    function hasParameter()
    {

        return count($this->parameter) > 0;
    }


    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public
    function setParameterValue($key, $value
    ) {

        $this->parameter[$key]['value'] = $value;

        return $this;
    }


    /**
     * @return callable
     */
    public function getCallBack()
    {

        return $this->callBack;
    }
}