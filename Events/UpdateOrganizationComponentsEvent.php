<?php

namespace Idk\LegoBundle\Events;

use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Symfony\Component\EventDispatcher\Event;

class UpdateOrganizationComponentsEvent extends Event
{
    protected $configurator;
    protected $routeSuffix;
    protected $order;

    public function __construct(AbstractConfigurator $configurator, $routeSuffix, $order)
    {
        $this->configurator = $configurator;
        $this->routeSuffix = $routeSuffix;
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return AbstractConfigurator
     */
    public function getConfigurator()
    {
        return $this->configurator;
    }

    /**
     * @return mixed
     */
    public function getRouteSuffix()
    {
        return $this->routeSuffix;
    }


}