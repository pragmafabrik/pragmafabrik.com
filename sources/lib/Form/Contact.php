<?php // sources/lib/Form/Contact.php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class Contact extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('constraints' => new Constraints\NotBlank(array('message' => 'Champs obligatoire'))))
            ->add('company', 'text')
            ->add('email', 'text', array('constraints' => array(new Constraints\NotBlank(array('message' => 'Champs obligatoire')), new Constraints\Email(array('message' => 'Email invalide')))))
            ->add('message', 'textarea', array('constraints' => new Constraints\NotBlank(array('message' => 'Champs obligatoire'))));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_pf_token',
            'intention'       => 'uietdvmlqzergjk'
        ));
    }

    public function getName()
    {
        return 'contact';
    }
}
