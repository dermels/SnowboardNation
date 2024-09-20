<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Illustration;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IllustrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', FileType::class, [
                'label' => false,
                'attr' => [
                    'type' => 'file',
                    'class' => 'filepond', // si vous souhaitez sélectionner l'élément par sa classe
                    'accept' => 'image/jpeg, image/png', // si vous voulez restreindre les types de fichiers autorisés
                ],
                'required' => false,
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Illustration::class,
        ]);
    }
}
