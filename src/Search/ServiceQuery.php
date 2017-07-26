<?php

namespace Elucidate\Search;

class ServiceQuery implements SearchQuery
{
    /**
     * @var string
     */
    private $servicePath;
    /**
     * @var array
     */
    private $parameters;

    public function __construct(string $servicePath, array $parameters)
    {

        $this->servicePath = $servicePath;
        $this->parameters = $parameters;
    }

    public function __toString(): string
    {
    }
}