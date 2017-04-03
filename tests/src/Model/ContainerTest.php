<?php


namespace Elucidate\Tests\Model;


use Elucidate\Model\Container;
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
            "type": [
                "BasicContainer",
                "AnnotationCollection"
            ],
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
        $this->assertContains("http://www.w3.org/ns/anno.jsonld", $container['@context']);
        $this->assertContains("http://www.w3.org/ns/ldp.jsonld", $container['@context']);
        $this->assertContains("BasicContainer", $container['type']);
        $this->assertContains("AnnotationCollection", $container['type']);
    }

}
