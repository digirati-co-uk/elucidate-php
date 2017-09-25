<?php

use Elucidate\Search\ServiceQuery;

class ServiceQueryTest extends PHPUnit_Framework_TestCase
{
    public function testNullParametersAreNotIncluded()
    {
        $serviceQuery = new ServiceQuery('s', [
            'abc' => null,
            'def' => 'non-null',
        ]);

        $queryString = (string) $serviceQuery;
        $this->assertNotContains('abc=', $queryString);
    }

    public function testEmptyParametersAreIncluded()
    {
        $serviceQuery = new ServiceQuery('s', [
            'abc' => '',
            'def' => 'non-empty',
        ]);

        $queryString = (string) $serviceQuery;
        $this->assertContains('abc=', $queryString);
    }

    public function testParametersAreEncoded()
    {
        $serviceQuery = new ServiceQuery('s', [
            'abc' => 'a b',
        ]);

        $queryString = (string) $serviceQuery;
        $this->assertContains('abc=a+b', $queryString);
    }
}
