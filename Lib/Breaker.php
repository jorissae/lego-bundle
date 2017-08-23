<?php
namespace Idk\LegoBundle\Lib;



use Idk\LegoBundle\Configurator\AbstractConfigurator;

class Breaker
{

    private $header;
    private $footer;
    private $fieldName;
    private $enable;
    private $breakers = [];
    private $id;
    private $order;
    private $headerTemplate;
    private $classCssHeader;
    private $footerTemplate;
    private $classCssFooter;


    static $NBINSTANCE = 0;


    public function __construct($fieldName, array $options = []){
        self::$NBINSTANCE = self::$NBINSTANCE + 1;
        $this->fieldName= $fieldName;
        $this->id = md5($this->fieldName . self::$NBINSTANCE);
        $this->header = (isset($options['header']))? $options['header']:null;
        $this->footer = (isset($options['footer']))? $options['footer']:null;
        $this->enable = (isset($options['enable']))? $options['enable']:false;
        $this->order = (isset($options['order']))? $options['order']:'ASC';

        $this->footerTemplate = (isset($options['footer_template']))? $options['footer_template']:null;
        $this->classCssFooter = (isset($options['class_css_footer']))? $options['class_css_footer']:null;
        $this->headerTemplate = (isset($options['header_template']))? $options['header_template']:null;
        $this->classCssHeader = (isset($options['class_css_header']))? $options['class_css_header']:null;

    }

    public function getId(){
        return $this->id;
    }

    public function getOrder(){
        $order = strtoupper($this->order);
        if($order == 'ASC') return 'ASC';
        return 'DESC';
    }

    public function getHeaderTemplate()
    {
        return $this->headerTemplate;
    }


    public function getClassCssHeader()
    {
        return $this->classCssHeader;
    }


    public function getFooterTemplate()
    {
        return $this->footerTemplate;
    }


    public function getClassCssFooter()
    {
        return $this->classCssFooter;
    }


    public function getHeader(){
        return $this->header;
    }

    public function getFooter(){
        return $this->footer;
    }

    public function getFieldName(){
        return $this->fieldName;
    }

    public function hasBreakers(){
        return count($this->breakers);
    }

    public function getBreakers(){
        return $this->breakers;
    }

    public function addBreaker($label, array $options = []){
        $breaker = new Breaker($label, $options);
        $this->breakers[] = $breaker;
        return $breaker;
    }

    public function getCurrentBreaker(){
        foreach($this->getBreakers() as $breaker){
            if($breaker->isEnable()){
                return $breaker;
            }
        }
        return null;
    }

    public function calculateBreakerCollection(AbstractConfigurator $configurator, $entities){
        $calculate = [];
        foreach($entities as $entity){
            $key = $configurator->getStringValue($entity, $this->fieldName);
            if(!isset($calculate[$key])){
                $calculate[$key] = new BreakerCollection($this, $key);
            }
            $calculate[$key]->add($entity);
        }

        if($this->getCurrentBreaker()){
            foreach($calculate as $key => $collection){
                $calculate[$key]->setCollections($this->getCurrentBreaker()->calculateBreakerCollection($configurator, $collection->getEntities()));
            }
        }
        return $calculate;
    }

    public function enable(){
        return $this->enable = true;
    }

    public function disable(){
        return $this->enable = false;
    }

    public function isEnable(){
        return $this->enable;
    }
}
