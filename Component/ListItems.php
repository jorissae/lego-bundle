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
    const BULK_ACTION_DELETE = 'bulk_action_delete';

    private $fields = [];
    private $entityActions = [];
    private $bulkActions = [];

    protected function init(){
        return;
    }

    public function bindRequest(Request $request)
    {
        parent::bindRequest($request);
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
            $this->entityActions[] = new EntityAction('lego.action.edit', ['icon'=>'pencil' ,'route' => $this->getConfigurator()->getPathRoute('edit')]);
        }
    }

    public function addPredefinedBulkAction($action){
        if($action == self::BULK_ACTION_DELETE){
            $this->addBulkAction('lego.action.bulk_delete', ['route'=>$this->getConfigurator()->getPathRoute('bulk'), 'params'=>['type'=>'delete']]);
        }
    }

    public function getFields(){
        return array_merge($this->fields, $this->get('lego.service.meta_entity_manager')->generateFields($this->getConfigurator()->getEntityName(), $this->getOption('fields')));
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
                $queryBuilder->andWhere('b.' . $fieldAssociation . ' = :' . $fieldAssociation . '_')->setParameter($fieldAssociation . '_', $this->request->get('id'));
            }
        }
    }

    private function getFieldAssociationOfParent(){
        $fieldAssociation = null;
        if($this->getOption('field_association', null)){
            return $this->getOption('field_association', null);
        }else {
            $c = $this->getConfigurator()->getClassMetaData();
            foreach ($c->getAssociationNames() as $assocName) {
                if ($c->getAssociationTargetClass($assocName) == $this->getConfigurator()->getParent()->getClass()) {
                    $fieldAssociation = $assocName;
                }
            }
            return $fieldAssociation;
        }
    }

}
