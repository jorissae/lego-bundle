<?php
namespace Idk\LegoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Attribut
 *
 * @ORM\MappedSuperclass()
 */
abstract class AbstractAttribut{



    private static $LIST = 'list';
    private static $NUMBER = 'nombre';
    private static $BOOLEA = 'bool';
    private static $TEXT = 'text';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

	/**
     * @var string
     *
     * @ORM\Column(name="widget", type="string", length=255, nullable=false)
     */
    private $widget;

    /**
     * @var string
     *
     * @ORM\Column(name="widget_search", type="string", length=255, nullable=true)
     */
    private $widgetSearch;

    /**
     * @var string
     *
     * @ORM\Column(name="options", type="text", nullable=true)
     */
    private $options;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

     /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="ordre", type="integer", length=255, nullable=true)
     */
    private $ordre;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     * @return Attribut
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    public function __toString(){
        return $this->libelle;
    }

     /**
     * Set libelle
     *
     * @param string $libelle
     * @return Attribut
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }


    /**
     * Set widget
     *
     * @param string $widget
     * @return Attribut
     */
    public function setWidget($widget)
    {
        $this->widget = $widget;

        return $this;
    }

    /**
     * Get widget
     *
     * @return string
     */
    public function getWidget()
    {
        if($this->widget) return $this->widget;
        //return self::$TEXT;
        return null;
    }

    public function getAllWidget(){
        if($this->getWidget() == $this->getWidgetSearch()) return $this->getWidget();
        return $this->getWidget().','. $this->getWidgetSearch();
    }

    /**
     * Set widgetSearch
     *
     * @param string $widgetSearch
     * @return Attribut
     */
    public function setWidgetSearch($widgetSearch)
    {
        $this->widgetSearch = $widgetSearch;

        return $this;
    }

    /**
     * Get widgetSearch
     *
     * @return string
     */
    public function getWidgetSearch()
    {
        if($this->widgetSearch) return $this->widgetSearch;
        if($this->isList()) return 'choice';
        if($this->isBool()) return 'checkbox';
        if($this->isNumber()) return 'lle_range';
        return 'text';
    }

    /**
     * Set options
     *
     * @param string $options
     * @return Attribut
     */
    public function setOptions($options)
    {
        $this->options = json_encode($options);

        return $this;
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return json_decode($this->options,true);
    }

    public function getSearchOptions(){
        $options = $this->getOptions();
        $return = array();
        if($this->getWidgetSearch() == 'lle_range'){
            if(isset($options['min'])) $return['min'] = $options['min'];
            if(isset($options['max'])) $return['max'] = $options['max'];
            if(isset($options['step'])) $return['step'] = $options['step'];
            if(isset($options['unite'])) $return['unite'] = $options['unite'];
            if(isset($options['slide'])) $return['slide'] = $options['slide']; else $return['slide'] = false;
            if(isset($options['multiplicator'])) $return['multiplicator'] = ($options['multiplicator'] > 0)? $options['multiplicator']:1;
        }elseif($this->getWidgetSearch() == 'choice'){
            if(isset($options['list'])) $return['choices'] = $options['list'];
        }
        return $return;
    }

    public function getList(){
        $o = $this->getoptions();
        $list = $o['list'];
        return $list;
    }

    public function isEditInPlace(){
        return true;
    }


    public function isValid($value){
        if($this->isNumber()){
            $options = $this->getOptions();
            $isNotMin = (isset($options['min']) && $options['min'] > $value);
            $isNotMax = (isset($options['max']) && $options['max'] < $value);
            // ED : on enlève le test sur le step pour la validation à la saisie.
            // $isNotStep = (isset($options['step']) && $value % $options['step']);
            // return !($isNotMin or $isNotMax or $isNotStep);
            return !($isNotMin or $isNotMax );
        }
        return true;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Attribut
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    public function isNumber(){
        return $this->is(self::$NUMBER);
    }

    public function isList(){
        return $this->is(self::$LIST);
    }

    public function isBool(){
        return $this->is(self::$BOOLEA);
    }

    public function is($widget){
        return ($this->getWidget() == $widget);
    }

    static function getChoiceWidget(){
        return array(
            self::$NUMBER => 'Nombre',
            self::$TEXT  => 'Text',
            self::$BOOLEA => 'Boolean',
            self::$LIST => 'Liste de choix',
        );
    }
}
