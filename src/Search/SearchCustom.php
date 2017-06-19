<?php

namespace Elucidate\Search;

class SearchCustom implements SearchQuery
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function __toString() : string
    {
        return $this->getPath();
    }
}
