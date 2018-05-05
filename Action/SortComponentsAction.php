<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Idk\LegoBundle\Events\UpdateOrganizationComponentsEvent;
use Idk\LegoBundle\LegoEvents;

final class SortComponentsAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $order = $configurator->getConfiguratorSessionStorage('sort', []);
        $order[$request->get('suffix_route')] = $request->request->get('sort');
        $configurator->setConfiguratorSessionStorage('sort', $order);
        $this ->get('event_dispatcher')->dispatch(
            LegoEvents::onMoveComponents,
            new UpdateOrganizationComponentsEvent($configurator, $request->get('suffix_route'), $request->request->get('sort')));
        return new JsonResponse(['status'=>'ok']);
    }

}