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

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Idk\LegoBundle\Annotation\Entity\Field;
use Idk\LegoBundle\Component\Component;
use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Idk\LegoBundle\Service\Tag\ComponentChain;
use Symfony\Component\Asset\Package;
use Symfony\Component\HttpFoundation\Request;
use Idk\LegoBundle\Lib\Path;



abstract class AbstractConfigurator
{

    const ROUTE_SUFFIX_EDIT_IN_PLACE = 'editinplace';
    const ROUTE_SUFFIX_ORDER_COMPONENTS = 'ordercomponents';
    const ROUTE_SUFFIX_ORDER_COMPONENTS_RESET = 'ordercomponentsreset';
    const ROUTE_SUFFIX_INDEX = 'index';
    const ROUTE_SUFFIX_ADD = 'add';
    const ROUTE_SUFFIX_EDIT = 'edit';
    const ROUTE_SUFFIX_SHOW = 'show';

    const ENTITY_CLASS_NAME = 'entity_class_name_empty';

    const TITLE = 'lego.default.title';

    protected $em;

    protected $configuratorBuilder;

    private $showTemplate = '@IdkLego/Default/show.html.twig';

    private $logTemplate = '@IdkLego/Default/log.html.twig';

    private $logsTemplate = '@IdkLego/Default/logs.html.twig';

    private $addTemplate = '@IdkLego/Default/add.html.twig';

    private $editTemplate = '@IdkLego/Default/edit.html.twig';

    private $indexTemplate = '@IdkLego/Default/index.html.twig';

    private $defaultTemplate = '@IdkLego/Default/default.html.twig';

    private $title;

    protected $page = 1;

    protected $orderBy = '';

    protected $orderDirection = '';


    protected $entityClassName;

    private $isBuild = false;

    protected $request;

    protected $currentComponentSuffixRoute;

    protected $children = [];

    private $components = [];

    private $parent = null;

    private $pathParameters = [];

    public function __construct(ConfiguratorBuilder $builder, AbstractConfigurator $parent = null, $entityClassName = null, $pathParameters = [])
    {
        if($parent) $this->setParent($parent);
        $this->configuratorBuilder = $builder;
        $this->entityClassName = $entityClassName;
        $this->pathParameters = $pathParameters;
        if($this->getControllerPath() !== 'lego'){
           unset($this->pathParameters['entity']); //TODO rename in lego_entity
        }
        $this->build();
    }

    public function getConfiguratorBuilder(): ConfiguratorBuilder{
        return $this->configuratorBuilder;
    }

    public function getId(){
        return md5(get_class($this).'-'.$this->getEntityName());
    }

    abstract public function getType($item,$columnName);

    public function getEntityName(){
        return $this->entityClassName ?? $this::ENTITY_CLASS_NAME;
    }

    public function setEntityClassName($entityClassName){
        $this->entityClassName = $entityClassName;
    }


    public function buildIndex(){
        return;
    }

    public function buildShow(){
        return;
    }

    public function buildEdit(){
        return;
    }

    public function buildAdd(){
        return;
    }

    public function buildAll(){
        return;
    }

    public function build(){
        if($this->isBuild == false and !$this->getParent()){
            $this->isBuild = true;
            $this->buildAll();
            $this->buildIndex();
            $this->buildEdit();
            $this->buildAdd();
            $this->buildShow();
        }
    }


    public function getTitle() {
        return $this->title ?? $this::TITLE;
    }

