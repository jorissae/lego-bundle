<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Annotation\Entity\Filter;

use Idk\LegoBundle\Annotation\Entity\Field;


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

    public function setLabel($label){
        $this->label = $label;
        $this->options['label'] = $this->label;
        return $this;
    }

    public function getOption($key, $default = null){
        return (isset($this->options[$key]))? $this->options[$key]:$default;
    }

    public function getOptions(){
        return $this->options;
    }

    public function newInstanceOfType(){
        $reflectionClass = new \ReflectionClass($this->getClassNameType());
        dd($reflectionClass);
        return $reflectionClass->newInstance($this->name, $this->options);
    }

    public function setField(Field $field){
        if(!$this->name) $this->name = $field->getName();
        if(!$this->label) $this->setLabel($field->getHeader());
    }
}
