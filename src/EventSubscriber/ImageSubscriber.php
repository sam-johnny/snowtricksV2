<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 19/10/2022
 * Time: 21:41
 */

namespace App\EventSubscriber;

use App\Entity\Image;
use App\Event\ImageEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ImageSubscriber implements EventSubscriberInterface
{
    #[ArrayShape([ImageEvent::NAME => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            ImageEvent::NAME => "onSetter",
        ];
    }

    public function onSetter(ImageEvent $event): void
    {
        if ($event->getData() instanceof Image) {
            $event->getData()->setImageFilename($event->getData()->getImageFile());
        }
    }
}