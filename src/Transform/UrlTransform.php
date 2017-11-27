<?php

namespace Elucidate\Transform;

use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use InvalidArgumentException;
use Zend\Uri\Uri;

class UrlTransform
{
    private $uri;

    public function __construct(string $uri)
    {
        $this->uri = new Uri($uri);
    }

    private function transformUri($url)
    {
        $newUrl = new Uri($url);
        $newUrl->setHost($this->uri->getHost());
        $newUrl->setPort($this->uri->getPort());
        $newUrl->setScheme($this->uri->getScheme());

        return $newUrl->toString();
    }

    public function transformAnnotation(Annotation $annotation): Annotation
    {
        $fields = $annotation->jsonSerialize();

        // JSON.id
        $fields['id'] = $this->transformUri($annotation['id']);

        // JSON.via
        if (isset($fields['via'])) {
            $fields['via'] = $this->transformUri($annotation['via']);
        }

        return (Annotation::fromArray($fields))
            ->withContainer($annotation->getContainer())
            ->setHeaders($annotation->getHeaders());
    }

    public function transformContainer(Container $container): Container
    {
        $fields = $container->jsonSerialize();
        // JSON.id
        $fields['id'] = $this->transformUri($container['id']);

        // JSON.first.items
        if (isset($fields['first']['items'])) {
            $fields['first']['items'] = array_map(function ($annotation) {
                $annotation['id'] = $this->transformUri($annotation['id']);

                return $annotation;
            }, $fields['first']['items']);
        }

        // JSON.first.partOf
        if (isset($fields['first']['partOf'])) {
            $fields['first']['partOf'] = $this->transformUri($container['first']['partOf']);
        }

        // JSON.last
        if (isset($fields['last'])) {
            $fields['last'] = $this->transformUri($container['last']);
        }

        // JSON.next
        if (isset($fields['next'])) {
            $fields['next'] = $this->transformUri($container['next']);
        }

        // JSON.prev
        if (isset($fields['prev'])) {
            $fields['prev'] = $this->transformUri($container['prev']);
        }

        $headers = $container->getHeaders();
        if (isset($headers['Location'])) {
            $headers['Location'] = is_array($headers['Location']) ?
                (is_string($headers['Location'][0]) ? $this->transformUri($headers['Location'][0]) : $headers['Location']) :
                (is_string($headers['Location']) ? $this->transformUri($headers['Location']) : $headers['Location']);
        }


        return (Container::fromArray($fields))
            ->setHeaders($headers);
    }

    public function __invoke($annotationOrContainer)
    {
        if ($annotationOrContainer instanceof Annotation) {
            return $this->transformAnnotation($annotationOrContainer);
        }
        if ($annotationOrContainer instanceof Container) {
            return $this->transformContainer($annotationOrContainer);
        }
        throw new InvalidArgumentException('Unsupported type in Url Transform: ' . get_class($annotationOrContainer));
    }
}
