<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Widget;

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Idk\LegoBundle\Widget\Widget;

class ListWidget extends Widget{


    private $provider;

    public function __construct($provider){
        $this->provider = $provider;
    }

    public function getName(){
        return 'widget.list_widget';
    }

    public function getDescription(){
        return 'widget.list_widget_description';
    }

    public function getId(){
        return 'list_widget';
    }

    public function getTemplate(){
        return "@IdkLego/Widget/list_widget.html.twig";
    }

    public function getClassCss(){
        return 'col-md-12';
    }

    public function getActive(){
        return false;
    }

    public function getParams(){
        return ['widgets' => $provider->getWidgets()];
    }
}
