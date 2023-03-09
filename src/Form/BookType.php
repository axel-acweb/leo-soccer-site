<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Player;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('team_name', TextType::class, [
                "label" => 'Nom de votre équipe'
            ])
            ->add('players', CollectionType::class, [
                'entry_type'   => PlayerType::class,
                'label' => false,
                'by_reference' => false,
                'allow_add' => false,
                'allow_delete' => true,
                'entry_options' => [
                    "label" => false
                ],
                'attr' => [
                    'class' => 'order-items'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider"
            ]
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
