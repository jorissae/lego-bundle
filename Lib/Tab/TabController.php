<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Lib\Tab;


use Idk\LegoBundle\Component\BrickInterface;

class Tab extends AbstractTab
{

    private $controller;
    private $libelle;

    public function __construct($libelle, $controller){
        $this->controller = $controller;
        $this->libelle = $libelle;
    }

    public function getController(){
        return $this->controller;
    }

    public function getLibelle(){
        return $this->libelle;
    }

    public function getId(){
        return md5($this->libelle.$this->controller);
    }
}
