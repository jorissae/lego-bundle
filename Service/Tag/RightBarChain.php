<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service\Tag;


use Idk\LegoBundle\Service\RightBar\RightBarInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class RightBarChain
{
    private $rightbars= [];

    public function __construct(iterable $rightbars)
    {
        foreach($rightbars as $rightbar){
            $this->rightbars[get_class($rightbar)] = $rightbar;
        }
    }

    public function addWidget(RightBarInterface $rightBar)
    {
        $this->rightbars[] = $rightbar;
    }

    public function get(string $classname){
        return $this->rightbars[$classname];
    }

}
