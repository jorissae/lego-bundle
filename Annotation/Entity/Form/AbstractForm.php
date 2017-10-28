<?php
namespace Idk\LegoBundle\Annotation\Entity\Form;

use Idk\LegoBundle\Annotation\Entity\Field;

/**
 * @Annotation
 */
class AbstractForm
{
    protected $type;
    protected $label;
    protected $name;

    public function __construct(array $options = [])
    {
        $this->type = isset($options['type'])? $options['type']:null;
        $this->label = isset($options['label'])? $options['label']:null;
    }

    public function getType(){
        return $this->type;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function getLabel(){
        return $this->label;
    }

    public function setField(Field $field){
        if(!$this->name) $this->name = $field->getName();
        if(!$this->label) $this->label = $field->getHeader();
    }

}