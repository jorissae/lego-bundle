<?php

namespace Idk\LegoBundle\AdminList;

/**
 * Field
 */
class SubList
{


    /**
     * @var string
     */
    private $configuratorName;

    private $parentConfigurator;

    private $key;

    private $title;

    private $name;

    private $view = null;

    private static $nbInstance = 0;
    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct($parentConfigurator, $configuratorName, array $options)
    {
        $this->parentConfigurator = $parentConfigurator;
        $this->configuratorName = $configuratorName;
        $this->key = (isset($options['key']))? $options['key']:null; //key de la relation a ne pas confondre avec name
        $this->title = (isset($options['title']))? $options['title']:null;
        $this->name = (isset($options['name']))? $options['name']:self::$nbInstance;
        self::$nbInstance++;
    }

    /**
     * @return string
     */
    public function getConfigurator()
    {
        return $this->configuratorName;
    }


    //nom unique sur tous le projet ( a ne pâs confondre avec name unique pour son parent)
    public function getUniqueName(){
        return get_class($this->parentConfigurator).'-'.$this->configuratorName.'-'.$this->name;
    }

    public function getName(){
        return $this->name;
    }

    //key de la relation a ne pas confondre avec name
    public function getKey(){
        return $this->key;
    }

    public function getView(){
        return $this->view;
    }

    public function setView($view){
        $this->view = $view;
    }

    public function getTitle(){
        return ($this->title)? $this->title:$this->getView()->getTitle();
    }


    public function getParamsForOrderBy($columnName,$orderDirection){
        return array($this->getId() => array('orderBy'=>$columnName,'orderDirection'=>$orderDirection));
    }

    public function getId(){
        return 'sublist_'.$this->getName();
    }


}
