<?php

namespace Elucidate;

use Elucidate\Adapter\HttpAdapter;
use Elucidate\Event\AnnotationLifecycleEvent;
use Elucidate\Event\ContainerLifecycleEvent;
use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use Elucidate\Search\SearchQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventAwareClient implements ClientInterface
{
    private $client;
    private $ev;

    public function __construct(Client $client, EventDispatcherInterface $ev)
    {
        $this->client = $client;
        $this->ev = $ev;
    }

    public static function create(HttpAdapter $client, EventDispatcherInterface $ev)
    {
        return new static(
            new Client($client),
            $ev
        );
    }

    private function containerLifecycle($idOrContainer, string $before, string $after, callable $action, ...$args)
    {
        /** @var ContainerLifecycleEvent $preEvent */
        $preEvent = $this->ev->dispatch($before, new ContainerLifecycleEvent($idOrContainer));

        $container = $preEvent->containerExists() ? $preEvent->getContainer() : $args ? $action($idOrContainer, ...$args) : $action($idOrContainer);

        /** @var ContainerLifecycleEvent $postEvent */
        $postEvent = $this->ev->dispatch($after, new ContainerLifecycleEvent($container));

        return $postEvent->containerExists() ? $postEvent->getContainer() : $container;
    }

    private function annotationLifecycle($idOrContainer, string $before, string $after, callable $action, ...$args)
    {
        /** @var AnnotationLifecycleEvent $preEvent */
        $preEvent = $this->ev->dispatch($before, new AnnotationLifecycleEvent($idOrContainer));

        $container = $preEvent->annotationExists() ? $preEvent->getAnnotation() : $args ? $action($idOrContainer, ...$args) : $action($idOrContainer);

        /** @var AnnotationLifecycleEvent $postEvent */
        $postEvent = $this->ev->dispatch($after, new AnnotationLifecycleEvent($container));

        return $postEvent->annotationExists() ? $postEvent->getAnnotation() : $container;
    }

    public function getContainer($idOrContainer): Container
    {
        return $this->containerLifecycle(
            $idOrContainer,
            ContainerLifecycleEvent::PRE_READ,
            ContainerLifecycleEvent::READ,
            function ($id) {
                return $this->client->getContainer($id);
            }
        );
    }

    public function createContainer(Container $container): Container
    {
        return $this->containerLifecycle(
            $container,
            ContainerLifecycleEvent::PRE_CREATE,
            ContainerLifecycleEvent::CREATE,
            function ($container) {
                return $this->client->createContainer($container);
            }
        );
    }

    public function getAnnotation($container, $annotation): Annotation
    {
        return $this->annotationLifecycle(
            $annotation,
            AnnotationLifecycleEvent::PRE_READ,
            AnnotationLifecycleEvent::READ,
            function ($annotation, $container) {
                return $this->client->getAnnotation($container, $annotation);
            },
            $container
        );
    }

    public function createAnnotation(Annotation $annotation): Annotation
    {
        return $this->annotationLifecycle(
            $annotation,
            AnnotationLifecycleEvent::PRE_CREATE,
            AnnotationLifecycleEvent::CREATE,
            function ($annotation) {
                return $this->client->createAnnotation($annotation);
            }
        );
    }

    public function updateAnnotation(Annotation $annotation): Annotation
    {
        return $this->annotationLifecycle(
            $annotation,
            AnnotationLifecycleEvent::PRE_UPDATE,
            AnnotationLifecycleEvent::UPDATE,
            function ($annotation) {
                return $this->client->updateAnnotation($annotation);
            }
        );
    }

    public function deleteAnnotation(Annotation $annotation)
    {
        $call = false;
        $this->annotationLifecycle(
            $annotation,
            AnnotationLifecycleEvent::PRE_DELETE,
            AnnotationLifecycleEvent::DELETE,
            function ($annotation) use (&$call) {
                $call = $this->client->deleteAnnotation($annotation);
            }
        );
        return $call;
    }

    public function search(SearchQuery $query)
    {
        return $this->client->search($query);
    }
}
