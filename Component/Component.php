<?php

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\Configurator\AbstractConfigurator;

abstract class Component{

    private $options;

    private $configurator;

    public function __construct(array $options, AbstractConfigurator $configurator){
        $this->options = $options;
        $this->configurator = $configurator;
        $this->init();
    }

    abstract protected function init();
    abstract protected function requiredOptions();
    abstract protected function getTemplate();
    abstract protected function getParameters();

    public function getOption($key, $default = null){
        return (isset($this->options[$key]))? $this->options[$key]:$default;
    }

    public function get($name){
        return $this->configurator->get($name);
    }

    public function getAllParameters(){
        return array_merge($this->getParameters(), ['component' => $this]);
    }

    public function getConfigurator(){
        return $this->configurator;
    }

}
