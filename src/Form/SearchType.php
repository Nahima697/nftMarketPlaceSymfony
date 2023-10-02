<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Nft;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchBar', TelType::class, [
                'label' => 'Recherche textuelle',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'label' => 'Recherche par catégorie',
                'attr' => ['class' => 'form-select'],
                'class' => Category::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->where('c.parent IS NULL');
                },
            ])
            ->add('categoryChild', EntityType::class, [
                'label' => 'Recherche par sous-catégorie',
                'attr' => ['class' => 'form-select'],
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->where('c.parent IS NOT NULL');
                },
                'required' => false,
            ])
            ->add('userAjout', EntityType::class, [
                'class' => User::class,
                'attr' => ['class' => 'form-select'],
                'required' => false,
                'label' => "Recherche par nom d'utilisateur"
            ])
            ->add('valueMin', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => "Prix Minimum"
            ])
            ->add('valueMax', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => "Prix Maximum"
            ])
            ->add('valider', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success']
            ]);

        $formModifier = function (FormInterface $form, Category $category = null) {
            $form->add('categoryChild', EntityType::class, [
                'label' => 'Recherche par sous-catégorie',
                'attr' => ['class' => 'form-select'],
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) use ($category) {
                    $qb = $er->createQueryBuilder('c')
                        ->where('c.parent = :parent')
                        ->setParameter('parent', $category);

                    return $qb;
                },
                'required' => false,
            ]);
        };

        $builder->get('category')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {
            $category = $event->getForm()->getData();
            $form = $event->getForm()->getParent();

            $formModifier($form, $category);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
