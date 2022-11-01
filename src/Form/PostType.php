<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageBanner', ImageBannerType::class, [
                'required' => false,
                'label' => 'Bannière:'
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre:',
                'empty_data' => ''
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu:',
                'empty_data' => ''
            ])
            ->add('imageFiles',FileType::class, [
                'required' => false,
                'multiple' => true,
                'label' => 'Ajouter une image:'
            ])
            ->add('urlsMedia', UrlFormattedType::class, [
                'label' => 'Ajouter un lien youtube:'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie:',
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
