<?php
namespace Idk\LegoBundle\Service;


use Idk\LegoBundle\Lib\LayoutItem\LabelItem;
use Idk\LegoBundle\Lib\LayoutItem\MenuItem;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Menu
{

    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $security) {
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
