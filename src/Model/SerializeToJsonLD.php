<?php

namespace Elucidate\Model;

trait SerializeToJsonLD
{
    abstract public function getContext();

    public function jsonSerialize()
    {
        $json = get_object_vars($this);
        unset($json['container']);
        $json['@context'] = $this->getContext();

        return $json;
    }

    public function withContainer($container)
    {
        if (is_string($container)) {
            $container = new Container(null, $container);
        }
        $this->container = $container;

        return $this;
    }

    public static function fromJson(string $json)
    {
        $data = json_decode($json, true);
        $model = new static();
        foreach ($data as $field => $value) {
            $model->{$field} = $value;
        }

        return $model;
    }
}
