<?php

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\Annotation\Entity\Field;
use Idk\LegoBundle\AdminList\Actions\EntityAction;
use Symfony\Component\HttpFoundation\Request;

class ListItems extends Component{

    const ENTITY_ACTION_DELETE = 'entity_action_delete';
    const ENTITY_ACTION_EDIT = 'entity_action.edit';

    private $fields = [];
    private $entityActions = [];

    protected function init(){
        return;
    }

    public function bindRequest(Request $request)
    {
        foreach($this->getOption('entity_actions', []) as $action){
            if($action instanceOf EntityAction){
                $this->entityActions[] = $action;
            }else{
                $this->addPredefinedEntityAction($action);
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

    public function addPredefinedEntityAction($action){
        if($action == self::ENTITY_ACTION_DELETE){
            $this->entityActions[] = new EntityAction('lego.action.delete', ['icon'=>'remove', 'css_class' => 'btn-danger' ,'modal' => $this->getPartial('modal_delete')]);
        }else if($action == self::ENTITY_ACTION_EDIT){
            $this->entityActions[] = new EntityAction('lego.action.edit', ['icon'=>'pencil' ,'route' => $this->getConfigurator()->getPathRoute('edit')]);
        }
    }

    public function getFields(){
        return array_merge($this->fields, $this->get('lego.service.meta_entity_manager')->generateFields($this->getConfigurator()->getEntityName(), $this->getOption('fields')));
    }

    public function getEntityActions(){
        return $this->entityActions;
    }

    public function getTemplate($name = 'index'){
        return 'IdkLegoBundle:Component\\ListItemsComponent:'.$name.'.html.twig';
    }

    public function getTemplateParameters(){
        return ['component' => $this];
    }

    public function hasBulkActions(){
        return $this->getOption('bulk',false);
    }

}
