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

use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Idk\LegoBundle\Events\UpdateOrganizationComponentsEvent;
use Idk\LegoBundle\LegoEvents;

final class SortComponentsAction extends AbstractAction
{

    private $eventDispatcher;

    public function __construct(ConfiguratorBuilder $configuratorBuilder, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($configuratorBuilder);
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $order = $configurator->getConfiguratorSessionStorage('sort', []);
        $order[$request->get('suffix_route')] = $request->request->get('sort');
        $configurator->setConfiguratorSessionStorage('sort', $order);
        $this->eventDispatcher->dispatch(
            LegoEvents::onMoveComponents,
            new UpdateOrganizationComponentsEvent($configurator, $request->get('suffix_route'), $request->request->get('sort')));
        return new JsonResponse(['status'=>'ok']);
    }

}