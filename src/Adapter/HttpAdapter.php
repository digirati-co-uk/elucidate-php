<?php

namespace Elucidate\Adapter;

use Elucidate\Model\RequestModel;
use Psr\Http\Message\ResponseInterface;

interface HttpAdapter
{
    public function getBaseUri(): string;

    public function post(string $endpoint, RequestModel $request): ResponseInterface;

    public function put(string $endpoint, RequestModel $request): ResponseInterface;

    public function delete(RequestModel $request): ResponseInterface;

    public function get(string $endpoint, array $headers = []): ResponseInterface;
}
