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
