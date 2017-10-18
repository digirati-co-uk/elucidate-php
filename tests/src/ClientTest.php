<?php

namespace Elucidate\Tests;

use Elucidate\Client;
use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use Elucidate\Search\SearchByBody;
use Elucidate\Search\SearchByTarget;
use Elucidate\Tests\Mocks\MockHttpAdapter;
use PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    /** @var $http MockHttpAdapter */
    private $http;
    /** @var $client Client */
    private $client;

    public function setUp()
    {
        $this->http = new MockHttpAdapter();
        $this->client = new Client($this->http);
    }

    public function test_can_create_container()
    {
        $this->http->setPost(function ($endpoint) {
            $this->assertEquals('/', $endpoint);

            return '{
            "label": "my new container",
            "type": [
                "BasicContainer",
                "AnnotationCollection"
            ],
            "id": "http://example.org/w3c/123",
            "@context": [
                "http:\/\/www.w3.org\/ns\/anno.jsonld",
                "http:\/\/www.w3.org\/ns\/ldp.jsonld"
            ]
        }';
        });

        // Actual test.
        $container = $this->client->createContainer(
            new Container('my new container')
        );

        $this->assertEquals('http://example.org/w3c/123', $container['id']);
    }

    public function test_can_get_container()
    {
        $this->http->setGet(function ($endpoint) {
            $this->assertEquals('http://example.org/w3c/123/', $endpoint);

            return '{
            "label": "my new container",
            "type": [
                "BasicContainer",
                "AnnotationCollection"
            ],
            "id": "http://example.org/w3c/123",
            "@context": [
                "http:\/\/www.w3.org\/ns\/anno.jsonld",
                "http:\/\/www.w3.org\/ns\/ldp.jsonld"
            ]
        }';
        });

        // Actual test.
        $container = $this->client->getContainer('http://example.org/w3c/123');
        $this->assertEquals('http://example.org/w3c/123', $container['id']);

        $container = $this->client->getContainer(new Container('my new container', 'http://example.org/w3c/123'));
        $this->assertEquals('http://example.org/w3c/123', $container['id']);

        $container = $this->client->getContainer(new Container('out of date', 'http://example.org/w3c/123'));
        $this->assertEquals('my new container', $container['label']);
        $this->assertEquals('http://example.org/w3c/123', $container['id']);

        $container = $this->client->getContainer(new Container(null, 'http://example.org/w3c/123'));
        $this->assertEquals('my new container', $container['label']);
        $this->assertEquals('http://example.org/w3c/123', $container['id']);
    }

    public function test_get_annotation()
    {
        $this->http->setGet(function () {
            return '{
              "@context": "http://www.w3.org/ns/anno.jsonld",
              "id": "http://example.org/w3c/123/456",
              "type": "Annotation",
              "body": {
                "type": "TextualBody",
                "value": "I like this page!"
              },
              "target": "http://www.example.com/index.html"
            }';
        });
        $annotation = $this->client->getAnnotation('123', '456');

        $this->assertEquals('http://example.org/w3c/123/456', $annotation['id']);
        $this->assertEquals('Annotation', $annotation['type']);
        $this->assertEquals('TextualBody', $annotation['body']['type']);
        $this->assertEquals('I like this page!', $annotation['body']['value']);
        $this->assertEquals('http://www.example.com/index.html', $annotation['target']);
    }

    public function test_create_annotation()
    {
        $this->http->setPost(function ($endpoint) {
            $this->assertEquals('http://example.org/w3c/123/', $endpoint);

            return '{
              "@context": "http://www.w3.org/ns/anno.jsonld",
              "id": "http://example.org/w3c/123/456",
              "type": "Annotation",
              "body": {
                "type": "TextualBody",
                "value": "I like this page!"
              },
              "target": "http://www.example.com/index.html"
            }';
        });

        $annotation = new Annotation(null, [
            'type' => 'TextualBody',
            'value' => 'I like this page! Updated',
        ], 'http://www.example.com/index.html');
        $annotation->withContainer('http://example.org/w3c/123');

        $newAnnotation = $this->client->createAnnotation($annotation);

        $this->assertEquals($newAnnotation['id'], 'http://example.org/w3c/123/456');
    }

    public function test_put_annotation()
    {
        $this->http->setPut(function ($endpoint, $annotation) {
            $this->assertEquals('http://example.org/w3c/123/456', $endpoint);

            return json_encode($annotation);
        });

        $annotation = new Annotation('http://example.org/w3c/123/456', [
            'type' => 'TextualBody',
            'value' => 'I like this page! Updated',
        ], 'http://www.example.com/index.html');

        $annotation->withContainer('http://example.org/w3c/123');

        $newAnnotation = $this->client->updateAnnotation($annotation);

        $this->assertEquals($annotation['body'], $newAnnotation['body']);
        $this->assertEquals($annotation['target'], $newAnnotation['target']);
        $this->assertEquals($annotation['id'], $newAnnotation['id']);
    }

    public function test_delete_annotation()
    {
        $this->http->setDelete(function ($endpoint) {
            $this->assertEquals('http://example.org/w3c/123/456', $endpoint);

            return true;
        });

        $annotation = new Annotation('http://example.org/w3c/123/456', [
            'type' => 'TextualBody',
            'value' => 'I like this page! Updated',
        ], 'http://www.example.com/index.html');

        $deleted = $this->client->deleteAnnotation($annotation);

        $this->assertNotNull($deleted);
    }

    public function test_can_search()
    {
        $this->http->setGet(function ($endpoint) {
            $this->assertEquals('services/search/body?fields=id&strict=0&value=http%3A%2F%2Fwww.example.com%2Findex.html', $endpoint);

            return '{}';
        });
        $this->client->performSearch(new SearchByBody(['id'], 'http://www.example.com/index.html'));

        $this->http->setGet(function ($endpoint) {
            $this->assertEquals('services/search/target?fields=source&value=http%3A%2F%2Fwww.example.com%2Findex.html&strict=0&xyhw=10%2C10%2C10%2C10&t=1', $endpoint);

            return '{}';
        });
        $this->client->performSearch(new SearchByTarget(['source'], 'http://www.example.com/index.html', false, '10,10,10,10', '1'));
    }
}
