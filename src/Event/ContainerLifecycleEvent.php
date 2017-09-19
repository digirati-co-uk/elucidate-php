<?php

namespace Elucidate\Event;

use Elucidate\Model\Container;

class ContainerLifecycleEvent extends ElucidateEvent
{
    const PRE_CREATE = 'elucidate.container.pre_create';
    const CREATE = 'elucidate.container.create';
    const PRE_UPDATE = 'elucidate.container.pre_update';
    const UPDATE = 'elucidate.container.update';
    const PRE_READ = 'elucidate.container.pre_read';
    const READ = 'elucidate.container.read';
    const PRE_DELETE = 'elucidate.container.pre_delete';
    const DELETE = 'elucidate.container.delete';
    const EMBEDDED_READ = 'elucidate.container.embedded_read';

    public function getOriginalContainer(): Container
    {
        return $this->subject;
    }

    public static function getType(): string
    {
        return 'container';
    }

    public function setContainer(Container $container)
    {
        $this->setArgument('container', $container);
        return $this;
    }

    public function containerExists(): bool
    {
        return !!$this->hasArgument('container');
    }

    public function getContainer(): Container
    {
        return $this->getArgument('container');
    }
}
