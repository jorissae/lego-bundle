<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $response = $this->comunicateComponents($configurator, $request);
        if($response) return $response;
        return new Response($this->renderView($configurator->getIndexTemplate(), [ 'configurator' => $configurator ]));
    }

}