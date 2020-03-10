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

class TabBrick extends AbstractTab
{

    private $brick;
    private $libelle;

    public function __construct($libelle, BrickInterface $brick){
        $this->brick = $brick;
        $this->libelle = $libelle;
    }

    public function getTemplateAllParameters(){
        return $this->brick->getTemplateAllParameters();
    }

    public function getTemplate(){
        return $this->brick->getTemplate();
    }

    public function getLibelle(){
        return $this->libelle;
    }

    public function setDisplayIn($id){
        $this->brick->setDisplayIn($id);
    }

    public function isDisplayIn($id){
        return $this->brick->isDisplayIn($id);
    }

    public function getId(){
        return $this->brick->getId();
    }

    public function getClass(){
        return $this->brick->getClass();
    }
}
