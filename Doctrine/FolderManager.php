<?php

namespace Ip\BibliothequeBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Ip\BibliothequeBundle\Model\FolderInterface;
use Ip\BibliothequeBundle\Model\FolderManager as BaseFolderManager;

class FolderManager extends BaseFolderManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * FolderManager constructor.
     * @param EntityManager $em
     * @param $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findFolders()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateFolder(FolderInterface $folder, $andFlush = true)
    {
        $this->em->persist($folder);
        if ($andFlush) {
            $this->em->flush();
        }
    }
}