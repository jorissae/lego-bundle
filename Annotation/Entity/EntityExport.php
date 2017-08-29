<?php
namespace Idk\LegoBundle\Annotation\Entity;

/**
 * @Annotation
 */
class EntityExport extends Field
{
    private $fields;

    public function __construct(array $options = [])
    {
        $this->fields = (isset($options['fields']))? $options['fields']:[];
    }

    public function getFields(){
        return $this->fields;
    }
}