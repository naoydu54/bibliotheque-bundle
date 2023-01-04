<?php

namespace Ip\BibliothequeBundle\Service;

use Doctrine\ORM\EntityManager;
use Ip\BibliothequeBundle\Model\File;

class IpBblImageView extends \Twig_Extension
{
    private $em;
    private $fileClass;
    private $uploadFolder;
    private $defaultImage;

    public function __construct(EntityManager $entityManager, $fileClass, $uploadFolder, $defaultImage)
    {
        $this->em = $entityManager;
        $this->fileClass = $fileClass;
        $this->uploadFolder = $uploadFolder;
        $this->defaultImage = $defaultImage;
    }

    public function getFilters()
    {
        return [];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ipBblImageView', [$this, 'ipBblImageView'],
                array('is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('ipBblImageUrl', [$this, 'ipBblImageUrl']),
        ];
    }

    public function ipBblImageView($idimage, $classes = [])
    {
        /** @var File $image */
        $image = $this->em->getRepository($this->fileClass)->findOneById($idimage);
        if (is_null($image)) {
            return '<img src="'.$this->defaultImage.'" alt="Default" class="img-responsive">';
        }
        $c = "";
        foreach ($classes as $classe) {
            $c .= " ".$classe;
        }
        trim($c);

        return '<img src="/web'.$this->uploadFolder.'/'.$image->getUrl().'" alt="'.$image->getName().'" class="'.$c.'">';
    }

    public function ipBblImageUrl(File $image = null)
    {
        if (is_null($image)) {
            return $this->defaultImage;
        }

        return $this->uploadFolder.'/'.$image->getUrl();
    }

    public function getName()
    {
        return 'ip_bibliotheque_image_view';
    }
}