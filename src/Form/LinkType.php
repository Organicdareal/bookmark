<?php

namespace App\Form;

use App\Form\DataTransformer\StringToKeywordTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LinkType extends AbstractType
{
    private $em;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->em = $options['entity_manager'];
        $builder
            ->add('keywords', CollectionType::class, array(
                'label' => " ",
                'entry_type' => KeywordType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'OK',
                'attr' => array('class' => 'btn btn-success')
            ));

        $builder
            ->get('keywords')
            ->addModelTransformer(new StringToKeywordTransformer($this->em));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $link = $event->getData();
            $form = $event->getForm();

            if (!$link || null === $link->getId()) {
                $form->add('url');
            } else {
                $className = $this->em->getMetadataFactory()->getMetadataFor(get_class($link))->getName();
                $form->add('title')
                    ->add('author')
                    ->add('date')
                    ->add('width')
                    ->add('height');
                if ($className == "App\\Entity\\Video"){
                    $form->add('duration');
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('entity_manager');
    }
}
