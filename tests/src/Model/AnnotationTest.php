<?php

namespace Elucidate\Tests\Model;

use Elucidate\Model\Annotation;
use PHPUnit_Framework_TestCase;

class AnnotationTest extends PHPUnit_Framework_TestCase
{
    public function test_can_instantiate()
    {
        new Annotation();
    }

    public function test_can_serialize()
    {
        $annotation = new Annotation(
            '123',
            [
            'type' => 'TextualBody',
            'value' => 'I like this page!',
        ],
            'http://www.example.com/index.html'
        );

        $annotation = $annotation->withMetaData([
           'creator' => [
               'id' => 'http://localhost:8888/api/users/6',
               'type' => 'Person',
               'name' => 'test',
               'nickname' => 'test',
               'email_sha1' => 'test@test.com',
           ],
            'generator' => [
                'id' => 'http://example.org/client1',
                'type' => 'Software',
                'name' => 'Code v2.1',
                'homepage' => 'http://example.org/client1/homepage1',
            ],
        ]);

        $this->assertEquals('123/', (string) $annotation);

        $json = '{
            "type": "Annotation",
            "body": {
                "type": "TextualBody",
                "value": "I like this page!"
            },
            "target": "http:\/\/www.example.com\/index.html",
            "id": "123",
            "creator": {
                "id": "http:\/\/localhost:8888\/api\/users\/6",
                "type": "Person",
                "name": "test",
                "nickname": "test",
                "email_sha1": "test@test.com"
            },
            "generator": {
                "id": "http:\/\/example.org\/client1",
                "type": "Software",
                "name": "Code v2.1",
                "homepage": "http:\/\/example.org\/client1\/homepage1"
            },
            "@context": [
                "http:\/\/www.w3.org\/ns\/anno.jsonld",
                "http:\/\/www.w3.org\/ns\/ldp.jsonld"
            ]
        }';

        $this->assertJsonStringEqualsJsonString($json, json_encode($annotation));
    }

    public function test_can_deserialize()
    {
        $json = '{
            "type": "Annotation",
            "body": {
                "type": "TextualBody",
                "value": "I like this page!"
            },
            "target": "http:\/\/www.example.com\/index.html",
            "id": "123",
            "creator": {
                "id": "http:\/\/localhost:8888\/api\/users\/6",
                "type": "Person",
                "name": "test",
                "nickname": "test",
                "email_sha1": "test@test.com"
            },
            "generator": {
                "id": "http:\/\/example.org\/client1",
                "type": "Software",
                "name": "Code v2.1",
                "homepage": "http:\/\/example.org\/client1\/homepage1"
            },
            "@context": [
                "http:\/\/www.w3.org\/ns\/anno.jsonld",
                "http:\/\/www.w3.org\/ns\/ldp.jsonld"
            ]
        }';

        $annotation = Annotation::fromJson($json)->withContainer('124');

        $this->assertEquals('Annotation', $annotation['type']);
        $this->assertEquals('http://www.example.com/index.html', $annotation['target']);
        $this->assertEquals('123', $annotation['id']);
        $this->assertEquals('test', $annotation['creator']['name']);
        $this->assertEquals('test@test.com', $annotation['creator']['email_sha1']);
        $this->assertEquals('Software', $annotation['generator']['type']);
        $this->assertEquals('I like this page!', $annotation['body']['value']);
        $this->assertContains('http://www.w3.org/ns/anno.jsonld', $annotation['@context']);
        $this->assertContains('http://www.w3.org/ns/ldp.jsonld', $annotation['@context']);
        $this->assertContains('124', $annotation['container']['id']);
    }

    public function test_error_response_returns_null()
    {
        $annotation = Annotation::fromJson('{"errors": {"error": "something went wrong"}}');

        $this->assertNull($annotation);
    }

    public function test_annotation_can_be_change_into_relative_version()
    {
        $json = '{
            "@context": [
              "http:\/\/www.w3.org\/ns\/anno.jsonld",
              "http:\/\/www.w3.org\/ns\/ldp.jsonld"
            ],
            "type": "Annotation",
            "body": null,
            "id": "http://server.com/w3c/annotation/CONTAINERID123/ANNOTATIONID123",
            "target": "http:\/\/www.example.com\/index.html"
        }';

        $annotation = Annotation::fromJson($json);

        $relativeAnnotation = $annotation->withRelativeId();
        $this->assertEquals('CONTAINERID123/ANNOTATIONID123', $relativeAnnotation['id']);

        $relativeAnnotation = $annotation->withRelativeId('CUSTOM_ID_I_PARSED');
        $this->assertEquals('CUSTOM_ID_I_PARSED', $relativeAnnotation['id']);
    }
}
