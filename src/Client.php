<?php

namespace Elucidate;

use Elucidate\Adapter\HttpAdapter;
use Elucidate\Exception\AnnotationOrphanException;
use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use Elucidate\Search\SearchQuery;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface
{
    private $client;

    public function __construct(
        HttpAdapter $client
    ) {
        $this->client = $client;
    }

    public function getContainer($idOrContainer): Container
    {
        if ($idOrContainer instanceof Container) {
            $idOrContainer = $idOrContainer->withRelativeId();
        }
        $id = substr($idOrContainer, -1, 1) === '/' ? (string) $idOrContainer : $idOrContainer.'/';

        return Container::fromResponse(
            $this->client->get($id)
        );
    }

    public function createContainer(Container $container): Container
    {
        if ($container['id']) {
            $setHeaders = $container->getHeaders();
            $container->setHeaders(
                array_merge($setHeaders, [
                    'Slug' => $container['id'],
                ])
            );
        }

        return Container::fromResponse(
            $this->client->post($this->client->getBaseUri(), $container)
        );
    }

    public function updateContainer(Container $container): Container
    {
        return Container::fromResponse(
            $this->client->put($container->withRelativeId(), $container)
        );
    }

    public function deleteContainer(Container $container)
    {
        return $this->client->delete($container->withRelativeId());
    }

    public function getAnnotation($container, $annotation): Annotation
    {
        if ($container instanceof Container) {
            $container = $container->withRelativeId();
        }
        if ($annotation instanceof Annotation && $annotation->getContainer() === null) {
            $annotation->withContainer($container);
        }
        if (is_string($annotation) && strpos($annotation, '/') === false) {
            $annotation = $container.'/'.$annotation;
        }

        return Annotation::fromResponse(
            $this->client->get($annotation)
        )->withContainer($container);
    }

    public function createAnnotation(Annotation $annotation): Annotation
    {
        $container = $annotation->getContainer();
        if (!$container) {
            throw new AnnotationOrphanException();
        }

        return Annotation::fromResponse(
            $this->client->post($container->withRelativeId().'/', $annotation)
        )->withContainer($container);
    }

    public function updateAnnotation(Annotation $annotation): Annotation
    {
        $container = $annotation->getContainer();
        if (!$container) {
            throw new AnnotationOrphanException();
        }

        return Annotation::fromResponse(
            $this->client->put($annotation->withRelativeId(), $annotation)
        )->withContainer($container);
    }

    public function deleteAnnotation(Annotation $annotation)
    {
        return $this->client->delete($annotation->withRelativeId());
    }

    /**
     * @deprecated
     */
    public function search(SearchQuery $query)
    {
        $response = $this->performSearch($query);

        return (string) $response->getBody();
    }

    public function performSearch(SearchQuery $query): ResponseInterface
    {
        return $this->client->get((string) $query);
    }
}
