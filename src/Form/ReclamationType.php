<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intitule')
            ->add('corps', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
                'attr' => ['row' => 30]
            ])
            ->add('priorite',  ChoiceType::class, [
                'choices' => [
                    'Moyenne' => "Moyenne",
                    'Urgente' => "Urgente",
                ],])
            ->add('etat',  ChoiceType::class, [
                'choices' => [
                    'En Attente' => "En Attente",
                    'En cours' => "En cours",
                    'Traité' => "Traité",
                ],])
            ->add('date', DateType::class, [
                'data' => new \DateTime(),
            ])
            ->add('file', FileType::class, [
                'label' => 'Pièce jointe',
                'required' => false,
                'data' => null,
//                'multiple' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF valide.',
                        'maxSizeMessage' => 'Le fichier est trop volumineux. Veuillez télécharger un fichier jusqu\'à {{ limit }} {{ suffix }}.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
