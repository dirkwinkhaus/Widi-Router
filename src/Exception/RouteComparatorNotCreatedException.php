<?php

namespace Widi\Components\Router\Exception;

/**
 * Class RouteComparatorNotCreated
 *
 * @package Widi\Components\Router\Exception
 */
class RouteComparatorNotCreatedException extends RouterException
{

    /**
     * @var int
     */
    const CODE = 1400;


    /**
     * RequestMethodNotCreated constructor.
     *
     * @param \Exception|null $previous
     */
    public function __construct(
        \Exception $previous = null
    ) {

        parent::__construct(
            'Could not create route comparator.',
            self::CODE,
            $previous
        );
    }
}