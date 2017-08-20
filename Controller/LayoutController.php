<?php

namespace Idk\LegoBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The admin list controller for Cabinet
 * @Route("/admin/layout")
 */
class LayoutController extends Controller
{

    public function menuAction(){
        $menu = $this->get('lego.service.menu');
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
