<?php

namespace Elucidate\Tests\Mocks;


use Elucidate\Adapter\HttpAdapter;
use Elucidate\Model\RequestModel;

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

    public function post(string $endpoint, RequestModel $request): string
    {
        $post =  $this->post;
        return $post($endpoint, $request);
    }

    public function put(string $endpoint, RequestModel $request): string
    {
        $put =  $this->put;
        return $put($endpoint, $request);
    }

    public function delete(RequestModel $request): bool
    {
        $delete =  $this->delete;
        return $delete($request);
    }

    public function get(string $endpoint, array $headers = []): string
    {
        $get =  $this->get;
        return $get($endpoint);
    }
}
