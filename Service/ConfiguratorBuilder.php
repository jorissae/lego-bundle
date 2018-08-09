<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service;

use Idk\LegoBundle\Annotation\Entity\Entity;
use Idk\LegoBundle\Service\Tag\ComponentChain;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\DataCollectorTranslator;
use Idk\LegoBundle\Configurator\DefaultConfigurator;

class ConfiguratorBuilder
{


    private $mem;
    private $security;
    private $authorizationChecker;
    private $session;
    private $twig;
    private $translator;
    private $componentChain;

    public function __construct(
        Session $session,
        MetaEntityManager $mem,
        TokenStorageInterface $security,
        AuthorizationCheckerInterface $authorizationChecker,
        \Twig_Environment $twig,
        DataCollectorTranslator $translator,
        Router $router,
        ComponentChain $componentChain
){
        $this->session = $session;
        $this->mem = $mem;
        $this->security = $security;
        $this->authorizationChecker = $authorizationChecker;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->router = $router;
        $this->componentChain = $componentChain;
    }

    public function getComponentChain(){
        return $this->componentChain;
    }

    public function getConfigurator($className, $parent = null, $entityName = null, $parameters = []){
        $reflectionClass =  new \ReflectionClass($className);
        return $reflectionClass->newInstance($this, $parent, $entityName, $parameters);
    }

    public function generateConfigurator($entityClassName, $nameConfigurator = null, $parent = null){
        return $this->getConfigurator($this->getConfiguratorClassName($entityClassName, $nameConfigurator), $parent, $entityClassName, ['entity'=>$this->mem->getEntityShortName($entityClassName)]);
    }

    public function getConfiguratorClassName($entityClassName, $nameConfigurator = null){
        $annotation = $this->mem->getMetaDataEntityByClassName($entityClassName);
        return $annotation->getConfigClass($nameConfigurator) ?? DefaultConfigurator::class;
    }

    public function isGranted($attributes, $subject = null){
        return $this->authorizationChecker->isGranted($attributes, $subject);
    }

    public function getDefaultConfigurator($shortname, $entityClassName, Entity $annotation, $parent = null){
        $c = new DefaultConfigurator($this, $parent, $entityClassName, ['entity'=>$shortname]);
        $c->setTitle($annotation->getTitle() ?? 'lego.'.$shortname.'.title');
        return $c;
    }

    public function getSessionStorage($id, $key, $default = null){
        if($this->session->has($id)){
            $componentSessionStorage = $this->session->get($id);
            return (isset($componentSessionStorage[$key]))? $componentSessionStorage[$key]:$default;
        }else{
            return $default;
        }
    }

    public function setSessionStorage($id, $key, $value){
        if(!$this->session->has($id)){
            $this->session->set($id, []);
        }
        $componentSessionStorage = $this->session->get($id);
        $componentSessionStorage[$key] = $value;
        $this->session->set($id, $componentSessionStorage);
        return $this;
    }

    public function getUser(){
        return $this->security->getToken()->getUser();
    }

    public function getMetaEntityManager(): MetaEntityManager{
        return $this->mem;
    }

    public function trans($str, $vars){
        $this->translator->trans($str, $vars);
    }

    public function render($view, $params){
        return $this->twig->render($view, $params);
    }

    public function getRouter(){
        return $this->router;
    }

    public function getTwig(): \Twig_Environment{
        return $this->twig;
    }

    public function getSession(){
        return $this->session;
    }

    public function hasAccess($className, $suffixRoute){
        $annotation = $this->mem->getMetaDataEntityByClassName($className);
        $roles = $annotation->getRoles($suffixRoute);
        foreach($roles as $role){
            if($this->isGranted($role)){
                return true;
            }
        }
        if(\count($roles) === 0) return true;
        return false;
    }

}
