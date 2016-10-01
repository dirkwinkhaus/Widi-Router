<?php

namespace Widi\Components\Router\Exception;

/**
 * Class RouteNotCreated
 *
 * @package Widi\Components\Router\Exception
 */
class RouteNotCreatedException extends RouterException
{

    /**
     * @var int
     */
    const CODE = 1500;


    /**
     * RequestMethodNotCreated constructor.
     *
     * @param \Exception|null $previous
     */
    public function __construct(
        \Exception $previous = null
    ) {

        parent::__construct(
            'Could not create route.',
            self::CODE,
            $previous
        );
    }
}