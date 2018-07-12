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

final class BulkAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $type = $request->query->get('type');
        $ids = $request->request->get('ids');
        $em = $this->getEntityManager();
        $i=0;
        $entities= $configurator->getRepository()->createQueryBuilder('i')->where('i.id IN (:ids)')->setParameter('ids',$ids)->getQuery()->getResult();
        $msg = null;
        if($type == 'delete'){
            foreach($entities as $entity){
                $em->remove($entity);
                $i++;
            }
            $msg = $this->trans('lego.delete_entities', ['%nb%' => $i]);
        }
        $em->flush();
        return new JsonResponse(['status'=>'ok', 'message' => $msg]);
    }

}