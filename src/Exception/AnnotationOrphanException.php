<?php

namespace Elucidate\Exception;

use LogicException;
use Throwable;

class AnnotationOrphanException extends LogicException
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Annotation must have Contains associated with it', $code, $previous);
    }
}
