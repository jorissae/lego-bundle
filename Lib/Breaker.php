<?php
namespace Idk\LegoBundle\Lib;



use Idk\LegoBundle\Configurator\AbstractConfigurator;

class Breaker
{

    private $header = null;
    private $footer = null;
    private $fieldName;
    private $enable;
    private $breakers = [];
    private $parentBreaker = null;
    private $id;
    private $order;


    static $NBINSTANCE = 0;


    public function __construct($fieldName, array $options = []){
        self::$NBINSTANCE = self::$NBINSTANCE + 1;
        $this->fieldName= $fieldName;
        $this->id = md5($this->fieldName . self::$NBINSTANCE);
        $this->enable = (isset($options['enable']))? $options['enable']:false;
        $this->order = (isset($options['order']))? $options['order']:'ASC';


        $footer = (isset($options['footer']))? $options['footer']:null;
        $footerTemplate = (isset($options['footer_template']))? $options['footer_template']:null;
        $footerTwig = (isset($options['footer_twig']))? $options['footer_twig']:null;
        $footerCssClass = (isset($options['footer_css_class']))? $options['footer_css_class']:null;
        if($footer or $footerTemplate or $footerTwig or $footerCssClass){
            $this->footer = new BreakerSeparator($footer, BreakerSeparator::FOOTER);
            $this->footer->setTemplate($footerTemplate);
            $this->footer->setTwig($footerTwig);
            $this->footer->setCssClass($footerCssClass);
        }

        $header = (isset($options['header']))? $options['header']:null;
        $headerTemplate = (isset($options['header_template']))? $options['header_template']:null;
        $headerTwig = (isset($options['header_twig']))? $options['header_twig']:null;
        $headerCssClass = (isset($options['header_css_class']))? $options['header_css_class']:null;
        if(!$header and !$headerTemplate and !$headerTwig and !$headerCssClass){
            $header = ucfirst($fieldName);
        }
        $this->header = new BreakerSeparator($header, BreakerSeparator::HEADER);
        $this->header->setTemplate($headerTemplate);
        $this->header->setTwig($headerTwig);
        $this->header->setCssClass($headerCssClass);

        $this->name = ($this->header->getTitle())? $this->header->getTitle():ucfirst($fieldName);
        if(isset($options['name'])){
            $this->name = $options['name'];
        }


    }

    private function setParent(Breaker $parentBreaker){
        $this->parentBreaker = $parentBreaker;
    }

    public function getId(){
        return $this->id;
    }

    public function getOrder(){
        $order = strtoupper($this->order);
        if($order == 'ASC') return 'ASC';
        return 'DESC';
    }


    public function getFooter(){
        return $this->footer;
    }


    public function getHeader(){
        return $this->header;
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
        $breaker->setParent($this);
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
        if($this->parentBreaker){
            $this->parentBreaker->enable();
        }
        return $this->enable = true;
    }

    public function disable(){
        foreach($this->getBreakers() as $breaker){
            $breaker->disable();
        }
        return $this->enable = false;
    }

    public function isEnable(){
        return $this->enable;
    }
}
