<?php

namespace App\Handler\Form;

use App\Event\ImageBannerEvent;
use App\Form\ImageBannerType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use JetBrains\PhpStorm\Pure;

class ImageBannerHandler extends AbstractHandler
{

    public function __construct(private EntityManagerInterface $entityManager,)
    {
    }

    protected function getFormType(): string
    {
        return ImageBannerType::class;
    }

    protected function process($data): void
    {

        if ($this->entityManager->getUnitOfWork()->getEntityState($data) === UnitOfWork::STATE_NEW) {
            $this->entityManager->persist($data);
        }
        $this->entityManager->flush();
    }

    #[Pure] protected function getEvent($data, $form): ?object
    {
        return new ImageBannerEvent($data);
    }

    protected function getEventName(): ?string
    {
        return ImageBannerEvent::NAME;
    }


}