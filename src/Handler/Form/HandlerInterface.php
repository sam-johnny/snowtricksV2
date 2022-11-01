<?php

namespace App\Handler\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    /**
     * @param Request $request
     * @param object|null $data
     * @param array $options
     * @return bool
     */
    public function handle(Request $request, ?object $data, array $options = []): bool;

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface;

    /**
     * @return FormView
     */
    public function createView(): FormView;
}