<?php

use Elucidate\Search\AnnotationQueryBuilder;

final class AnnotationQueryBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedExceptionMessage Invalid fields given.  Expected 'id' or 'source'
     * @expectedException \Exception
     */
    public function testItThrowsAnExceptionWithInvalidFields()
    {
        AnnotationQueryBuilder::byBody()
            ->field('invalid_id', 'abc')
            ->build();
    }

    /**
     * @expectedExceptionMessage Must specify fields for an annotation query
     * @expectedException \Exception
     */
    public function testItThrowsAnExceptionWithNoFields()
    {
        AnnotationQueryBuilder::byBody()
            ->field([], 'abc')
            ->build();
    }

    /**
     * @expectedExceptionMessage Must specify a search term for an annotation query
     * @expectedException \Exception
     */
    public function testItThrowsAnExceptionWithNoValue()
    {
        AnnotationQueryBuilder::byBody()
            ->build();
    }
}
