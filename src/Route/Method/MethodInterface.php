<?php

namespace Widi\Components\Router\Route\Method;

/**
 * Interface MethodInterface
 *
 * @package Widi\Components\Router\Route\Method
 */
interface MethodInterface
{

    /**
     * @param $requestMethod
     *
     * @return bool
     */
    public function isSame($requestMethod);
}