<?php

namespace Elucidate\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Throwable;

abstract class ElucidateEvent extends GenericEvent
{
    const NAMESPACE = 'elucidate';

    protected $subject;
    protected $arguments;

    abstract public static function getType(): string;

    public function setException(Throwable $exception)
    {
        $this->setArgument('exception', $exception);
        return $this;
    }

    public function isValid(): bool
    {
        return $this->hasArgument('exception');
    }

    public function getException(): Throwable
    {
        return $this->getArgument('exception');
    }
}
