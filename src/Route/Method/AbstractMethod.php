<?php

namespace Widi\Components\Router\Route\Method;

abstract class AbstractMethod implements MethodInterface
{

    /**
     * @var string
     */
    const METHOD_STRING = 'empty';


    /**
     * @param $requestMethod
     *
     * @return bool
     */
    public function isSame($requestMethod)
    {

        if (trim(strtolower($requestMethod)) === static::METHOD_STRING) {

            return true;
        }

        return false;
    }

}