<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Lib;



use Doctrine\ORM\Mapping\ClassMetadata;
use Idk\LegoBundle\Annotation\Entity\Entity;
use Idk\LegoBundle\Configurator\DefaultConfigurator;
use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Doctrine\Common\Annotations\AnnotationReader;

class MetaEntity
{

    private $metadata;
    private $annotation;
    private $shortname;

    public function __construct(string $shortname, ClassMetadata $metadata){
        $this->shortname = $shortname;
        $this->metadata = $metadata;
        $reflectionClass = new \ReflectionClass($metadata->getName());
        $r = new AnnotationReader();
        $this->annotation = $r->getClassAnnotation($reflectionClass, Entity::class);
    }

    public function getName(){
        return $this->metadata->getName();
    }

    public function getLibelle(){
        if($this->annotation->getTitle()) return $this->annotation->getTitle();
        return $this->shortname;
    }

    public function getConfigurator(ConfiguratorBuilder $configuratorBuilder){
        $class = $this->annotation->getConfig();
        if($class) {
            return $configuratorBuilder->getConfigurator($class, null, $this->getName(), ['entity'=>$this->shortname]);
        }else{
            return $configuratorBuilder->getDefaultConfigurator($this->shortname, $this->getName(), $this->annotation);
        }
        return $c;
    }

    public function getIcon(){
        return $this->annotation->getIcon();
    }

    public function getPath(ConfiguratorBuilder $configuratorBuilder){
        $class = $configuratorBuilder->getConfiguratorClassName($this->metadata->getName());
        $params = [];
        $route =  call_user_func($class .'::getControllerPath').'_index';
        if($route === 'lego_index'){
            $params['entity'] = $this->shortname;
        }
        return new Path($route,$params);
    }

}
