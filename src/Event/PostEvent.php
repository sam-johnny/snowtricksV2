<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 19/10/2022
 * Time: 21:26
 */

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PostEvent extends Event
{
    const NAME = "app.event.post";

    public function __construct(private object $data){}

    /**
     * @return object
     */
    public function getData(): object
    {
        return $this->data;
    }
}