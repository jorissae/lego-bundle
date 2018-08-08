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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ComponentAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $component = $configurator->getComponent($request->get('suffix_route'),$request->get('cid'));
        $configurator->bindRequestCurrentComponents($request, $component);
        $component->xhrBindRequest($request);
        return new JsonResponse(['html'=>$this->renderView($component->getTemplate(), $component->getTemplateAllParameters())]);
    }

}