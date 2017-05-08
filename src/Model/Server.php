<?php

namespace Elucidate\Model;

final class Server
{
    private $baseUri;

    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }
}
