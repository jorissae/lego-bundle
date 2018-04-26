<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Idk\LegoBundle\Events\UpdateOrganizationComponentsEvent;
use Idk\LegoBundle\LegoEvents;

final class OrderComponentsAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $order = $configurator->getConfiguratorSessionStorage('order', []);
        $order[$request->get('suffix_route')] = $request->request->get('order');
        $configurator->setConfiguratorSessionStorage('order', $order);
        $this ->get('event_dispatcher')->dispatch(
            LegoEvents::onMoveComponents,
            new UpdateOrganizationComponentsEvent($configurator, $request->get('suffix_route'), $request->request->get('order')));
        return new JsonResponse(['status'=>'ok']);
    }

}