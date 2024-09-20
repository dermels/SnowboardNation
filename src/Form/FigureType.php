<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('figureGroup', null, [
                'label' => 'Groupe de la figure',
            ])
            ->add('mainIllustration', IllustrationType::class, [
                'label' => 'illustarion principale',
                'required' => true,
            ])
            ->add('illustrations', CollectionType::class, [
                'entry_type' => IllustrationType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
//            ->add('hash', HiddenType::class, [
//                'label' => "TESTING",
//                'mapped' => false,
//                'required' => false,
//            ])
        ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
