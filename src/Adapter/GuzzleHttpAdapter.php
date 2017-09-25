<?php

namespace Elucidate\Adapter;

use Elucidate\Exception\ElucidateRequestException;
use Elucidate\Exception\ElucidateUncaughtException;
use Elucidate\Model\RequestModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Zend\Diactoros\Response\JsonResponse;

class GuzzleHttpAdapter implements HttpAdapter
{
    private $client;
    private $suppressUncaughtExceptions;

    public function getBaseUri(): string
    {
        return $this->client->getConfig('base_uri');
    }

    public function __construct(Client $client, bool $suppressUncaughtExceptions = true)
    {
        $this->client = $client;
        $this->suppressUncaughtExceptions = $suppressUncaughtExceptions;
    }

    public function request(string $method, string $uri, array $options = [])
    {
        try {
            return $this->client->request($method, $uri, $options);
        } catch (RequestException $requestException) {
            throw ElucidateRequestException::fromRequestException($requestException);
        } catch (Throwable $e) {
            if ($this->suppressUncaughtExceptions === false) {
                throw new ElucidateUncaughtException('Uncaught exception', 500, $e);
            }

            return new JsonResponse(['errors' => ['error' => $e->getMessage()]], 500);
        }
    }

    public function post(string $endpoint, RequestModel $request): ResponseInterface
    {
        $headers = $request->getHeaders();
        $body = json_encode($request);

        return $this->request('post', $endpoint, [
            'headers' => $headers,
            'body' => $body,
        ]);
    }

    public function put(string $endpoint, RequestModel $request): ResponseInterface
    {
        $headers = $request->getHeaders();
        $body = json_encode($request);

        return $this->request('put', $endpoint, [
            'headers' => $headers,
            'body' => $body,
        ]);
    }

    public function delete(RequestModel $request): ResponseInterface
    {
        $headers = $request->getHeaders();
        $body = json_encode($request);

        try {
            return $this->request('delete', (string) $request, [
                'headers' => $headers,
                'body' => $body,
            ]);
        } catch (Throwable $e) {
            throw new HttpException('Something wen\'t wrong deleting this item', $e->getCode(), $e);
        }
    }

    public function get(string $endpoint, array $headers = []): ResponseInterface
    {
        return $this->request('get', $endpoint, [
            'headers' => $headers,
        ]);
    }
}
