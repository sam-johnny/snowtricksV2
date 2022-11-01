<?php

namespace App\Handler\Form;

use App\Form\LinkMediaType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use JetBrains\PhpStorm\Pure;

class LinkMediaHandler extends AbstractHandler
{

    public function __construct(private EntityManagerInterface $entityManager,)
    {
    }

    protected function getFormType(): string
    {
        return LinkMediaType::class;
    }

    protected function process($data): void
    {

        if ($this->entityManager->getUnitOfWork()->getEntityState($data) === UnitOfWork::STATE_NEW) {
            $this->entityManager->persist($data);
        }
        $this->entityManager->flush();
    }

    protected function getEvent($data, $form): ?object
    {
        return null;
    }

    protected function getEventName(): ?string
    {
        return null;
    }


}