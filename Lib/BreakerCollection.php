<?php
namespace Idk\LegoBundle\Lib;



class BreakerCollection
{

    private $breaker;
    private $entities = [];
    private $collections = null;
    private $name = null;


    public function __construct(Breaker $breaker, $name){
        $this->breaker = $breaker;
        $this->name = $name;
    }

    public function getId(){
        return spl_object_hash($this);
    }

    public function getBreaker(){
        return $this->breaker;
    }

    public function getName(){
        return $this->name;
    }

    public function add($entity){
        $this->entities[] = $entity;
    }

    public function setCollections($collections){
        $this->collections = $collections;
    }

    public function hasCollections(){
        return ($this->collections !== null);
    }

    public function getCollections(){
        return $this->collections;
    }

    public function getEntities(){
        return $this->entities;
    }
}
