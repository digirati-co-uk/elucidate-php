<?php

namespace Elucidate\Model;

use Elucidate\Search\SearchCustom;

class SearchResult
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /** @return SearchCustom|null */
    public function getNextSearchQuery()
    {
        return new SearchCustom($this->container['first']['next']) ?? null;
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

    public static function fromArray(array $data) : SearchResult
    {
        return new static(Container::fromArray($data));
    }

    public static function fromJson(string $json) : SearchResult
    {
        return new static(Container::fromJson($json));
    }
}
