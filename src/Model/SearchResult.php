<?php

namespace Elucidate\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Elucidate\Search\SearchCustom;

class SearchResult
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public static function fromArray(array $data): SearchResult
    {
        return new static(Container::fromArray($data));
    }

    public static function fromJson(string $json): SearchResult
    {
        return new static(Container::fromJson($json));
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
            return new ArrayCollection();
        }

        $items = $this->container['first']['items'];
        return new ArrayCollection(array_map([Annotation::class, 'fromArray'], $items));
    }
}
