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


interface LegoHeaderInterface
{


    public function getTemplate();

    public function getTitle($size = 'lg');

    public function hasActionToggle();

    public function hasMenuRight();

    public function getItems();


}
