<?php
namespace Lle\AdminListBundle\Service;


use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;


class LogManager
{

	private $em;

    public function __construct($em) {
       	$this->em = $em;
    }

    public function getLogs($finder= array(),$currentPage = 1,$page = 20){
        $em = $this->getEntityManager();
        $repolog = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        if($finder and count($finder)){
            $eLogs = $repolog->findBy($finder);
        }else{
            $eLogs = $repolog->findAll();
        }
        
        $pf = new Pagerfanta(new ArrayAdapter(array_reverse($eLogs)));
        $pf->setNormalizeOutOfRangePages(true);
        $pf->setCurrentPage($currentPage);
        $pf->setMaxPerPage($page);

        $logs = array();
        foreach($pf->getCurrentPageResults() as $log){
            $repo = $em->getRepository($log->getObjectClass());
            $data = array();
            if ($log->getData()) {
                foreach($log->getData() as $k => $entry){
                    if($entry instanceof \DateTime){
                        $retour = $entry->format('d/m/Y');
                    }else if(is_object($entry)){
                        $retour = (method_exists($entry,'__toString'))? $entry->toString():$entry->getId();
                    }else if(is_array($entry)){
                        $retour = implode('-',$entry);
                    }else{
                        $retour = $entry;
                    }
                    $data[$k] = $retour;
                }
            }
            $logs[] = array('log'=>$log,'entity'=>$repo->find($log->getObjectId()),'data'=>$data);
        } 
        return array('logs'=>$logs,'pf'=>$pf);
    }

    private function getEntityManager(){
        return $this->em;
    }

    
}
