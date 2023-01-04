<?php

namespace Ip\BibliothequeBundle\Service;

class IpBblView extends \Twig_Extension
{
    private $templating;
    private $prefix;

    public function __construct(\Twig_Environment $templating, $prefix)
    {
        $this->templating = $templating;
        $this->prefix = $prefix;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('showBbl', [$this, 'showBbl'], array(
                'is_safe' => array(
                    'html'
                ))
            )
        ];
    }

    public function showBbl($multiple = false)
    {
        return $this->templating->render('@IpBibliotheque/Bibliotheque/bibliotheque.html.twig',array(
            'prefix' => $this->prefix,
            'multiple' => $multiple
        ));
    }
}