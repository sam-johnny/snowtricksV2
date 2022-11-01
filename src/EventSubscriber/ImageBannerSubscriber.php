<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 19/10/2022
 * Time: 21:41
 */

namespace App\EventSubscriber;


use App\Entity\ImageBanner;
use App\Event\ImageBannerEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ImageBannerSubscriber implements EventSubscriberInterface
{
    #[ArrayShape([ImageBannerEvent::NAME => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            ImageBannerEvent::NAME => "onSetter",
        ];
    }

    public function onSetter(ImageBannerEvent $event): void
    {
        if ($event->getData() instanceof ImageBanner) {
            $event->getData()->setImageFilename($event->getData()->getImageFile());
        }
    }
}