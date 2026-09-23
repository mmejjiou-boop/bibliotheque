<?php

namespace App\Form;

use App\Entity\Emprunt;
use App\Entity\Lecteur;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateEmprunt')
            ->add('dateRetourPrevue')
            ->add('dateRetourEffective')
            ->add('livre', EntityType::class, [
                'class' => Livre::class,
                'choice_label' => 'titre',
            ])
            ->add('lecteur', EntityType::class, [
                'class' => Lecteur::class,
                'choice_label' => fn (Lecteur $lecteur) => $lecteur->getNom() . ' ' . $lecteur->getPrenom(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emprunt::class,
        ]);
    }
}
