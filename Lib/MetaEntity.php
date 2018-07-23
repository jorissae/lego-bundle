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

class MetaEntity
{

    private $metadata;
    private $annotation;
    private $shortname;

    public function __construct(string $shortname, ClassMetadata $metadata, Entity $annotation){
        $this->shortname = $shortname;
        $this->metadata = $metadata;
        $this->annotation = $annotation;
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
        $c = $this->getConfigurator($configuratorBuilder);
        return new Path($c->getPathRoute('index'), $c->getPathParameters());
    }

}
