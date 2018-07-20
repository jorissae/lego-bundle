<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Model;

interface LegoTreeInterface
{
    public function getId();
    public function setLevel($level);
    public function getLevel();
    public function setLeft($left);
    public function getLeft();
    public function setName($name);
    public function getName();
    public function setRight($right);
    public function getRight();
    public function getParent():LegoTreeInterface;
    public function setParent(LegoTreeInterface $node);
}