<?php

namespace Idk\LegoBundle\Controller;


use Idk\LegoBundle\Service\Interf\MenuInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * The layout controller
 * @Route("/admin/layout")
 */
class LayoutController extends Controller
{

    public function menuAction(MenuInterface $menu){
        return $this->render($menu->getTemplate(), ['menu' => $menu]);
    }

    public function headerAction(){
        $header = $this->get('lego.service.header');
        return $this->render($header->getTemplate(), ['header' => $header]);
    }

    public function footerAction(){
        $footer = $this->get('lego.service.footer');
        return $this->render($footer->getTemplate(), ['footer' => $footer]);
    }
}
