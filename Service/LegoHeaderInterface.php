<?php
namespace Idk\LegoBundle\Service;


interface LegoHeaderInterface
{


    public function getTemplate();

    public function getTitle($size = 'lg');

    public function hasActionToggle();

    public function hasMenuRight();

    public function getItems();


}
