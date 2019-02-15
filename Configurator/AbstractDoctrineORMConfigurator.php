<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Configurator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Idk\LegoBundle\Component\Component;
use Idk\LegoBundle\Lib\Pager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

use Idk\LegoBundle\Lib\QueryHelper;


use Idk\LegoBundle\Annotation\Entity\Field;

/**
 * An abstract LEGO configurator that can be used with the orm query builder
 */
abstract class AbstractDoctrineORMConfigurator extends AbstractConfigurator
{

    /**
     * @var Query
     */
    private $query = null;


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
/*
    public function getPager($page = 1,$nbPerPage = null, $unlimited = false)
    {
        $pager = new Pager($this->getQueryBuilder(), $page, $nbPerPage, $unlimited);
        return $pager;
    }*/

    /*
     *  @deprecated
     */
    public function getAllIterator()
    {
        return $this->getQuery()->getResult();
    }

    public function getQuery(){
        if($this->query == null) return $this->getQueryBuilder()->getQuery();
        return $this->query;
    }


    /**
     * @return Query|null
     * @deprecated
     */
    public function getQueryBuilder()
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('b');
        $this->adaptQueryBuilder($queryBuilder);
        foreach($this->getCurrentComponents() as $component){
            /* @var Component $component */
            $component->catchQueryBuilder($queryBuilder);
        }
        return $queryBuilder;
    }

    public function initQueryBuilderForComponent(Component $component): QueryBuilder{
        $queryBuilder = $this->getRepository()->createQueryBuilder('b');
        $this->adaptQueryBuilder($queryBuilder);
        foreach($this->getCurrentComponents() as $curComponent){
            /* @var Component $curComponent */
            if($curComponent->canCatchQuery($component) || $curComponent->getId() == $component->getId()) {
                $curComponent->catchQueryBuilder($queryBuilder);
            }
        }
        return $queryBuilder;
    }

    public function getItems(Component $component){
        return $this->initQueryBuilderForComponent($component)->getQuery()->getResult();
    }

    public function getRepository(){
        return  $this->getEntityManager()->getRepository($this->getRepositoryName());
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getConfiguratorBuilder()->getMetaEntityManager()->getEntityManager();
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



    public function getClassMetaData(): ClassMetadata{
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

    /* @TODO revers args $filedName, $item */
    public function getType($item,$fieldName){
        if(is_object($item) or $item === null){
            $classMetaData = $this->getEntityManager()->getClassMetaData(($item === null)? $this->getEntityName():get_class($item));
            foreach(explode('.', $fieldName) as $fieldn){
                if($classMetaData->hasAssociation($fieldn)) {
                    $mapping = $classMetaData->getAssociationMapping($fieldn);
                    $classMetaData = $this->getEntityManager()->getClassMetaData( $mapping['targetEntity']);
                    /*@ TODO return */
                }else{
                    return $classMetaData->getTypeOfColumn($fieldn);
                }
            }
        }
    }



    public function getAssociationClass($fieldname){
        if($this->getClassMetaData()->hasAssociation($fieldname)) {
            $mapping = $this->getClassMetaData()->getAssociationMapping($fieldname);
            return $mapping['targetEntity'] ?? null;
        }
        return null;
    }
/* TODO
    public function getClassMetaData(): ClassMetadata{
        return $this->getConfiguratorBuilder()->getMetaEntityManager()->getClassMetaData($this->getRepository());
    }
    public function getAssociationClass($fieldname){
        return $this->getConfiguratorBuilder()->getMetaEntityManager()->getAssociationClass($this->getRepository(), $fieldname);
    }
*/

}
