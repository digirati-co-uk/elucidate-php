<?php
namespace Elucidate\Adapter;

use Zend\Http\Client;

class ZendHttpAdapter implements HttpAdapter
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
