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
                'placeholder' => 'Ingrese el codigo del producto',
                'novalidate' => 'novalidate'
            ],
        ])
        ->add('name', TextType::class,[
            'label' => 'Nombre',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Ingrese el nombre del producto',
                'novalidate' => 'novalidate',
            ],
        ])
        ->add('description', TextareaType::class,[
            'label' => 'Descripción',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Ingrese la descripción del producto',
            ],
        ])
        ->add('brand', TextType::class,
        [
            'label' => 'Marca',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Ingrese la marca del producto',
            ],
        ])
        ->add('price',  IntegerType::class, [
            'label' => 'Precio',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Ingrese el precio del producto',
            ],
        ])
        ->add('createdAt', DateType::class, array(
            'label' => 'Fecha de creación', 
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Ingrese la fecha de creación del producto',
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
