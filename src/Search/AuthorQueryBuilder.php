<?php

namespace Elucidate\Search;

use Assert\Assertion;

class AuthorQueryBuilder
{
    use StrictQueryAware;

    /**
     * Relative path to the annotation creator search service.
     */
    const SEARCH_CREATOR_SERVICE_PATH = 'services/search/creator';

    /**
     * Relative path to the annotation generator search service.
     */
    const SEARCH_GENERATOR_SERVICE_PATH = 'services/search/generator';

    /**
     * The levels within an annotation to search for the {@code creator} or {@code generator}.
     *
     * @var string[]
     */
    private $levels = [];

    /**
     * The path of the author search service in use.
     *
     * @var string
     */
    private $path;

    /**
     * The type of field
     *
     * @var string
     */
    private $type;

    /**
     * The search term for this query.
     *
     * @var string
     */
    private $value;

    /**
     * AuthorQueryBuilder constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Create a new query builder that searches by {@code creator}.
     *
     * @return AuthorQueryBuilder
     */
    public static function byCreator()
    {
        return new self(static::SEARCH_CREATOR_SERVICE_PATH);
    }

    /**
     * Create a new query builder that searches by {@code generator}.
     *
     * @return AuthorQueryBuilder
     */
    public static function byGenerator()
    {
        return new self(static::SEARCH_GENERATOR_SERVICE_PATH);
    }

    /**
     * Add a search level to this query builder.  Valid search levels are: 'annotation', 'body', and 'target'.  These
     * reflect which part of the annotation will be searched for the search term.
     *
     * @param string $level
     * @return $this
     */
    public function atLevel(string $level)
    {
        $this->levels[] = $level;
        return $this;
    }

    /**
     * Make this search match at all levels of an annotation.
     *
     * @return $this
     */
    public function atAllLevels()
    {
        $this->levels = ['body', 'target', 'annotation'];
        return $this;
    }

    public function build(): ServiceQuery
    {
        Assertion::notEmpty($this->levels, "Must provide search levels to query at");
        Assertion::allInArray($this->levels, ['body', 'target', 'annotation'], 'Levels can only be one of: body, target, annotation');
        Assertion::notNull($this->type, 'Must provide a type to query by.');
        Assertion::notNull($this->value, 'Must provide a search term');

        $parameters = [
            'levels' => implode($this->levels, ','),
            'type' => $this->type,
            'value' => $this->value,
            'strict' => $this->strict ? 'true' : 'false'
        ];

        return new ServiceQuery($this->path, $parameters);
    }

    private function with(string $field, string $value)
    {
        Assertion::null($this->type, "Already filtering by '{$this->type}', cant filter by another field");

        $this->type = $field;
        $this->value = $value;

        return $this;
    }

    /**
     * Filter this query by {@code email} fields matching the given {@code value}.
     *
     * @param string $value
     * @return $this
     */
    public function withEmail(string $value)
    {
        return $this->with('email', $value);
    }

    /**
     * Filter this query by {@code emailsha1} fields matching the given {@code value}.
     *
     * @param string $value
     * @return $this
     */
    public function withEmailSha1(string $value)
    {
        return $this->with('emailsha1', $value);
    }

    /**
     * Filter this query by {@code id} fields matching the given {@code value}.
     *
     * @param string $value
     * @return $this
     */
    public function withId(string $value)
    {
        return $this->with('id', $value);
    }

    /**
     * Filter this query by {@code name} fields matching the given {@code value}.
     *
     * @param string $value
     * @return $this
     */
    public function withName(string $value)
    {
        return $this->with('name', $value);
    }

    /**
     * Filter this query by {@code nickname} fields matching the given {@code value}.
     *
     * @param string $value
     * @return $this
     */
    public function withNickname(string $value)
    {
        return $this->with('nickname', $value);
    }
}