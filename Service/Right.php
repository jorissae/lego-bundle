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


use Idk\LegoBundle\Lib\LayoutItem\LabelItem;
use Idk\LegoBundle\Lib\LayoutItem\MenuItem;
use Idk\LegoBundle\Lib\LayoutItem\RightItem;
use Idk\LegoBundle\Service\RightBar\HistoryRightBar;
use Idk\LegoBundle\Service\Tag\RightBarChain;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Idk\LegoBundle\Lib\Path;

class Right implements LegoRightInterface
{

    public function getTemplate(){
        return '@IdkLego/Layout/_menu_right.html.twig';
    }
    
    public function getItems(){
        return [
            new RightItem(['icon' => 'home', 'title' => 'sidebar.history', 'rightbar' => HistoryRightBar::class]),
            new RightItem(['icon' => 'gears', 'title' => 'sidebar.setting'])
        ];
    }


}
