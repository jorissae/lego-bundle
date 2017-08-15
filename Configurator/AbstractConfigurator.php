<?php

namespace Idk\LegoBundle\Configurator;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use Idk\LegoBundle\AdminList\FilterType\FilterTypeInterface;
use Idk\LegoBundle\AdminList\FilterBuilder;
use Idk\LegoBundle\Annotation\Entity\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Idk\LegoBundle\AdminList\SubList;
use Idk\LegoBundle\AdminList\Onglet;
use Idk\LegoBundle\AdminList\Rupteur;
use Idk\LegoBundle\AdminList\HtmlElement;
use Idk\LegoBundle\AdminList\FormField;
use Idk\LegoBundle\AdminList\Group;
use Idk\LegoBundle\AdminList\Actions\ItemAction;
use Idk\LegoBundle\AdminList\Actions\BulkAction;
use Idk\LegoBundle\AdminList\Actions\ListAction;


/**
 * Abstract admin list configurator, this implements the most common functionality from the AdminListConfiguratorInterface
 */
abstract class AbstractConfigurator
{
    const SUFFIX_ADD = 'add';
    const SUFFIX_EDIT = 'edit';
    const SUFFIX_EXPORT = 'export';
    const SUFFIX_DELETE = 'delete';
    const SUFFIX_ITEMACTION = 'item';
    const SUFFIX_LISTACTION = 'alist';
    const SUFFIX_BULKACTION = 'bulk';
    const SUFFIX_SHOW = 'show';
    const SUFFIX_EDIT_IN_PLACE = 'edit_in_place';
    const SUFFIX_EDIT_IN_PLACE_ATTR = 'edit_in_place_attribut';
    const SUFFIX_LOGS = 'logs';
    const SUFFIX_LOG = 'log';
    const SUFFIX_WORKFLOW = 'wf';


    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var Field[]
     */
    private $fields = array();

    /**
     * @var Field[]
     */
    private $showFields = array();

    /**
     * @var FormField[]
     */
    private $editFormFields = array();



    /**
     * @var FormField[]
     */
    private $newFormFields = array();

    /**
     * @var Field[]
     */
    private $exportFields = array();

    /**
     * @var ItemActionInterface[]
     */
    private $itemActions = array();

    /**
     * @var ListActionInterface[]
     */
    private $listActions = array();

    /**
     * @var BulkActionInterface[]
     */
    private $bulkActions = array();


    /**
     * @var Rupteur[]
     */
    private $rupteurs = array();

    /**
     * @var AbstractType
     */
    private $type = null;

    /**
     * @var AbstractType
     */
    private $editType = null;

    /**
     * @var AbstractType
     */
    private $newType = null;




    /**
     * @var string
     */
    private $showTemplate = 'IdkLegoBundle:Default:show.html.twig';

    /**
     * @var string
     */
    private $logTemplate = 'IdkLegoBundle:Default:log.html.twig';

    /**
     * @var string
     */
    private $logsTemplate = 'IdkLegoBundle:Default:logs.html.twig';

    /**
     * @var string
     */
    private $addTemplate = 'IdkLegoBundle:Default:add.html.twig';

    private $formTemplate = 'IdkLegoBundle:Default:_form.html.twig';

    private $scriptTemplate = 'IdkLegoBundle:Default:script.html.twig';

    /**
     * @var string
     */
    private $editTemplate = 'IdkLegoBundle:Default:edit.html.twig';

    /**
     * @var string
     */
    private $deleteTemplate = 'IdkLegoBundle:Default:delete.html.twig';

    /**
     * @var FilterBuilder
     */
    private $filterBuilder = null;

    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var string
     */
    protected $orderBy = '';

    /**
     * @var string
     */
    protected $orderDirection = '';

    protected $parentConfig = null;

    protected $currentShowGroup = -1;
    protected $currentEditFormGroup = -1;
    protected $currentNewFormGroup = -1;

    protected $showGroups = array();
    protected $defaultOptionShowGroup = array();
    protected $editFormGroups = array();
    protected $newFormGroups = array();

    protected $showSubLists = array();

    protected $showOnglets = array();

    protected $currentRupteurs = array();

    protected $container;

    protected $htmlElements = array();

    private $isBuild = false;

    private $subListUniqueName = null;

    protected $sublistParentItem = null;

    public function getSublistParentItem(){
        return ($this->isSubList())? $this->sublistParentItem:null;
    }

    /**
     * Return current bundle name.
     *
     * @return string
     */
    abstract public function getBundleName();

    /**
     * Return current entity name.
     *
     * @return string
     */
    //abstract public function getEntityName();

