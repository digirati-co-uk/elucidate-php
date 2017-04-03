# Elucidate PHP
[![Build Status](https://travis-ci.com/digirati-co-uk/elucidate-php.svg?token=a5qCzEBD9SzwsGr2LNL2&branch=master)](https://travis-ci.com/digirati-co-uk/elucidate-php)

## Installation
```
composer require dlcs/elucidate-php
```

You will also need to install GuzzleHttp >= v6

## Usage
```php

// Create an HTTP driver.
$guzzle = new Elucidate\Adapter\GuzzleHttpAdapter(
  new GuzzleHttp\Client(['base_uri' => 'http://my.eluciadte.server'])
);

// Create the Elucidate client.
$elucidate = new Elucidate\Client($guzzle);

// Create a container for storing annotations.
$container = $elucidate->createContainer('My first container');

// Create an annotation.
$annotation = new Elucidate\Model\Annotation(null, [
            'type' => 'TextualBody',
            'value' => 'I like this page!'
        ], 'http://www.example.com/index.html');

// Assign it to a container.
$annotation->withContainer($container);

// Create it in the server.
$elucidate->createAnnotation($annotation);

// Search for the annotation.
$json = $elucidate->search(new Elucidate\Search\SearchByTarget(['id'], 'http://www.example.com/index.html'));

$rawSearch = json_decode($json);

...

```
