<?php
namespace Idk\LegoBundle\Service\Tag;

use Idk\LegoBundle\FilterType\FilterTypeInterface;

class FilterChain
{

    private $filters = [];

    public function __construct(iterable $filters)
    {
        foreach($filters as $filter){
            $this->filters[get_class($filter)] = $filter;
        }
    }

    public function addFilter(FilterTypeInterface $filter)
    {
        $this->filters[get_class($filter)] = $filter;
    }

    public function getfilters():iterable{
        return $this->filters;
    }

    public function get($filterName, $fieldName, $filterConfig): FilterTypeInterface{
        $filter = clone $this->filters[$filterName];
        $filter->load($fieldName, $filterConfig);
        return $filter;
    }

    public function has($filterName): bool{
        return isset($this->filters[$filterName]);
    }
}
