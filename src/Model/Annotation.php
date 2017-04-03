<?php


namespace Elucidate\Model;


use ArrayAccess as ArrayAccessInterface;
use LogicException;

class Annotation implements RequestModel, ResponseModel, ArrayAccessInterface
{
    use JsonLDContext;
    use SerializeToJsonLD;
    use ArrayAccess;

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
        return $this->id;
    }
}
