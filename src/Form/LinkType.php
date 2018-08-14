<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url')
            ->add('keywords', CollectionType::class, array(
                'label' => " ",
                'entry_type' => KeywordType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-success')
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
