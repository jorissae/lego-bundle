<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Lib\LayoutItem;


use Idk\LegoBundle\Lib\Path;

class MenuItem{

    const TYPE_HEADER = 'header';
    const TYPE_BODY = 'body';


    protected $type;
    protected $libelle;
    protected $icon;
    protected $path;
    protected $labels;
    protected $children;


    public function __construct($libelle, array $options = []){
        $this->libelle = $libelle;
        $this->type = (isset($options['type']))? $options['type']:self::TYPE_BODY;
        $this->icon = (isset($options['icon']))? $options['icon']:null;
        $this->roles = (isset($options['roles']))? $options['roles']:null;
        $this->path = (isset($options['path']))? $options['path']: null;
        $this->labels = (isset($options['labels']))? $options['labels']:[];
        $this->children = (isset($options['children']))? $options['children']:[];
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed|null
     */
    public function getPath(): ?Path
    {
        return $this->path;
    }

    /**
     * @param mixed|null $route
     */
    public function setPath(Path $path)
    {
        $this->path = $path;
    }


    /**
     * @return mixed
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param mixed $labels
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    public function add(MenuItem $item){
        $this->children[] = $item;
    }

    public function getRole(){
        return $this->roles;
    }


}
