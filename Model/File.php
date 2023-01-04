<?php

namespace Ip\BibliothequeBundle\Model;

use Doctrine\ORM\Mapping as ORM;

abstract class File implements FileInterface
{
    const TYPE = 'file';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Folder
     */
    protected $folder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    protected $url;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getFolder()
    {
        return $this->folder;
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    public function getBasename()
    {
        return pathinfo($this->getName(), PATHINFO_FILENAME);
    }
}