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

abstract class AbstractTab implements TabInterface
{

    protected $displayIn;

    public function getTemplateAllParameters(){
        return [];
    }

    public function getTemplate(){
        return null;
    }

    public function getController(){
        return null;
    }

    public function getComponents(){
        return [];
    }

    public function setDisplayIn($id){
        $this->displayIn = $id;
    }

    public function isDisplayIn($id){
        return $id === $this->displayIn;
    }

    public function getClass(){
        return null;
    }

}
