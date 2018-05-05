<?php

namespace Idk\LegoBundle\Widget;


abstract class Widget
{

    public function getClassCss()
    {
        return 'col-lg-3 col-md-3 col-sm-3 col-xs-12 pull-left';
    }

    public function getSharedMaxAge()
    {
        return 60 * 60 * 24;
    }

    public function getActive()
    {
        return true;
    }

    public function getName()
    {
        return 'lego.widget.no_name';
    }

    public function getIcon()
    {
        return 'arrows';
    }

    public function getDescription()
    {
        return 'lego.widget.no_description';
    }

    public function getId()
    {
        return md5(get_class($this));
    }

    abstract public function getTemplate();

    abstract function getParams();
}
