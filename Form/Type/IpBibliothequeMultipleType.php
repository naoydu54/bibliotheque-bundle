<?php

namespace Ip\BibliothequeBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IpBibliothequeMultipleType extends AbstractType
{
    /** @var string */
    protected $prefix;
    /** @var string */
    protected $fileClass;

    /**
     * IpBibliothequeMultipleType constructor.
     *
     * @param string $prefix
     * @param string $fileClass
     */
    public function __construct($prefix, $fileClass)
    {
        $this->prefix = $prefix;
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
            ->add('position', IntegerType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'prefix' => $this->prefix
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'prefix' => null,
            'compound' => true
        ));
    }

    public function getParent()
    {
        return CollectionType::class;
    }

}