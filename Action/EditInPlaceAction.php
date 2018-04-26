<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditInPlaceAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $this->createFormBuilder();
        $em = $this->getEntityManager();
        $reload = $request->request->get('reload');
        $entity = $em->getRepository($configurator->getRepositoryName())->findOneById($request->request->get('id'));
        $columnName = $request->request->get('columnName');
        $class = $request->request->get('cls');
        $type = $configurator->editInplaceInputType($entity,$columnName);
        $method = 'set'.$configurator->to_camel_case($columnName);
        if ($type == 'object'){
            $value = $em->getRepository($class)->find($request->request->get('value'));
        }elseif($type == 'datetime'){
            $value = $request->request->get('value');
            if($value != ''){
                $value = \DateTime::createFromFormat('d/m/Y H:i',$request->request->get('value'));
            } else {
                $value = null;
            }
        }elseif($type == 'date'){
            $value = $request->request->get('value');
            if($value != ''){
                $value = \DateTime::createFromFormat('d/m/Y',$value);
            } else {
                $value = null;
            }
        }elseif($type == 'time'){
            $value = $request->request->get('value');
            if($value != ''){
                $value = \DateTime::createFromFormat('H:i',$request->request->get('value'));
            } else {
                $value = null;
            }
        }elseif($type == 'boolean'){
            $value = ($request->request->get('value') == '1');
        } else{
            $value = $request->request->get('value');
        }
        $entity->$method($value);
        $em->persist($entity);
        $em->flush();
        if($type == 'text'){
            $stringValue = $configurator->getValue($entity,$columnName);
        } else {
            $stringValue = $configurator->getStringValue($entity,$columnName);
        }
        if($reload == 'tr'){
            $return = array('code'=>'OK','val'=> (string)html_entity_decode($this->getLineResponse($configurator,$entity)));
        }else{
            $return = array('code'=>'OK','val'=>(string)$stringValue,'setter'=>$value);
        }
        return new Response(json_encode($return));
    }

}