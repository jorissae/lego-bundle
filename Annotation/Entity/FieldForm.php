<?php
namespace Idk\LegoBundle\Annotation\Entity;

/**
 * @Annotation
 */
class FieldForm extends Field
{
    protected $type;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->type = isset($options['type'])? $options['type']:null;
    }

    public function getType(){
        return $this->type;
    }
}