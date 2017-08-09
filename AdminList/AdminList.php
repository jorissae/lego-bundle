<?php

namespace Idk\LegoBundle\AdminList;

use Idk\LegoBundle\AdminList\Configurator\AdminListConfiguratorInterface;

use Symfony\Component\HttpFoundation\Request;

use Pagerfanta\Pagerfanta;
use Idk\LegoBundle\AdminList\Field;
use Idk\LegoBundle\Twig\FilterTwigExtension;

/**
 * AdminList
 */
class AdminList
{

    /**
     * @var Request
     */
    protected $request = null;

    /**
     * @var AdminListConfiguratorInterface
     */
    protected $configurator = null;

    /**
     * @param AdminListConfiguratorInterface $configurator The configurator
     */
    public function __construct(AdminListConfiguratorInterface $configurator)
    {
        $this->configurator = $configurator;
        $configurator->build();
    }

    public function getListHeaderTemplate(){
        return $this->configurator->getListHeaderTemplate();
    }

    public function isXhrDelete(){
        return $this->configurator->isXhrDelete();
    }

    /**
     * @return AdminListConfiguratorInterface|null
     */
    public function getConfigurator()
    {
        return $this->configurator;
    }

    /**
     * @return FilterBuilder
     */
    public function getFilterBuilder()
    {
        return $this->configurator->getFilterBuilder();
    }

    public function hideItemAction(){
        return $this->configurator->hideItemAction();
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->configurator->bindRequest($request);
    }

    public function setFilter($filter)
    {
        $filter['filter'] = true;
        $request = new Request($filter);
        $this->configurator->bindRequest($request);
    }

    public function getExcel(array $filter = null){
        if($filter) $this->setFilter($filter);
        return $this->configurator->get("lle_adminlist.service.export")->createExcelSheet($this);
    }

    public function getExcelResponse(array $filter = null,$filename = null){
        $excel = $this->getExcel($filter);
        $response = $this->configurator->get("lle_adminlist.service.export")->createResponseForExcel($excel);
        $filename = ($filename)? $filename:sprintf('entries.%s', 'xlsx');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename=%s', $filename));
        return $response;
    }

    /**
     * @return Field[]
     */
    public function getColumns()
    {
        return $this->configurator->getFields();
    }

    /**
     * @return Field[]
     */
    public function getShowFields()
    {
        return $this->configurator->getShowFields();
    }

    /**
     * @return Onglet[]
     */
    public function getShowOnglets()
    {
        return $this->configurator->getShowOnglets();
    }

    /**
     * @return Group[]
     */
    public function getShowGroups()
    {
        return $this->configurator->getShowGroups();
    }

    /**
     * @return Group[]
     */
    public function getEditFormGroups()
    {
        return $this->configurator->getEditFormGroups();
    }

    /**
     * @return Group[]
     */
    public function getNewFormGroups()
    {
        return $this->configurator->getNewFormGroups();
    }

    /**
     * @return Field[]
     */
    public function getExportColumns()
    {
        return $this->configurator->getExportFields();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->configurator->getCount();
    }

    /**
     * @return array|null
     */
    public function getItems()
    {
        return $this->configurator->getItems();
    }

