<?php

namespace Elucidate\Tests;

use Elucidate\Client;
use Elucidate\Event\AnnotationLifecycleEvent;
use Elucidate\Event\ContainerLifecycleEvent;
use Elucidate\EventAwareClient;
use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use Elucidate\Tests\Mocks\MockHttpAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventAwareClientTest extends TestCase
{
    /** @var $http MockHttpAdapter */
    private $http;
    /** @var $ogClient Client */
    private $ogClient;
    /** @var $client EventAwareClient */
    private $client;
    /** @var $ev EventDispatcher */
    private $ev;

    public function setUp()
    {
        $this->http = new MockHttpAdapter();
        $this->ogClient = new Client($this->http);
        $this->ev = new EventDispatcher();
        $this->client = new EventAwareClient($this->ogClient, $this->ev);
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

    public function test_can_create_container_with_id()
    {
        $this->http->setPost(function ($endpoint, $request) {
            $headers = $request->getHeaders();
            $this->assertEquals('test-id-123', $headers['Slug']);

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
            new Container('my new container', 'test-id-123')
        );

        $this->assertEquals('http://example.org/w3c/123', $container['id']);
    }

    public function test_can_create_container_with_change_at_create()
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
        $wasCalled = new WasCalled('containerLifeCycleEvent', function (ContainerLifecycleEvent $event) {
            $this->assertEquals('http://example.org/w3c/123', $event->getSubject()['id']);
            $event->setContainer(new Container('Testing', 'http://example.org/w3c/456'));
        });

        $this->ev->addListener(ContainerLifecycleEvent::CREATE, $wasCalled);

        // Actual test.
        $container = $this->client->createContainer(
            new Container('my new container')
        );

        $this->assertEquals('http://example.org/w3c/456', $container['id']);
        $this->assertEquals('Testing', $container['label']);

        $wasCalled->assertWasCalledExactly(1);
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

        $this->ev->addListener(ContainerLifecycleEvent::PRE_READ, $preRead = new WasCalled(ContainerLifecycleEvent::PRE_READ));
        $this->ev->addListener(ContainerLifecycleEvent::READ, $read = new WasCalled(ContainerLifecycleEvent::READ));

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

        $this->ev->addListener(
            ContainerLifecycleEvent::READ,
            $listener = new WasCalled('listener', function (ContainerLifecycleEvent $container) {
                $container->setArgument('container', new Container('FROM EVENT', 'http://from.event/123'));
            })
        );

        $container = $this->client->getContainer(new Container(null, 'http://example.org/w3c/123'));
        $this->assertEquals('FROM EVENT', $container['label']);
        $this->assertEquals('http://from.event/123', $container['id']);
        $listener->assertWasCalled();

        $this->ev->addListener(
            ContainerLifecycleEvent::READ,
            $listener = new WasCalled('listener', function (ContainerLifecycleEvent $container) {
                $container->setContainer(new Container('FROM EVENT', 'http://from.event/123'));
            })
        );

        $container = $this->client->getContainer(new Container(null, 'http://example.org/w3c/123'));
        $this->assertEquals('FROM EVENT', $container['label']);
        $this->assertEquals('http://from.event/123', $container['id']);
        $listener->assertWasCalled();

        $preRead->assertWasCalledExactly(6);
        $read->assertWasCalledExactly(6);
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

        $this->ev->addListener(
            AnnotationLifecycleEvent::READ,
            $read = new WasCalled(AnnotationLifecycleEvent::READ)
        );
        $this->ev->addListener(
            AnnotationLifecycleEvent::PRE_READ,
            $preRead = new WasCalled(AnnotationLifecycleEvent::PRE_READ)
        );
        $this->ev->addListener(
            AnnotationLifecycleEvent::CREATE,
            $create = new WasCalled(AnnotationLifecycleEvent::CREATE)
        );
        $this->ev->addListener(
            AnnotationLifecycleEvent::PRE_CREATE,
            $preCreate = new WasCalled(AnnotationLifecycleEvent::PRE_CREATE)
        );
        $annotation = $this->client->getAnnotation('123', '456');

        $this->assertEquals('http://example.org/w3c/123/456', $annotation['id']);
        $this->assertEquals('Annotation', $annotation['type']);
        $this->assertEquals('TextualBody', $annotation['body']['type']);
        $this->assertEquals('I like this page!', $annotation['body']['value']);
        $this->assertEquals('http://www.example.com/index.html', $annotation['target']);

        $read->assertWasCalledExactly(1);
        $preRead->assertWasCalledExactly(1);
        $create->assertWasNotCalled();
        $preCreate->assertWasNotCalled();
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
        $this->ev->addListener(
            AnnotationLifecycleEvent::CREATE,
            $create = new WasCalled('annotationListener')
        );
        $this->ev->addListener(
            AnnotationLifecycleEvent::PRE_CREATE,
            $preCreate = new WasCalled('annotationListener')
        );

        $annotation = new Annotation(null, [
            'type' => 'TextualBody',
            'value' => 'I like this page! Updated',
        ], 'http://www.example.com/index.html');
        $annotation->withContainer('http://example.org/w3c/123');

        $newAnnotation = $this->client->createAnnotation($annotation);

        $this->assertEquals($newAnnotation['id'], 'http://example.org/w3c/123/456');
        $preCreate->assertWasCalledExactly(1);
        $create->assertWasCalledExactly(1);
    }

    public function test_create_annotation_with_event_mutation()
    {
        $this->http->setPost(function ($endpoint, Annotation $annotation) {
            $this->assertEquals('http://example.org/w3c/123/', $endpoint);
            $json = $annotation->jsonSerialize();
            $this->assertEquals($json['findMe'], 'custom meta data');

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
        $this->ev->addListener(
            AnnotationLifecycleEvent::PRE_CREATE,
            $preCreate = new WasCalled(AnnotationLifecycleEvent::PRE_CREATE, function (AnnotationLifecycleEvent $ev) {
                $annotation = $ev->getOriginalAnnotation();
                $annotation->addMetaData(['findMe' => 'custom meta data']);

                $this->assertFalse($ev->annotationExists());
            })
        );

        $annotation = new Annotation(null, [
            'type' => 'TextualBody',
            'value' => 'I like this page! Updated',
        ], 'http://www.example.com/index.html');
        $annotation->withContainer('http://example.org/w3c/123');

        $newAnnotation = $this->client->createAnnotation($annotation);

        $this->assertEquals($newAnnotation['id'], 'http://example.org/w3c/123/456');

        $preCreate->assertWasCalledExactly(1);
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

    public function test_post_can_be_stopped()
    {
        $this->http->setPost(function ($endpoint) {
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
        $this->ev->addListener(
            AnnotationLifecycleEvent::CREATE,
            $create = new WasCalled('annotationListener')
        );
        $this->ev->addListener(
            AnnotationLifecycleEvent::PRE_CREATE,
            $preCreate = new WasCalled('annotationListener', function (AnnotationLifecycleEvent $e) {
                $e->preventPostProcess();
            })
        );

        $annotation = new Annotation(null, [
            'type' => 'TextualBody',
            'value' => 'I like this page! Updated',
        ], 'http://www.example.com/index.html');
        $annotation->withContainer('http://example.org/w3c/123');

        $newAnnotation = $this->client->createAnnotation($annotation);

        $this->assertEquals($newAnnotation['id'], 'http://example.org/w3c/123/456');

        $preCreate->assertWasCalledExactly(1);
        $create->assertWasNotCalled();
    }

    public function test_post_can_be_replaced_and_stopped()
    {
        $this->http->setPost(function ($endpoint) {
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
        $this->ev->addListener(
            AnnotationLifecycleEvent::CREATE,
            $create = new WasCalled('annotationListener')
        );
        $this->ev->addListener(
            AnnotationLifecycleEvent::PRE_CREATE,
            $preCreate = new WasCalled('annotationListener', function (AnnotationLifecycleEvent $e) {
                $e->preventPostProcess();
                $e->setAnnotation(new Annotation('http://example.org/omeka/123456789'));
                $this->assertTrue($e->annotationExists());
            })
        );

        $annotation = new Annotation(null, [
            'type' => 'TextualBody',
            'value' => 'I like this page! Updated',
        ], 'http://www.example.com/index.html');
        $annotation->withContainer('http://example.org/w3c/123');

        $newAnnotation = $this->client->createAnnotation($annotation);

        $this->assertEquals($newAnnotation['id'], 'http://example.org/omeka/123456789');

        $preCreate->assertWasCalledExactly(1);
        $create->assertWasNotCalled();
    }

    public function test_post_can_be_replaced_and_continued()
    {
        $this->http->setPost($post = new WasCalled('post', function ($endpoint, $request) {
            $this->assertEquals('http://example.org/w3c/123/', $endpoint);
            $this->assertEquals('I was changed!', $request['body']['value']);
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
        }));
        $this->ev->addListener(
            AnnotationLifecycleEvent::CREATE,
            $create = new WasCalled('annotationListener')
        );
        $this->ev->addListener(
            AnnotationLifecycleEvent::PRE_CREATE,
            $preCreate = new WasCalled('annotationListener', function (AnnotationLifecycleEvent $e) {
                $annotation = $e->getLatestAnnotation();
                if ($annotation) {
                    $e->setAnnotation($annotation->changeBody([
                        'value' => 'I was changed!'
                    ]));
                    $e->markAsModified();
                }
                $this->assertTrue($e->annotationExists());
            })
        );

        $annotation = new Annotation(null, [
            'type' => 'TextualBody',
            'value' => 'I like this page! Updated',
        ], 'http://www.example.com/index.html');
        $annotation->withContainer('http://example.org/w3c/123');

        $newAnnotation = $this->client->createAnnotation($annotation);

        $this->assertEquals($newAnnotation['id'], 'http://example.org/w3c/123/456');

        $preCreate->assertWasCalledExactly(1);
        $preCreate->assertWasCalledExactly(1);
        $post->assertWasCalledExactly(1);
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

        $true = $this->client->deleteAnnotation($annotation);

        $this->assertNotNull($true);
    }
}
