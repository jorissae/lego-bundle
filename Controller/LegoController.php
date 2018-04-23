<?php

namespace Idk\LegoBundle\Controller;

use Doctrine\ORM\EntityManager;
use Idk\LegoBundle\ComponentResponse\MessageComponentResponse;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Idk\LegoBundle\LegoEvents;
use Idk\LegoBundle\Events\UpdateOrganizationComponentsEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;

/**
 * LegoController
 */
abstract class LegoController extends Controller
{

    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    protected function getSecurity(){
        return $this->container->get('security.context');
    }

    protected function addNoticeFlash($msg){
        $this->addFlash('notice',$msg);
    }

    protected function addErrorFlash($msg){
        $this->addFlash('error',$msg);
    }

    protected function addWarningFlash($msg){
        $this->addFlash('warning',$msg);
    }

    protected function addInfoFlash($msg){
        $this->addFlash('info',$msg);
    }

    protected function addFlash(string $type,string $msg){
        $this->get('session')->getFlashBag()->add($type, $msg);
    }

    protected function comunicateComponents(AbstractConfigurator $configurator,  $request, $entityId = null){
        $redirect = null;
        $componentResponses = $configurator->bindRequest($request);
        foreach($componentResponses as $componentResponse){
            if($componentResponse instanceof MessageComponentResponse) {
                if($componentResponse->hasRedirect()){
                    if($redirect != null){
                        throw new \Exception('Component Conflit: You have several redirection from your components');
                    }else{
                        $redirect = $componentResponse->getRedirect();
                    }
                }
                $this->addFlash($componentResponse->getType(),$componentResponse->getMessage());
            }
        }
        if($redirect){
            return $this->redirectToRoute($redirect['path'], $redirect['params']);
        }else{
            return null;
        }
    }



    protected function doIndexAction(AbstractConfigurator $configurator, Request $request)
    {
        $response = $this->comunicateComponents($configurator, $request);
        if($response){
            return $response;
        }
        return new Response($this->renderView($configurator->getIndexTemplate(), [ 'configurator' => $configurator]));
    }

    protected function doShowAction(AbstractConfigurator $configurator, $entityId, Request $request)
    {
        $response = $this->comunicateComponents($configurator, $request, $entityId);
        if($response){
            return $response;
        }
        return new Response($this->renderView($configurator->getShowTemplate(), [ 'configurator' => $configurator]));
    }


    protected function doAddAction(AbstractConfigurator $configurator, Request $request)
    {
        $response = $this->comunicateComponents($configurator, $request);
        if($response){
            return $response;
        }
        return new Response($this->renderView($configurator->getAddTemplate(), [ 'configurator' => $configurator]));
    }


    protected function doEditAction(AbstractConfigurator $configurator, $entityId, Request $request)
    {
        $response = $this->comunicateComponents($configurator, $request, $entityId);
        if($response){
            return $response;
        }
        return new Response($this->renderView($configurator->getEditTemplate(), [ 'configurator' => $configurator]));
    }

