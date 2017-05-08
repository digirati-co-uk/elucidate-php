<?php

namespace Elucidate\Model;

use ArrayAccess as ArrayAccessInterface;

final class Container implements RequestModel, ResponseModel, ArrayAccessInterface
{
    use SerializeToJsonLD;
    use JsonLDContext;
    use ArrayAccess;

    private $label;
    private $type = [
        'BasicContainer',
        'AnnotationCollection',
    ];
    private $id;

    public function __construct(string $label = null, string $id = null)
    {
        $this->label = $label;
        $this->id = $id;
    }

    public function __toString()
    {
        return $this->id;
    }
}
