<?php
namespace Elucidate\Adapter;

use Elucidate\Model\RequestModel;

interface HttpAdapter
{
    public function post(string $endpoint, RequestModel $request) : string;

    public function put(string $endpoint, RequestModel $request) : string;

    public function delete(RequestModel $request) : bool;

    public function get(string $endpoint, array $headers = []) : string;

}
