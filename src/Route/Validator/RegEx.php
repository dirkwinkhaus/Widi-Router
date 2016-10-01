<?php

namespace Widi\Components\Router\Route\Validator;

/**
 * Class RegEx
 *
 * @package Widi\Components\Router\Route\Validator
 */
class RegEx implements ValidatorInterface
{

    /**
     * @param string $routeParameter
     * @param null   $validatorParameter
     *
     * @return bool
     */
    public function isValid($routeParameter, $validatorParameter = null)
    {

        return preg_match($validatorParameter, $routeParameter);
    }
}