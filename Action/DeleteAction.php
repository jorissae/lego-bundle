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

use Idk\LegoBundle\LegoEvents;
use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteAction extends AbstractAction
{
    
    private $dispatcher;
    
    public function __construct(ConfiguratorBuilder $configuratorBuilder, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($configuratorBuilder);
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $this->denyAccessUnlessGranted($configurator->getEntityName(), 'delete');
        $em = $this->getEntityManager();
        $entity = $configurator->getRepository()->findOneById($request->get('id'));
        if ($entity === null) {
            throw new NotFoundHttpException($this->trans('lego.entity_not_found'));
        }
        if ('POST' == $request->getMethod()) {
            try {
                $this->dispatcher->dispatch( new GenericEvent($entity), LegoEvents::preDeleteEntity);
                $em->remove($entity);
                $this->dispatcher->dispatch( new GenericEvent($entity), LegoEvents::postDeleteEntity);
                $em->flush();
            } catch (\Exception $e) {
                return new Response(json_encode(array('status'=>'ko', 'exception' => $e->getMessage(),'message'=>$this->trans('lego.error.delete_entity'))));
            }
        }
        return new Response(json_encode(array('status'=>'ok')));
    }

}
