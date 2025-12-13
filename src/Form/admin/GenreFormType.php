<?php

declare(strict_types=1);

namespace App\Form\admin;

use App\Entity\Genre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GenreFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => 64,
                        'maxMessage' => 'The name cannot be longer than {{ limit }} characters',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a genre name',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Genre::class,
        ]);
    }
}
