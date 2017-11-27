<?php

namespace Elucidate\Model;

use ArrayAccess as ArrayAccessInterface;

final class Container implements RequestModel, ResponseModel, ArrayAccessInterface
{
    use SerializeToJsonLD;
    use JsonLDContext;
    use ArrayAccess;

    private $label;
    private $type = 'AnnotationCollection';
    private $id;
    private $toStringTransformer;

    public function __construct(string $label = null, string $id = null)
    {
        $this->label = $label;
        $this->id = $id;
    }

    public function withRelativeId(string $replacementId = null)
    {
        if (!$this->id) {
            return $this;
        }
        $that = clone $this;
        $id = substr($that->id, -1) === '/' ? substr($this->id, 0, -1) : $this->id;

        $that->id = $replacementId ? $replacementId : implode('/', array_slice(explode('/', $id), -1, 1));

        return $that;
    }

    /** @internal */
    public function setToStringTransformer(callable $toStringTransformer)
    {
        $this->toStringTransformer = $toStringTransformer;

        return $this;
    }

    public function __toString()
    {
        if ($this->toStringTransformer) {
            $toStringTransformer = $this->toStringTransformer;

            return $toStringTransformer($this->id, $this);
        }

        return $this->id;
    }
}
