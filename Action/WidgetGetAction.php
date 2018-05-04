<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class WidgetGetAction
{

    private $widgetChain;
    private $twig;

    public function __construct(WidgetChain $widgetChain, \Twig_Environment $twig){
        $this->twig = $twig;
        $this->widgetChain = $widgetChain;
    }

    public function __invoke(Request $request): Response
    {
        $widget = $this->widgetChain->get($request->get('widget'));
        $template = $this->twig->loadTemplate($this->widgetChain->getTemplate());
        //todo ['status' => ...
        return new Response($template->render(['widget'=>$widget]));
    }

}