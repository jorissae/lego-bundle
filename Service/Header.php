<?php
namespace Idk\LegoBundle\Service;


use Idk\LegoBundle\Lib\LayoutItem\HeaderItem;
use Idk\LegoBundle\Lib\LayoutItem\LabelItem;
use Symfony\Component\Yaml\Yaml;


class Header
{

    private $em;
    private $security;

    public function __construct($em, $security) {
        $this->em = $em;
        $this->security = $security;
    }

    public function getTemplate(){
        return 'IdkLegoBundle:Layout:_header.html.twig';
    }

    public function getTitle($size = 'lg'){
        if($size == 'lg'){
            return '<b>Admin</b>LEGO';
        }else{
            return '<b>L</b>GO';
        }
    }

    public function hasActionToggle(){
        return true;
    }

    public function getUser(){
        return $this->security->getToken();
    }

    public function hasMenuRight(){
        return false;
    }

    public function getItems(){
        return [
            new HeaderItem([
                'template' => 'IdkLegoBundle:Header:_messages.html.twig',
                'template_parameters' => ['messages'=> [['user' => 'Edmond Becquerel','time' => '5 mins', 'subject' => 'To discover evidence of radioactivity']], 'libelle'=> 'You have 1 messagese', 'route'=>null],
                'icon' => 'envelope-o',
                'label'=> new LabelItem(1, ['css_class'=>'label-success']),
                'css_class'=>'messages-menu',
            ]),
            new HeaderItem([
                'template' => 'IdkLegoBundle:Header:_notifications.html.twig',
                'template_parameters' => ['notifications' => [['icon'=>'users', 'subject'=> '5 new members joined today']], 'libelle'=> 'You have 10 notifications', 'route'=>null],
                'icon' => 'bell-o',
                'libelle' => null,
                'label'=> new LabelItem(9, ['css_class'=>'label-warning']),
                'css_class'=> 'notifications-menu',
            ]),
            new HeaderItem([
                'template' => 'IdkLegoBundle:Header:_tasks.html.twig',
                'template_parameters' => ['tasks' => [['percent'=>'20', 'title' => 'Design some buttons']], 'libelle'=> 'You have 9 tasks', 'route'=>null],
                'icon' => 'flag-o',
                'libelle' => null,
                'label'=> new LabelItem(10, ['css_class'=>'label-danger']),
                'class'=> 'tasks-menu',
            ]),
            new HeaderItem([
                'template' => 'IdkLegoBundle:Header:_user.html.twig',
                'template_parameters' => ['user'=> $this->getUser(), 'route_logout' => 'fos_user_security_logout', 'route_profile'=> null],
                'libelle' => $this->getUser()->getUsername(),
                'css_class'=> 'user-menu',
            ])
        ];
    }


}
