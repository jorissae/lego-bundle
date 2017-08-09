<?php

namespace Idk\LegoBundle\Twig;

use Idk\LegoBundle\Service\ExportService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\CompiledRoute;
use Idk\LegoBundle\AdminList\AdminList;
use Idk\LegoBundle\Entity\AbstractAttribut;
use Idk\LegoBundle\Interfaces\Iattributable;
use Idk\LegoBundle\AdminList\SubList;
use Idk\LegoBundle\Entity\AttachableFolder;
use Idk\LegoBundle\Interfaces\IzoneAttachable;
use \Twig_Environment;
use Idk\LegoBundle\Component\Component;
use Idk\LegoBundle\Annotation\Entity\Field;

/**
 * AdminListTwigExtension
 */
class AdminListTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $formBuilder;

    protected $em;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->formBuilder = $container->get('form.factory');
        $this->em  = $container->get('doctrine.orm.entity_manager');
        $this->container = $container;

    }



    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('adminlist_widget',array($this,'renderWidget'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('attachement',array($this,'getAttachement'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('attach_browser' ,array($this,'renderAttachBrowser'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('sub_adminlist_widget',array($this,'renderSubWidget'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('render_field_value',array($this,'renderFieldValue'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('render_field_print',array($this,'renderFieldPrint'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('value',array($this,'value'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('row',array($this,'row'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('show_value',array($this,'showValue'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('show_row',array($this,'showRow'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('attribut',array($this,'attribut'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('attribut_options',array($this,'attributOptions'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('my_router_params',array($this,'routerParams')),
            new \Twig_SimpleFunction('supported_export_extensions',array($this,'getSupportedExtensions'))
        );
    }

    /**
     * Renders the HTML for a given view
     *
     * Example usage in Twig:
     *
     *     {{ form_widget(view) }}
     *
     * You can pass options during the call:
     *
     *     {{ form_widget(view, {'attr': {'class': 'foo'}}) }}
     *
     *     {{ form_widget(view, {'separator': '+++++'}) }}
     *
     * @param AdminList $view      The view to render
     * @param string    $basepath  The base path
     * @param array     $urlparams Additional url params
     * @param array     $addparams Add params
     *
     * @return string The html markup
     */
    public function renderWidget(Twig_Environment $env, AdminList $view, $basepath, array $urlparams = array(), array $addparams = array())
    {
        $template = $env->loadTemplate("IdkLegoBundle:AdminListTwigExtension:widget.html.twig");

        $filterBuilder  = $view->getFilterBuilder();

        return $template->render(array(
            'filter'        => $filterBuilder,
            'basepath'      => $basepath,
            'addparams'     => $addparams,
            'extraparams'   => $urlparams,
            'adminlist'     => $view
        ));
    }

    public function renderAttachBrowser(Twig_Environment $env,AdminList $view,$item){
        $template = $env->loadTemplate("IdkLegoBundle:Attachable:browser.html.twig");
        $zones = array();
        $curFolder = new AttachableFolder();
        $zoneCode = null;
        if(method_exists($item,'getAttachableZones')){
            $attachZones = $item->getAttachableZones();
            $zones = array();
            foreach($attachZones as $key => $v){
                if($v instanceof IzoneAttachable){
                    $code = $v->getCode();

                }else{
                    $code =$key;
                }
                $zones[$code]['libelle'] = $v->getLibelle();
                $zones[$code]['nb'] = count($view->getRootAttachementFiles($item,$code));
            }
            $keys = array_keys($zones);
            $zoneCode = (count($keys))? $keys[0]:null;
        }
        if($zoneCode){
            $curFolder->setZoneCode($zoneCode);
            $files = $view->getRootAttachementFiles($item,$zoneCode);
            $folders = $view->getRootAttachementFolders($item,$zoneCode);
        }else{
            $files = $view->getRootAttachementFiles($item);
            $folders = $view->getRootAttachementFolders($item);
        }
        $form = $this->createForm(new FolderType(), new AttachableFolder());
        $displayFolder = false;
        if(method_exists($item,'getAttachableWithFolder')){
            $displayFolder = $item->getAttachableWithFolder($zoneCode);
        }
        $clickableFile = false;
        if(method_exists($item,'getAttachableClickableFile')){
            $clickableFile = $item->getAttachableClickableFile($zoneCode);
        }
        return $template->render(array(
            'curFolder'=> $curFolder,
            'files'=>$files,
            'folders'=>$folders,
            'form'=> $form->createView(),
            'class'=>$view->getRepositoryName(),
            'itemId'=>$item->getId(),
            'zones'=>$zones,
            'displayFolder'=>$displayFolder,
            'options'=> ['clickableFile'=>$clickableFile],
        ));
    }

    public function showValue(Twig_Environment $env, AdminList $view,$columnName,$item)
    {
        $field = $view->showField($columnName);
        return $this->renderFieldValue($env,$view,$field,$item);
    }

    public function showRow(Twig_Environment $env, AdminList $view,$columnName,$item)
    {
        $field = $view->showField($columnName);
        return $this->renderFieldRow($env,$view,$field,$item);
    }

    public function value(Twig_Environment $env, AdminList $view,$columnName,$item)
    {
        return $this->renderFieldValue($env, $view,$view->field($columnName),$item);
    }

    public function row(Twig_Environment $env, AdminList $view,$columnName,$item)
    {
        return $this->renderFieldRow($env,$view,$view->field($columnName),$item);
    }


    public function attributOptions(Twig_Environment $env, AdminList $view,AbstractAttribut $attribut){
        $template = $env->loadTemplate("IdkLegoBundle:AdminListTwigExtension:attribut_options.html.twig");
        return $template->render(array(
            'adminlist' => $view,
            'item'  => $attribut
        ));
    }

    public function getAttachement($item,$type = null,$zone = null,$limit = null){
        $attachementManager = $this->get('lle_attachement_manager');
        $attachementManager->setItem($item);
        return $attachementManager->get(array('type'=>$type,'zone'=>$zone,'limit'=>$limit));
    }

    public function attribut(Twig_Environment $env, AdminList $view,Iattributable $item,AbstractAttribut $attribut)
    {

        $values = $item->getAttributValues();
        $value = (isset($values[$attribut->getId()]))? $values[$attribut->getId()]:null;
        $template = $env->loadTemplate("IdkLegoBundle:AdminListTwigExtension:attribut.html.twig");
        return $template->render(array(
            'adminlist' => $view,
            'attribut'        => $attribut,
            'value'      => $value,
            'item'      => $item,
        ));
    }

    public function renderFieldValue(Twig_Environment $env, Component $component, Field $field, $item)
    {
        $template = $env->loadTemplate("IdkLegoBundle:LegoTwigExtension:_field_value.html.twig");
        return $template->render(array(
            'field'        => $field,
            'configurator'      => $component->getConfigurator(),
            'item'     => $item,
            'string_value' => $field->getStringValue($component->getConfigurator(), $item),
        ));
    }

    public function renderFieldPrint(Twig_Environment $env, AdminList $view,Field $field,$item)
    {
        $template = $env->loadTemplate("IdkLegoBundle:AdminListTwigExtension:field_print.html.twig");

        return $template->render(array(
            'field'        => $field,
            'adminlist'      => $view,
            'item'     => $item,
            'string_value' => $field->getStringValue($view,$item),
        ));
    }

    public function renderFieldRow(Twig_Environment $env, AdminList $view,Field $field,$item)
    {
        $template = $env->loadTemplate("IdkLegoBundle:AdminListTwigExtension:field_row.html.twig");
        return $template->render(array(
            'field'        => $field,
            'adminlist'      => $view,
            'item'     => $item,
            'string_value' => $field->getStringValue($view,$item),
        ));
    }

    public function renderSubWidget(Twig_Environment $env, SubList $sublist, AdminList $parentAdminlist, $item)
    {
        $view = $sublist->getView();
        $indexUrl = $view->getIndexUrl();
        $basepath = $indexUrl['path'];
        $urlparams = $indexUrl;
        $template = $env->loadTemplate("IdkLegoBundle:AdminListTwigExtension:sub_widget.html.twig");
        return $template->render(array(
            'basepath'      => $basepath,
            'addparams'     => array(),
            'extraparams'   => $urlparams,
            'adminlist'     => $view,
            'sublist'        => $sublist,
            'parentItem'    => $item,
            'parentAdminlist' => $parentAdminlist,
        ));
    }


    /**
     * Emulating the symfony 2.1.x $request->attributes->get('_route_params') feature.
     * Code based on PagerfantaBundle's twig extension.
     *
     * @return array
     */
    public function routerParams()
    {
        /* @var Router $router  */
        $router = $this->container->get('router');
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $routeName = $request->attributes->get('_route');
        $routeParams = $request->query->all();
        $routeCollection = $router->getRouteCollection();
        /* @var CompiledRoute $compiledRouteConnection */
        $compiledRouteConnection = $routeCollection->get($routeName)->compile();
        foreach ($compiledRouteConnection->getVariables() as $variable) {
            $routeParams[$variable] = $request->attributes->get($variable);
        }

        return $routeParams;
    }

    public function getSupportedExtensions()
    {
        return ExportService::getSupportedExtensions();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'adminlist_twig_extension';
    }

    private function createForm($type, $data = null, array $options = array())
    {
        return $this->formBuilder->create($type, $data, $options);
    }

    private function get($name){
        return $this->container->get($name);
    }

}
