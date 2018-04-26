<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ExportAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $response = $this->comunicateComponents($configurator, $request);
        if($response){
            return $response;
        }
        $return =  $this->get("lego.service.export")->getDownloadableResponse($configurator, $request->get('format'));
        return $return;
    }

}