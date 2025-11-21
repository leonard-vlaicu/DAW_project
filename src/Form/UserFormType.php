<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('firstName', TextType::class, [
                'trim' => true,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Your first name cannot be longer than {{ limit }} characters',
                    ])
                ]
            ])
            ->add('lastName', TextType::class, [
                'trim' => true,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Your last name cannot be longer than {{ limit }} characters',
                    ])
                ]
            ])
            ->add('phoneNumber', TelType::class, [
                'trim' => true,
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'required' => false,
                'disabled' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
