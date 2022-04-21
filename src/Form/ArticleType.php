<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // l'objet $builder permet de construire un formulaire
        // add() est une méthode permettant d'ajouter un champ au formulaire

        $builder
            ->add('title')
            ->add('content')
            ->add('imageFile', FileType::class, ['required' => false])
            // ->add('createdAt')
            // on commente ce champ car il ne doit pas être rempli par l'utilisateur
            ->add('category', EntityType::class, [  // je précise que le champ 'category' est une entity
                'class' => Category::class, // j'indique le nom de l'entity
                'choice_label' => 'title'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'attr' => [
                'novalidate' => 'novalidate'    // permet de désactiver la validation html pour ce formulaire
            ]
        ]);
    }
}
