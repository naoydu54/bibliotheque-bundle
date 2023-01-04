<?php

namespace Ip\BibliothequeBundle\Service;

class IpBblDownload
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function download($url, $name = "")
    {
        if($name == ""){
            $name = basename (parse_url($url)['path']);
        }

        file_put_contents($this->targetDir . '/' . $name, file_get_contents($url));

        return $name;
    }
}