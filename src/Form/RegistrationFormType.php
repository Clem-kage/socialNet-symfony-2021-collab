<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentYear = (int) date("Y");

        $builder
            // On laisse null ici pour laisser le framework choisir le type de widget HTML à utiliser
            ->add('username', null, [
                'label' => 'Choisi ton pseudo',
                'help' => 'Personne ne peut avoir le même, tu es unique !!'
            ])
            ->add('fullName')

            ->add('picture', FileType::class, [
                'label' => 'Photo de profil',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false
                ])

            ->add('birthdate', DateType::class, [
                'label' => 'Ton anniversaire',
                // on autorise des gens majeurs (+18 ans) et de moins de 120 ans !
                'years' => range($currentYear - 18, $currentYear - 18 - 120)
            ])
            ->add('email')
            ->add('phone')
            ->add('plainPassword', PasswordType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 's3cr3T'
                ],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}