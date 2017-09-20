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

    public function transformAnnotation(Annotation $annotation) : Annotation
    {
        $fields = $annotation->jsonSerialize();
        $fields['id'] = $this->transformUri($annotation['id']);
        return Annotation::fromArray($fields);
    }

    public function transformContainer(Container $container) : Container
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

        return Container::fromArray($fields);
    }

    public function __invoke($annotationOrContainer)
    {
        if ($annotationOrContainer instanceof Annotation) {
            return $this->transformAnnotation($annotationOrContainer);
        }
        if ($annotationOrContainer instanceof Container) {
            return $this->transformContainer($annotationOrContainer);
        }
        throw new InvalidArgumentException("Unsupported type in Url Transform: " . get_class($annotationOrContainer));
    }
}
