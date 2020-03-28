<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Image1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('image',FileType::class,[
                'label'=>'Blog Main Image',
                'mapped' =>false,
                'required'=>false,
                'constraints'=>[
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize'=>'1024k',
                        'mimeTypes'=>[
                            'image/*',
                        ],
                        'mimeTypesMessage'=>'Please upload a valid Image File'
                    ])
                ],
            ])
            ->add('blog',EntityType::class,[
                'class'=>Blog::class,
                'choice_label'=> 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'csrf_protection'=>false,
        ]);
    }
}
