<?php

namespace App\Form;

use App\Entity\Search;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class,
                [
                    'class' => Site::class,
                    'choice_label' => 'nom',
                    'label' => 'Site :         '
                ])
            ->add('nom', \Symfony\Component\Form\Extension\Core\Type\SearchType::class, [
                    'label' => 'Le nom de la sortie contient :   ',
                    'required' => false
            ])

            ->add ('date',DateType::class, [
                'format' => 'dd-MM-yyyy',
                'required' => false
            ])

            ->add ('dateFin',DateType::class, [
                'format' => 'dd-MM-yyyy',
                'required' => false
            ])

            ->add ('mesSorties',CheckboxType::class, [
                'required' => false
            ])

            ->add ('registered',CheckboxType::class, [
                'required' => false
            ])

            ->add ('unregistered',CheckboxType::class, [
                'required' => false
            ])

            ->add ('last',CheckboxType::class, [
                'required' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
        ]);
    }
}
