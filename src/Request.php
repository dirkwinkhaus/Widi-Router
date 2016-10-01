<?php

namespace Widi\Components\Router;

use Widi\Components\Router\Exception\NoMethodRequestException;

/**
 * Class Request
 *
 * @package Widi\Components\Router
 * @author  Dirk Winkhaus <dirk.winkhaus@check24.de>
 */
class Request
{

    /**
     * @var string
     */
    const GET_DATA = 'get';

    /**
     * @var string
     */
    const COOKIE_DATA = 'cookie';

    /**
     * @var string
     */
    const POST_DATA = 'post';

    /**
     * @var string
     */
    const FUNCTION_GET_PREFIX = self::GET_DATA;

    /**
     * @var array
     */
    protected $requestData;


    /**
     * Request constructor.
     *
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $cookie
     */
    public function __construct(
        array $server,
        array $get,
        array $post,
        array $cookie
    ) {

        foreach ($server as $serverKey => $serverValue) {
            $this->requestData[$this->snakeCaseToCamelCase($serverKey)]
                = $serverValue;
        }

        $this->requestData[self::COOKIE_DATA] = $_COOKIE;
        $this->requestData[self::GET_DATA]    = $_GET;
        $this->requestData[self::POST_DATA]   = $_POST;
    }


    /**
     * @param       $functionName
     * @param array $parameter
     *
     * @return mixed
     * @throws NoMethodRequestException
     */
    public function __call($functionName, array $parameter)
    {

        if ($this->hasGetMethod($functionName)) {
            $key = substr($functionName, strlen(self::FUNCTION_GET_PREFIX));

            return $this->getDataValue($key);
        }

        throw new NoMethodRequestException($functionName);
    }


    /**
     * @param $key
     *
     * @return mixed
     */
    protected function getDataValue($key)
    {

        return $this->requestData[$key];
    }


    /**
     * @param $functionName
     *
     * @return bool
     */
    public function hasGetMethod($functionName)
    {

        if (strpos($functionName, self::FUNCTION_GET_PREFIX) === 0) {
            return isset($this->requestData[substr(
                    $functionName, strlen(self::FUNCTION_GET_PREFIX)
                )]
            );
        }

        return false;
    }


    /**
     * @param string $snakeCaseString
     * @param bool   $beginLower
     *
     * @return string
     */
    protected function snakeCaseToCamelCase(
        $snakeCaseString,
        $beginLower = false
    ) {

        $camelcase = implode(
            '',
            array_map(
                function ($string) {

                    return ucfirst(strtolower($string));
                },
                explode('_', $snakeCaseString)
            )
        );

        if ($beginLower) {
            return lcfirst($camelcase);
        }

        return $camelcase;
    }
}