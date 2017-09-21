<?php
namespace Elucidate;

use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use Elucidate\Search\SearchQuery;

interface ClientInterface
{
    public function getContainer($idOrContainer) : Container;

    public function createContainer(Container $container) : Container;

    public function updateContainer(Container $container): Container;

    public function deleteContainer(Container $container);

    public function getAnnotation($container, $annotation) : Annotation;

    public function createAnnotation(Annotation $annotation) : Annotation;

    public function updateAnnotation(Annotation $annotation) : Annotation;

    public function deleteAnnotation(Annotation $annotation);

    public function search(SearchQuery $query);
}
