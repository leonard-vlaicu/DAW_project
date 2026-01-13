<?php

declare(strict_types=1);

namespace App\Form\admin;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthorFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Length(max: 64, maxMessage: 'The first name cannot be longer than {{ limit }} characters'),
                    new NotBlank(message: 'Please enter a first name')
                ]
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Length(max: 64, maxMessage: 'The last name cannot be longer than {{ limit }} characters'),
                    new NotBlank(message: 'Please enter a last name')
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
