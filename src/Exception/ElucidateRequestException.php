<?php


namespace Elucidate\Exception;

use GuzzleHttp\Exception\RequestException;

class ElucidateRequestException extends RequestException
{
    public static function fromRequestException(RequestException $requestException)
    {
        return new static(
            $requestException->getMessage(),
            $requestException->getRequest(),
            $requestException->getResponse(),
            $requestException,
            $requestException->getHandlerContext()
        );
    }
}
