<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 16/09/2022
 * Time: 22:17
 */

namespace App\Handler\Form;

use App\Event\CommentEvent;
use App\Form\CommentType;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;

class CommentHandler extends AbstractHandler
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private EntityManagerInterface $entityManager){}

    /**
     * @return string
     */
    protected function getFormType(): string
    {
        return CommentType::class;
    }

    /**
     * @param
     * @return void
     */
    protected function process($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    #[Pure] protected function getEvent($data, $form = null): ?object
    {
        return new CommentEvent($data);
    }

    protected function getEventName(): ?string
    {
        return CommentEvent::NAME;
    }

}