<?php

namespace App\Handler\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var FormInterface
     */
    protected FormInterface $form;

    /**
     * @param object|null $data
     * @param null $form
     * @return object|null
     */
    abstract protected function getEvent(?object $data, $form): ?object;

    /**
     * @return string|null
     */
    abstract protected function getEventName(): ?string;

    /**
     * @return string
     */
    abstract protected function getFormType(): string;

    /**
     * @param $data
     * @return void
     */
    abstract protected function process($data): void;

    /**
     * @required
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @required
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     * @param object|null $data
     * @param array $options
     * @return bool
     */
    public function handle(Request $request, ?object $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)
            ->handleRequest($request);

            if ($this->form->isSubmitted() && $this->form->isValid()) {

            if ($this->getEventName() !== null) {
                $this->eventDispatcher->dispatch(
                    $this->getEvent($data, $this->form),
                    $this->getEventName(),
                );
            }
            $this->process($data);
            return true;
        }

        return false;
    }

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @inheritDoc
     */
    public function createView(): FormView
    {
        return $this->form->createView();
    }
}