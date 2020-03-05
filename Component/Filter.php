<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Component;

use Idk\LegoBundle\Lib\Filter\FilterBuilder;
use Idk\LegoBundle\FilterType\ORM\AbstractORMFilterType;
use Idk\LegoBundle\Service\MetaEntityManager;
use Idk\LegoBundle\Service\MetaEntityManagerInterface;
use Idk\LegoBundle\Service\Tag\FilterChain;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use Idk\LegoBundle\Lib\Filter\Filter as Fi;


class Filter extends Component{

    private $filterBuilder;
    private $mem;
    private $components = [];
    private $urlParams = [];
    private $filterChain;

    public function __construct(MetaEntityManager $mem, FilterChain $filterChain){
        $this->mem = $mem;
        $this->filterChain = $filterChain;
    }

    protected function init(){
        foreach($this->mem->generateFilters($this->getConfigurator()->getEntityName(), $this->getOption('fields')) as $filter){
            /* @var \Idk\LegoBundle\Annotation\Entity\Filter\AbstractFilter $filter */
            $filterType = $this->filterChain->get($filter->getClassNameType(), $filter->getName(), $filter->getOptions());
            $this->getFilterBuilder()->add($filterType);
        }
        return $this;
    }

    public function add($name, $class, $options){
        $reflectionClass = new \ReflectionClass($class);
        $this->getFilterBuilder()->add($reflectionClass->newInstance($name, $options));
        return $this;
    }

    public function addComponent(Component $component){
        $this->components[] = $component;
        $this->addCanCatchQuery($component);
    }

    public function getComponents(){
        return $this->components;
    }

    public function getFilterBuilder()
    {
        if (is_null($this->filterBuilder)) {
            $this->filterBuilder = new FilterBuilder($this->getConfigurator(),$this->getId());
        }

        return $this->filterBuilder;
    }

    protected function requiredOptions(){
        return [];
    }

    public function getTemplate($name = 'index'){
        return '@IdkLego/Component/FilterComponent/'.$name.'.html.twig';
    }

    public function getTemplateParameters(){
        return ['filter' => $this->getFilterBuilder(), 'component' => $this];
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

    public function getPath(string $suffix = 'component', $params = []){
        return parent::getPath($suffix, array_merge($this->urlParams,$params));
    }

    public function bindRequest(Request $request){
        parent::bindRequest($request);
        if ($request->get('id')) {
            $this->urlParams = ['id' => $request->get('id')];
        }
        $this->getFilterBuilder()->bindRequest($request, $this->defaultValueFilter());
        //$this->setComponentSessionStorages($storage);
    }

    public function initWithComponents(iterable $components): void{
        if(\count($this->components) === 0){
            foreach ($components as $c) {
                if ($c instanceof ListItems) {
                    $this->addComponent($c);
                    return;
                }
            }
        }
    }
}
