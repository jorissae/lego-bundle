<?php

namespace Idk\LegoBundle\Configurator;

use Idk\LegoBundle\Component\Component;
use Traversable;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

use Idk\LegoBundle\AdminList\FilterType\ORM\AbstractORMFilterType;
use Idk\LegoBundle\AdminList\Lib\QueryHelper;


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
     * @var PermissionDefinition
     */
    private $permissionDef = null;

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct($container)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->setContainer($container);
    }

    /**
     * Return the url to edit the given $item
     *
     * @param object $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        $params = array('id' => $item->getId());
        $params = array_merge($params, $this->getExtraParameters());

        return array(
            'path'	 => $this->getPathByConvention($this::SUFFIX_EDIT),
            'params' => $params
        );
    }

    /**
     * Get the delete url for the given $item
     *
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        $params = array('id' => $item->getId());
        if($this->isSubList()){
            $params['sublist'] = true;
        }
        $params = array_merge($params, $this->getExtraParameters());
        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_DELETE),
            'params' => $params
        );
    }



    /**
     * @param QueryBuilder $queryBuilder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->where('1=1');
    }

    public function subQuery(QueryBuilder $queryBuilder){
        $parentConfig = $this->getParentConfig();
        if($parentConfig){
            $entity = $parentConfig['item'];
            if($parentConfig['key']) $queryBuilder->andWhere('b.'.$parentConfig['key'].' = :parent_item')->setParameter('parent_item',$entity);
        }
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->getQuery()->getResult());
    }

    /**
     * @return array|Traversable
     */
    public function getItems()
    {
        return $this->getQuery()->getResult();
    }

    /**
     * Return an iterator for all items that matches the current filtering
     *
     * @return \Iterator
     */
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
            $this->subQuery($queryBuilder);
            $queryHelper = new QueryHelper();

            foreach($this->getIndexComponents() as $component){
                /* @var Component $component */
                $component->catchQueryBuilder($queryBuilder);
            }


            foreach($this->getRupteurs() as $k => $rupteur){
                if(count($this->currentRupteurs)){
                    if(in_array($rupteur->getKey(),$this->currentRupteurs)){
                        $rupteur->setActive(true);
                        $path = $queryHelper->getPath($queryBuilder,'b',$rupteur->getName());
                        $queryBuilder->addOrderBy($path['alias'].$path['column'], $rupteur->getOrder());
                        foreach($rupteur->getContentOrderBy() as $orderBy){
                            $path = $queryHelper->getPath($queryBuilder,'b',$orderBy[0]);
                            $queryBuilder->addOrderBy($path['alias'].$path['column'],$orderBy[1]);
                        }
                    } else {
                        $rupteur->setActive(false);
                    }
                }

            }

            // Apply sorting
            $dataClass = $this->em->getClassMetadata($this->getRepositoryName());
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
        $queryBuilder = $this->em
            ->getRepository($this->getRepositoryName())
            ->createQueryBuilder('b');
        return $queryBuilder;
    }

    /**
     * Get current permission definition.
     *
     * @return PermissionDefinition|null
     */
    public function getPermissionDefinition()
    {
        return $this->permissionDef;
    }

    /**
     * Set permission definition.
     *
     * @param PermissionDefinition $permissionDef
     *
     * @return AbstractAdminListConfigurator|AbstractDoctrineORMAdminListConfigurator
     */
    public function setPermissionDefinition(PermissionDefinition $permissionDef)
    {
        $this->permissionDef = $permissionDef;

        return $this;
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager($em)
    {
        $this->em = $em;
        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
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

    //request null for retrocompatibilite
    public function generateShowSubLists($controller,$item, $request = null){
        $sublists = $this->getShowSubLists();
        $return = array();
        foreach($sublists as $k => $sublist){
            $class = $sublist->getConfigurator();
            $subConfigurator = new $class($this->getEntityManager());
            $subConfigurator->setSubListUniqueName($sublist->getUniqueName());
            $subConfigurator->setContainer($this->container, $item);
            if($request){
                $subRequest = $request->duplicate($request->query->get($sublist->getId()),$request->request->get($sublist->getId()));
                $subConfigurator->bindRequest($subRequest);
            }
            $subConfigurator->setParentConfig(array('key'=>$sublist->getKey(),'item'=>$item));
            $sublist->setView($controller->get("lle_adminlist.factory")->createList($subConfigurator, $this->getEntityManager()));
            $return[$sublist->getName()] = $sublist;
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


    // return ClassMetadata
    public function getClass(){
        $em = $this->getEntityManager();
        return $em->getClassMetadata($this->getRepositoryName());
    }


    // return schema column array string
    public function getClassFields(){
        return $this->getClass()->getColumnNames();
    }
    //$em->getClassMetadata(get_class($attribut))->getName())

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

    public function getRootAttachementFolders($item,$codeZone = null){
        return $this->getEntityManager()->getRepository('LleAdminListBundle:AttachableFolder')->findRacineByClassAndItemId($this->getRepositoryName(),$item->getId(),$codeZone);

    }

    public function getRootAttachementFiles($item,$codeZone = null){
        return $this->getEntityManager()->getRepository('LleAdminListBundle:AttachableFile')->findRacineByClassAndItemId($this->getRepositoryName(),$item->getId(),$codeZone);

    }

    public function getSummaryAttachementFiles($item,$codeZone = null){
        return $this->getEntityManager()->getRepository('LleAdminListBundle:AttachableFile')->getSummaryByClassAndItemId($this->getRepositoryName(),$item->getId(),$codeZone);
    }

    public function getClassWorkflow(){
        if($this->getFieldWorkflow()){
            return $this->getClass()->getAssociationMapping($this->getLocalFieldWorkflow())['targetEntity'];
        }else{
            return null;
        }
    }

    public function decorateNewEntity($item, $request = null){
        $workClass = $this->getClassWorkflow();
        $em = $this->getEntityManager();
        if($workClass){
            $work = $em->getClassMetadata($workClass)->newInstance()->getDefault();
            $set = 'set'.ucfirst($this->getLocalFieldWorkflow());
            $defaultWork = $em->getRepository($workClass)->findOneBy(array($this->getWfFieldWorkflow() => trim($work)));
            $item->$set($defaultWork);
        }
        return $item;
    }

}
