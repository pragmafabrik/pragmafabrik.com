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
            ->add('name', 'text', array('constraints' => new Constraints\NotBlank(array('message' => 'contact.name.not_blank'))))
            ->add('company', 'text')
            ->add('email', 'text', array('constraints' => array(new Constraints\NotBlank(array('message' => 'contact.email.not_blank')), new Constraints\Email(array('message' => 'contact.email.invalid')))))
            ->add('message', 'textarea', array('constraints' => new Constraints\NotBlank(array('message' => 'contact.message.not_blank'))));
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
