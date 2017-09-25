<?php

namespace Elucidate\Transform;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Http\Request as ZendRequest;
use Zend\Psr7Bridge\Psr7ServerRequest;

abstract class AbstractRequestTransform
{
    public function __invoke($request)
    {
        if ($request instanceof ZendRequest && class_exists(Psr7ServerRequest::class)) {
            $request = Psr7ServerRequest::fromZend($request);
        }
        if (!$request instanceof ServerRequestInterface) {
            throw new \TypeError('Invalid Request provided, must implement PSR7');
        }

        return $this->transform($request);
    }

    abstract public function transform(ServerRequestInterface $request);
}
