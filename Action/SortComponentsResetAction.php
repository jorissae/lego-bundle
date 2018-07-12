<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Idk\LegoBundle\Events\UpdateOrganizationComponentsEvent;
use Idk\LegoBundle\LegoEvents;

final class SortComponentsResetAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $order = $configurator->getConfiguratorSessionStorage('sort');
        if($order != null and isset($order[$request->get('suffix_route')])){
            unset($order[$request->get('suffix_route')]);
        }
        $configurator->setConfiguratorSessionStorage('sort', $order);
        $this ->get('event_dispatcher')->dispatch(
            LegoEvents::onResetSortComponents,
            new UpdateOrganizationComponentsEvent($configurator, $request->get('suffix_route'), $order));
        return $this->redirectToRoute($configurator->getPathRoute($request->get('suffix_route')), $configurator->getPathParameters());
    }

}