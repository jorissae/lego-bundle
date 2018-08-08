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

final class DeleteAction extends AbstractAction
{

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
                $em->remove($entity);
                $em->flush();
            } catch (\Exception $e) {
                return new Response(json_encode(array('status'=>'ko', 'message'=>$this->trans('lego.error.delete_entity'))));
            }
        }
        return new Response(json_encode(array('status'=>'ok')));
    }

}