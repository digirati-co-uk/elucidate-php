<?php
namespace Elucidate;

use Elucidate\Adapter\HttpAdapter;
use Elucidate\Exception\AnnotationOrphanException;
use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use Elucidate\Search\SearchQuery;

class Client
{
    public function __construct(HttpAdapter $client)
    {
        $this->client = $client;
    }

    public function getContainer($idOrContainer) {
        return Container::fromJson(
            $this->client->get((string) $idOrContainer)
        );
    }

    public function createContainer(Container $container)
    {
        return Container::fromJson(
            $this->client->post('/', $container)
        );
    }

    public function getAnnotation($container, $annotation)
    {
        return Annotation::fromJson(
            $this->client->get($annotation)
        )->withContainer($container);
    }

    public function createAnnotation(Annotation $annotation) : Annotation
    {
        $container = $annotation->getContainer();
        if (!$container) {
            throw new AnnotationOrphanException();
        }
        return Annotation::fromJson(
            $this->client->post($container, $annotation)
        )->withContainer($container);
    }

    public function updateAnnotation(Annotation $annotation) : Annotation
    {
        $container = $annotation->getContainer();
        if (!$container) {
            throw new AnnotationOrphanException();
        }
        return Annotation::fromJson(
            $this->client->put($annotation, $annotation)
        )->withContainer($container);
    }

    public function deleteAnnotation(Annotation $annotation) : bool
    {
        return $this->client->delete($annotation);
    }

    public function search(SearchQuery $query)
    {
        return $this->client->get($query);
    }
}
