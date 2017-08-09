<?php
namespace Idk\LegoBundle\Annotation\Entity\Filter;


abstract class AbstractFilter
{

    private $name = null;

    private $label = null;

    private $options = [];

    public function init(){
        return;
    }

    abstract public function getClassNameType();

    public function __construct(array $options = [])
    {
        $this->label = (isset($options['label']))? $options['label']:null;
        $this->options = $options;
        $this->init();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getLabel(){
        return ($this->label)? $this->label:$this->name;
    }

    public function getOption($key, $default = null){
        return (isset($this->options[$key]))? $this->options[$key]:$default;
    }

    public function newInstanceOfType(){
        $reflectionClass = new \ReflectionClass($this->getClassNameType());
        return $reflectionClass->newInstance($this->name, $this->options);
    }

}
