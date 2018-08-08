<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Lib\Filter;

use Idk\LegoBundle\FilterType\AbstractFilterType;
use Symfony\Component\HttpFoundation\Request;



class FilterBuilder
{

    /**
     * @var array
     */
    private $filters = array();

    /**
     * @var Filter[]
     */
    private $currentFilters = array();

    /**
     * @var array
     */
    private $currentParameters = array();

    private $configurator;

    private $uniqueName = null;


    public function __construct($configurator,$uniqueName){
        $this->configurator = $configurator;
        $this->uniqueName = $uniqueName;
    }

    public function setUniqueName($uniqueName){
        $this->uniqueName = $uniqueName;
    }
    /**
     * @param string              $columnName The column name
     * @param FilterTypeInterface $type       The filter type
     * @param string              $filterName The name of the filter
     * @param array               $options    Options
     *
     * @return FilterBuilder
     */
    public function add(AbstractFilterType $filter)
    {
        $this->filters[$filter->getName()] = $filter;
        return $this;
    }

    public function setConfigurator($configurator){
        $this->configurator = $configurator;
    }

    /**
     * @param string $columnName
     *
     * @return mixed|null
     */
    public function get($columnName)
    {
        if (isset($this->filters[$columnName])) {
            return $this->filters[$columnName];
        }

        return null;
    }

    /**
     * @param string $columnName
     *
     * @return FilterBuilder
     */
    public function remove($columnName)
    {
        if (isset($this->filters[$columnName])) {
            unset($this->filters[$columnName]);
        }

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function has($columnName)
    {
        return isset($this->filters[$columnName]);
    }

    public function getValueFilter($id){
        $ids = explode('[',$id);
        if(count($ids) > 1){
            if(isset($this->currentParameters[$ids[0]]) and isset($this->currentParameters[$ids[0]][str_replace(']','',$ids[1])])){
                return $this->currentParameters[$ids[0]][str_replace(']','',$ids[1])];
            }
        }else{
            if(isset($this->currentParameters[$id])) return $this->currentParameters[$id];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    public function isHidden(){

        foreach($this->filters as $k => $filter){
            if(!$filter->isHidden()) return false;
        }
        return true;
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request,$defaultValue = array())
    {
        $filterColumnNames = array();
        foreach($this->filters as $k => $filter){
            $filterColumnNames[] = $k;
        }
        $filterBuilderName = 'filter_' .  $this->uniqueName;
        if($request->hasSession() and is_array($request->getSession()->get($filterBuilderName))){
            $this->currentParameters = $request->getSession()->get($filterBuilderName);
        }
        if($request->query->has('filter')){
            $this->currentParameters = $request->query->all();
        }
        if($request->request->has('filter') && 'filter_'.$request->request->get('filter') === $filterBuilderName){
            $this->currentParameters = $request->request->all();
        }
        if ($request->query->has('reset') && 'filter_'.$request->query->get('reset') === $filterBuilderName) {
            $this->currentParameters = array();
        }


        if(count($this->currentParameters) == 0){
            $this->currentParameters = $defaultValue ;
        } 
        if($request->hasSession()){
            $request->getSession()->set($filterBuilderName, $this->currentParameters);
        }

        if (isset($filterColumnNames)) {
            $filterColumnNames = array_unique($filterColumnNames);
            $index = 0;
            foreach ($filterColumnNames as $filterColumnName) {
                $uniqueId = $filterColumnName;
                $filter = new Filter($filterColumnName, $this->get($filterColumnName), $uniqueId);
                if ($filter->bindRequest($this->currentParameters) === true) {
                    $this->currentFilters[] = $filter;
                }
                $index++;
            }
        }
    }

    /**
     * @return array
     */
    public function getCurrentParameters()
    {
        return $this->currentParameters;
    }

    /**
     * @return Filter[]
     */
    public function getCurrentFilters()
    {
        return $this->currentFilters;
    }
}
