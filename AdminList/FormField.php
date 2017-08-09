<?php

namespace Idk\LegoBundle\AdminList;

/**
 * Field
 */
class FormField
{

    /**
     * @var string
     */
    private $name;
    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct($name, array $options)
    {
        $this->name     = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


}
