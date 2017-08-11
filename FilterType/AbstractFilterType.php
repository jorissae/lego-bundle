<?php

namespace Idk\LegoBundle\FilterType;

/**
 * AbstractFilterType
 *
 * Abstract base class for all admin list filters
 */
abstract class AbstractFilterType implements FilterTypeInterface
{
    /**
     * @var null|string
     */
    protected $columnName = null;

    protected $hidden = false;
    /**
     * @var null|string
     */
    protected $alias = null;

    protected $request = null;

    protected $config = null;

    protected $label;

    /**
     * @param string $columnName The column name
     * @param string $alias      The alias
     */
    public function __construct($columnName, $config = array(), $alias = 'b')
    {
        $this->config = $config;
        $this->columnName = $columnName;
        $this->label = (isset($config['label']))? $config['label']:$columnName;
        $this->alias      = $alias;
        $this->hidden = (isset($confi['hidden']))? $config['hidden']:false;
    }

    public function getName(){
        return $this->columnName;
    }
    public function getLabel(){
        return $this->label;
    }

    /**
     * Returns empty string if no alias, otherwise make sure the alias has just one '.' after it.
     *
     * @return string
     */
    protected function getAlias()
    {
        if (empty($this->alias)) {
            return '';
        }

        if (strpos($this->alias, '.') !== false) {
            return $this->alias;
        }

        return $this->alias . '.';
    }

    public function isHidden(){
        return $this->hidden;
    }

    public function setHidden($hidden){
        $this->hidden = $hidden;
    }

    public function setRequest($request){
        $this->request = $request;
    }

    public function getRequest(){
        return $this->request;
    }

    public function getValueSession($id){
        return (isset($this->request[$id]))? $this->request[$id]:null;
    }

    public function getOptions(){
        return $this->config;
    }
}
