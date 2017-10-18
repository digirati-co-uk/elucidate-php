<?php

namespace Elucidate\Tests\Transform;

use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use Elucidate\Transform\UrlTransform;
use PHPUnit\Framework\TestCase;

class UrlTransformTest extends TestCase
{
    public function test_url_transform_basic_annotation()
    {
        $annotation = new Annotation('http://google.com/123/456');
        $transform = new UrlTransform('https://yahoo.com');

        $newAnnotation = $transform($annotation);
        $this->assertEquals($newAnnotation['id'], 'https://yahoo.com/123/456');
    }

    public function test_url_transform_basic_annotation_with_headers()
    {
        $annotation = new Annotation('http://google.com/123/456');
        $annotation->setHeaders([ 'X-Find-Me' => 'expected value']);

        $transform = new UrlTransform('https://yahoo.com');

        $newAnnotation = $transform($annotation);
        $this->assertEquals($newAnnotation['id'], 'https://yahoo.com/123/456');

        $this->assertEquals($newAnnotation->getHeaders(), [
            'Accept' => 'application/ld+json; profile="http://www.w3.org/ns/anno.jsonld"',
            'Content-Type' => 'application/ld+json; profile="http://www.w3.org/ns/anno.jsonld"',
            'X-Find-Me' => 'expected value',
        ]);
    }

    public function test_url_transform_full_container()
    {
        $containerJson = file_get_contents(__DIR__.'/container.json');
        $container = Container::fromJson($containerJson);

        $transform = new UrlTransform('https://yahoo.com');
        $newContainer = $transform($container);

        $this->assertEquals($newContainer['id'], 'https://yahoo.com/annotation/w3c/0fe60b581d19c5c8203e3ec8870d196a/');

        $this->assertFalse(strpos(json_encode($newContainer), 'elucidate'));
    }
}
