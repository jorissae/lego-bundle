<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Lib\Actions;

use Idk\LegoBundle\Lib\Path;

/**
 * The simple bulk action is a default implementation of the bulk action interface, this can be used
 * in very simple use cases.
 */
class BulkAction
{

    private $icon;
    private $label;
    private $type;
    private $id;
    private $cid;
    private $role;

    /**
     * @param array    $url      The url path and parameters
     * @param string   $label    The label
     * @param string   $icon     The icon
     * @param string   $template The template
     */
    public function __construct($label, $options)
    {
        $this->label = $label;
        $this->id = md5($label);
        $this->icon = (isset($options['icon']))? $options['icon']:null;
        $this->type = (isset($options['type']))? $options['type']:null;
        $this->role = (isset($options['role']))? $options['role']:null;
    }

    public function getId(){
        return $this->id;
    }

    public function setCid($cid){
        $this->cid = $cid;
    }

    public function getParams(){
        return ['ida'=>$this->id, 'cid' => $this->cid, 'type' => md5($this->type)];
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function getRole(){
        return $this->role;
    }
    public function getType(){
        return $this->type;
    }
}
