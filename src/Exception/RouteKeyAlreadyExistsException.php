<?php

namespace Widi\Components\Router\Exception;

/**
 * Class RouteKeyAlreadyExistsException
 *
 * @package Widi\Components\Router\Exception
 */
class RouteKeyAlreadyExistsException extends RouterException
{

    /**
     * @var int
     */
    const CODE = 1700;


    /**
     * RouteNotCreatedException constructor.
     *
     * @param string          $message
     * @param \Exception|null $previous
     */
    public function __construct(
        $message,
        \Exception $previous = null
    ) {

        parent::__construct(
            'Route key already exists: ' . $message,
            self::CODE,
            $previous
        );
    }
}