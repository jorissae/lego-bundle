<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $this->denyAccessUnlessGranted($configurator->getEntityName(), 'add');
        $response = $this->comunicateComponents($configurator, $request);
        if($response){
            return $response;
        }
        return new Response($this->renderView($configurator->getAddTemplate(), [ 'configurator' => $configurator]));
    }

}