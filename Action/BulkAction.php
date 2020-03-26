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

use Idk\LegoBundle\Component\ListItems;
use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Idk\LegoBundle\Service\Tag\BulkChain;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

final class BulkAction extends AbstractAction
{

    private $bulks;

    public function __construct(ConfiguratorBuilder $configuratorBuilder, BulkChain $bulks)
    {
        parent::__construct($configuratorBuilder);
        $this->bulks = $bulks;
    }

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);

        if($request->get('all')) {
            $items = $this->getAllItems($configurator, $request);
        }else {
            $items = $configurator->getRepository()->createQueryBuilder('i')->where('i.id IN (:ids)')->setParameter('ids',$request->get('ids'))->getQuery()->getResult();
        }
        $bulk = $this->bulks->getByMd5($request->get('type'));
        if($bulk->check($request)) {
            $bulk->execute($items, $request);
            return new JsonResponse(['status'=>'ok', 'message' => $this->trans($bulk->getSuccess()[0], $bulk->getSuccess()[1])]);
        } else {
            return new JsonResponse(['status'=>'nok', 'message' => $this->trans($bulk->getError()[0], $bulk->getError()[1])]);
        }

    }

    public function getAllItems($configurateur, $request){
        $response = $this->comunicateComponents($configurator, $request);
        if($response){
            return $response;
        }
        if($request->get('cid') !== '0') {
            $component = $configurator->getComponent($request->get('suffix_route'), $request->get('cid'));
        }else{
            $component = $configurator->getComponentByClass($request->get('suffix_route'), ListItems::class);
        }
        return $configurator->getItems($component);
    }

}
