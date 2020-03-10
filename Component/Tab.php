<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\Lib\Actions\ListAction;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Gedmo\Loggable\Entity\LogEntry;
use Idk\LegoBundle\Lib\Tab\TabInterface;

class Tab extends Component{

    private $tabs;


    protected function init(){
        return;
    }

    public function addTab(TabInterface $tab){
        $tab->setDisplayIn($this->getId());
        $this->tabs[] = $tab;
    }

    protected function requiredOptions(){
        return [];
    }

    public function getTemplate($name = 'index'){
        return '@IdkLego/Component/TabComponent/index.html.twig';
    }

    public function getTemplateParameters(){
        return [];
    }

    public function getTabs(){
        return $this->tabs;
    }


}
