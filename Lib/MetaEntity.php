<?php
namespace Idk\LegoBundle\Lib;



use Doctrine\ORM\Mapping\ClassMetadata;
use Idk\LegoBundle\Annotation\Entity\Entity;
use Idk\LegoBundle\Configurator\DefaultConfigurator;

class MetaEntity
{

    private $metadata;
    private $annotation;
    private $shortame;

    public function __construct(string $shortname, ClassMetadata $metadata, Entity $annotation){
        $this->shortname = $shortname;
        $this->metadata = $metadata;
        $this->annotation = $annotation;
    }

    public function getName(){
        return $this->metadata->getName();
    }

    public function getConfigurator($container){
        $class = $this->annotation->getConfig();
        if($class) {
            return new $class($container);
        }else{
            $c = new DefaultConfigurator($container, null, $this->getName());
            $c->setTitle($this->annotation->getTitle() ?? 'lego.'.$this->shortname.'.title');
            return $c;
        }
    }

}
