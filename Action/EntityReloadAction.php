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
use Idk\LegoBundle\Service\EditInPlaceFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EntityReloadAction extends AbstractFormAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $component = $configurator->getComponent($request->get('suffix_route'), $request->get('cid'));
        $entity = $this->getEntityManager()->getRepository($component->getConfigurator()->getRepositoryName())->findOneById($request->get('id'));

        $configurator->bindRequestCurrentComponents($request, $component);
        $component->xhrBindRequest($request);

        return new Response((string)html_entity_decode($component->renderEntity($entity)));
    }

}