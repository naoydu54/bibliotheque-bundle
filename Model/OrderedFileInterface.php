<?php

namespace Ip\BibliothequeBundle\Model;

interface OrderedFileInterface{
    public function getPosition();

    public function setPosition($position);

    public function getFile();

    public function setFile(FileInterface $file);
}