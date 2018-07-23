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

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Idk\LegoBundle\LegoEvents;
use Idk\LegoBundle\Events\UpdateOrganizationWidgetsEvent;
use Symfony\Component\HttpFoundation\Session\Session;

final class WidgetSaveAction
{

    private $widgetChain;
    private $twig;
    private $eventDispatcher;

    public function __construct(WidgetChain $widgetChain, \Twig_Environment $twig, EventDispatcherInterface $eventDispatcher){
        $this->twig = $twig;
        $this->widgetChain = $widgetChain;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Request $request): Response
    {
        $this->widgetChain->saveInSession($request->request->get('sort'));
        $this->eventDispatcher->dispatch(
            LegoEvents::onMoveWidgets,
            new UpdateOrganizationWidgetsEvent($request->request->get('sort')));
        return new JsonResponse(['status'=>'ok']);
    }

}