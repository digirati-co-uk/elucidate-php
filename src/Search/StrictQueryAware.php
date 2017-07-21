<?php

namespace Elucidate\Search;

trait StrictQueryAware
{
    /**
     * A flag indicating whether this query should be a strict comparison between {@code value} and the subject.
     *
     * @var bool
     */
    private $strict;

    /**
     * Toggles the strict flag of this query.  Iff a query is {@code strict} then only exact matches against {@code value}
     * will be returned.
     *
     * @param bool $strict
     * @return $this
     */
    public function strict(bool $strict)
    {
        $this->strict = $strict;
        return $this;
    }
}