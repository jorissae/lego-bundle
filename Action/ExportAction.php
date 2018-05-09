<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Idk\LegoBundle\Service\ExportService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\DependencyInjection\Container;

final class ExportAction extends AbstractAction
{

    public function __construct(ConfiguratorBuilder $configuratorBuilder, ExportService $export){
        parent::__construct($configuratorBuilder);
        $this->export = $export;
    }

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $response = $this->comunicateComponents($configurator, $request);
        if($response){
            return $response;
        }
        $return =  $this->export->getDownloadableResponse($configurator, $request->get('format'));
        return $return;
    }

}