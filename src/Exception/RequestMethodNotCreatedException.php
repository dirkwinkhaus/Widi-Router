<?php

namespace Widi\Components\Router\Exception;

/**
 * Class RequestMethodNotCreated
 *
 * @package Widi\Components\Router\Exception
 */
class RequestMethodNotCreatedException extends RouterException
{

    /**
     * @var int
     */
    const CODE = 1300;


    /**
     * RequestMethodNotCreated constructor.
     *
     * @param \Exception|null $previous
     */
    public function __construct(
        \Exception $previous = null
    ) {

        parent::__construct(
            'Could not create request method.',
            self::CODE,
            $previous
        );
    }
}