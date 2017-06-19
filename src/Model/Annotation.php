<?php

namespace Elucidate\Model;

use ArrayAccess as ArrayAccessInterface;

class Annotation implements RequestModel, ResponseModel, ArrayAccessInterface
{
    use JsonLDContext;
    use SerializeToJsonLD;
    use ArrayAccess;
    use WithMetaData;

    private $type;
    private $body;
    private $target;
    private $id;
    private $container;

    public function getContainer()
    {
        return $this->container;
    }

    public function __construct(
        string $id = null,
        $body = null,
        $target = null,
        Container $container = null
    ) {
        $this->type = 'Annotation';
        $this->body = $body;
        $this->target = $target;
        $this->id = $id;
        $this->container = $container;
    }

    public function __toString()
    {
        return substr($this->id, -1) === '/' ? $this->id : $this->id.'/';
    }
}
