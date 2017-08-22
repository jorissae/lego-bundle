<?php

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\Annotation\Entity\Field;
use Idk\LegoBundle\Lib\Actions\EntityAction;
use Symfony\Component\HttpFoundation\Request;
use Idk\LegoBundle\Lib\Actions\BulkAction;
use Doctrine\ORM\QueryBuilder;

class ListItems extends Component{

    const ENTITY_ACTION_DELETE = 'entity_action_delete';
    const ENTITY_ACTION_EDIT = 'entity_action.edit';
    const ENTITY_ACTION_SHOW = 'entity_action.show';
    const BULK_ACTION_DELETE = 'bulk_action_delete';

    private $fields = [];
    private $entityActions = [];
    private $bulkActions = [];
    private $page = 1;
    private $nbEntityPerPage = null;

    protected function init(){
        return;
    }

    public function bindRequest(Request $request)
    {
        parent::bindRequest($request);
        if($request->query->has('nbepp')){
            $this->get('session')->set($this->gid('nbepp'), $request->query->get('nbepp'));
        }
        $this->nbEntityPerPage = ($this->get('session')->has($this->gid('nbepp')))? $this->get('session')->get($this->gid('nbepp')):$this->getOption('entity_per_page');
        $this->page = $request->query->has('page')? $request->query->get('page'):1;
        foreach($this->getOption('entity_actions', []) as $action){
            if($action instanceOf EntityAction){
                $this->entityActions[] = $action;
            }else{
                $this->addPredefinedEntityAction($action);
            }
        }
        foreach($this->getOption('bulk_actions', []) as $action){
            if($action instanceOf BulkAction){
                $action->setCid($this->getId());
                $this->bulkActions[] = $action;
            }else{
                $this->addPredefinedBulkAction($action);
            }
        }
    }

    public function getPager(){
        return $this->getConfigurator()->getPager($this->page,$this->nbEntityPerPage, $this->getOption('page_unlimited', false));
    }

    protected function requiredOptions(){
        return [];
    }

    public function add($name, $options){
        $field = new Field($options);
        $field->setName($name);
        $this->fields[$name] = $field;
        return $this;
    }

    public function addEntityAction($label, $options){
        $this->entityActions[] = new EntityAction($label, $options);
        return $this;
    }

    public function addBulkAction($label,$options)
    {
        $bulkAction = new BulkAction($label,$options);
        $bulkAction->setCid($this->getId());
        $this->bulkActions[] =  $bulkAction;
        return $this;
    }

    public function addPredefinedEntityAction($action){
        if($action == self::ENTITY_ACTION_DELETE){
            $this->entityActions[] = new EntityAction('lego.action.delete', ['icon'=>'remove', 'css_class' => 'btn-danger' ,'modal' => $this->getPartial('modal_delete')]);
        }else if($action == self::ENTITY_ACTION_EDIT){
            $this->entityActions[] = new EntityAction('lego.action.edit', ['icon'=>'pencil' ,'css_class' => 'btn-primary' ,'route' => $this->getConfigurator()->getPathRoute('edit')]);
        }else if($action == self::ENTITY_ACTION_SHOW){
            $this->entityActions[] = new EntityAction('lego.action.show', ['icon'=>'eye' ,'css_class' => 'btn-success','route' => $this->getConfigurator()->getPathRoute('show')]);
        }
    }

    public function addPredefinedBulkAction($action){
        if($action == self::BULK_ACTION_DELETE){
            $this->addBulkAction('lego.action.bulk_delete', ['route'=>$this->getConfigurator()->getPathRoute('bulk'), 'params'=>['type'=>'delete']]);
        }
    }

    public function getFields(){
        return array_merge($this->get('lego.service.meta_entity_manager')->generateFields($this->getConfigurator()->getEntityName(), $this->getOption('fields')), $this->fields);
    }

    public function getEntityActions(){
        return $this->entityActions;
    }

    public function getBulkActions(){
        return $this->bulkActions;
    }

    public function getBulkAction($id){
        foreach($this->bulkActions as $action){
            if($action->getId() == $id){
                return $action;
            }
        }
        return false;
    }

    public function getTemplate($name = 'index'){
        return 'IdkLegoBundle:Component\\ListItemsComponent:'.$name.'.html.twig';
    }

    public function getTemplateParameters(){
        return ['component' => $this];
    }

    public function hasBulkActions(){
        return (count($this->bulkActions));
    }

    public function hideEntityActions(){
        return $this->getOption('hide_entity_actions', false);
    }

    public function catchQueryBuilder(QueryBuilder $queryBuilder)
    {
        if($this->request->get('id') and $this->getConfigurator()->getParent()) {
            $fieldAssociation = $this->getFieldAssociationOfParent();
            if($fieldAssociation) {

                if(isset($fieldAssociation['join'])){
                    $alias = $fieldAssociation['join'] . '_';
                    $queryBuilder->leftJoin('b.'.$fieldAssociation['join'], $fieldAssociation['alias'])->andWhere($fieldAssociation['name'] . ' = :' . $alias);
                }else {
                    $alias = $fieldAssociation['name'] . '_';
                    $queryBuilder->andWhere('b.' . $fieldAssociation['name'] . ' = :' . $alias);
                }
                $queryBuilder->setParameter($alias, $this->request->get('id'));
            }
        }
    }

    private function getFieldAssociationOfParent(){
        $fieldAssociation = null;
        $className = $this->getConfigurator()->getParent()->getClass();
        if($this->getOption('field_association', null)){
            return $this->getOption('field_association', null);
        }else {
            $c = $this->getConfigurator()->getClassMetaData();
            foreach($c->getAssociationMappings( ) as $association){
                if(isset($association['joinColumns']) and $association['targetEntity'] == $className) {
                    $fieldAssociation = ['name'=>$association['fieldName']];
                }elseif($association['targetEntity'] == $className){
                    $a = '_'.$association['fieldName'];
                    $fieldAssociation = ['name'=>$a.'.id', 'join' => $association['fieldName'], 'alias' => $a];
                }
            }
            return $fieldAssociation;
        }
    }

    public function canModifyNbEntityPerPage(){
        return $this->getOption('can_modify_nb_entity_per_page', false);
    }

}
