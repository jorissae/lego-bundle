<?php
namespace Idk\LegoBundle\Service;


use Idk\LegoBundle\Lib\LayoutItem\LabelItem;
use Idk\LegoBundle\Lib\LayoutItem\MenuItem;
use Symfony\Component\Yaml\Yaml;


class Menu
{

    private $em;
    private $security;

    public function __construct($em, $security) {
        $this->em = $em;
        $this->security = $security;
    }

    public function search(){
        return false;
    }

    public function getTemplate(){
        return 'IdkLegoBundle:Layout:_menu.html.twig';
    }

    public function getItems(){
        $return = [];
        $return[] = new MenuItem('ADMIN', ['type'=>MenuItem::TYPE_HEADER]);
        $return[] = new MenuItem('Dashboard', [
            'icon' => 'dashboard',
            'route' => 'homepage',
            'labels'=> [new LabelItem(5, ['css_class'=>'bg-red'])],
            'children' => [new MenuItem('index',['route'=>'homepage', 'icon'=>'circle-o'])]
        ]);
        return $return;
    }


}
