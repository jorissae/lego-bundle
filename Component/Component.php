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

    protected $suffixRoute;

    protected $listenQueryParameters = [];

    public function __construct(){
    }

    final function build(array $options, AbstractConfigurator $configurator, $suffixRoute){
        $this->options = $options;
        $this->configurator = $configurator;
        $this->suffixRoute = $suffixRoute;
        $this->id = md5($suffixRoute.'-'.get_class($this).'-'.get_class($configurator));
        $this->valId = $suffixRoute.'-'.get_class($this).'-'.get_class($configurator);
        $this->init();
        return $this;
    }

    public function getValId(){
        return $this->valId;
    }

    //les params utiliser en ajax peuve etre utilisé sans ajax
    public function addListenQueryParameter($queryParametersGlobal, $queryParametersComponent){
        $this->listenQueryParameters[$queryParametersComponent] = $queryParametersGlobal;
        return $this;
    }

    abstract protected function init();
    abstract protected function requiredOptions();
    abstract protected function getTemplate();
    abstract protected function getTemplateParameters();

    public function isMovable(){
        return false;
    }

    //params reporting from query to ajax request
    public function getListenParamsForReload(){
        return [];
    }

    public function getAllQueryParams(){
        return [];
    }

    public function bindRequest(Request $request){
        $this->initQueryParameters($request);
        $this->request = $request;
    }

    public function initQueryParameters(Request $request){
        foreach($this->getAllQueryParams() as $param){
            if(!$request->query->has($param)) {
                if ($this->hasQueryListen($request, $param)) $this->setComponentSessionStorage($param, $request->query->get($this->listenQueryParameters[$param]));
            }
        }
    }

    public function hasQueryListen(Request $request, $key){
        return (isset($this->listenQueryParameters[$key]) && $request->query->has($this->listenQueryParameters[$key]));
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

    public function getComponentSessionStorage($key, $default = null){
        if($this->get('session')->has($this->getId())){
            $componentSessionStorage = $this->get('session')->get($this->getId());
            return (isset($componentSessionStorage[$key]))? $componentSessionStorage[$key]:$default;
        }else{
            return $default;
        }
    }

    public function setComponentSessionStorage($key, $value){
        if(!$this->get('session')->has($this->getId())){
            $this->get('session')->set($this->getId(), []);
        }
        $componentSessionStorage = $this->get('session')->get($this->getId());
        $componentSessionStorage[$key] = $value;
        $this->get('session')->set($this->getId(), $componentSessionStorage);
        return $this;
    }

    public function getPath(){
        $params = [];
        foreach($this->getListenParamsForReload() as $key){
            if($this->request->get($key)){
                $params[$key] = $this->request->get($key);
            }
        }
        $params['cid'] = $this->getId();
        $params['suffix_route'] = $this->suffixRoute;
        $configurator = ($this->getConfigurator()->getParent())? $this->getConfigurator()->getParent():$this->getConfigurator();
        return new Path($configurator->getPathRoute('component'), $params);
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

    protected function trans($str, $vars= []){
        return $this->get('translator')->trans($str, $vars);
    }

}
