<?php

namespace Elucidate\Model;

class SearchResult
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getNextPage()
    {
        return $this->container['first']['next'] ?? null;
    }

    public function getResults()
    {
        if (!$this->container['first']) {
            return;
        }
        foreach ($this->container['first']['items'] as $item) {
            yield Annotation::fromArray($item);
        }
    }
}
