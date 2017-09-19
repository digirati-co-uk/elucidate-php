<?php

use Elucidate\Search\AuthorQueryBuilder;

final class AuthorQueryBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Must provide search levels to query at
     */
    public function testItThrowsExceptionWithNoLevels()
    {
        AuthorQueryBuilder::byGenerator()
            ->withEmailSha1('abc123')
            ->build();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Levels can only be one of: body, target, annotation
     */
    public function testItThrowsExceptionAtInvalidLevel()
    {
        AuthorQueryBuilder::byGenerator()
            ->atLevel('invalid_level')
            ->withEmail('test@test.com')
            ->build();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Must provide a type to query by.
     */
    public function testItThrowsExceptionWithNoValue()
    {
        AuthorQueryBuilder::byCreator()
            ->atAllLevels()
            ->build();
    }
}
