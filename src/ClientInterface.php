<?php
namespace Elucidate;

use Elucidate\Model\Annotation;
use Elucidate\Model\Container;
use Elucidate\Search\SearchQuery;

interface ClientInterface
{
    public function getContainer($idOrContainer);

    public function createContainer(Container $container);

    public function getAnnotation($container, $annotation);

    public function createAnnotation(Annotation $annotation) : Annotation;

    public function updateAnnotation(Annotation $annotation) : Annotation;

    public function deleteAnnotation(Annotation $annotation);

    public function search(SearchQuery $query);
}
