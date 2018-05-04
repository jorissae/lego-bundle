<?php

namespace Idk\LegoBundle\Events;

use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Symfony\Component\EventDispatcher\Event;

class UpdateOrganizationWidgetsEvent extends Event
{
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }



}