<?php

namespace Widi\Components\Router\Route\Validator;

/**
 * Class IsString
 *
 * @package Widi\Components\Router\Route\Validator
 */
class IsString implements ValidatorInterface
{

    /**
     * @param string $routeParameter
     * @param null   $validatorParameter
     *
     * @return bool
     */
    public function isValid($routeParameter, $validatorParameter = null)
    {

        return is_string($routeParameter);
    }
}