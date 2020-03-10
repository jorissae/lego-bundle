<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Lib\Tab;


interface TabInterface
{
    public function getTemplateAllParameters();
    public function getTemplate();
    public function getController();
    public function getLibelle();
    public function getId();
}
