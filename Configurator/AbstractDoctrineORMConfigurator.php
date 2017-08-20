<?php

namespace Idk\LegoBundle\Configurator;

use Idk\LegoBundle\Component\Component;
use Traversable;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

use Idk\LegoBundle\Lib\QueryHelper;


use Idk\LegoBundle\Annotation\Entity\Field;

/**
 * An abstract admin list configurator that can be used with the orm query builder
 */
abstract class AbstractDoctrineORMConfigurator extends AbstractConfigurator
{

    /**
     * @var Query
     */
    private $query = null;


    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct($container, AbstractConfigurator $parent = null)
    {
        parent::__construct($container, $parent);
    }


    /**
     * @param QueryBuilder $queryBuilder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->where('1=1');
    }


    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->getQuery()->getResult());
    }

    public function getItems()
    {
        return $this->getQuery()->getResult();
    }

    public function getAllIterator()
    {
        return $this->getQuery()->getResult();
    }


    /**
     * @return Query|null
     */
    public function getQuery()
    {
        if (is_null($this->query)) {
            $queryBuilder = $this->getQueryBuilder();
            $this->adaptQueryBuilder($queryBuilder);
            $queryHelper = new QueryHelper();

            foreach($this->getCurrentComponents() as $component){
                /* @var Component $component */
                $component->catchQueryBuilder($queryBuilder);
            }

            // Apply sorting
            $dataClass = $this->getClassMetaData();
            if (!empty($this->orderBy)) {
                $columnName = $this->orderBy;
                $pathInfo = $queryHelper->getPathInfo($this,$dataClass,$columnName);
                if($pathInfo['association']) $columnName.= '.id';
                $path = $queryHelper->getPath($queryBuilder,'b',$columnName);
                $orderBy = $path['alias'] . $path['column'];
                $queryBuilder->orderBy($orderBy, ($this->orderDirection == 'DESC' ? 'DESC' : 'ASC'));
            }
            $this->query = $queryBuilder->getQuery();
        }

        return $this->query;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->getRepository()->createQueryBuilder('b');
    }

    public function getRepository(){
        return  $this->getEntityManager()->getRepository($this->getRepositoryName());
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    public function listOptionsForCombobox($object,Field $line){
        $em = $this->getEntityManager();
        $edit = $line->getEditInPlace();
        $class = (isset($edit['class']))? $edit['class']:$this->getClass()->getAssociationMapping($line->getName())['targetEntity'];
        $method = (isset($edit['method']))? $edit['method']:'findAll';
        if(isset($edit['object-in-argument']) and $edit['object-in-argument']){
            $list = $em->getRepository($class)->$method($object);
        } else {
            $list = $em->getRepository($class)->$method();
        }
        $return = array();
        if($list){
            foreach($list as $entity){
                $return[$entity->getId()] = $entity->__toString();
            }
        }
        return $return;
    }

    public function getAutocompleteField(){
        $fields = $this->getClassFields();
        if(count($fields)>1){
            $fieldSearch = null;
            $fieldSearch = (in_array('firstname',$fields))? 'firstname':$fieldSearch;
            $fieldSearch = (in_array('lastname',$fields))? 'lastname':$fieldSearch;
            $fieldSearch = (in_array('name',$fields))? 'name':$fieldSearch;
            $fieldSearch = (in_array('nom',$fields))? 'nom':$fieldSearch;
            $fieldSearch = (in_array('prenom',$fields))? 'prenom':$fieldSearch;
            $fieldSearch = (in_array('label',$fields))? 'label':$fieldSearch;
            $fieldSearch = (in_array('libelle',$fields))? 'libelle':$fieldSearch;
            $fieldSearch = ($fieldSearch == null)? $fields[1]:$fieldSearch;
            return $fieldSearch;
        }else{
            return $fields[0];
        }
    }



    public function getClassMetaData(){
        $em = $this->getEntityManager();
        return $em->getClassMetadata($this->getRepositoryName());
    }

    public function getClass(){
        return $this->getRepositoryName();
    }

    public function newInstance(){
        return $this->getClassMetaData()->newInstance();
    }


    public function getClassFields(){
        return $this->getClassMetaData()->getColumnNames();
    }

    public function find($id){
        return $this->getEntityManager()->getRepository($this->getRepositoryName())->find($id);
    }

    public function isLoggable(){
        /*if(class_exists('Gedmo\Loggable\Entity\LogEntry')){
            $repo = $this->getEntityManager()->getRepository('Gedmo\Loggable\Entity\LogEntry');
            return false;
            return ($repo->findOneBy(array('objectClass'=>$this->getClass()->getName())));
        }*/
        return false;

    }

    public function getType($item,$columnName){

        if(is_object($item)){
            $return = $this->getClassMetadata()->getTypeOfColumn($columnName);
            return $return;
        }
    }


}
