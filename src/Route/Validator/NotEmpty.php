<?php

namespace Widi\Components\Router\Route\Validator;

/**
 * Class NotEmpty
 *
 * @package Widi\Components\Router\Route\Validator
 */
class NotEmpty implements ValidatorInterface
{

    /**
     * @param string $routeParameter
     * @param null   $validatorParameter
     *
     * @return bool
     */
    public function isValid($routeParameter, $validatorParameter = null)
    {

        return !empty($routeParameter);
    }
}