    public function build(){
        if($this->isBuild == false){
            $this->isBuild = true;
            $this->buildIndex();
            $this->buildFilters();
            $this->buildExportFields();
            $this->showFields();
            $this->showSubLists();
            $this->showOnglets();
            $this->editFormFields();
            $this->newFormFields();
            $this->formFields();
            $this->buildItemActions();
            $this->buildListActions();
            $this->buildBulkActions();
            $this->buildRupteurs();
            $this->buildHtml();
        }
    }

    public function setSubListUniqueName($subListUniqueName){
        $this->subListUniqueName = $subListUniqueName;
        $this->getFilterBuilder()->setUniqueName($this->subListUniqueName);
    }

    public function isSubList(){
        return ($this->subListUniqueName != null);
    }

    public function reBuild(){
        $this->resetBuilds();
        $this->build();
    }

    /**
     * Reset all built members
     */
    public function resetBuilds()
    {
        if($this->isBuild == true){
            $this->isBuild = false;
            $this->fields = array();
            $this->showFields = array();
            $this->showOnglets = array();
            $this->showGroups = array();
            $this->formFields = array();
            $this->exportFields = array();
            $this->filterBuilder = null;
            $this->itemActions = array();
            $this->listActions = array();
            $this->bulkActions = array();
            $this->currentShowGroup = -1;
            $this->currentEditFormGroup = -1;
            $this->currentNewFormGroup = -1;
            $this->editFormGroups = array();
            $this->newFormGroups = array();
            $this->showSubLists = array();
            $this->showOnglets = array();
        }
    }

    public function setContainer($container,$sublistParentItem = null){
        $this->sublistParentItem = $sublistParentItem;
        $this->container = $container;
        $this->build();
    }

    public function get($id)
    {
        if($this->container){
            return $this->container->get($id);
        } else {
            throw new \Exception('Attention votre controller n\'a pas setter le container (new monConfigurator($this->container);)');
        }

    }

    public function getPrefixeRoleName() {
        return null;
    }

    public function getRoleName() {
        return $this->getPrefixeRoleName().$this->getEntityName();
    }

    public function getTitle() {
        return $this->getEntityName();
    }

    public function getSubTitle() {
        return "";
    }

    public function getShowSubTitle($item) {
        return "";
    }

    public function getRupteurs(){
        return $this->rupteurs;
    }

    public function getFiltersTitle() {
        return "Rechercher";
    }

    public function getFiltersClass() {
        return "col-md-12";
    }

    public function addShowSubList($configurator,$options){
        $sublist = new SubList($this,$configurator,$options);
        $this->showSubLists[] = $sublist;
    }

    public function addShowOnglet($name,$options){
        $onglet = new Onglet($name,$options);
        $this->showOnglets[] = $onglet;
    }

    public function getShowSubLists(){
        return $this->showSubLists;
    }

    public function getShowOnglets(){
        return $this->showOnglets;
    }

    public function hideItemAction(){
        return false;
    }

    /**
     * Return default repository name.
     *
     * @return string
     */
    public function getRepositoryName()
    {
        return $this->getEntityName();
    }

    /**
     * Configure the fields you can filter on
     */
    public function buildFilters()
    {
    }

    public function buildExportFields()
    {
    }

    /**
     * Configure the show fields you can show
     */
    public function showFields()
    {
    }

    /**
     * Configure the show fields you can show
     */
    public function showSubLists()
    {
    }

    /**
     * Configure the show fields you can show
     */
    public function showOnglets()
    {
    }

    /**
     * Configure the actions for each line
     */
    public function buildItemActions()
    {
    }

    /**
     * Configure the actions that can be executed on the whole list
     */
    public function buildListActions()
    {
    }

    public function buildBulkActions()
    {
    }

    public function buildRupteurs()
    {
    }

    public function buildHtml()
    {
    }



