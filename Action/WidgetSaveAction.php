<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Idk\LegoBundle\LegoEvents;
use Idk\LegoBundle\Events\UpdateOrganizationWidgetsEvent;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;

final class WidgetSaveAction
{

    private $widgetChain;
    private $twig;
    private $eventDispatcher;

    public function __construct(WidgetChain $widgetChain, \Twig_Environment $twig, TraceableEventDispatcher $eventDispatcher){
        $this->twig = $twig;
        $this->widgetChain = $widgetChain;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Request $request): Response
    {
        $this->eventDispatcher->dispatch(
            LegoEvents::onMoveWidgets,
            new UpdateOrganizationWidgetsEvent($request->request->get('order')));
        return new JsonResponse(['status'=>'ok']);
    }

}