<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 19/10/2022
 * Time: 21:41
 */

namespace App\EventSubscriber;

use App\Entity\Comment;
use App\Event\CommentEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class CommentSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security)
    {
    }

    #[ArrayShape([CommentEvent::NAME => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            CommentEvent::NAME => "onSetter",
        ];
    }

    public function onSetter(CommentEvent $event): void
    {
        if ($event->getData() instanceof Comment) {
            $event->getData()->setUser($this->security->getUser());
        }
    }
}