<?php

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\AdminList\Actions\ListAction;
use Symfony\Component\HttpFoundation\Request;

class Action extends Component{

    private $actions = [];

    const ADD = 'add';
    const BACK = 'back';
    const LOGS = 'logs';
    const EXPORT_CSV = 'export_csv';
    const EXPORT_XLSX = 'export_xlsx';

    protected function init(){
        return;
    }

    protected function requiredOptions(){
        return [];
    }

    public function add($label, $options){
        $this->actions[] = new ListAction($label, $options);
    }

    public function bindRequest(Request $request)
    {
        foreach($this->getOption('actions', []) as $action){
            if($action instanceOf ListAction){
                $this->actions[] = $action;
            }else{
                if($action == self::ADD){
                    $action = new ListAction('lego.action.add', ['route'=>$this->getConfigurator()->getPathRoute('add')]);
                }elseif($action == self::BACK){
                    $action = new ListAction('lego.action.back', ['url'=>$request->headers->get('referer')]);
                }elseif($action == self::LOGS){
                    $action = new ListAction('lego.action.logs', ['route'=>$this->getConfigurator()->getPathRoute('logs')]);
                }elseif($action == self::EXPORT_CSV) {
                    $action = new ListAction('lego.action.export_csv', ['route' => $this->getConfigurator()->getPathRoute('export'), 'params'=>['format'=>'csv']]);
                }elseif($action == self::EXPORT_XLSX) {
                    $action = new ListAction('lego.action.export_xlsx', ['route' => $this->getConfigurator()->getPathRoute('export'), 'params'=>['format'=>'xlsx']]);
                }
                $this->actions[] = $action;
            }
        }
    }

    public function getActions(){
        return $this->actions;
    }

    public function getTemplate($name = 'index'){
        return 'IdkLegoBundle:Component\\ActionComponent:'.$name.'.html.twig';
    }

    public function getTemplateParameters(){
        return [];
    }


}
