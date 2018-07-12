<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Controller;


use Idk\LegoBundle\Service\LegoFooterInterface;
use Idk\LegoBundle\Service\LegoHeaderInterface;
use Idk\LegoBundle\Service\LegoMenuInterface;
use Idk\LegoBundle\Service\Interf\MenuInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
