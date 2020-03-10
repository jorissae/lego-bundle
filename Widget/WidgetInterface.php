<?php

namespace Idk\LegoBundle\Widget;

interface WidgetInterface{

    public function getClassCss();
    public function getSharedMaxAge();
    public function getActive();
    public function getName();
    public function getIcon();
    public function getDescription();
    public function getId();
    public function getTemplate();
    function getParams();
}
