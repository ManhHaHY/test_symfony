<?php

declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Model\Task;
use Symfony\Component\{Form\AbstractType,Form\FormBuilderInterface,OptionsResolver\OptionsResolver};

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('completed')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
                'data_class' => Task::class,
            ]
        );
    }
}
