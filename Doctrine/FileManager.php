<?php

namespace Ip\BibliothequeBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Ip\BibliothequeBundle\Model\FileInterface;
use Ip\BibliothequeBundle\Model\FileManager as BaseFileManager;

class FileManager extends BaseFileManager
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
     * FileManager constructor.
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
    public function findFileBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findFiles()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile(FileInterface $file, $andFlush = true)
    {
        $this->em->persist($file);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param FileInterface $file
     * @param bool $andFlush
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeFile(FileInterface $file, $andFlush = true)
    {
        $this->em->remove($file);
        if ($andFlush) {
            $this->em->flush();
        }
    }
}