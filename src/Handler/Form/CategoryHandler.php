<?php

namespace App\Handler\Form;

use App\Form\CategoryType;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use JetBrains\PhpStorm\Pure;

class CategoryHandler extends AbstractHandler
{

    public function __construct(private EntityManagerInterface $entityManager){}

    /**
     * @inheritDoc
     */
    #[Pure] protected function getFormType(): string
    {
        return CategoryType::class;
    }

    protected function process($data): void
    {

        if ($this->entityManager->getUnitOfWork()->getEntityState($data) === UnitOfWork::STATE_NEW){
            $this->entityManager->persist($data);
        }
        $this->entityManager->flush();
    }

    protected function getEvent($data, $form = null): ?object
    {
        return null;
    }

    protected function getEventName(): ?string
    {
        return null;
    }


}