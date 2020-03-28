<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('surname')
           ->add('roles',ChoiceType::class,[
                'choices' => [
                    'ADMIN' => 'ROLE_ADMIN',
                    'USER' => 'ROLE_USER'],
                ]
            )
            ->add('email')
           // ->add('roles')
            ->add('password', PasswordType::class,[
                'mapped' =>false,
               'constraints'=>[
                   new NotBlank([
                       'message' =>'Please enter a password',
                   ]),
                   new Length([
                       'min' => 6,
                       'minMessage' => 'Your password should be at least {{ limit }} characters',
                       'max' =>4096,
                   ]),
               ],
           ])
            ->add('image',FileType::class,[
                'label'=>'Image',
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
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'True' => 'True',
                    'False' => 'False',


                ],
            ])


        ;

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    //transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
