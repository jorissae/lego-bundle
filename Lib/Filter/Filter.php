<?php

namespace Idk\LegoBundle\Lib\Filter;

use Symfony\Component\HttpFoundation\Request;


/**
 * Filter
 */
class Filter
{
    /**
     * @var string $columnName
     */
    protected $columnName = null;

    /**
     * @var array
     */
    protected $filterDefinition = null;

    /**
     * @var string
     */
    protected $uniqueId = null;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param string $columnName       The column name
     * @param array  $filterDefinition The filter configuration
     * @param string $uniqueId         The unique id
     */
    public function __construct($columnName, $filterDefinition, $uniqueId)
    {
        $this->columnName       = $columnName;
        $this->filterDefinition = $filterDefinition;
        $this->uniqueId         = $uniqueId;
    }

    /**
     * @param Request $request
     */
    public function bindRequest($params)
    {
        /* @var FilterTypeInterface $type */
        $type = $this->filterDefinition;
        $type->setRequest($params);
        return $type->bindRequest($this->data, $this->uniqueId);
    }

    /**
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return FilterTypeInterface
     */
    public function getType()
    {
        return $this->filterDefinition;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->filterDefinition->getOptions();
    }

    /**
     * Apply the filter
     */
    public function apply()
    {
        $donnes = $this->getType()->init();
        $this->getType()->apply($this->getData(), $this->getUniqueId(),$donnes['alias'],$donnes['column']);
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }
}
