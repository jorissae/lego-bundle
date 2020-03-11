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

class TabBricks extends AbstractTab
{

    private $bricks;
    private $libelle;

    public function __construct($libelle, array $bricks){
        $this->bricks = $bricks;
        $this->libelle = $libelle;
    }

    

    public function getLibelle(){
        return $this->libelle;
    }

    public function setDisplayIn($id){
        foreach($this->bricks as $brick){
            $brick->setDisplayIn($id);
        }
    }

    public function isDisplayIn($id)
    {
        foreach($this->bricks as $brick){
            if(!$brick->isDisplayIn($id)){
                return false;
            }
        }
        return true;
    }

    public function getComponents()
    {
        return $this->bricks;
    }

    public function getId(){
        $id = null;
        foreach($this->getComponents() as $c){
            $id.=$c->getId();
        }
        return 'tab-'.md5($id);
    }
}
