<?php

namespace App\Form;

use App\Entity\User;
use App\Form\FormExtension\RepeatedPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Username',
                'required' => true,
                'attr' => [
                    'autofocus' => true
                ]
            ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "J'accepte les conditions d'utilisation de ce site.",
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => "vous devez accepter les conditions d'utilisation de ce site pour vous inscrire.",
                    ]),
                ],
            ])
            ->add('password', RepeatedPasswordType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
