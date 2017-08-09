<?php

namespace Idk\LegoBundle\Component;


class ListItems extends Component{


    protected function init(){
    return;
}

    protected function requiredOptions(){
        return [];
    }

    public function getTemplate(){
        return 'IdkLegoBundle:Component:list_items.html.twig';
    }

    public function hasBulkActions(){
        return $this->getOption('bulk',false);
    }

    public function getParameters(){
        return ['component' => $this];
    }

    public function getColumns(){
        return  $this->getConfigurator()->getFields($this->getOption('columns'));
    }
}
