<?php

declare(strict_types=1);

namespace App\Form\admin;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class BookFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new Length(max: 255, maxMessage: 'The title cannot be longer than {{ limit }} characters')
                ],
                'required' => true,
            ])
            ->add('year', IntegerType::class, [
                'required' => false,
            ])
            ->add('pages', IntegerType::class, [
                'required' => false,
            ])
            ->add('isbn', TextType::class, [
                'constraints' => [
                    new Length(min: 13, max: 13, exactMessage: 'The ISBN must be exactly 13 characters long')
                ]
            ])
            ->add('copies', IntegerType::class, [
                'required' => true,
            ])
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'required' => false,
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => function (Author $author) {
                    return $author->getFirstName() . ' ' . $author->getLastName();
                },
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
