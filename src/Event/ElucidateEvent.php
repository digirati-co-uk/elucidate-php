<?php

namespace Elucidate\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

abstract class ElucidateEvent extends GenericEvent
{
    const NAMESPACE = 'elucidate';

    protected $subject;
    protected $arguments;

    abstract public static function getType(): string;
}