    /**
     * Return an iterator for all items that matches the current filtering
     *
     * @return \Iterator
     */
    public function getAllIterator()
    {
        return $this->configurator->getAllIterator();
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function hasSort($columnName = null)
    {
        if (is_null($columnName)) {
            return count($this->configurator->getSortFields()) > 0;
        }
        $dataClass = $this->configurator->getEntityManager()->getClassMetadata($this->configurator->getRepositoryName());
        $return = false;
        $fields = explode('.',$columnName);
        foreach($fields as $field){
            if($dataClass->hasField($field) or $dataClass->hasAssociation($field)){
                if($dataClass->hasAssociation($field)) {
                    $dataClass = $this->configurator->getEntityManager()->getClassMetadata($dataClass->getAssociationTargetClass($field));
                }
                $return =  in_array($columnName, $this->configurator->getSortFields());
            }
        }
        return $return;
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return $this->configurator->canEdit($item);
    }

    /**
     * @return bool
     */
    public function canEditInShow($item = null)
    {
        return $this->configurator->canEditInShow($item);
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->configurator->getEntityName();
    }

     /**
     * @return string
     */
    public function getRoleName()
    {
        return $this->configurator->getRoleName();
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return $this->configurator->canAdd();
    }

    /**
     * @return array
     */
    public function getIndexUrl()
    {
        return $this->configurator->getIndexUrl();
    }

    /**
     * @return array
     */
    public function getLogsUrl()
    {
        return $this->configurator->getLogsUrl();
    }

    /**
     * @return array
     */
    public function getLogUrl($item)
    {
        return $this->configurator->getLogUrl($item);
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return $this->configurator->getEditUrlFor($item);
    }

    public function getWorkflowUrlFor($item,$work)
    {
        return $this->configurator->getWorkflowUrl($item,$work);
    }

    public function getEditInPlaceUrl()
    {
        return $this->configurator->getEditInPlaceUrl();
    }

    public function getEditInPlaceAttrUrl()
    {
        return $this->configurator->getEditInPlaceAttrUrl();
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->configurator->getTitle();
    }

    public function getSubTitle()
    {
        return $this->configurator->getSubTitle();
    }

    public function isSubList(){
        return $this->configurator->isSubList();
    }

    public function getShowSubTitle($item)
    {
        return $this->configurator->getShowSubTitle($item);
    }

    public function getFiltersTitle()
    {
        return $this->configurator->getFiltersTitle();
    }

    public function getFiltersClass()
    {
        return $this->configurator->getFiltersClass();
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return $this->configurator->getDeleteUrlFor($item);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrl($itemOfParentList = null)
    {
        return $this->configurator->getAddUrl($itemOfParentList);
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return $this->configurator->canDelete($item);
    }

    /**
     * @return bool
     */
    public function canExport()
    {
        return $this->configurator->canExport();
    }

    /**
     * @return string
     */
    public function getExportUrl($itemOfParentList = null)
    {
        return $this->configurator->getExportUrl($itemOfParentList);
    }

    /**
     * @return string
     */
    public function getShowUrl($item,$column = null)
    {
        return $this->configurator->getShowUrl($item,$column);
    }

    /**
     * @param object|array $object    The object
     * @param string       $attribute The attribute
     *
     * @return mixed
     */
    public function getValue($object, $attribute)
    {
        return $this->configurator->getValue($object, $attribute);
    }

    /**
     * @param object|array $object    The object
     * @param string       $attribute The attribute
     *
     * @return string
     */
    public function getStringValue($object, $attribute)
    {
        return $this->configurator->getStringValue($object, $attribute);
    }

    public function editInplaceInputType($object,$coumnName,$class)
    {
        return $this->configurator->editInplaceInputType($object, $coumnName,$class);
    }

    public function listOptionsForCombobox($object, Field $field)
    {
        return $this->configurator->listOptionsForCombobox($object, $field);
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->configurator->getOrderBy();
    }


    public function getRupteurs(){
        return $this->configurator->getRupteurs();
    }

    /**
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->configurator->getOrderDirection();
    }

    /**
     * @return array
     */
    public function getItemActions()
    {
        return $this->configurator->getItemActions();
    }

    /**
     * @return bool
     */
    public function hasItemActions()
    {
        return $this->configurator->hasItemActions();
    }

    /**
     * @return bool
     */
    public function hasListActions()
    {
        return $this->configurator->hasListActions();
    }

    /**
     * @return array
     */
    public function getListActions()
    {
        return $this->configurator->getListActions();
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return $this->configurator->getBulkActions();
    }

    /**
     * @return bool
     */
    public function hasBulkActions()
    {
        return $this->configurator->hasBulkActions();
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        return $this->configurator->getPagerfanta();
    }

    public function isLoggable(){
        return $this->configurator->isLoggable();
    }

    public function showField($columnName){
        return $this->configurator->showField($columnName);
    }

    public function field($columnName){
        return $this->configurator->field($columnName);
    }

    public function custom(Field $field,$item){
        $value = $this->getValue($item, $field->getName());
        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);
        $twig->addExtension(new FilterTwigExtension());
        $render = $twig->render($field->getCustom(),array('item'=>$item,'label'=>$field->getName(),'value'=>$value));
        return $render;
    }

    public function getRepositoryName(){
        return $this->configurator->getRepositoryName();
    }

    public function getRootAttachementFiles($item,$zoneCode = null){
        return $this->configurator->getRootAttachementFiles($item,$zoneCode);
    }

    public function getSummaryAttachementFiles($item,$options = array()){
        return $this->configurator->getSummaryAttachementFiles($item,$options);
    }

    public function getRootAttachementFolders($item,$zoneCode = null){
        return $this->configurator->getRootAttachementFolders($item,$zoneCode);
    }

    public function getClass(){
        return $this->configurator->getClass();
    }

    public function getNewFormTheme(){
        return $this->configurator->getNewFormTheme();
    }

    public function getEditFormTheme(){
        return $this->configurator->getEditFormTheme();
    }

    public function label($word){
        return $this->configurator->label($word);
    }

    public function workflows($item,$user = null){
        return $this->configurator->workflows($item,$user);
    }

    public function classCssLine($item){
        return $this->configurator->classCssLine($item);
    }

    public function getItemActionUrl($item,$type,$id){
        return $this->configurator->getItemActionUrl($item,$type,$id);
    }

    public function getBulkActionUrl($type,$id){
        return $this->configurator->getBulkActionUrl($type,$id);
    }

    public function getListActionUrl($type,$id){
        return $this->configurator->getListActionUrl($type,$id);
    }

    public function getScriptTemplate(){
        return $this->configurator->getScriptTemplate();
    }

    public function rupteurs($item){
        $return = array();
        foreach($this->getRupteurs() as $rupteur){
            if($rupteur->isBreak($this,$item) && $rupteur->isActive()){
                $return[] = $rupteur;
            }
        }
        return $return;
    }

    public function getHtmlElements(){
        return $this->configurator->getHtmlElements();
    }

    public function valueFilter($id){
        return $this->configurator->valueFilter($id);
    }

    public function getType($item,$columnName){
        return $this->configurator->getType($item,$columnName);
    }

}
