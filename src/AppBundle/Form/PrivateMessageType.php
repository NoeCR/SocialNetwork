<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PrivateMessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['empty_data'];
        $builder
                ->add('receiver', EntityType::class, array(
                        'class' => 'BackendBundle:Users',
                        'query_builder' => function($er) use($user){                            
                            return $er->getFollowingUsers($user);
                        },
                        'choice_label' => function($user){
                            return $user->getName(). ' '. $user->getSurname() . ' - '. $user->getNick(); 
                        },
                        'label' => 'Para:',
                        'attr' => array('class' => 'form-controler')
                    ))
                ->add('message', TextareaType::class, array(
                        'label' => 'Mensaje',
                        'required' => 'required',
                        'attr' => array(
                            'class' => 'form-control'
                        )
                    ))
                ->add('image', FileType::class, array(
                        'label' => 'Imagen',
                        'required' => false,
                        'data_class' => null,
                        'attr' => array(
                            'class' => ''
                        )
                    ))
                ->add('file', FileType::class, array(
                        'label' => 'Archivo',
                        'required' => false,
                        'data_class' => null,
                        'attr' => array(
                            'class' => ''
                        )
                    ))                
                ->add('Enviar', SubmitType::class, array(
            "attr" => array(
                "class" => "form-submit btn btn-success"
            )
        ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BackendBundle\Entity\PrivateMessages'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'backendbundle_private_messages';
    }


}
