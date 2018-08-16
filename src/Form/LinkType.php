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
        //get Entity Manager
        $this->em = $options['entity_manager'];
        //Add keywords and submit fields
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

        //attach datatransformer
        $builder
            ->get('keywords')
            ->addModelTransformer(new StringToKeywordTransformer($this->em));

        //Add other fields based on the passed entity's class
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $link = $event->getData();
            $form = $event->getForm();

            //if entity is not inserted in database, displays only URL
            if (!$link || null === $link->getId()) {
                $form->add('url');
            //Entity exists, load the full form
            } else {
                $className = $this->em->getMetadataFactory()->getMetadataFor(get_class($link))->getName();
                $form->add('title')
                    ->add('author')
                    ->add('date')
                    ->add('width')
                    ->add('height');
                //Add duration for videos
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
