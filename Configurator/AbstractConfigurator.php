<?php

namespace Idk\LegoBundle\Configurator;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;



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
    const SUFFIX_EDIT_IN_PLACE = 'editinplace';
    const SUFFIX_EDIT_IN_PLACE_ATTR = 'editinplace_attribut';
    const SUFFIX_LOGS = 'logs';
    const SUFFIX_LOG = 'log';
    const SUFFIX_WORKFLOW = 'wf';

    const ROUTE_SUFFIX_INDEX = 'index';
    const ROUTE_SUFFIX_ADD = 'add';
    const ROUTE_SUFFIX_EDIT = 'edit';
    const ROUTE_SUFFIX_SHOW = 'show';

    protected $em;

    private $showTemplate = 'IdkLegoBundle:Default:show.html.twig';

    private $logTemplate = 'IdkLegoBundle:Default:log.html.twig';

    private $logsTemplate = 'IdkLegoBundle:Default:logs.html.twig';

    private $addTemplate = 'IdkLegoBundle:Default:add.html.twig';

    private $editTemplate = 'IdkLegoBundle:Default:edit.html.twig';

    private $indexTemplate = 'IdkLegoBundle:Default:index.html.twig';

    protected $page = 1;

    protected $orderBy = '';

    protected $orderDirection = '';

    protected $container;

    private $isBuild = false;

    protected $request;

    protected $currentComponentSuffixRoute;

    protected $children = [];

    private $components = [];

    private $parent = null;

    public function __construct($container, AbstractConfigurator $parent = null)
    {
        if($parent) $this->setParent($parent);
        $this->container = $container;
        $this->build();
    }

    public function getSublistParentItem(){
        return ($this->isSubList())? $this->sublistParentItem:null;
    }


    abstract public function getBundleName();
    abstract function buildIndex();
    abstract function getItems();
    abstract public function getType($item,$columnName);

    public function build(){
        if($this->isBuild == false and !$this->getParent()){
            $this->isBuild = true;
            $this->buildIndex();
        }
    }

    public function get($id)
    {
        return $this->container->get($id);
    }

    public function getTitle() {
        return $this->getEntityName();
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
            $func = create_function('$c', 'return "_" . strtolower($c[1]);');
            return preg_replace_callback('/([A-Z])/', $func, $str);
    }


    function to_camel_case($str, $capitalise_first_char = false) {
          if($capitalise_first_char) {
                  $str[0] = strtoupper($str[0]);
                    }
            $func = create_function('$c', 'return strtoupper($c[1]);');
            return preg_replace_callback('/_([a-z])/', $func, $str);
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



    public function editInplaceInputType($item,$columnName,$class = null){
        $methodName = 'get' . $this->to_camel_case($columnName);
        if (is_array($item)) {
            $item = $item[0];
        }
        $type = $this->getType($item,$columnName);
        $retour = $item->$methodName();
        if ($type == 'boolean') {
            return $type;
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


    public function getAddTemplate()
    {
        return $this->addTemplate;
    }

    public function getEditTemplate()
    {
        return $this->editTemplate;
    }

    public function bindRequest(Request $request, $entityId = null)
    {
        $this->request = $request;
        $lastUnserscore = strrchr( $request->get('_route'), '_');

        $index = substr($lastUnserscore, 1);
        $index = ($index == 'export')? 'index':$index;
        $componentResponse = [];
        $this->currentComponentSuffixRoute = $index;

        foreach($this->getChildren($this->currentComponentSuffixRoute) as $configurator){
            $configurator->bindRequest($request);
        }
        foreach($this->components[$this->currentComponentSuffixRoute] as $components){
            $componentResponse[] = $components->bindRequest($request);
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
        return $this->get('security.context')->getToken()->getUser();
    }




    public function setParent(AbstractConfigurator $configurator){
        $this->parent = $configurator;
        return $this;
    }

    public function getParent(){
        return $this->parent;
    }

    public function getIndexComponents(){
        return $this->components[self::ROUTE_SUFFIX_INDEX];
    }

    public function getAddComponents(){
        return $this->components[self::ROUTE_SUFFIX_ADD];
    }

    public function getEditComponents(){
        return $this->components[self::ROUTE_SUFFIX_EDIT];
    }

    public function getShowComponents(){
        return $this->components[self::ROUTE_SUFFIX_SHOW];
    }

    public function getComponent($id){
        foreach($this->components as $route => $components){
            foreach($components as $component){
                if($component->getId() == $id) return $component;
            }
        }
        return null;
    }

    public function getComponents($routeSuffix){
        if(isset($this->components[$routeSuffix])){
            return $this->components[$routeSuffix];
        }
        return [];
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

    public function addIndexComponent($className, array $options, $configuratorClassName = null)
    {
        return $this->addComponent($className, $options, self::ROUTE_SUFFIX_INDEX, $configuratorClassName);
    }

    public function addAddComponent($className, array $options, $configuratorClassName = null)
    {
        return $this->addComponent($className, $options, self::ROUTE_SUFFIX_ADD, $configuratorClassName);
    }

    public function addEditComponent($className, array $options,  $configuratorClassName = null)
    {
        return $this->addComponent($className, $options, self::ROUTE_SUFFIX_EDIT, $configuratorClassName);
    }

    public function addShowComponent($className, array $options, $configuratorClassName = null)
    {
        return $this->addComponent($className, $options, self::ROUTE_SUFFIX_SHOW, $configuratorClassName);
    }

    public function addComponent($className, array $options, $routeSuffix, $configuratorClassName = null){
        if(!isset($this->components[$routeSuffix])){
            $this->components[$routeSuffix] = [];
        }
        $component = $this->generateComponent($className, $options, $routeSuffix, $configuratorClassName);
        $this->components[$routeSuffix][] = $component;
        return $component;
    }

    private function generateComponent($className, array $options, $routeSuffix, $configuratorClassName = null)
    {
        $reflectionClass =  new \ReflectionClass($className);
        if($configuratorClassName){
            $configurator = $this->generateConfigurator($configuratorClassName);
            $configurator->addComponent($className,$options, $routeSuffix);
            $this->addChild($routeSuffix, $configurator);
        }else{
            $configurator = $this;
        }
        return $reflectionClass->newInstance($options, $configurator, $routeSuffix);
    }

    private function addChild($routeSuffix, AbstractConfigurator $configurator){
        if(!isset($this->children[$routeSuffix])){
            $this->children[$routeSuffix] = [];
        }
        $this->children[$routeSuffix][] = $configurator;
    }

    private function generateConfigurator($className = null){
        $reflectionClass =  new \ReflectionClass($className);
        return $reflectionClass->newInstance($this->container, $this);
    }



    public function getPathRoute($sufix = 'index'){
        return $this->getControllerPath().'_'.$sufix;
    }

    public function getPathParams($item){
            return ['id' => $item->getId()];
    }



}
