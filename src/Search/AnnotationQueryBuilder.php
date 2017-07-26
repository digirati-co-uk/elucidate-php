<?php

namespace Elucidate\Search;

use Assert\Assertion;

final class AnnotationQueryBuilder
{
    use StrictQueryAware;

    /**
     * Relative path to the annotation body search service.
     */
    const SEARCH_BODY_SERVICE_PATH = '/services/search/body';

    /**
     * Relative path to the annotation target search service.
     */
    const SEARCH_TARGET_SERVICE_PATH = '/services/search/target';

    /**
     * @var string
     */
    private $creator;

    /**
     * The list of fields to be compared for this query.
     *
     * @var string[]
     */
    private $fields;

    /**
     * @var string
     */
    private $generator;

    /**
     * The relative path of the search service this query targets.
     *
     * @var string
     */
    private $path;

    /**
     * A media fragment spatial selector to filter annotations that intersect with the given dimensions.
     *
     * @var string
     */
    private $spatialSelector;

    /**
     * A flag indicating whether a strict comparison should be made between the subject and {@code value}.
     *
     * @var bool
     */
    private $strict = false;

    /**
     * A media fragment temporal selector to filter annotations that intersect with the given time period.
     *
     * @var string
     */
    private $temporalSelector;

    /**
     * The subject of comparison against the list of {@code fields}.
     *
     * @var string
     */
    private $value;

    private function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Create a new {@link AnnotationQueryBuilder} that searches annotation bodies.
     *
     * @return AnnotationQueryBuilder
     */
    public static function byBody()
    {
        return new self(static::SEARCH_BODY_SERVICE_PATH);
    }

    /**
     * Create a new {@link AnnotationQueryBuilder} that searches annotation targets.
     *
     * @return AnnotationQueryBuilder
     */
    public static function byTarget()
    {
        return new self(static::SEARCH_TARGET_SERVICE_PATH);
    }

    public function build(): ServiceQuery
    {
        Assertion::notEmpty($this->fields, "Must specify fields for an annotation query");
        Assertion::allInArray($this->fields, ['id', 'source'], "Invalid fields given.  Expected 'id' or 'source'");
        Assertion::notNull($this->value, "Must specify a search term for an annotation query");

        $parameters = [
            'fields' => implode($this->fields, ','),
            'value' => $this->value,
            'strict' => $this->strict ? 'true' : 'false',
            't' => $this->temporalSelector,
            'xywh' => $this->spatialSelector,
            'generator' => $this->generator,
            'creator' => $this->creator
        ];

        return new ServiceQuery($this->path, $parameters);
    }

    private function field($id, string $value)
    {
        $this->fields = is_array($id) ? $id : [$id];
        $this->value = $value;

        return $this;
    }

    /**
     * Filter this query by annotations that have the given creator.
     *
     * @param string $value
     * @return $this
     */
    public function withCreator(string $value)
    {
        $this->creator = $value;
        return $this;
    }

    /**
     * Filter this query by annotations that have the given generator.
     *
     * @param string $value
     * @return $this
     */
    public function withGenerator(string $value)
    {
        $this->generator = $value;
        return $this;
    }

    /**
     * Filter this query by annotations having the given {@code id} value.
     *
     * @param string $value
     * @return AnnotationQueryBuilder
     */
    public function withId(string $value)
    {
        return $this->field('id', $value);
    }

    /**
     * Filter this query by annotations having the given {@code source} value.
     *
     * @param string $value
     * @return AnnotationQueryBuilder
     */
    public function withSource(string $value)
    {
        return $this->field('source', $value);
    }

    /**
     * Filter this query by annotations which intersect with the given spatial selector.
     *
     * @param string $selector
     * @return $this
     */
    public function withSpatialDimensions(string $selector)
    {
        $this->spatialSelector = $selector;
        return $this;
    }

    /**
     * Filter this query by annotations which intersect with the given temporal selector.
     *
     * @param string $selector
     * @return $this
     */
    public function withTemporalDimensions(string $selector)
    {
        $this->temporalSelector = $selector;
        return $this;
    }
}