<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service\Tag;


use Idk\LegoBundle\Action\AbstractAction;
use Idk\LegoBundle\Service\BulkActionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Idk\LegoBundle\Configurator\AbstractConfigurator;

class BulkChain
{
    private $actions = [];

    public function __construct(iterable $actions)
    {
        foreach($actions as $action){
            $this->actions[get_class($action)] = $action;
        }
    }

    public function get($action): ?BulkActionInterface
    {
        return $this->actions[$action]  ?? null;
    }

    public function getByMd5($md5){
        foreach($this->actions as $k => $bulk){
            if(md5($k) === $md5){
                return $bulk;
            }
        }
        return null;
    }

}
