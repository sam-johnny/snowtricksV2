<?php

namespace App\Handler\Form;

use App\Event\PostEvent;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use JetBrains\PhpStorm\Pure;

class PostHandler extends AbstractHandler
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function getFormType(): string
    {
        return PostType::class;
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
        return new PostEvent($data);
    }

    protected function getEventName(): ?string
    {
        return PostEvent::NAME;
    }


}