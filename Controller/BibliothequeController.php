<?php

namespace Ip\BibliothequeBundle\Controller;

use Ip\BibliothequeBundle\Doctrine\FileManager;
use Ip\BibliothequeBundle\Doctrine\FolderManager;
use Ip\BibliothequeBundle\Model\File;
use Ip\BibliothequeBundle\Model\FileInterface;
use Ip\BibliothequeBundle\Model\Folder;
use Ip\BibliothequeBundle\Model\FolderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BibliothequeController extends Controller
{
    public function foldersAction(Request $request)
    {
        $folderManager = $this->get('ip_bibliotheque.folder_manager');

        /** @var Folder[] $folders */
        $folders = $folderManager->findFolders();

        $json = [];
        $json[] = [
            'id' => 0,
            'type' => 'folder',
            'name' => 'Accueil',
            'parent' => null,
            'active' => true,
            'open' => true,
        ];
        foreach ($folders as $folder) {
            $jsonFiles = [];

            foreach ($folder->getFiles() as $file) {
                /** @var File $file */
                $jsonFiles[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'url' => $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().$this->getParameter('ip_bibliotheque.root_folder').'/'.$file->getUrl(),
                ];
            }

            if ($folder->getParent() instanceof FolderInterface) {
                $parent = $folder->getParent()->getId();
            } else {
                $parent = $folder->getParent();
            }

            $json[] = [
                'id' => $folder->getId(),
                'type' => 'folder',
                'name' => $folder->getName(),
                'parent' => $parent,
                'active' => false,
                'open' => false,
                'files' => $jsonFiles,
            ];
        }

        return new JsonResponse($json);
    }

    public function addAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            switch ($request->get('type')) {
                case 'folder':
                    if ($request->get('id_parent') == 0) {
                        $parent = null;
                    } else {
                        $parent = $this->findFolderBy('id', $request->get('id_parent'));
                    }
                    /** @var FolderManager $folderManager */
                    $folderManager = $this->get('ip_bibliotheque.folder_manager');
                    $folder = $folderManager->createFolder();
                    $folder->setName($request->get('name'));
                    $folder->setLock(false);
                    if (!is_null($parent)) {
                        $folder->setParent($parent);
                    }
                    $folderManager->updateFolder($folder);
                    $json['result'] = "success";
                    $json['reponse'] = "Le dossier a bien été créé";
                    $json['id'] = $folder->getId();
                    break;
                default:
                    $baseurl = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().$this->getParameter('ip_bibliotheque.root_folder').'/';
                    /** @var UploadedFile $fileUpload */
                    $fileUpload = $request->files->get('file');
                    /** @var FolderInterface $folder */
                    $folder = $this->findFolderBy('id', $request->get('id_maitre'));
                    /** @var FileManager $fileManager */
                    $fileManager = $this->get('ip_bibliotheque.file_manager');
                    /** @var FileInterface $file */
                    $file = $fileManager->createFile();
                    $fileName = $this->get('ip_bibliotheque.file_uploader')->upload($fileUpload);
                    $file->setName($fileUpload->getClientOriginalName());
                    $file->setUrl($fileName);
                    $file->setFolder($folder);
                    $fileManager->updateFile($file);
                    $json['result'] = "success";
                    $json['file']['id'] = $file->getId();
                    $json['file']['name'] = $file->getBasename();
                    $json['file']['url'] = $baseurl.'/'.$file->getUrl();
                    $json['file']['parent'] = $folder->getId();
                    $json['file']['nameretour'] = $fileName;
                    break;
            }
        } else {
            $json['result'] = "fail";
            $json['reponse'] = "L'upload ne s'est pas bien déroulé";
        }

        return new JsonResponse($json);
    }

    public function renameElementAction(Request $request)
    {
        $json = [];
        switch ($request->get('type')) {
            case File::TYPE:
                $file = $this->findFileBy('id', $request->get('id'));
                /** @var FileManager $fileManager */
                $fileManager = $this->get('ip_bibliotheque.file_manager');
                $file->setName($request->get('name'));
                $fileManager->updateFile($file);
                break;
            case Folder::TYPE:
                $folder = $this->findFolderBy('id', $request->get('id'));
                /** @var FolderManager $folderManager */
                $folderManager = $this->get('ip_bibliotheque.folder_manager');
                $folder->setName($request->get('name'));
                $folderManager->updateFolder($folder);
                $json = [
                    'success' => true,
                    'message' => "Le dossier a été correctement renommé",
                    'element' => $folder->getId(),
                ];
                break;
            default:
                $json = [
                    'success' => false,
                    'message' => "Impossible de trouver un element avec le type ".$request->get('type'),
                ];
                break;
        }

        return new JsonResponse($json);
    }

    public function moveElementAction(Request $request)
    {
        $json = [];
        switch ($request->get('type')) {
            case File::TYPE:
                $file = $this->findFileBy('id', $request->get('fileid'));
                $folder = $this->findFolderBy('id', $request->get('folderid'));
                /** @var FileManager $fileManager */
                $fileManager = $this->get('ip_bibliotheque.file_manager');
                $file->setFolder($folder);
                $fileManager->updateFile($file);
                $json = [
                    'success' => true,
                    'message' => "Le fichier a bien été déplacé",
                    'element' => $folder->getId(),
                ];
                break;
            case Folder::TYPE:
                $folder = $this->findFolderBy('id', $request->get('id'));
                /** @var FolderManager $folderManager */
                $folderManager = $this->get('ip_bibliotheque.folder_manager');
                $folder->setName($request->get('name'));
                $folderManager->updateFolder($folder);
                $json = [
                    'success' => true,
                    'message' => "Le dossier a été correctement renommé",
                    'element' => $folder->getId(),
                ];
                break;
            default:
                $json = [
                    'success' => false,
                    'message' => "Impossible de trouver un element avec le type ".$request->get('type'),
                ];
                break;
        }

        return new JsonResponse($json);
    }

    public function deleteElementAction(Request $request)
    {
        $json = [];
        switch ($request->get('type')) {
            case File::TYPE:
                $file = $this->findFileBy('id', $request->get('id'));
                /** @var FileManager $fileManager */
                $fileManager = $this->get('ip_bibliotheque.file_manager');
                $fileManager->removeFile($file);
                $json = [
                    'success' => true,
                ];
                break;
            default:
                $json = [

                ];
                break;
        }

        return new JsonResponse($json);
    }

    /**
     * @param $key
     * @param $value
     * @return FileInterface
     */
    protected function findFileBy($key, $value)
    {
        if (!empty($value)) {
            $file = $this->get('ip_bibliotheque.file_manager')->{'findFileBy'.ucfirst($key)}($value);
        }

        if (empty($file)) {
            throw new NotFoundHttpException(sprintf('The file with "%s" does not exist for value "%s"', $key, $value));
        }

        return $file;
    }

    /**
     * @param $key
     * @param $value
     * @return FolderInterface
     */
    protected function findFolderBy($key, $value)
    {
        if (!empty($value)) {
            $folder = $this->get('ip_bibliotheque.folder_manager')->{'findFolderBy'.ucfirst($key)}($value);
        }

        if (empty($folder)) {
            throw new NotFoundHttpException(sprintf('The folder with "%s" does not exist for value "%s"', $key, $value));
        }

        return $folder;
    }

    public function pixlrBackAction(Request $request, $idfolder, $idfile)
    {

        $folder = null;
        if (is_null($idfolder)) {
            return new Response("Folder must not be null");
        }
        $folder = $this->findFolderBy('id', $idfolder);

        if (is_null($folder)) {
            return new Response("Impossible de trouver le dossier");
        }

        $state = $request->get('state');
        $image = $request->get('image');
        $name = $request->get('title');

        switch ($state) {
            case 'new':
                $fileName = $this->get('ip_bibliotheque.file_downloader')->download($image);
                /** @var FileManager $fileManager */
                $fileManager = $this->get('ip_bibliotheque.file_manager');
                $file = $fileManager->createFile();
                $file->setName($name);
                $file->setUrl($fileName);
                $file->setFolder($folder);
                $fileManager->updateFile($file);

                return new Response('File saved');
                break;
            case 'replace':
                if (is_null($idfile)) {
                    return new Response('File must not be null');
                }
                $file = $this->findFileBy('id', $idfile);
                if (is_null($file)) {
                    return new Response('File not found');
                }
                $this->get('ip_bibliotheque.file_downloader')->download($image, $file->getUrl());
                /** @var FileManager $fileManager */
                $fileManager = $this->get('ip_bibliotheque.file_manager');
                $file->setName($name);
                $fileManager->updateFile($file);

                return new Response('File saved');
                break;
        }

        return new Response('State not found');
    }//http://www.api.info-plus.fr/web/app_dev.php/pixelr/back/1/6?image=http://apps.pixlr.com/_temp/59a98403471e3cfeab001c56.jpg&type=jpg&title=aKoala&state=replace
}
