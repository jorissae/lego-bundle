<?php
namespace Idk\LegoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Idk\LegoBundle\Entity\AttachableFile;


class AttachementManager{

    private $em;
    private $container;
    private $repoFile;
    private $repoFolder;
    private $item;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->repoFile = $this->em->getRepository('LleAdminListBundle:AttachableFile');
        $this->repoFolder = $this->em->getRepository('LleAdminListBundle:AttachableFolder');
    }

    public function getDir(){
        return $this->container->get('kernel')->getRootDir().'/../web/uploads/LleAttachable';
    }

    public function setItem($item){
        $this->item = $item;
    }

    public function get($options){
        return $this->repoFile->get($this->item,$options);
    }

    public static function GenMiniaturPdf($pdfGenerate,$imgPath){
        if(!file_exists($imgPath)){
            $root = dirname($imgPath).'/'.basename($imgPath,'.png');
            exec('pdftoppm -scale-to 142 -singlefile -png "' .$pdfGenerate. '" ' .$root);
        }
    }

    public function getPdfPathThumb(AttachableFile $file){
         return $this->getDir().'/thumbs/'.md5($file->getFichier()).'.png';
    }

    public function miniaturiser(AttachableFile $file){
        if($file->isType('pdf')){   
            self::GenMiniaturPdf($file->getRealPath(),$this->getPdfPathThumb($file));
            return $this->getPdfPathThumb($file);
        }
    }



}

        

        
        
        
        