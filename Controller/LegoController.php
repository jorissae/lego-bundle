<?php

namespace Idk\LegoBundle\Controller;

use Doctrine\ORM\EntityManager;
use Idk\LegoBundle\AdminList\AdminList;
use Idk\LegoBundle\ComponentResponse\MessageComponentResponse;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Form;

/**
 * AdminListController
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

    protected function addFlash($type,$msg){
        $this->get('session')->getFlashBag()->add($type, $msg);
    }

    protected function getAdminListConfigurator(){
        return null;
    }

    protected function comunicateComponents(AbstractConfigurator $configurator,  $request, $entityId = null){
        $redirect = null;
        $componentResponses = $configurator->bindRequest($request, $entityId);
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
        }elseif($type == 'bool'){
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

    protected function doComponentAction(AbstractConfigurator $configurator, Request $request){
        $component = $configurator->getComponent($request->get('cid'));
        $component->xhrBindRequest($request);
        return new JsonResponse(['html'=>$this->renderView($component->getTemplate(), $component->getTemplateAllParameters())]);
    }



    /**
     * Export a list of Entities
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param string                        $_format      The format to export to
     *
     * @return array
     */
    protected function doExportAction(AbstractConfigurator $configurator, $_format, Request $request = null,array $filter = null)
    {
        if (!$configurator->canExport()) {
            throw new AccessDeniedHttpException('You do not have sufficient rights to access this page.');
        }

        $em = $this->getEntityManager();

        /* @var AdminList $adminlist */
        $adminlist = $this->get("lle_adminlist.factory")->createList($configurator, $em);
        if($filter) {
            $adminlist->setFilter($filter);
        } else {
                if (is_null($request)) {
                    $request = $this->getRequest();
                }
                $adminlist->bindRequest($request);
        }
        return $this->get("lle_adminlist.service.export")->getDownloadableResponse($adminlist, $_format);
    }





    /**
     * Delete the Entity using its ID
     *
     * @param AbstractAdminListConfigurator $configurator The adminlist configurator
     * @param integer                       $entityId     The id to delete
     *
     * @throws NotFoundHttpException
     * @return Response
     */
    protected function doDeleteAction(AbstractConfigurator $configurator, $entityId, Request $request = null)
    {
        /* @var $em EntityManager */
        $em = $this->getEntityManager();
        if (is_null($request)) {
            $request = $this->getRequest();
        }
        $helper = $em->getRepository($configurator->getRepositoryName())->findOneById($entityId);
        if ($helper === null) {
            throw new NotFoundHttpException("Entity not found.");
        }

        $indexUrl = $configurator->getUrlAfterDelete($helper,$request);
        if ('POST' == $request->getMethod()) {
                try { 
                    $em->remove($helper);
                    $em->flush();
                } catch (\Exception $e) {
                    return new Response(json_encode(array('status'=>'ko', 'message'=>"Cet élément ne peut être supprimé : il dépend d'un autre objet")));
                }
        }
        if($request->isXmlHttpRequest()){
            return new Response(json_encode(array('status'=>'ok')));
        }else{
            if($request->query->get('sublist')){
                return new RedirectResponse($this->getRequest()->headers->get('referer'));
            }
            return new RedirectResponse(
                $this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array())
            );
        }
    }








    protected function doLogsAction(AbstractConfigurator $configurator, Request $request = null){
        $r =  $this->container->get('log_manager')->getLogs(array('objectClass'=>$configurator->getClass()->getName()),$request->query->get('page',1));
        $adminlist = $this->getList($configurator);
        return new Response(
            $this->renderView(
                $configurator->getLogsTemplate(),
                array('logs'=>$r['logs'], 'adminlistconfigurator' => $configurator,'adminlist'=>$adminlist,'pf'=>$r['pf'])
            )
        );

    }

    protected function doLogAction(AbstractConfigurator $configurator, $id, Request $request = null){
        $r = $this->container->get('log_manager')->getLogs(array('objectClass'=>$configurator->getClass()->getName(),'objectId'=>$id),$request->query->get('page',1));
        $item = $configurator->find($id);
        $adminlist = $this->getList($configurator);
        return new Response(
            $this->renderView(
                $configurator->getLogTemplate(),
                array('logs'=>$r['logs'], 'adminlistconfigurator' => $configurator,'adminlist'=>$adminlist,'pf'=>$r['pf'],'item'=>$item)
            )
        );
    }

    protected function doWorkflowAction(AbstractConfigurator $configurator, $item){
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $work = $request->query->get('work');
        $work = $em->getRepository($configurator->getClassWorkflow())->findOneBy(array($configurator->getWfFieldWorkflow() => trim($work)));
        $set = 'set'.ucfirst($configurator->getLocalFieldWorkflow());
        $get = 'get'.ucfirst($configurator->getLocalFieldWorkflow());
        $fromWork = $item->$get();
        $item->$set($work);
        $em->persist($item);
        $this->get('event_dispatcher')->dispatch(AdminListEvents::onWorkflowChange, new WorkflowChangeEvent($item,$this->getUser(),$fromWork,$work));
        $em->flush();
        if($request->isXmlHttpRequest()) {
            $donnes = $request->request->get('data');
            $index = $donnes['index'];
            return new Response($this->renderView('LleAdminListBundle:Default:_line.html.twig',array('adminlist'=>$this->getList($configurator),'item'=>$item,'index'=>$index)));
        }else{
            $this->addNoticeFlash('Nouvelle valeur pour "'.$configurator->getLocalFieldWorkflow().'": '. $work->__toString());
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
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
            return new Response($this->renderView('LleAdminListBundle:Default:_line.html.twig',array('adminlist'=>$this->getList($configurator),'item'=>$item,'index'=>$index)));
        }else{
            $this->addNoticeFlash('Modification effectuée');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
    }

    protected function doBulkAction(AbstractConfigurator $configurator, $ida, $type){
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $repo = $em->getRepository($configurator->getRepositoryName());
        $ids = $request->request->get('ids');
        $bulkAction = $configurator->getBulkAction($ida);
        $get = 'get'.ucfirst($bulkAction->getField());
        $set = 'set'.ucfirst($bulkAction->getField());
        $classMetaData = $configurator->getClass();
        $i=0;
        $value = $bulkAction->getValue();
        $items = $repo->createQueryBuilder('i')->where('i.id IN (:ids)')->setParameter('ids',$ids)->getQuery()->getResult();
        if($type == 'change'){
            if($request->request->get('value')){
                if ($classMetaData->hasAssociation($bulkAction->getField())){
                    $value = $em->getRepository($classMetaData->getAssociationTargetClass($bulkAction->getField()))->find($request->request->get('value'));
                }else{
                    $value = $request->request->get('value');
                }
            }
            foreach($items as $item){
                $item->$set($value);
                $em->persist($item);
                $i++;
            }
            $msg = 'Modification effectuée sur <strong>'.$i.'</strong> items';
        }elseif($type == 'delete'){
            foreach($items as $item){
                $em->remove($item);
                $i++;
            }
            $msg = '<strong>'.$i.'</strong> items supprimé';
        }
        $em->flush();
        $this->addNoticeFlash($msg);
        return $this->redirect($this->getRequest()->headers->get('referer'));
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
        if($configurator == null) $configurator = $this->getAdminListConfigurator();
        $adminlist = $this->get("lle_adminlist.factory")->createList($configurator);
        $adminlist->bindRequest($this->getRequest());
        return $adminlist;
    }

    protected function getAllIterator(){
        return $this->getList($this->getAdminListConfigurator())->getAllIterator();
    }

    protected function getLineResponse($configurator,$item,$index = 0){
        return new Response($this->renderView('LleAdminListBundle:Default:_line.html.twig',array('adminlist'=>$this->getList($configurator),'item'=>$item,'index'=>$index)));
    }

    protected function getLine($configurator,$item,$index = 0){
        return $this->renderView('LleAdminListBundle:Default:_line.html.twig',array('adminlist'=>$this->getList($configurator),'item'=>$item,'index'=>$index));
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

}
