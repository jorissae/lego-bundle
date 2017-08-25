<?php

namespace Idk\LegoBundle\Component;


use Doctrine\ORM\QueryBuilder;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Idk\LegoBundle\Lib\Path;
use Symfony\Component\HttpFoundation\Request;

abstract class Component{

    private $options;

    private $configurator;

    private $id;

    protected $request;

    public function __construct(array $options, AbstractConfigurator $configurator, $suffixRoute){
        $this->options = $options;
        $this->configurator = $configurator;
        $this->id = md5($suffixRoute.'-'.get_class($this).'-'.get_class($configurator));
        $this->init();
    }

    abstract protected function init();
    abstract protected function requiredOptions();
    abstract protected function getTemplate();
    abstract protected function getTemplateParameters();

    public function bindRequest(Request $request){
        $this->request = $request;
    }

    public function getClass(){
        return $this->getOption('class', 'lego-component');
    }


    public function xhrBindRequest(Request $request){
        $this->bindRequest($request);
    }

    public function catchQuerybuilder(QueryBuilder $queryBuilder){
        return $queryBuilder;
    }

    public function getRequest(){
        return $this->request;
    }



    public function getOption($key, $default = null){
        return (isset($this->options[$key]))? $this->options[$key]:$default;
    }

    public function getPartial($name){
        return $this->getTemplate('_'.$name);
    }

    public function get($name){
        return $this->configurator->get($name);
    }

    public function getConfigurator(){
        return $this->configurator;
    }

    public function getTemplateAllParameters(){
        return array_merge($this->getTemplateParameters(), ['component'=>$this, 'configurator'=> $this->getConfigurator()]);
    }

    public function getPath(){
        return new Path( $this->getConfigurator()->getPathRoute('component'), ['cid'=>$this->getId()]);
    }


    public function getUrl(array $params = []){
        $path = $this->getPath();
        return $this->get('router')->generate($path->getRoute(), $path->getParams($params));
    }

    public function getId(){
        return $this->id;
    }

    public function gid($id){
        return md5($this->id.$id);
    }

}
