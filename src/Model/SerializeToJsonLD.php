<?php

namespace Elucidate\Model;

use Psr\Http\Message\ResponseInterface;

trait SerializeToJsonLD
{
    private $httpInternalHeaders = [];

    abstract public function getContext();

    public function jsonSerialize()
    {
        $json = get_object_vars($this);
        unset($json['metaData']);
        unset($json['container']);
        unset($json['httpInternalHeaders']);
        $json['@context'] = $this->getContext();
        // Add metaData.
        if (isset($this->metaData)) {
            foreach ($this->metaData as $key => $metaData) {
                $json[$key] = $metaData;
            }
        }
        if ($json['id'] === null) {
            unset($json['id']);
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
        if (isset($data['errors'])) {
            return null;
        }

        return self::fromArray($data);
    }

    public function setHeaders($headers)
    {
        $this->httpInternalHeaders = $headers;

        return $this;
    }

    public function getInternalHeaders(): array
    {
        return $this->httpInternalHeaders;
    }

    public static function fromResponse(ResponseInterface $response)
    {
        // @todo flesh out model to include eTag and other data inside of the response that is not part of the body.
        return (static::fromJson($response->getBody()))->setHeaders($response->getHeaders());
    }
}
