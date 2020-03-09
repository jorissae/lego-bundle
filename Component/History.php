<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\Lib\Actions\ListAction;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Gedmo\Loggable\Entity\LogEntry;

class History extends Component{

    private $entityId;
    private $mem;



    public function __construct(MetaEntityManager $mem) {
        $this->em = $mem->getEntityManager();
    }

    protected function init(){
        return;
    }

    protected function requiredOptions(){
        return [];
    }

    public function bindRequest(Request $request){
        parent::bindRequest($request);
        if($request->get('id')){
            $this->entityId = $this->getRequest()->get('id');
        }
    }

    public function getTemplate($name = 'index'){
        return '@IdkLego/Component/HistoryComponent/index.html.twig';
    }

    public function getTemplateParameters(){
        if($this->entityId) {
            $entity = $this->getConfigurator()->getRepository()->find($this->entityId);
            /* @var \Gedmo\Loggable\Entity\Repository\LogEntryRepository $repo */
            $repo = $this->em->getRepository(LogEntry::class);
            $logs = $repo->getLogEntries($entity);
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
        return [];
    }


}
