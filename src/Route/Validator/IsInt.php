<?php

namespace Widi\Components\Router\Route\Validator;

/**
 * Class IsInt
 *
 * @package Widi\Components\Router\Route\Validator
 */
class IsInt implements ValidatorInterface
{

    /**
     * @param string $routeParameter
     * @param null   $validatorParameter
     *
     * @return int
     */
    public function isValid($routeParameter, $validatorParameter = null)
    {

        return preg_match('/^\d*$/', $routeParameter);
    }
}