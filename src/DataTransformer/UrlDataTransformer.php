<?php
declare(strict_types=1);

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class UrlDataTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): void
    {}

    public function reverseTransform(mixed $value): string
    {
        if ($value === null){
            return '';
        }

        $regex = "/=([\w-]*)/";
        preg_match($regex, $value, $matches);
        return "https://www.youtube.com/embed/" . $matches[1];
    }

}