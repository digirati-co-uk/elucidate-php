<?php

namespace Elucidate\Adapter;

use Elucidate\Model\RequestModel;
use Zend\Http\Client;
use Zend\Http\Header\GenericHeader;
use Zend\Http\Header\HeaderValue;
use Zend\Http\Headers;
use Zend\Http\Request;

class ZendHttpAdapter implements HttpAdapter
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    private function requestFromModel(RequestModel $request = null, $headers = []): Request
    {
        $headers = $headers ? $headers : $request->getHeaders();
        $httpRequest = new Request();
        if ($request) {
            $httpRequest->setContent(json_encode($request));
        }
        $httpHeaders = new Headers();
        foreach ($headers as $name => $value) {
            $httpHeaders->addHeader(new GenericHeader($name, $value));
        }
        $httpRequest->setHeaders(
            $httpHeaders
        );
        return $httpRequest;
    }

    public function post(string $endpoint, RequestModel $request): string
    {
        $httpRequest = $this->requestFromModel($request);
        $httpRequest->setMethod('post');
        $httpRequest->setUri($endpoint);
        $response = $this->client->dispatch($httpRequest);
        return (string)$response->getContent();
    }

    public function put(string $endpoint, RequestModel $request): string
    {
        $httpRequest = $this->requestFromModel($request);
        $httpRequest->setMethod('put');
        $httpRequest->setUri($endpoint);
        $response = $this->client->dispatch($httpRequest);
        return (string)$response->getContent();
    }

    public function delete(RequestModel $request): bool
    {
        $httpRequest = $this->requestFromModel($request);
        $httpRequest->setMethod('delete');
        $httpRequest->setUri((string)$request);
        $response = $this->client->dispatch($httpRequest);
        return (string)$response->getContent();
    }

    public function get(string $endpoint, array $headers = []): string
    {
        $httpRequest = $this->requestFromModel(null, $headers);
        $httpRequest->setMethod('get');
        $httpRequest->setUri($endpoint);
        $response = $this->client->dispatch($httpRequest);
        return (string)$response->getContent();
    }

    public function getBaseUri(): string
    {
        return $this->client->getUri();
    }
}
