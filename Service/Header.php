<?php
namespace Idk\LegoBundle\Service;


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
            [
                'template' => 'IdkLegoBundle:Header:_messages.html.twig',
                'templateParameters' => ['messages'=> [['user' => 'Edmond Becquerel','time' => '5 mins', 'subject' => 'To discover evidence of radioactivity']], 'libelle'=> 'You have 1 messagese', 'route'=>null],
                'icon' => 'envelope-o',
                'libelle' => null,
                'label'=> ['class'=>'label-success','libelle'=>1],
                'class'=>'messages-menu',
            ],
            [
                'template' => 'IdkLegoBundle:Header:_notifications.html.twig',
                'templateParameters' => ['notifications' => [['icon'=>'users', 'subject'=> '5 new members joined today']], 'libelle'=> 'You have 10 notifications', 'route'=>null],
                'icon' => 'bell-o',
                'libelle' => null,
                'label'=> ['class'=>'label-warning','libelle'=>9],
                'class'=> 'notifications-menu',
            ],
            [
                'template' => 'IdkLegoBundle:Header:_tasks.html.twig',
                'templateParameters' => ['tasks' => [['percent'=>'20', 'title' => 'Design some buttons']], 'libelle'=> 'You have 9 tasks', 'route'=>null],
                'icon' => 'flag-o',
                'libelle' => null,
                'label'=> ['class'=>'label-danger','libelle'=>10],
                'class'=> 'tasks-menu',
            ],
            [
                'template' => 'IdkLegoBundle:Header:_user.html.twig',
                'templateParameters' => ['user'=> $this->getUser(), 'route_logout' => 'fos_user_security_logout', 'route_profile'=> null],
                'icon' => null,
                'libelle' => $this->getUser()->getUsername(),
                'label'=> null,
                'class'=> 'user-menu',
            ],
        ];
    }


}
