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
use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use Idk\LegoBundle\Service\Tag\ActionChain;

/**
 * The layout controller
 * @Route("/admin/layout")
 */
abstract class AbstractLegoController extends AbstractController
{
    public static function getSubscribedServices(){
        $subscribeds = parent::getSubscribedServices();
        $subscribeds[ActionChain::class] = '?'.ActionChain::class;
        $subscribeds['lego.service.configurator.builder'] = '?'.ConfiguratorBuilder::class;
        return $subscribeds;
    }
}
