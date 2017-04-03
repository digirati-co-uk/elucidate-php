<?php
namespace Elucidate\Adapter;

use Elucidate\Model\RequestModel;
use Exception;
use GuzzleHttp\Client;

class GuzzleHttpAdapter implements HttpAdapter
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function post(string $endpoint, RequestModel $request): string
    {
        $headers = $request->getHeaders();
        $body = json_encode($request);

        $response = $this->client->post($endpoint, [
            'headers' => $headers,
            'body' => $body
        ]);

        return $response->getBody();
    }

    public function put(string $endpoint, RequestModel $request): string
    {
        $headers = $request->getHeaders();
        $body = json_encode($request);

        $response = $this->client->put($endpoint, [
            'headers' => $headers,
            'body' => $body
        ]);

        return $response->getBody();
    }

    public function delete(RequestModel $request): bool
    {
        $headers = $request->getHeaders();
        $body = json_encode($request);

        try {
            $this->client->delete((string)$request, [
                'headers' => $headers,
                'body' => $body
            ]);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function get(string $endpoint, array $headers = []): string
    {
        $response = $this->client->get($endpoint, [
            'headers' => $headers
        ]);

        return $response->getBody();
    }
}
