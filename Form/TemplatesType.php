<?php

namespace killoblanco\TemplateManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplatesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('base')
            ->add('html')
            ->add('active')
            ->add('thumbnail')
            ->add('type');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'killoblanco\TemplateManagerBundle\Entity\Template'
        ]);
    }

    public function getName()
    {
        return 'tm_template';
    }
}
