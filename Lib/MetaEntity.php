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
    private $configname;

    public function __construct(string $shortname, ClassMetadata $metadata, string $configname = null){
        $this->shortname = $shortname;
        $this->metadata = $metadata;
        $this->configname = $configname;
        $reflectionClass = new \ReflectionClass($metadata->getName());
        $r = new AnnotationReader();
        $this->annotation = $r->getClassAnnotation($reflectionClass, Entity::class);
    }

    public function getName(){
        return $this->metadata->getName();
    }

    public function getLibelle(){
        $config = $this->getConfig();
        if(isset($config['title'])) return $config['title'];
        if($this->annotation->getTitle()) return $this->annotation->getTitle();
        return $this->shortname;
    }

    public function getAnnotation():Entity{
        return $this->annotation;
    }

    public function getConfig(){
        return $this->getAnnotation()->getConfig($this->shortname);
    }

    public function getConfigurator(ConfiguratorBuilder $configuratorBuilder, string $configname = null){
        $class = $this->annotation->getConfigClass($this->shortname);
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
        $class = $configuratorBuilder->getConfiguratorClassName($this->metadata->getName(), $this->shortname);
        $params = [];
        $route =  call_user_func($class .'::getControllerPath').'_index';
        if($route === 'lego_index'){
            $params['entity'] = $this->shortname;
        }
        return new Path($route,$params);
    }

}
