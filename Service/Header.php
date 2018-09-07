<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\Lib\LayoutItem\HeaderItem;
use Idk\LegoBundle\Lib\LayoutItem\LabelItem;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Yaml\Yaml;


class Header implements LegoHeaderInterface
{

    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $security) {
        $this->em = $em;
        $this->security = $security;
    }

    public function getTemplate(){
        return '@IdkLego/Layout/_header.html.twig';
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
                'template' => '@IdkLego/Header/_messages.html.twig',
                'template_parameters' => ['messages'=> [['user' => 'Edmond Becquerel','time' => '5 mins', 'subject' => 'To discover evidence of radioactivity']], 'libelle'=> 'You have 1 messagese', 'route'=>null],
                'icon' => 'envelope-o',
                'label'=> new LabelItem(1, ['css_class'=>'label-success']),
                'css_class'=>'messages-menu',
            ]),
            new HeaderItem([
                'template' => '@IdkLegoBundle/Header/_notifications.html.twig',
                'template_parameters' => ['notifications' => [['icon'=>'users', 'subject'=> '5 new members joined today']], 'libelle'=> 'You have 10 notifications', 'route'=>null],
                'icon' => 'bell-o',
                'libelle' => null,
                'label'=> new LabelItem(9, ['css_class'=>'label-warning']),
                'css_class'=> 'notifications-menu',
            ]),
            new HeaderItem([
                'template' => '@IdkLego/Header/_tasks.html.twig',
                'template_parameters' => ['tasks' => [['percent'=>'20', 'title' => 'Design some buttons']], 'libelle'=> 'You have 9 tasks', 'route'=>null],
                'icon' => 'flag-o',
                'libelle' => null,
                'label'=> new LabelItem(10, ['css_class'=>'label-danger']),
                'css_class'=> 'tasks-menu',
            ]),
            new HeaderItem([
                'template' => '@IdkLego/Header/_user.html.twig',
                'template_parameters' => ['user'=> $this->getUser(), 'route_logout' => 'lego.route.logout', 'route_profile'=> null],
                'libelle' => $this->getUser()->getUsername(),
                'css_class'=> 'user-menu',
            ])
        ];
    }


}
