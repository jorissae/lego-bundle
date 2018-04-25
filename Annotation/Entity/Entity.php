<?php
namespace Idk\LegoBundle\Annotation\Entity;

/**
 * @Annotation
 */
class Entity
{
    private $name;

    public function __construct(array $options = [])
    {
        $this->name = $options['name'] ?? null;
        $this->config = $options['config'] ?? null;
        $this->title = $options['title'] ?? null;
    }

    public function getConfig(){
        return $this->config;
    }

    public function getName(){
        return $this->name;
    }

    public function getTitle(){
        return $this->title;
    }
}