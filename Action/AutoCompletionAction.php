<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AutoCompletionAction extends AbstractAction
{

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $em = $this->getEntityManager();
        $repo = $em->getRepository($configurator->getRepositoryName());
        $term = $request->query->get('term');
        $params = array();
        foreach($request->query->all() as $k => $parameter){
            if($k != 'term'){
                $params[$k] = $parameter;
            }
        }
        $return = array();
        if (method_exists($repo, "autoCompleteQuery")){
            $entities = $repo->autoCompleteQuery($term,$params)->getResult();
            foreach($entities as $entity){
                $return[] = array('label'=>$entity->__toString(),'value'=>$entity->getId());
            }
        } else if(method_exists($repo, "autoComplete")){
            $return = $repo->autoComplete($term, $params);
        } else {
            $fieldSearch = $configurator->getAutocompleteField();
            $cl = $configurator->getClassMetaData();
            $entities = $repo->createQueryBuilder('al')->where('al.'.$fieldSearch.' LIKE :term')->setParameter('term', '%'.$term.'%')->getQuery()->getResult();
            foreach($entities as $entity){
                $return[] = array('label'=>$cl->getFieldValue($entity,$fieldSearch),'value'=>$entity->getId());
            }
        }
        return new JsonResponse($return);
    }

}