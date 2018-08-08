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

final class ShowAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {

        $configurator = $this->getConfigurator($request);
        $this->denyAccessUnlessGranted($configurator->getEntityName(), 'show');
        $response = $this->comunicateComponents($configurator, $request, $request->get('id'));
        if($response){
            return $response;
        }
        return new Response($this->renderView($configurator->getShowTemplate(), [ 'configurator' => $configurator]));
    }

}