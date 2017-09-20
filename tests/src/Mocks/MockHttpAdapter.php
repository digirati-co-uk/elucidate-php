<?php

namespace Elucidate\Tests\Mocks;

use Elucidate\Adapter\HttpAdapter;
use Elucidate\Model\RequestModel;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class MockHttpAdapter implements HttpAdapter
{
    private $post;
    private $get;
    private $put;
    private $delete;

    public function setPost(callable $post)
    {
        $this->post = $post;

        return $this;
    }

    public function setGet(callable $get)
    {
        $this->get = $get;

        return $this;
    }

    public function setPut(callable $put)
    {
        $this->put = $put;

        return $this;
    }

    public function setDelete(callable $delete)
    {
        $this->delete = $delete;

        return $this;
    }

    public function post(string $endpoint, RequestModel $request, array $headers = [], int $code = 200): ResponseInterface
    {
        $post = $this->post;

        return new Response($code, $headers, $post($endpoint, $request));
    }

    public function put(string $endpoint, RequestModel $request, array $headers = [], int $code = 200): ResponseInterface
    {
        $put = $this->put;

        return new Response($code, $headers, $put($endpoint, $request));
    }

    public function delete(RequestModel $request, array $headers = [], int $code = 200): ResponseInterface
    {
        $delete = $this->delete;

        return new Response($code, $headers, $delete($request));
    }

    public function get(string $endpoint, array $headers = [], int $code = 200): ResponseInterface
    {
        $get = $this->get;

        return new Response($code, $headers, $get($endpoint));
    }

    public function getBaseUri(): string
    {
        return '/';
    }
}