    protected function doEditInPlaceAction(AbstractConfigurator $configurator, Request $request){
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

    protected function doOrderComponents(AbstractConfigurator $configurator, Request $request){
        $order = $configurator->getConfiguratorSessionStorage('order', []);
        $order[$request->get('suffix_route')] = $request->request->get('order');
        $configurator->setConfiguratorSessionStorage('order', $order);
        $this ->get('event_dispatcher')->dispatch(
            LegoEvents::onMoveComponents,
            new UpdateOrganizationComponentsEvent($configurator, $request->get('suffix_route'), $request->request->get('order')));
        return new JsonResponse(['status'=>'ok']);
    }

    protected function doOrderComponentsReset(AbstractConfigurator $configurator, Request $request){
        $order = $configurator->getConfiguratorSessionStorage('order');
        if($order != null and isset($order[$request->get('suffix_route')])){
            unset($order[$request->get('suffix_route')]);
        }
        $configurator->setConfiguratorSessionStorage('order', $order);
        $this ->get('event_dispatcher')->dispatch(
            LegoEvents::onResetOrderComponents,
            new UpdateOrganizationComponentsEvent($configurator, $request->get('suffix_route'), $order));
        return $this->redirectToRoute($configurator->getPathRoute($request->get('suffix_route')));
    }

    protected function doAutoCompleteAction(AbstractConfigurator $configurator, Request $request){
        $em = $this->getEntityManager();
        $repo = $em->getRepository($configurator->getRepositoryName());
        $term = $request->query->get('term');
        $params = array();
        foreach($request->query->all() as $k => $parameter){
            if($k != 'term'){
                $params[$k] = $parameter;
            }
        }
        $return = array();
        if (method_exists($repo, "autoCompleteQuery")){
            $entities = $repo->autoCompleteQuery($term,$params)->getResult();
            foreach($entities as $entity){
                $return[] = array('label'=>$entity->__toString(),'value'=>$entity->getId());
            }
        } else if(method_exists($repo, "autoComplete")){
            $return = $repo->autoComplete($term, $params);
        } else {
            $fieldSearch = $configurator->getAutocompleteField();
            $cl = $configurator->getClassMetaData();
            $entities = $repo->createQueryBuilder('al')->where('al.'.$fieldSearch.' LIKE :term')->setParameter('term', '%'.$term.'%')->getQuery()->getResult();
            foreach($entities as $entity){
                $return[] = array('label'=>$cl->getFieldValue($entity,$fieldSearch),'value'=>$entity->getId());
            }
        }
        return new Response(json_encode($return));
    }

    protected function doExportAction(AbstractConfigurator $configurator, Request $request)
    {

        $response = $this->comunicateComponents($configurator, $request);
        if($response){
            return $response;
        }
        $return =  $this->get("lego.service.export")->getDownloadableResponse($configurator, $request->get('format'));
        return $return;
    }

    protected function doComponentAction(AbstractConfigurator $configurator, Request $request){
        $component = $configurator->getComponent($request->get('suffix_route'),$request->get('cid'));
        $configurator->bindRequestCurrentComponents($request, $component);
        $component->xhrBindRequest($request);
        return new JsonResponse(['html'=>$this->renderView($component->getTemplate(), $component->getTemplateAllParameters())]);
    }

    protected function doBulkAction(AbstractConfigurator $configurator, Request $request){
        $type = $request->query->get('type');
        $ids = $request->request->get('ids');
        $em = $this->getEntityManager();
        $i=0;
        $entities= $configurator->getRepository()->createQueryBuilder('i')->where('i.id IN (:ids)')->setParameter('ids',$ids)->getQuery()->getResult();
        $msg = null;
        if($type == 'delete'){
            foreach($entities as $entity){
                $em->remove($entity);
                $i++;
            }
            $msg = $this->trans('lego.delete_entities', ['%nb%' => $i]);
        }
        $em->flush();
        return new JsonResponse(['status'=>'ok', 'message' => $msg]);
    }

    protected function doDeleteAction(AbstractConfigurator $configurator, $entityId, Request $request)
    {
        /* @var $em EntityManager */
        $em = $this->getEntityManager();
        $entity = $configurator->getRepository()->findOneById($entityId);
        if ($entity === null) {
            throw new NotFoundHttpException($this->trans('lego.entity_not_found'));
        }
        if ('POST' == $request->getMethod()) {
                try { 
                    $em->remove($entity);
                    $em->flush();
                } catch (\Exception $e) {
                    return new Response(json_encode(array('status'=>'ko', 'message'=>$this->trans('lego.error.delete_entity'))));
                }
        }
        return new Response(json_encode(array('status'=>'ok')));
    }












    protected function doItemAction(AbstractConfigurator $configurator, $item, $ida, $type){
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $itemAction = $configurator->getItemAction($ida);
        $field = $itemAction->getField();
        $get = 'get'.ucfirst($field);
        $set = 'set'.ucfirst($field);
        if($type == 'toggle'){
            $item->$set(!$item->$get());
        }
        $em->persist($item);
        $em->flush();
        if($request->isXmlHttpRequest()) {
            $donnes = $request->request->get('data');
            $index = $donnes['index'];
            return new Response($this->renderView('IdkLegoBundle:Default:_line.html.twig',array('configurator'=>$this->getList($configurator),'item'=>$item,'index'=>$index)));
        }else{
            $this->addNoticeFlash('Modification effectuée');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }



    protected function doAlistAction(AbstracConfigurator $configurator, $ida, $type){
        $em = $this->getEntityManager();
        $items = $this->getAllIterator();
        $listAction = $configurator->getListAction($ida);
        if($listAction->getRole() and !$this->getSecurity()->isGranted($listAction->getRole())){
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }
        $get = 'get'.ucfirst($listAction->getField());
        $set = 'set'.ucfirst($listAction->getField());
        $value = $listAction->getValue();
        $i=0;
        if($type == 'change'){
            foreach($items as $item){
                $item->$set($value);
                $em->persist($item);
                $i++;
            }
        }
        $em->flush();
        $this->addNoticeFlash('Modification effectuée sur <strong>'.$i.'</strong> items');
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    protected function getList($configurator = null){
        return null;
    }

    protected function getAllIterator(){
        return $this->getConfigurator()->getAllIterator();
    }

    protected function getLineResponse($configurator,$item,$index = 0){
        return new Response($this->renderView('IdkLegoBundle:Default:_line.html.twig',array('configurator'=>$this->getList($configurator),'item'=>$item,'index'=>$index)));
    }

    protected function getLine($configurator,$item,$index = 0){
        return $this->renderView('IdkLegoBundle:Default:_line.html.twig',array('configurator'=>$this->getList($configurator),'item'=>$item,'index'=>$index));
    }

    protected function getRefererResponse($typeFlash = null, $msgFlash = null){
        if($typeFlash and $msgFlash)  $this->addFlash($typeFlash,$msgFlash);
        $referer = $this->getRequest()->headers->get('referer');
        return $this->redirect($referer);
    }



    protected function getAllErrorMessagesByForm(Form $form) {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            if($error->getOrigin()->createView()->vars['label']){
                $errors[] = $error->getOrigin()->createView()->vars['label'] . ' '. $error->getMessage();
            }else{
                $errors[] = $error->getOrigin()->createView()->vars['name'] . ' '. $error->getMessage();
            }
        }
        if ($form->count()) {
            foreach ($form as $child) {
                if (!$child->isValid()) {
                    $view = $child->createView();
                    $errors[] = $view->vars['name'] . ' ' . $view->vars['errors'];
                }
            }
        }
        return $errors;
    }

    protected function trans($str, $vars= []){
        return $this->get('translator')->trans($str, $vars);
    }


}
