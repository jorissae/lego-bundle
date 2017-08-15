<?php

namespace Idk\LegoBundle\Component;


use Doctrine\ORM\QueryBuilder;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Symfony\Component\HttpFoundation\Request;

abstract class Component{

    private $options;

    private $configurator;

    protected $request;

    public function __construct(array $options, AbstractConfigurator $configurator){
        $this->options = $options;
        $this->configurator = $configurator;
        $this->init();
    }

    abstract protected function init();
    abstract protected function requiredOptions();
    abstract protected function getTemplate();
    abstract protected function getTemplateParameters();

    public function bindRequest(Request $request){
        $this->request = $request;
    }

    public function catchQuerybuilder(QueryBuilder $queryBuilder){
        return $queryBuilder;
    }

    public function getRequest(){
        return $this->request;
    }

    public function getOption($key, $default = null){
        return (isset($this->options[$key]))? $this->options[$key]:$default;
    }

    public function getPartial($name){
        return $this->getTemplate('_'.$name);
    }

    public function get($name){
        return $this->configurator->get($name);
    }

    public function getConfigurator(){
        return $this->configurator;
    }

}
