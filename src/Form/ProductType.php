<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('code', TextType::class,[
            'label' => 'Codigo',
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('description', TextareaType::class,[
            'label' => 'Descripción',
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('brand', TextType::class,
        [
            'label' => 'Marca',
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('price',  IntegerType::class, [
            'label' => 'Precio',
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('createdAt', DateType::class, array(
            'label' => 'Fecha de creación', 
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control',
            ],
        ))
      //traer categorias 
     ->add('category', EntityType::class, [
        'class' => Category::class,
        'choice_label' => 'name',
        'choice_value' => 'id',
        'attr' => [
            'class' => 'form-control',
        ],
     ])
        ->add('submit', SubmitType::class, [
            'label' => 'Guardar',
            'attr' => [
                'class' => 'btn btn-primary mt-3',
            ]
        ])

            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
