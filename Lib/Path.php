<?php
namespace Idk\LegoBundle\Lib;



class Path
{

    private $route;
    private $params;

    public function __construct($route, array $params = []){
        $this->route = $route;
        $this->params = $params;
    }

    public function getRoute(){
        return $this->route;
    }

    public function getParams(array $params = []){
        return array_merge($this->params, $params);
    }
}
