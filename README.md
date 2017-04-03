# Elucidate PHP

## Installation
```
composer require dlcs/elucidate-php
```

You will also need to install GuzzleHttp >= v6

## Usage
```php

$guzzle = new Elucidate\Adapter\GuzzleHttpAdapter(
  new GuzzleHttp\Client(['base_uri' => 'http://my.eluciadte.server'])
);

$elucidate = new Elucidate\Client($guzzle);


$container = $elucidate->createContainer('My first container');

$annotation = new Eluciadte\Model\Annotation(null, [
            'type' => 'TextualBody',
            'value' => 'I like this page!'
        ], 'http://www.example.com/index.html');

$annotation->withContainer($container);

$elucidate->createAnnotation($annotation);

$json = $elucidate->search(new Elucidate\Search\SearchByTarget(['id'], 'http://www.example.com/index.html'));

$rawSearch = json_decode($json);

...

```
