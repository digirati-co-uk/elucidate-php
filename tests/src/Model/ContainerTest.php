<?php

namespace Elucidate\Tests\Model;

use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use Elucidate\Model\SearchResult;
use Elucidate\Search\SearchQuery;
use PHPUnit_Framework_TestCase;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    public function test_can_instantiate()
    {
        new Container('label');
    }

    public function test_can_serialize()
    {
        $container = new Container('Something label');

        $json = '{
            "label": "Something label",
            "type": "AnnotationCollection",
            "id": null,
            "@context": [
                "http:\/\/www.w3.org\/ns\/anno.jsonld",
                "http:\/\/www.w3.org\/ns\/ldp.jsonld"
            ]
        }';

        $this->assertJsonStringEqualsJsonString($json, json_encode($container, JSON_PRETTY_PRINT));
    }

    public function test_can_deserialize()
    {
        $json = '{
            "id": "1234",
            "label": "Something label",
            "type": [
                "BasicContainer",
                "AnnotationCollection"
            ],
            "@context": [
                "http:\/\/www.w3.org\/ns\/anno.jsonld",
                "http:\/\/www.w3.org\/ns\/ldp.jsonld"
            ]
        }';

        $container = Container::fromJson($json);

        $this->assertEquals('1234', $container['id']);
        $this->assertEquals('Something label', $container['label']);
        $this->assertContains('http://www.w3.org/ns/anno.jsonld', $container['@context']);
        $this->assertContains('http://www.w3.org/ns/ldp.jsonld', $container['@context']);
        $this->assertContains('BasicContainer', $container['type']);
        $this->assertContains('AnnotationCollection', $container['type']);
    }

    public function test_can_load_container()
    {
        $json = file_get_contents(__DIR__ . '/../../fixtures/container.json');
        Container::fromJson($json);
    }

    public function test_can_load_search_result()
    {
        $json = file_get_contents(__DIR__ . '/../../fixtures/search.json');
        $search = new SearchResult(Container::fromJson($json));

        foreach ($search->getResults() as $result) {
            $this->assertInstanceOf(Annotation::class, $result);
            $this->assertNotNull($result['target']);
            $this->assertNotNull($result['body']);
        }

        $this->assertEquals(
            'https://elucidate.dlcs-ida.org/annotation/w3c/services/search/body?page=1&fields=source&value=https%3A%2F%2Fomeka.dlcs-ida.org&desc=1',
            $search->getNextPage()
        );

        $this->assertInstanceOf(SearchQuery::class, $search->getNextSearchQuery());
    }
}
