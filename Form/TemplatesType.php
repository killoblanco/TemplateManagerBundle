<?php

namespace killoblanco\TemplateManagerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplatesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('base')
            ->add('language', EntityType::class, [
                'placeholder' => 'Choose one',
                'class' => 'TemplateManagerBundle:Language',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.active = 1');
                },
            ])
            ->add('active')
            ->add('thumbnail', FileType::class, [
                'required' => false
            ])
            ->add('type', EntityType::class, [
                'placeholder' => 'Choose one',
                'class' => 'TemplateManagerBundle:Type',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->where('t.active = 1');
                },
            ]);
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
