<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service\RightBar;

use Gedmo\Loggable\Entity\LogEntry;
use Idk\LegoBundle\Service\MetaEntityManager;

class HistoryRightBar implements RightBarInterface {

    private $mem;

    public function __construct(MetaEntityManager $mem)
    {
        $this->em = $mem->getEntityManager();
    }

    public function getParameters(){
        /* @var \Gedmo\Loggable\Entity\Repository\LogEntryRepository $repo */
        $repo = $this->em->getRepository(LogEntry::class);
        $logs = $repo->createQueryBuilder('b')->orderBy('b.loggedAt', 'DESC')->setMaxResults(10)->getQuery()->getResult();
        $result = [];

        foreach ($logs as $log) {
            /* @var \Gedmo\Loggable\Entity\LogEntry $log */
            $data = array();
            if ($log->getData()) {
                $metaData = $this->em->getClassMetadata($log->getObjectClass());
                foreach($log->getData() as $k => $entry){
                    $type = $metaData->getTypeOfField($k);
                    $retour = $entry;
                    if($metaData->hasAssociation($k) && is_array($entry)){
                        if ($entry) {
                            $type = $metaData->isSingleValuedAssociation($k)? 'single_assoc':'multi_assoc';
                            $assoc = $metaData->getAssociationMapping($k);
                            $obj = $this->em->getRepository($assoc['targetEntity'])->findOneBy($entry);
                            if ($obj) {
                                $id = $this->em->getClassMetadata($assoc['targetEntity'])->getIdentifierValues($obj);
                                $retour = (string) $obj; //(method_exists($obj, '__toString')) ? implode(',', $id) . ' ' . $obj->__toString() : $id;
                            } else {
                                $retour = "??";
                            }
                        }
                    } else if($type === 'boolean'){
                        $retour = ($entry)? 'label.true':'label.false';
                    } else if($type === 'date'){
                        $retour = ($entry)? $entry->format('d/m/Y'):'';
                    } else if($type === 'datetime') {
                        $retour = ($entry)? $entry->format('d/m/Y H:i'):'';
                    } else if(is_array($entry)){
                        $retour = implode('-',$entry);
                    }
                    $data[$k] = ['value' => $retour, 'type' => $type, 'raw' => $entry];
                }
            }
            $names = explode('\\', $log->getObjectClass());
            $result[] = array('log'=>$log,'data'=>$data, 'name' => strtolower($names[\count($names)-1]));
        }
        return ['logs'=>$result];
    }
    
    public function getTemplate(){
        return '@IdkLego/RightBar/history.html.twig';
    }
}
