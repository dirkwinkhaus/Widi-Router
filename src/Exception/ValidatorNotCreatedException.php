<?php

namespace Widi\Components\Router\Exception;

/**
 * Class ValidatorNotCreatedException
 *
 * @package Widi\Components\Router\Exception
 */
class ValidatorNotCreatedException extends RouterException
{

    /**
     * @var int
     */
    const CODE = 1600;


    /**
     * RequestMethodNotCreated constructor.
     *
     * @param \Exception|null $previous
     */
    public function __construct(
        \Exception $previous = null
    ) {

        parent::__construct(
            'Could not create route validator.',
            self::CODE,
            $previous
        );
    }
}