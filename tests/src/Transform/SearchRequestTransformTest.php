<?php

namespace Elucidate\Tests\Transform;

use Elucidate\Transform\SearchRequestTransform;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequestFactory;

class SearchRequestTransformTest extends TestCase
{
    public function test_url_transform_basic_annotation()
    {
        $request = ServerRequestFactory::fromGlobals([
            'REQUEST_URI' => 'https://elucidate.com/services/search/target',
        ], [
            // ?fields=test_field&value=test_value
            'fields' => 'test_field',
            'value' => 'test_value',
        ]);
        $transform = new SearchRequestTransform();

        $search = $transform($request);
        $this->assertEquals((string) $search, 'services/search/target?fields=test_field&value=test_value');
    }
}
