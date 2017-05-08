<?php

namespace Elucidate\Model;

use LogicException;

trait ArrayAccess
{
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->{$offset} : null;
    }

    public function offsetSet($offset, $value)
    {
        throw new LogicException('Object is immutable');
    }

    public function offsetUnset($offset)
    {
        throw new LogicException('Object is immutable');
    }
}
