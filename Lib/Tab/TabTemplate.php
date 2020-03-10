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

class TabTemplate extends AbstractTab
{

    private $template;
    private $libelle;
    private $params;

    public function __construct($libelle, $template, array $params = []){
        $this->controller = $controller;
        $this->libelle = $libelle;
        $this->params = $params;
    }

    public function getTemplateAllParameters(){
        return $this->params;
    }

    public function getTemplate(){
        return $this->template;
    }

    public function getLibelle(){
        return $this->libelle;
    }

    public function getId(){
        return md5($this->libelle.$this->template);
    }
}
