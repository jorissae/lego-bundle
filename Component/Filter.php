<?php

namespace Idk\LegoBundle\Component;

use Idk\LegoBundle\AdminList\FilterBuilder;
use Idk\LegoBundle\FilterType\ORM\AbstractORMFilterType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use Idk\LegoBundle\AdminList\Filter as Fi;


class Filter extends Component{

    private $filterBuilder;

    protected function init(){
        foreach($this->get('lego.service.meta_entity_manager')->generateFilters($this->getConfigurator()->getEntityName(), null) as $filter){
            /* @var \Idk\LegoBundle\Annotation\Entity\Filter\AbstractFilter $filter */
            $this->getFilterBuilder()->add($filter->newInstanceOfType());
        }
        return $this;
    }

    public function add($name, $class, $options){
        $reflectionClass = new \ReflectionClass($class);
        $this->getFilterBuilder()->add($reflectionClass->newInstance($name, $options));
        return $this;
    }

    public function getFilterBuilder()
    {
        if (is_null($this->filterBuilder)) {
            $this->filterBuilder = new FilterBuilder($this->getConfigurator(),get_class($this->getConfigurator()));
        }

        return $this->filterBuilder;
    }

    protected function requiredOptions(){
        return [];
    }

    public function getTemplate($name = 'index'){
        return 'IdkLegoBundle:Component\\FilterComponent:'.$name.'.html.twig';
    }

    public function getTemplateParameters(){
        return ['filter' => $this->getFilterBuilder()];
    }

    public function defaultValueFilter(){
        return array();
    }

    public function valueFilter($id){
        return $this->getFilterBuilder()->getValueFilter($id);
    }

    public function catchQuerybuilder(QueryBuilder $queryBuilder){
        $filters = $this->getFilterBuilder()->getCurrentFilters();
        /* @var Fi $filter */
        foreach ($filters as $filter) {
            /* @var AbstractORMFilterType $type */
            $type = $filter->getType();
            $type->setQueryBuilder($queryBuilder);
            $filter->apply();
        }
    }

    public function bindRequest(Request $request){
        $this->request = $request;
        $query      = $request->query;
        $session    = $request->getSession();
        $adminListName = 'listconfig_' . $request->get('_route');



        $this->page             = ($query->get('page'))? (int)$query->get('page'):1;
        $this->orderBy          = preg_replace('/[^[a-zA-Z0-9\_\.]]/', '', $query->get('orderBy', $this->getConfigurator()->getOrderBy()));
        $this->orderDirection   = $query->getAlpha('orderDirection', $this->getConfigurator()->getOrderDirection());
        if($query->get('rupteurs')){
            $this->currentRupteurs = explode('/',$request->query->get('rupteurs'));
        }

        // there is a session and the filter param is not set
        if ($session and $session->has($adminListName)) {
            $adminListSessionData = $request->getSession()->get($adminListName);
            if(!$query->has('filter')){
                if (!$query->has('orderBy') and $adminListSessionData['orderBy']) {
                    $this->orderBy = $adminListSessionData['orderBy'];
                }

                if (!$query->has('orderDirection') and $adminListSessionData['orderDirection']) {
                    $this->orderDirection = $adminListSessionData['orderDirection'];
                }
            }
        }


        // save current parameters
        if($session){
            $session->set($adminListName, array(
                'page'              => $this->page,
                'orderBy'           => $this->orderBy,
                'orderDirection'    => $this->orderDirection,
            ));
        }

        $this->getFilterBuilder()->bindRequest($request,$this->defaultValueFilter());
    }
}
