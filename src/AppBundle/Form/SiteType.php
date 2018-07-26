<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SiteType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tNumber', TextType::class, array('required' => false))
                ->add('siteName', TextType::class, array('required' => false))
                ->add('siteType', null, array('required' => false))
                ->add('latitude', null, array('required' => false))
                ->add('longitude', null, array('required' => false))
                ->add('siteCity', TextType::class, array('required' => false))
                
                ->add('equipments', CollectionType::class, array(
                    'entry_type' => EquipmentType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Site'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_site';
    }

}
