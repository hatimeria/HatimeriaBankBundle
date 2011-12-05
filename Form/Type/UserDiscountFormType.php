<?php

namespace Hatimeria\BankBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add("discount");
    }

    public function getParent(array $options)
    {
        return 'form';
    }

    function getName()
    {
        return 'user_discount_form';
    }
}
