<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Idk\LegoBundle\Annotation\Entity\Field;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
/**
 * @Annotation
 */
class AbstractForm
{
    protected $type;
    protected $label;
    protected $name;
    protected $options;

    public function __construct(array $options = [])
    {
        $this->type = isset($options['type'])? $options['type']:null;
        $this->label = isset($options['label'])? $options['label']:null;
        unset($options['type']);
        $this->options = $options;
    }

    public function addIn(FormFactoryInterface $formFactory, FormBuilderInterface &$formBuilder, MetaEntityManager $mem){
        $formBuilder->add($this->getName(), $this->getType(), $this->getOptions());
    }

    public function getType(){
        return $this->type;
    }

    public function getShortType(){
        if($this->getUseClass()){
            return substr(strrchr($this->getUseClass(), '\\'), 1).'::class';
        }
        return $this->getType();
    }

    public function getUseClass(){
        if(strstr($this->getType(), '\\')) return $this->getType();
        return null;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function getLabel(){
        return $this->label;
    }

    public function setLabel($label){
        $this->label = $label;
        $this->options['label'] = $this->label;
        return $this;
    }

    public function setField(Field $field){
        if(!$this->name) $this->name = $field->getName();
        if(!$this->label) $this->setLabel($field->getHeader());
    }

    public function getOptions(){
        return $this->options;
    }

}