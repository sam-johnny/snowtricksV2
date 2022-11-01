<?php
declare(strict_types=1);

namespace App\Form;

use App\DataTransformer\UrlDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UrlFormattedType extends AbstractType
{
    public function __construct(private UrlDataTransformer $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'invalid_message' => 'message error',
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}