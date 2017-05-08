<?php

namespace Elucidate\Adapter;

use Elucidate\Model\RequestModel;
use Zend\Http\Client;

class ZendHttpAdapter implements HttpAdapter
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function post(string $endpoint, RequestModel $request): string
    {
        // TODO: Implement post() method.
    }

    public function put(string $endpoint, RequestModel $request): string
    {
        // TODO: Implement put() method.
    }

    public function delete(RequestModel $request): bool
    {
        // TODO: Implement delete() method.
    }

    public function get(string $endpoint, array $headers = []): string
    {
        // TODO: Implement get() method.
    }
}
