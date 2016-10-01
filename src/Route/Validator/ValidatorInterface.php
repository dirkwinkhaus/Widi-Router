<?php

namespace Widi\Components\Router\Route\Validator;

/**
 * Interface UrlValidatorInterface
 *
 * @package Widi\Components\Router\Route\Method
 */
interface ValidatorInterface
{

    /**
     * @param string $routeParameter
     * @param null   $validatorParameter
     *
     * @return mixed
     */
    public function isValid($routeParameter, $validatorParameter = null);
}