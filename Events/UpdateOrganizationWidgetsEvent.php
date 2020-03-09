<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Events;

use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Symfony\Contracts\EventDispatcher\Event;

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
