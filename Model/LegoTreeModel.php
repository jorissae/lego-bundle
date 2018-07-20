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


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
/**
 *
 * @MappedSuperclass
 */
class LegoTreeModel implements LegoTreeInterface
{
    /**
     * Identifiant.
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * Name.
     *
     * @var string
     * @ORM\Column(type="string", nullable=false, name="tree_name")
     */
    protected $name;
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, name="tree_lvl", options={"unsigned":true})
     */
    protected $level;
    /**
     * Le
     * @var int
     * @ORM\Column(type="integer", nullable=false, name="tree_left", options={"unsigned":true})
     */
    protected $left;
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, name="tree_right", options={"unsigned":true})
     */
    protected $right;
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param string $name
     *
     * @return Tree
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param int $level
     *
     * @return TreeInterface
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }
    /**
     * @param int $left
     *
     * @return TreeInterface
     */
    public function setLeft($left)
    {
        $this->left = $left;
        return $this;
    }
    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }
    /**
     * @param int $right
     *
     * @return TreeInterface
     */
    public function setRight($right)
    {
        $this->right = $right;
        return $this;
    }
    /**
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }
}