<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category',EntityType::class,[
                'class'=>Category::class,
                'choice_label'=> 'title',
            ])
            ->add('title')
            ->add('keyword')
            ->add('description')
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
            ->add('details', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    //....
                ),

            ))
            ->add('status',ChoiceType::class,[
                'choices' => [
                    'True' => 'True',
                    'False' => 'False'],
            ])
            ->add('created_at')
            ->add('update_at')


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
