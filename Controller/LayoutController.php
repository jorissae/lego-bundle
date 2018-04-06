<?php

namespace Idk\LegoBundle\Controller;


use Idk\LegoBundle\Service\LegoFooterInterface;
use Idk\LegoBundle\Service\LegoHeaderInterface;
use Idk\LegoBundle\Service\LegoMenuInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The layout controller
 * @Route("/admin/layout")
 */
class LayoutController extends Controller
{

    public function menuAction(LegoMenuInterface $menu){
        return $this->render($menu->getTemplate(), ['menu' => $menu]);
    }

    public function headerAction(LegoHeaderInterface $header){
        return $this->render($header->getTemplate(), ['header' => $header]);
    }

    public function footerAction(LegoFooterInterface $footer){
        return $this->render($footer->getTemplate(), ['footer' => $footer]);
    }
}
