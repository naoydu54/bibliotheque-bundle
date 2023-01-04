<?php

namespace Ip\BibliothequeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListFileCommand extends ContainerAwareCommand
{
    private $prefix = '|_ ';

    protected function configure()
    {
        $this
            ->setName('ip:bibliotheque:list')
            ->setDescription('Liste tout les fichiers en bibliotheque');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $folders = $this->getContainer()->get('ip_bibliotheque.folder_manager')->findFolders();

        $output->writeln('');
        $prefix = '';
        $output->writeln($prefix.'Accueil');
        $parentlvl = 0;
        foreach ($folders as $folder) {
            $output->writeln($this->readFolder($folder, $parentlvl));
        }
    }

    private function readFolder($folder, $parentlvl)
    {
        if ($parentlvl > 2) {
            return str_repeat('&nbsp;', $parentlvl).$folder->getName();
        }

        return $this->readFolder($folder, $parentlvl + 2);
    }
}