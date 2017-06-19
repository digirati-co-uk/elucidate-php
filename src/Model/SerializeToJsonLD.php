<?php

namespace Elucidate\Model;

trait SerializeToJsonLD
{
    abstract public function getContext();

    public function jsonSerialize()
    {
        $json = get_object_vars($this);
        unset($json['metaData']);
        unset($json['container']);
        $json['@context'] = $this->getContext();
        // Add metaData.
        if (isset($this->metaData)) {
            foreach ($this->metaData as $key => $metaData) {
                $json[$key] = $metaData;
            }
        }

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

    public static function fromArray(array $data)
    {
        $model = new static();
        foreach ($data as $field => $value) {
            $model->{$field} = $value;
        }

        return $model;
    }

    public static function fromJson(string $json)
    {
        $data = json_decode($json, true);

        return self::fromArray($data);
    }
}
