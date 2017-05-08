<?php

namespace Elucidate\Search;

trait UriQueryString
{
    abstract public function getPath() : string;

    public function __toString(): string
    {
        return $this->getPath().'?'.http_build_query(get_object_vars($this));
    }
}
