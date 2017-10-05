<?php

namespace Elucidate\Event;

use Elucidate\Model\Annotation;

class AnnotationLifecycleEvent extends ElucidateEvent
{
    const PRE_CREATE = 'elucidate.annotation.pre_create';
    const CREATE = 'elucidate.annotation.create';
    const PRE_UPDATE = 'elucidate.annotation.pre_update';
    const UPDATE = 'elucidate.annotation.update';
    const PRE_READ = 'elucidate.annotation.pre_read';
    const READ = 'elucidate.annotation.read';
    const PRE_DELETE = 'elucidate.annotation.pre_delete';
    const DELETE = 'elucidate.annotation.delete';
    const EMBEDDED_READ = 'elucidate.annotation.embedded_read';

    public function getOriginalAnnotation(): Annotation
    {
        return $this->subject;
    }

    public static function getType(): string
    {
        return 'annotation';
    }

    public function setAnnotation(Annotation $annotation)
    {
        $this->setArgument('annotation', $annotation);

        return $this;
    }

    public function markAsModified()
    {
        $this->setArgument('markAsModified', true);
    }

    public function isModified()
    {
        if ($this->hasArgument('markAsModified')) {
            return $this->getArgument('markAsModified');
        }
        return false;
    }

    public function annotationExists(): bool
    {
        return $this->hasArgument('annotation');
    }

    public function getAnnotation(): Annotation
    {
        return $this->getArgument('annotation');
    }

    public function getLatestAnnotation()
    {
        if ($this->annotationExists()) {
            return $this->getAnnotation();
        }
        $annotation = $this->getSubject();
        if ($annotation instanceof Annotation) {
            return $annotation;
        }
        return null;
    }

    public static function supports($subject): bool
    {
        return $subject instanceof Annotation;
    }
}
