<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 19/10/2022
 * Time: 21:26
 */

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RegisterEvent extends Event
{
    const NAME = "app.event.register";

    public function __construct(private object $data, private mixed $form){}

    /**
     * @return object
     */
    public function getData(): object
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getForm(): mixed
    {
        return $this->form;
    }


}