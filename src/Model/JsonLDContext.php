<?php

namespace Elucidate\Model;

trait JsonLDContext
{
    public function getContext()
    {
        return [
            'http://www.w3.org/ns/anno.jsonld',
            'http://www.w3.org/ns/ldp.jsonld',
        ];
    }

    abstract public function getInternalHeaders(): array;

    public function getHeaders(): array
    {
        $headers = $this->getInternalHeaders();

        return array_merge([
            'Accept' => 'application/ld+json; profile="http://www.w3.org/ns/anno.jsonld"',
            'Content-Type' => 'application/ld+json; profile="http://www.w3.org/ns/anno.jsonld"',
        ], $headers);
    }
}