    /**
     * Configure the types of items you can add
     *
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array()) //deprecated
    {
        return null;
    }

    public function getAddUrl($itemOfParentList = null){
        $params = $this->getExtraParameters();
        if($this->getAddUrlFor()) return $this->getAddUrlFor($params); //compatibiliter ASC
        if($itemOfParentList){
            $params['id'] = $itemOfParentList->getId();
            $params['sublist'] = true;
        }

        $friendlyName = explode("\\", $this->getEntityName());
        $friendlyName = array_pop($friendlyName);
        $re = '/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/';
        $a = preg_split($re, $friendlyName);
        $superFriendlyName = implode(' ', $a);

        return array(
            $superFriendlyName => array('path'   => $this->getPathByConvention($this::SUFFIX_ADD),
                                        'params' => $params)
        );
    }

    /**
     * Get the url to export the listed items
     *
     * @return array
     */
    public function getExportUrl($itemOfParentList = null)
    {
        $params = $this->getExtraParameters();
        
        if($itemOfParentList){
            $params['id'] = $itemOfParentList->getId();
            $params['sublist'] = true;
        }
        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_EXPORT),
            'params' => array_merge(array('_format' => 'csv'), $params)
        );
    }

    public function getWorkflowUrl($item,$work)
    {
        $params = $this->getExtraParameters();

        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_WORKFLOW),
            'params' => array_merge($params,array('id'=>$item->getId(),'work'=>$work))
        );
    }

    public function getItemActionUrl($item,$type,$id){
        $params = $this->getExtraParameters();
        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_ITEMACTION),
            'params' => array_merge($params,array('id'=>$item->getId(),'type'=>$type,'ida'=>$id))
        );
    }

    public function getListActionUrl($type,$id){
        $params = $this->getExtraParameters();
        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_LISTACTION),
            'params' => array_merge($params,array('ida'=>$id,'type'=>$type))
        );
    }

    public function getBulkActionUrl($type,$id){
        $params = $this->getExtraParameters();
        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_BULKACTION),
            'params' => array_merge($params,array('ida'=>$id,'type'=>$type))
        );
    }


    public function getShowUrl($item,$column = null)
    {
        $params = $this->getExtraParameters();
        $path = null;
        //if(method_exists($item, 'getSlug'))  $params['slug'] = $elm->getSlug();
        if($column and is_array($column->getLinkTo())){
            $link = $column->getLinkTo();
            $elm = $this->getValue($item,$column->getName());
            if(is_object($elm)){
                $paramsConf = array();
                foreach($link['params'] as $param){
                    $paramsConf[$param] = $this->getValue($elm,$param);
                }
                $params = array_merge($paramsConf, $params);
                $path = $link['route'];
            }else{
                $paramsConf = array();
                foreach($link['params'] as $k => $param){
                    if(substr($k,0,1) == '*'){
                        $paramsConf[str_replace('*','',$k)] = $param;
                    }else {
                        $paramsConf[$k] = $this->getValue($item, $param);
                    }
                    //si un champ requi est null on retourne false
                    if(isset($link['required']) and in_array($k,$link['required']) and $paramsConf[$k] === null) return false;
                }
                $params = array_merge($paramsConf, $params);
                $path = $link['route'];
            }
        }else if($column and $column->getLinkTo() != 'self'){
            $path = $column->getLinkTo();
            $elm = $this->getValue($item,$column->getName());
            $params = array_merge(array('id' => $item->getId()), $params);
        }else{
            $path = $this->getPathByConvention($this::SUFFIX_SHOW);
            $params = array_merge(array('id' => $item->getId()), $params);
        }
        if(!$path) return false;

        //if(isset($params['slug'])) unset($params['id']);
        return array(
            'path' => $path,
            'params' => $params,
        );
    }

    public function getEditInPlaceUrl()
    {

        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_EDIT_IN_PLACE),
        );
    }

    public function getEditInPlaceAttrUrl()
    {

        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_EDIT_IN_PLACE_ATTR),
        );
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrl()
    {
        $params = $this->getExtraParameters();

        return array(
            'path' => $this->getPathByConvention(),
            'params' => $params
        );
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getLogsUrl()
    {
        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_LOGS),
        );
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getLogUrl($item)
    {
        return array(
            'path' => $this->getPathByConvention($this::SUFFIX_LOG),
            'params' => array('id' => $item->getId())
        );
    }

    /**
     * @param object $entity
     *
     * @throws InvalidArgumentException
     *
     * @return AbstractType
     */
    public function getAdminType($entity)
    {
        if (!is_null($this->type)) {
            return $this->type;
        }

        if (method_exists($entity, "getAdminType")) {
            return $entity->getAdminType();
        }

        throw new InvalidArgumentException("You need to implement the getAdminType method in " . get_class(
            $this
        ) . " or " . get_class($entity));
    }

    public function getEditAdminType($entity){
        if(!is_null($this->editType)) {
            return $this->editType;
        }
        return $this->getAdminType($entity);
    }

    public function getNewAdminType($entity){
        if(!is_null($this->newType)) {
            return $this->newType;
        }
        return $this->getAdminType($entity);
    }

    /**
     * @param AbstractType $type
     *
     * @return AbstractAdminListConfigurator
     */
    public function setAdminType($type)
    {
        $this->type = $type;
        $this->newType = $type;
        $this->editType = $type;

        return $this;
    }

    public function setNewAdminType(AbstractType $type)
    {
        $this->newType = $type;

        return $this;
    }

    public function setEditAdminType(AbstractType $type)
    {
        $this->editType = $type;

        return $this;
    }

    /**
     * @param object|array $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return true;
    }

    /**
     * @param object|array $item
     *
     * @return bool
     */
    public function canShow($item)
    {
        return true;
    }

    /**
     * Configure if it's possible to delete the given $item
     *
     * @param object|array $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return true;
    }

    /**
     * Configure if it's possible to edit item in his show's view
     *
     * @return bool
     */
    public function canEditInShow($item = null)
    {
        return true;
    }

    /**
     * Configure if it's possible to edit item in his show's view
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'lol';
    }

    /**
     * Configure if it's possible to add new items
     *
     * @return bool
     */
    public function canAdd()
    {
        return true;
    }

    /**
     * Configure if it's possible to add new items
     *
     * @return bool
     */
    public function canExport()
    {
        return false;
    }

    /**
     * @param string $name     The field name
     * @param string $options   The array options
     * @param string $sort     Sortable column or not
     * @param string $template The template
     * @param string $link_to  Url show entity
     *
     * @return AbstractAdminListConfigurator
     */
    public function addField($name, $options = [])
    {

        $field = new Field($options);
        $field->setName($name);
        $this->fields[] = $field;
        return $this;
    }

    public function setOptionField($name,$key,$value){
        $field = $this->fields[strtolower($name)];
        $field->set($key,$value);
        $this->fields[strtolower($name)] = $field;
    }


    public function removeField($name){
        unset($this->fields[strtolower($name)]);
    }

    public function addHtml($where,$src,$type,$options = array()){
        $this->htmlElements[] = new HtmlElement($where,$type,$src,$options);
    }

    public function addController($where,$src,$options = array()){
        $this->addHtml($where,$src,'controller',$options);
    }

    public function addTemplate($where,$src,$options = array()){
        $this->addHtml($where,$src,'template',$options);
    }

    public function getHtmlElements(){
        return $this->htmlElements;
    }


    public function addRupteur($fieldName, $options = array()){
        $rupteur = new Rupteur($fieldName,$options);
        foreach($this->rupteurs as $r){
            $r->addChild($rupteur);
        }
        $this->rupteurs[] = $rupteur;
    }

    public function addEditFormField($name, $options = array())
    {
        if ($this->currentEditFormGroup < 0) $this->addEditFormGroup(12);
        $field = new FormField($name, $options);
        $group = $this->editFormGroups[$this->currentEditFormGroup];
        $group->add($field);
        $this->editFormFields[strtolower($name)] = $field;
        return $this;
    }

    public function addNewFormField($name, $options = array())
    {
        if ($this->currentNewFormGroup < 0) $this->addNewFormGroup(12);
        $field = new FormField($name, $options);
        $group = $this->newFormGroups[$this->currentNewFormGroup];
        $group->add($field);
        $this->newFormFields[strtolower($name)] = $field;
        return $this;
    }

    public function addFormField($name, $options = array())
    {
        $this->addNewFormField($name,$options);
        $this->addEditFormField($name,$options);
        return $this;
    }

    public function addFormFields($names){
        foreach($names as $name) $this->addFormField($name);
    }

    public function addEditFormFields($names){
        foreach($names as $name) $this->addEditFormField($name);
    }

    public function addNewFormFields($names){
        foreach($names as $name) $this->addNewFormField($name);
    }

    private function generateGroup($cols,$options){
        if (!is_array($cols)) {
            $cols = array('lg'=>$cols);
        }
        $cols['lg'] = (isset($cols['md']))? $cols['md']:4;
        $cols['md'] = (isset($cols['md']))? $cols['md']:6;
        $cols['sm'] = (isset($cols['md']))? $cols['md']:6;
        $cols['xs'] = (isset($cols['md']))? $cols['lg']:12;
        return new Group($cols,array_merge($this->defaultOptionShowGroup,$options));
    }

    public function addShowGroup($cols, $options = array()) {
        $this->showGroups[] = $this->generateGroup($cols,$options);
        $this->currentShowGroup++;
    }

    public function setDefaultOptionShowGroup($options){
        $this->defaultOptionShowGroup = $options;
    }

    public function addFormGroup($cols, $options = array()) {
        $this->addEditFormGroup($cols,$options);
        $this->addNewFormGroup($cols,$options);
    }

    public function addEditFormGroup($cols, $options = array()) {
        $this->editFormGroups[] = $this->generateGroup($cols,$options);
        $this->currentEditFormGroup++;
    }

    public function addNewFormGroup($cols, $options = array()) {
        $this->newFormGroups[] = $this->generateGroup($cols,$options);
        $this->currentNewFormGroup++;
    }
    /**
     * @param string $name     The field name
     * @param string $options   The array options
     *
     * @return AbstractAdminListConfigurator
     */
    public function addShowField($name, $options = array())
    {
        if ($this->currentShowGroup < 0) $this->addShowGroup(12);
        if(is_array($options)){
            $field = new Field($name, $options);
        }else{
            $field = new Field($name, array('label'=>$options));
        }
        $group = $this->showGroups[$this->currentShowGroup];
        $group->add($field);
        $this->showFields[strtolower($name)] = $field;
        return $this;
    }

    /**
     * @param string $name     The field name
     * @param string $options   The array options
     *
     * @return AbstractAdminListConfigurator
     */
    public function addExportField($name, $options = array())
    {
        if(is_array($options)){
            $this->exportFields[] = new Field($name, $options);
        }else{
            $this->exportFields[] = new Field($name, array('label'=>$options));
        }

        return $this;
    }

    public function addExportRealField(Field $field){
        $this->exportFields[] = $field;
        return $this;
    }

    /**
     * @param string              $columnName The column name
     * @param FilterTypeInterface $type       The filter type
     * @param string              $filterName The name of the filter
     * @param array               $options    Options
     *
     * @return AbstractAdminListConfigurator
     */


    public function addMainFilter($label, FilterTypeInterface $type = null, $options = array())
    {
        $type->setEm($this->em);
        if(!is_array($options)){
            $label = $options;
            $options = array();
        }
        $this->getFilterBuilder()->add($label, $type, $options);

        return $this;
    }
    /**
     * @return int
     */
    public function getLimit()
    {
        if($this->isSubList()) {
            return null;
        }
        return 25;
    }

    /**
     * @return array
     */
    public function getSortFields()
    {
        $array = array();
        foreach ($this->getFields() as $field) {
            if ($field->isSortable()) {
                $array[] = $field->getName();
            }
        }

        return $array;
    }



    /**
     * @return Field[]
     */
    public function getShowFields()
    {
        return $this->showFields;
    }

    /**
     * @return Group[]
     */
    public function getShowGroups()
    {
        return $this->showGroups;
    }

    /**
     * @return Group[]
     */
    public function getEditFormGroups()
    {
        return $this->editFormGroups;
    }

    /**
     * @return Group[]
     */
    public function getEditFormTopBtn()
    {
        return false;
    }

    /**
     * @return Group[]
     */
    public function getNewFormGroups()
    {
        return $this->newFormGroups;
    }

    /**
     * @return Field[]
     */
    public function getExportFields()
    {
        if (empty($this->exportFields)) {
            return $this->fields;
        } else {
            return $this->exportFields;
        }
    }

    /**
     * @param ItemActionInterface $itemAction
     *
     * @return AbstractAdminListConfigurator
     */
    public function addItemAction($label,$options)
    {
        $this->itemActions[] = new ItemAction($label,$options);

        return $this;
    }

    public function addSimpleItemAction($label,$route_callback,$icon = null){
        $this->addItemAction($label,array('route_callback'=>$route_callback,'icon'=>$icon));
    }

    /**
     * @return bool
     */
    public function hasItemActions()
    {
        return !empty($this->itemActions);
    }

    /**
     * @return ItemActionInterface[]
     */
    public function getItemActions()
    {
        return $this->itemActions;
    }

    /**
     * @param ListActionInterface $listAction
     *
     * @return AdminListConfiguratorInterface
     */
    public function addListAction($label,$options)
    {
        $this->listActions[] = new ListAction($label,$options);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasListActions()
    {
        return !empty($this->listActions);
    }

    /**
     * @return ListActionInterface[]
     */
    public function getListActions()
    {
        return $this->listActions;
    }

    /**
     * @param BulkActionInterface $bulkAction
     *
     * @return AdminListConfiguratorInterface
     */
    public function addBulkAction($label,$options)
    {
        $this->bulkActions[] = new BulkAction($label,$options);

        return $this;
    }



    public function workflows($item,$user = null){
        if(!$this->getFieldWorkflow()) return null;
        $get = 'get'.ucfirst($this->to_camel_case($this->getLocalFieldWorkflow()));
        if($item->$get()) {
            $return = $item->$get()->getTransitionsPossible($item,$user);
        } else {
            $return = array();
        }
        return $return;
    }

    /**
     * @return bool
     */
    public function hasBulkActions()
    {
        return !empty($this->bulkActions);
    }

    /**
     * @return BulkActionInterface[]
     */
    public function getBulkActions()
    {
        return $this->bulkActions;
    }



    /**
     * @return string
     */
    public function getLogTemplate()
    {
        return $this->logTemplate;
    }

    /**
     * @return string
     */
    public function getLogsTemplate()
    {
        return $this->logsTemplate;
    }





    /**
     * @return string
     */
    public function getShowTemplate()
    {
        return $this->showTemplate;
    }

    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setShowTemplate($template)
    {
        $this->showTemplate = $template;

        return $this;
    }

    function from_camel_case($str) {
          $str[0] = strtolower($str[0]);
            $func = create_function('$c', 'return "_" . strtolower($c[1]);');
            return preg_replace_callback('/([A-Z])/', $func, $str);
    }

    /**
     *  * Translates a string with underscores
     *   * into camel case (e.g. first_name -> firstName)
     *    *
     *     * @param string $str String in underscore format
     *      * @param bool $capitalise_first_char If true, capitalise the first char in $str
     *       * @return string $str translated into camel caps
     *        */
    function to_camel_case($str, $capitalise_first_char = false) {
          if($capitalise_first_char) {
                  $str[0] = strtoupper($str[0]);
                    }
            $func = create_function('$c', 'return strtoupper($c[1]);');
            return preg_replace_callback('/_([a-z])/', $func, $str);
    }
    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return mixed
     */
    public function getValue($item, $columnName)
    {
        if (is_array($item)) {
            if (isset($item[$columnName])) {
                return $item[$columnName];
            } else {
                return '';
            }
        }
        $methodName = $columnName;
        if (method_exists($item, $methodName)) {
            $result = $item->$methodName();
        } else {
            $methodName = 'get' . ucfirst($this->to_camel_case($columnName));
            if (method_exists($item, $methodName)) {
                $result = $item->$methodName();
            } else {
                $methodName = 'is' . $columnName;
                if (method_exists($item, $methodName)) {
                    $result = $item->$methodName();
                } else {
                    $methodName = 'has' . $columnName;
                    if (method_exists($item, $methodName)) {
                        $result = $item->$methodName();
                    } else {
                        $methods = explode('.',$columnName);
                        $subItem = $item;
                        foreach($methods as $m){
                            $methodName = 'get' . ucfirst($m);
                            if(!$subItem) return null;

                            if (method_exists($subItem, $methodName)){
                                try{
                                    $subItem = $subItem->$methodName();
                                }catch(\Exception $e){
                                    if(is_object($subItem)) return $this->em->getClassMetadata(get_class($subItem))->getName().':'.$subItem->getId() .' introuvable';
                                    else return '<strong>'.$columnName.'</strong> not found';
                                }
                            }else{
                                if(method_exists($subItem,'__get')){
                                    return $subItem->$methodName();
                                }else{
                                    if(is_object($subItem)) return sprintf(get_class($subItem).'() find but undefined function '.$methodName.'() not found');
                                    else return '<strong>'.$columnName.'</strong> not found';
                                }
                            }
                        }
                        $result = $subItem;
                    }
                }
            }
        }

        return $result;
    }

    public function getType($item,$columnName){

        if(is_object($item)){
            $return = $this->em->getClassMetadata(get_class($item))->getTypeOfColumn($columnName);
            return $return;
        }
    }

    public function editInplaceInputType($item,$columnName,$class = null){
        $methodName = 'get' . $this->to_camel_case($columnName);
        if (is_array($item)) {
            $item = $item[0];
        }
        $type = $this->getType($item,$columnName);
        $retour = $item->$methodName();
        if ($type == 'boolean') {
            return 'bool';
        }else if($type == 'datetime'){
            return $type;
        }else if($type == 'date') {
            return $type;
        }else if($type == 'time') {
            return $type;
        } else if($retour instanceof PersistentCollection) {
            return 'collection';
        } elseif(is_array($retour)) {
            return 'array';
        } elseif($type != null) {
            return 'text';
        } else {
            return 'object';
        }
    }

    public function listOptionsForCombobox($item,Field $line){
        return array();
    }

    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return string
     */
    public function getStringValue($item, $columnName)
    {
        $type = null;
        $subColumn = substr($columnName,0,strrpos($columnName,'.'));
        if($subColumn){
            $value = $this->getValue($item, $subColumn);
            if($value){
                $type = $this->getType($value,substr($columnName,strrpos($columnName,'.')+1));
            }
        }else{
            $type = $this->getType($item,$columnName);
        }
        $result = $this->getValue($item, $columnName);
        if(is_array($result)){
            return nl2br(implode("\n",$result));
        }
        if ($type == 'boolean') {
            if($result == null) return '';
            return ($result)? 'oui' : 'non';
        }elseif($type == 'text'){
            return ($result)? nl2br($result):$result;
        }
        if ($result instanceof \DateTime) {
            if($type == 'datetime'){
                return ($result->format('Y') > 0)? $result->format('d/m/Y H:i:s'):null;
            }elseif($type == 'date'){
                return ($result->format('Y') > 0)? $result->format('d/m/Y'):null;
            }elseif($type == 'time'){
                return $result->format('H:i');
            }
        } else {
            if ($result instanceof PersistentCollection) {
                $results = "";
                /* @var Object $entry */
                foreach ($result as $entry) {
                    $results[] = $entry->__toString();
                }
                if (empty($results)) {
                    return "";
                }

                return implode(', ', $results);
            } elseif ($result instanceof ArrayCollection) {
                $results = "";
                /* @var Object $entry */
                foreach ($result as $entry) {
                    $results[] = (string)$entry;
                }
                if (empty($results)) {
                    return "";
                }


                return implode(', ', $results);
            } else {
                if (is_array($result)) {
                    foreach($result as $r){
                        if(is_array($r)) return json_encode($result);
                    }
                    return implode(', ', $result);
                }elseif(is_object($result)){
                    return (string)$result;
                } else {
                    return $result;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getAddTemplate()
    {
        return $this->addTemplate;
    }

     /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setFormTemplate($template)
    {
        $this->formTemplate = $template;
        return $this;
    }

    public function getFormTemplate($type){
        return $this->formTemplate;
    }

     /**
     * @return string
     */
    public function getScriptTemplate()
    {
        return $this->scriptTemplate;
    }


    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setScriptTemplate($template)
    {
        $this->scriptTemplate = $template;
        return $this;
    }


    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setAddTemplate($template)
    {
        $this->addTemplate = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getEditTemplate()
    {
        return $this->editTemplate;
    }

    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setEditTemplate($template)
    {
        $this->editTemplate = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeleteTemplate()
    {
        return $this->deleteTemplate;
    }

    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setDeleteTemplate($template)
    {
        $this->deleteTemplate = $template;

        return $this;
    }

    /**
     * You can override this method to do some custom things you need to do when adding an entity
     *
     * @param object $entity
     *
     * @return mixed
     */
    public function decorateNewEntity($entity,$request = null)
    {
        return $entity;
    }
    public function addValideEntity($entity,$request = null)
    {
        return $entity;
    }

    public function editValideEntity($entity,$request = null)
    {
        return $entity;
    }

    public function decorateEditEntity($entity,$request = null){
        return $entity;
    }

    public function initEntity($entity,$request = null){
        return $entity;
    }



    /**
     * @param FilterBuilder $filterBuilder
     *
     * @return AbstractAdminListConfigurator
     */
    public function setFilterBuilder(FilterBuilder $filterBuilder)
    {

        $this->filterBuilder = $filterBuilder;
        $this->filterBuilder->setConfigurator($this);
        return $this;
    }

    /**
     * Bind current request.
     *
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $componentResponse = [];
        foreach($this->indexComponents as $components){
            $componentResponse[] = $components->bindRequest($request);
        }
        return $componentResponse;
    }



    /**
     * Return current page.
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }



    /**
     * Return current sorting column.
     *
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Return current sorting direction.
     *
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * @param string $suffix
     *
     * @return string
     */
    public function getPathByConvention($suffix = null)
    {
        $entityName = strtolower($this->getEntityName());
        $entityName = str_replace('\\', '_', $entityName);
        if (empty($suffix)) {
            return sprintf('%s_%s%s_index', str_replace('bundle','',strtolower($this->getBundleName())), $entityName, $this->getListRouteName());
        }

        return sprintf('%s_%s%s_%s', str_replace('bundle','',strtolower($this->getBundleName())), $entityName, $this->getListRouteName(), $suffix);
    }

    public function getListRouteName(){
        return 'lego';
    }

    /**
     * Get controller path.
     *
     * @return string
     */
    public function getControllerPath()
    {
        return sprintf('%s:%s', $this->getBundleName(), $this->getEntityName());
    }

    /**
     * Return extra parameters for use in list actions.
     *
     * @return array
     */
    public function getExtraParameters()
    {
        return array();
    }

    public function getUploadFileGetter() {
        return null;
    }

    public function field($columnName){
        if(isset($this->fields[strtolower($columnName)])) return $this->fields[strtolower($columnName)];
        return new Field($columnName);
    }

    public function showField($columnName){
        if (isset($this->showFields[strtolower($columnName)])) return $this->showFields[strtolower($columnName)];
        return new Field($columnName,array());
    }

    public function setParentConfig(array $config){
        $this->parentConfig = $config;
    }

    public function getParentConfig(){
        return $this->parentConfig;
    }

    public function generateShowSubLists($controller,$item){
        return array();
    }

    public function subList(){
        return null;
    }

    public function editFormFields(){
        return null;
    }

    public function newFormFields(){
        return null;
    }

    public function formFields(){
        return null;
    }

    public function getEditFormTheme(){
        return 'IdkLegoBundle:Form:lle_base_fields.html.twig';
    }

    public function getNewFormTheme(){
        return 'IdkLegoBundle:Form:lle_base_fields.html.twig';
    }

    public function label($word){
        return $word;
    }

    public function getFieldWorkflow(){
        return null;
    }

    public function getLocalFieldWorkflow(){
        if(!$this->getFieldWorkflow()) return null;
        $res = explode('.',$this->getFieldWorkflow());
        return $res[0];
    }

    public function getWfFieldWorkflow(){
        if(!$this->getFieldWorkflow()) return null;
        $res = explode('.',$this->getFieldWorkflow());
        if(isset($res[1])) return $res[1];
        return 'libelle';
    }

    public function getCssWorkflow($item){
        if(!$this->getFieldWorkflow()) return null;
        $get = 'get'.ucfirst($this->to_camel_case($this->getLocalFieldWorkflow()));
        $wf = $item->$get();
        if(method_exists($wf, 'getCssClass')){
            return $wf->getCssClass();
        }elseif(method_exists($wf, 'getCode')){
            return 'wf-'.$wf->getCode();
        }else{
            return null;
        }
    }


    public function classCssLine($item){
        return $this->getCssWorkflow($item);
    }

    public function getBulkAction($ida){
        return $this->searchInById($this->bulkActions,$ida);
    }

    public function getItemAction($ida){
        return $this->searchInById($this->itemActions,$ida);
    }

    public function getListAction($ida){
        return $this->searchInById($this->listActions,$ida);
    }

    protected function searchInById($liste,$id){
        foreach($liste as $elm){
            if($elm->getId() == $id){
                return $elm;
            }
        }
        return null;
    }

    public function getUser(){
        return $this->get('security.context')->getToken()->getUser();
    }

    /**
     * Return extra parameters for use in list actions.
     *
     * @return array
     */
    public function getTemplateAfterEdit()
    {
        return 'list';
    }

    public function getUrlAfterNew($item,$request){
        return $this->getIndexUrl();
    }

    public function getUrlAfterEdit($item,$request){
        return $this->getIndexUrl();
    }

    public function getUrlAfterDelete($item,$request){
        return $this->getIndexUrl();
    }

    public function isXhrDelete(){
        return true;
    }

    public function getListHeaderTemplate(){
        return 'IdkLegoBundle:Default:_list_header.html.twig';
    }

    /* NEW */

    /**
     * @var string
     */
    private $indexTemplate = 'IdkLegoBundle:Default:index.html.twig';

    private $indexComponents = [];

    private $parent = null;


    public function setParent(AbstractConfigurator $configurator){
        $this->parent = $configurator;
        return $this;
    }

    public function getParent(){
        return $this->parent;
    }

    public function getIndexComponents(){
        return $this->indexComponents;
    }

    public function getIndexTemplate()
    {
        return $this->indexTemplate;
    }

    public function setIndexTemplate($template)
    {
        $this->indexTemplate = $template;
        return $this;
    }

    public function addIndexComponent($className, array $options, AbstractConfigurator $configurator = null)
    {
        $component = $this->generateComponent($className, $options, $configurator);
        $this->indexComponents[] = $component;
        return $component;
    }

    private function generateComponent($className, array $options, AbstractConfigurator $configurator = null)
    {
        $reflectionClass =  new \ReflectionClass($className);
        if($configurator){
            $configurator->setParent($this);
        }else{
            $configurator = $this;
        }
        return $reflectionClass->newInstance($options, $configurator);
    }


    public function getFields(array $columns = null)
    {
        return array_merge($this->fields, $this->get('lego.service.meta_entity_manager')->generateFields($this->getEntityName(), $columns));
    }



    public function getPathRoute($sufix = 'index'){
        return $this->getControllerPath().'_'.$sufix;
    }

    public function getPathParams($item){
            return ['id' => $item->getId()];
    }

    abstract function buildIndex();
    abstract function getItems();

}
