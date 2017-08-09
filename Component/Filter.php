<?php

namespace Idk\LegoBundle\Component;


class Filter extends Component{

    protected function init(){
        $this->getConfigurator()->buildFilter();
        return;
    }

    protected function requiredOptions(){
        return [];
    }

    public function getTemplate(){
        return 'IdkLegoBundle:Component:filter.html.twig';
    }

    public function getParameters(){
        return ['filter' => $this->getConfigurator()->getFilterBuilder()];
    }
}
