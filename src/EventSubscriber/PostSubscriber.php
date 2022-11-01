<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 19/10/2022
 * Time: 21:41
 */

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Event\PostEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class PostSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security)
    {
    }

    #[ArrayShape([PostEvent::NAME => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            PostEvent::NAME => "onSetter",
        ];
    }

    public function onSetter(PostEvent $event): void
    {
        if ($event->getData() instanceof Post) {
            $event->getData()->setUser($this->security->getUser());
        }
    }
}