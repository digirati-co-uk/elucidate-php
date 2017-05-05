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
    $annotation = new Annotation('123', [
      'type' => 'TextualBody',
      'value' => 'I like this page!'
    ], 'http://www.example.com/index.html',
      [
        'id'=> 'http://localhost:8888/api/users/6',
        'type'=> 'Person',
        'name'=> 'test',
        'nickname'=> 'test',
        'email_sha1'=> 'test@test.com'
      ],
      [
        'id'=> 'http://example.org/client1',
        'type'=> 'Software',
        'name'=> 'Code v2.1',
        'homepage' => 'http://example.org/client1/homepage1'
      ]
    );

    $this->assertEquals('123', (string)$annotation);

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
    $this->assertContains("http://www.w3.org/ns/anno.jsonld", $annotation['@context']);
    $this->assertContains("http://www.w3.org/ns/ldp.jsonld", $annotation['@context']);
    $this->assertContains("124", $annotation['container']['id']);
  }
}
