<?php

namespace Idk\LegoBundle\AdminList;


/**
 * Field
 */
class HtmlElement
{



    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct($where,$type,$src, array $options)
    {
        $this->where = $where;
        $this->type  = $type;
        $this->src = $src;
    }

    public function is($where){
        return ($this->where == $where);
    }

    public function isTemplate(){
        return (strtolower($this->type) == 'template');
    }

    public function isController(){
        return (strtolower($this->type) == 'controller');
    }


    public function getSrc(){
        return $this->src;
    }

    private function valide(){
        $wheres = array('after_filter','before_filter','after_list','footer_show','header_show');
        $types = array('template','controller');
        return (in_array($this->where,$wheres) and in_array($this->type,$types));
    }



}
