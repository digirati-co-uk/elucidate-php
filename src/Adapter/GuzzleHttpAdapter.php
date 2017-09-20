<?php

namespace Elucidate\Adapter;

use Elucidate\Model\RequestModel;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GuzzleHttpAdapter implements HttpAdapter
{
    private $client;

    public function getBaseUri(): string
    {
        return $this->client->getConfig('base_uri');
    }

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function post(string $endpoint, RequestModel $request): ResponseInterface
    {
        $headers = $request->getHeaders();
        $body = json_encode($request);

        $response = $this->client->post($endpoint, [
            'headers' => $headers,
            'body' => $body,
        ]);

        return $response;
    }

    public function put(string $endpoint, RequestModel $request): ResponseInterface
    {
        $headers = $request->getHeaders();
        $body = json_encode($request);

        $response = $this->client->put($endpoint, [
            'headers' => $headers,
            'body' => $body,
        ]);

        return $response;
    }

    public function delete(RequestModel $request): ResponseInterface
    {
        $headers = $request->getHeaders();
        $body = json_encode($request);

        try {
            return $this->client->delete((string)$request, [
                'headers' => $headers,
                'body' => $body,
            ]);
        } catch (Throwable $e) {
            throw new HttpException('Something wen\'t wrong deleting this item', $e->getCode(), $e);
        }
    }

    public function get(string $endpoint, array $headers = []): ResponseInterface
    {
        $response = $this->client->get($endpoint, [
            'headers' => $headers,
        ]);

        return $response;
    }
}
