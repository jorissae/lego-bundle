<?php

namespace Idk\LegoBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Sensio\Bundle\GeneratorBundle\Command\AutoComplete\EntitiesAutoCompleter;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Idk\LegoBundle\Generator\LegoGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Idk\LegoBundle\Entity\AttachableFile;

/**
 * Generates a LleAdminList
 */
class SaveAttachementCommand extends ContainerAwareCommand
{

    private $fileManager;
    private $logs = array();
    private $entity;
    private $em;
    private $className;
    private $metaClass;
    private $i = null;
    private $nbError = 0;
    private $nbNew = 0;
    private $nbNoAction = 0;
    private $deleteError = false;


    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName('lle:attachement:sync')->setDescription('Syncronise la bdd et le dossier lle_attachement')
        ->addOption('entity',null, InputOption::VALUE_REQUIRED,'Entite a syncroniser LleAdminList:Entity')
        ->addOption('delete-error',false, InputOption::VALUE_NONE,'Supprime les fichier avec erreur');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws \RuntimeException
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $this->em =  $this->getContainer()->get('doctrine')->getManager();
        $this->fileManager = $container->get('lle_file_manager');
        $this->fileManager->setIgnore(array('..','.','.gitkeep'));
        $this->className = $input->getOption('entity');
        $this->deleteError = $input->getOption('delete-error');
        $this->metaClass = $this->em->getClassMetadata($this->className);
        $this->entity = $this->metaClass->newInstance();
        $dir = str_replace('\\','_',$this->metaClass->getName());
        $this->section('SYNCRO DEBUT');
        $this->syncDir('web/uploads/LleAttachable/'.$dir);
        $this->em->flush();
        $this->setIndent();
        $this->section('FLUSH FAIT');
        $this->section('SYNCRO FIN');
        $this->section('LOGS');
        $this->log('Erreur : '. $this->nbError);
        $this->log('Fichier sans action : '. $this->nbNoAction);
        $this->log('Fichier persisté : '. $this->nbNew);
        $this->printLog();

    }

    private function syncDir($path){
        $this->setIndent($path);
        $this->log('Directory '.$path);
        $files = $this->fileManager->ls($path);
        foreach($files as $file){
            if($file['dir']){
                $this->syncDir($file['path']);
            }else{
                $this->log('-'.$file['name']);
                $this->syncFile($file);
            }
        }
    }

    private function syncFile($file){
        $item = $this->entity;
        $this->setIndent($file['path']);
        $this->log('Traitement de '. $file['name']);
        if(method_exists($item,'getAttachableDirectory')){
            if(method_exists($item,'getAttachableData')){
                $data = $item->getAttachableData($file['path'],$file['name']);
            }else{
                throw new \Exception('Impossible de trouvé data[] crée une methode static '.get_class($this->entity).'::getAttachableData($path,$filename) qui retourne un array("zone","id"). ex: $path = '.$file['path']);
            }
        }else{
            $data = $this->getData($file);
        }
        if($data['zone']) $this->log('zone: '. $data['zone']);
        $this->log('id: '. $data['id']);
        $objAttach = $this->em->getRepository($this->className)->find($data['id']);
        if($objAttach){
            $zones = array();
            if(!is_array($objAttach->getAttachableZones())){
                foreach($objAttach->getAttachableZones() as $zone){
                    $zones[$zone->getCode()] = $zone->getLibelle();
                }
            }else{
                $zones = $objAttach->getAttachableZones();
            }
            if(($data['zone'] && in_array($data['zone'],array_keys($zones))) or !$data['zone']){
                $this->generateAttachement($objAttach,$data['zone'],$file);
            }else{
                $this->error($file,'Erreur: zone '.$data['zone'].' invalide pour objet '.$this->className.':'.$data['id']);
            }
        }else{
            $this->error($file,'Erreur: objet '.$this->className.':#id:'.$data['id'].' introuvable.');
        }
    }

    public function error($file,$msg){
        $this->nbError++;
        if($this->deleteError){
            unlink($file['real_path']);
            $msg.= ' (supprimé)';
        }
        $this->log($msg,true);
    }

    public function generateAttachement($item,$zone,$f){
        $file = new AttachableFile();
        if($zone) $file->setZoneCode($zone);

        $mtype = explode('/', $f['mime_type']);
        $type = $mtype[0];
        $subType = (isset($mtype[1]))? $mtype[1]:null;
        $file->setType($type);
        $file->setSubType($subType);
        $file->setNom($f['name']);
        $file->setTaille($f['size']);
        $file->setClass($this->className);
        $file->setRealClass($this->metaClass->getName());
        $file->setItemId($item->getId());
        if(method_exists($item,'getAttachableNameFile')){
            $file->setFichier($item->getAttachableNameFile($f['name']));
        }else{
            $file->setFichier(md5(time()).md5($f['name']).substr($f['name'], strrpos($f['name'], '.')));
        }
        if(method_exists($item,'getAttachableDirectory')){
            $file->setPath($item->getAttachableDirectory($file));
        }
        $attachement = $this->em->getRepository('LleAdminListBundle:AttachableFile')->isExist($file);
        if($attachement === true){
            $this->log('Existent: aucune action !');
            $this->nbNoAction++;
        }elseif($attachement === false){
            $this->em->persist($file);
            $this->log('Nouveau: persisté !');
            $this->nbNew++;
        }
    }

    public function getData($file){
        $item = $this->entity;
        if(method_exists($item,'getAttachableZones')){
            list($web,$uploads,$attach,$class,$zone,$id) = explode('/',$file['path']);
        } else {
            list($web,$uploads,$attach,$class,$id) = explode('/',$file['path']);
            $zone = null;
        }
        return array('id'=>$id,'zone'=>$zone);
    }

    public function log($txt,$save = 0){
        if($save){
            $this->logs[] = $txt;
        }
        echo $this->i.$txt."\n";
    }

    public function section($section){
        $this->log('---------------');
        $this->log($section);
        $this->log('---------------');
    }

    public function setIndent($path=''){
        $indent = null;
        for($i=0;$i <= substr_count($path,'/');$i++)$indent .= ' ';
        $this->i = $indent;
    }

    public function printLog(){
        if(count($this->logs)){
            $display = array();
            $this->section('DISTINCT ERRORS');
            foreach($this->logs as $log){
                if(!in_array($log,$display)){
                    $display[] = $log;
                    $this->log($log);
                }
            }
        }else{
            $this->log('SYNCRO OK ;-)');
        }

    }
}