    public function getSubTitle() {
        return "";
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


    public function getPathByField($item,Field $field)
    {
        $path = $field->getPath();
        if(!$path) return null;
        $params = [];
        if($path and isset($path['route'])) {
            $route = $path['route'];
            if (isset($path['params']) and $path['params']) {
                foreach ($path['params'] as $key => $fieldName) {
                    $params[$key] = $this->getValue($item, $fieldName);
                }
            }
        }else{
            if ($path == self::ROUTE_SUFFIX_SHOW) {
                $route = $this->getPathRoute(self::ROUTE_SUFFIX_SHOW);
                $params['id'] = $item->getId();
                $params = $this->getPathParameters($params);
            }
        }
        return ['route' => $route, 'params' => $params];
    }

    public function getEditInPlacePath()
    {
        return new Path($this->getPathByConvention(self::ROUTE_SUFFIX_EDIT_IN_PLACE), $this->getPathParameters());
    }





    /**
     * @return int
     */
    public function getLimit()
    {
        return 25;
    }


    public function getLogTemplate()
    {
        return $this->logTemplate;
    }
    public function getLogsTemplate()
    {
        return $this->logsTemplate;
    }
    public function getShowTemplate()
    {
        return $this->showTemplate;
    }



    function from_camel_case($str) {
          $str[0] = strtolower($str[0]);
            return preg_replace_callback('/([A-Z])/', function($c){ return "_" . strtolower($c[1]);}, $str);
    }


    function to_camel_case($str, $capitalise_first_char = false) {
          if($capitalise_first_char) {
                  $str[0] = strtoupper($str[0]);
                    }
            return preg_replace_callback('/_([a-z])/', function($c){ return "_" . strtolower($c[1]);}, $str);
    }

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
        if($type == 'json_array'){
            return 'Json';
        }
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
                return ($result->format('Y') > 0)? $result->format('d/m/Y H:i'):null;
            }elseif($type == 'date'){
                return ($result->format('Y') > 0)? $result->format('d/m/Y'):null;
            }elseif($type == 'time'){
                return $result->format('H:i');
            }
        } else {
            if ($result instanceof PersistentCollection) {
                $results = [];
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


    public function getAddTemplate()
    {
        return $this->addTemplate;
    }

    public function getEditTemplate()
    {
        return $this->editTemplate;
    }

    public function bindRequest(Request $request, $routeSuffix = null)
    {
        $this->request = $request;
        if(!$routeSuffix) {
            $lastUnderscore = strrchr($request->get('_route'), '_');

            $index = substr($lastUnderscore, 1);
            $index = ($index == 'export') ? 'index' : $index;
        }else{
            $index = $routeSuffix;
        }
        $this->currentComponentSuffixRoute = $index;


        return $this->bindRequestCurrentComponents($request);
    }


    public function bindRequestCurrentComponents(Request $request, Component $excepted = null){
        foreach($this->getCurrentComponents() as $component){
            $component->initWithComponents($this->getCurrentComponents());
        }
        $componentResponse = [];
        foreach($this->getChildren($this->currentComponentSuffixRoute) as $configurator){
            $configurator->bindRequest($request, $this->currentComponentSuffixRoute);
        }
        foreach($this->components[$this->currentComponentSuffixRoute] as $component){
            if($component->getConfigurator()->getId() == $this->getId() and ($excepted == null or $component->getId() != $excepted->getId())) {
                $componentResponse[] = $component->bindRequest($request);
            }
        }
        return $componentResponse;
    }

    public function getChildren($routeSuffix){
        if(isset($this->children[$routeSuffix])){
            return $this->children[$routeSuffix];
        }
        return [];
    }

    public function getCurrentComponents(){
        return $this->getComponents($this->currentComponentSuffixRoute);
    }

    public function getCurrentComponentSuffixRoute(){
        return $this->currentComponentSuffixRoute;
    }


    public function getPage()
    {
        return $this->page;
    }


    public function getOrderBy()
    {
        return $this->orderBy;
    }


    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    public function getPathByConvention($suffix = null)
    {
        return $this->getControllerPath().'_'.$suffix;
    }

    public function label($word){
        return $word;
    }

    public function getUser(){
        return $this->getConfiguratorBuilder()->getUser();
    }




    public function setParent(AbstractConfigurator $configurator){
        $this->parent = $configurator;
        return $this;
    }

    public function getParent(){
        return $this->parent;
    }

    public function getIndexComponents(){
        return $this->getComponents(self::ROUTE_SUFFIX_INDEX);
    }

    public function getAddComponents(){
        return $this->getComponents(self::ROUTE_SUFFIX_ADD);
    }

    public function getEditComponents(){
        return $this->getComponents(self::ROUTE_SUFFIX_EDIT);
    }

    public function getShowComponents(){
        return $this->getComponents(self::ROUTE_SUFFIX_SHOW);
    }

    public function getComponent($routeSuffix, $id){
        $this->currentComponentSuffixRoute = $routeSuffix;
        foreach($this->components[$routeSuffix] as $component){
            if($component->getId() == $id) {
                return $component;
            }
        }
        return null;
    }

    public function getComponentByClass(string $routeSuffix, string $className): ?Component{
        $this->currentComponentSuffixRoute = $routeSuffix;
        foreach($this->components[$routeSuffix] as $component){
            if($component instanceof $className) {
                return $component;
            }
        }
        return null;
    }

    public function getComponents($routeSuffix)
    {
        if (isset($this->components[$routeSuffix])) {

            $components = $this->components[$routeSuffix];
           /* foreach($components as $c){
                echo get_class($c).'--';
            }*/
            $order = $this->getConfiguratorSessionStorage('sort');
            if ($order != null and isset($order[$routeSuffix])) {
                return $this->sortComponents($components, $order[$routeSuffix]);
            } else {
                return $components;
            }
        }
        return [];
    }

    private function sortComponents($components, $order){
        $return = [];
        foreach($order as $id){
            foreach($components as $cid => $component){
                if($cid == $id){
                    $return[$id] = $component;
                    break;
                }
            }
        }
        //check if new component which not in the order array
        foreach($components as $cid => $component){
            if(!in_array($cid, array_keys($return))){
                $return[$cid] = $component;
            }
        }
        return $return;
    }

    public function getIndexTemplate()
    {
        return $this->indexTemplate;
    }

    public function getDefaultTemplate()
    {
        return $this->defaultTemplate;
    }

    public function setIndexTemplate($template)
    {
        $this->indexTemplate = $template;
        return $this;
    }

    public function addIndexComponent($className, array $options = [], $entityClassName = null, $nameConfigurator = null): Component
    {
        return $this->addComponent($className, $options, self::ROUTE_SUFFIX_INDEX, $entityClassName, $nameConfigurator);
    }

    public function addAddComponent($className, array $options = [], $entityClassName = null, $nameConfigurator = null)
    {
        return $this->addComponent($className, $options, self::ROUTE_SUFFIX_ADD, $entityClassName, $nameConfigurator);
    }

    public function addEditComponent($className, array $options = [],  $entityClassName = null, $nameConfigurator = null)
    {
        return $this->addComponent($className, $options, self::ROUTE_SUFFIX_EDIT, $entityClassName, $nameConfigurator);
    }

    public function addShowComponent($className, array $options = [], $entityClassName = null, $nameConfigurator = null)
    {
        return $this->addComponent($className, $options, self::ROUTE_SUFFIX_SHOW, $entityClassName, $nameConfigurator);
    }

    public function addComponent($className, array $options, $routeSuffix, $entityClassName = null, $nameConfigurator = null){
        if(!isset($this->components[$routeSuffix])){
            $this->components[$routeSuffix] = [];
        }
        $component = $this->generateComponent($className, $options, $routeSuffix, $entityClassName, $nameConfigurator);
        if(isset($this->components[$routeSuffix][$component->getId()])){
            $options['cid'] = count($this->components[$routeSuffix]);
            $component = $this->generateComponent($className, $options, $routeSuffix, $entityClassName, $nameConfigurator);
        }
        $this->components[$routeSuffix][$component->getId()] = $component;
        return $component;
    }

    private function generateComponent($componentClassName, array $options, $routeSuffix, $entityClassName = null, $nameConfigurator = null)
    {
        if($entityClassName){
            $configurator = $this->configuratorBuilder->generateConfigurator($entityClassName, $nameConfigurator, $this);
            if($this->getChild($routeSuffix,$configurator->getId())){
                $configurator = $this->getChild($routeSuffix, $configurator->getId());
            }
            $component = $configurator->addComponent($componentClassName,$options, $routeSuffix);
            $this->addChild($routeSuffix, $configurator);
            return $component;
        }else{
            return $this->configuratorBuilder->getComponentChain()->build($componentClassName, $options, $this, $routeSuffix);
        }

    }

    private function getChild($routeSuffix, $configuratorId): ?AbstractConfigurator{
        if(!isset($this->children[$routeSuffix])) return null;
        foreach($this->children[$routeSuffix] as $child){
            if($child->getId() == $configuratorId) return $child;
        }
        return null;
    }

    private function addChild($routeSuffix, AbstractConfigurator $configurator){
        if($this->getChild($routeSuffix, $configurator->getId()) === null) {
            if (!isset($this->children[$routeSuffix])) {
                $this->children[$routeSuffix] = [];
            }
            $this->children[$routeSuffix][] = $configurator;
        }
    }


    public function getPathRoute($sufix = 'index'){
        return $this->getControllerPath().'_'.$sufix;
    }

    public function getPath($suffix = 'index', $params = []){
        return new Path( $this->getPathRoute($suffix), $this->getPathParameters($params));
    }

    public function getUrl($suffix, array $params = []){
        $path = $this->getPath($suffix, $params);
        return $this->configuratorBuilder->getRouter()->generate($path->getRoute(), $path->getParams());
    }

    public function getPathParams($item){
            return $this->getPathParameters(['id' => $item->getId()]);
    }

    public function getPathParameters(array $params = []){
        return array_merge($params, $this->pathParameters);
    }

    public function getConfiguratorSessionStorage($key, $default = null){
        return $this->configuratorBuilder->getSessionStorage($this->getId(), $key, $default);
    }

    public function setConfiguratorSessionStorage($key, $value){
        return $this->configuratorBuilder->setSessionStorage($this->getId(), $key, $value);
    }

    public function setTitle($title){
        $this->title = $title;
    }

    static public function getControllerPath(){
        return 'lego';
    }








}
