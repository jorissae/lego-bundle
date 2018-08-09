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
        $this->denyAccessUnlessGranted($configurator->getEntityName(), 'export');
        $response = $this->comunicateComponents($configurator, $request);
        if($response){
            return $response;
        }
        $component = $configurator->getComponent($request->get('suffix_route'),$request->get('cid'));
        $return =  $this->export->getDownloadableResponse($configurator, $component, $request->get('format'));
        return $return;
    }

}