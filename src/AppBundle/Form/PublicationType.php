<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PublicationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('text', TextareaType::class, array(
                        'label' => 'Mensaje',
                        'required' => 'required',
                        'attr' => array(
                            'class' => 'form-control'
                        )
                    ))
                ->add('image', FileType::class, array(
                        'label' => 'Foto',
                        'required' => false,
                        'data_class' => null,
                        'attr' => array(
                            'class' => ''
                        )
                    ))
                ->add('document', FileType::class, array(
                        'label' => 'Documento',
                        'required' => false,
                        'data_class' => null,
                        'attr' => array(
                            'class' => ''
                        )
                    ))
                ->add('Publicar', SubmitType::class, array(
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
            'data_class' => 'BackendBundle\Entity\Publications'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'backendbundle_users';
    }


}
