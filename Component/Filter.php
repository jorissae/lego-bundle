<?php

namespace Idk\LegoBundle\Component;

use Idk\LegoBundle\Lib\Filter\FilterBuilder;
use Idk\LegoBundle\FilterType\ORM\AbstractORMFilterType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use Idk\LegoBundle\Lib\Filter\Filter as Fi;


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
        return [];
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
        parent::bindRequest($request);
        $this->getFilterBuilder()->bindRequest($request,$this->defaultValueFilter());
    }
}
