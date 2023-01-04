<?php

namespace Ip\BibliothequeBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderedFileType extends AbstractType
{
    protected $fileClass;

    public function __construct($fileClass)
    {
        $this->fileClass = $fileClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', EntityType::class, [
                'class' => $this->fileClass,
                'choice_label' => 'name'
            ])
            ->add('position', IntegerType::class)
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_multitest';
    }
}