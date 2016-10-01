<?php

namespace Widi\Components\Router\Exception;

/**
 * Class NoMethodRequestException
 *
 * @package Widi\Components\Router\Exception
 * @author  Dirk Winkhaus <dirk.winkhaus@check24.de>
 */
class NoMethodRequestException extends RequestException
{

    const CODE = 1100;


    /**
     * NoMethodRequestException constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct(
        $message,
        $code = self::CODE,
        \Exception $previous = null
    ) {

        parent::__construct(
            'Method not found in request: ' . $message,
            $code,
            $previous
        );
    }
}