<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;



use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\ComponentResponse\MessageComponentResponse;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAction
{

    use ControllerTrait;
    protected $mem;
    protected $container;
    protected $configurator = null;

    public function __construct(Container $container, MetaEntityManager $mem){
        $this->mem = $mem;
        $this->container = $container;
    }

    abstract function  __invoke(Request $request): Response;

    public function getResponse(Request $request): Response{
        return $this($request);
    }

    public function setConfigurator(AbstractConfigurator $configurator){
        $this->configurator = $configurator;
    }

    public function getEntityManager(){
        return $this->mem->getEntityManager();
    }

    protected function trans($str, $vars= []){
        return $this->get('translator')->trans($str, $vars);
    }

    public function getConfigurator(Request $request){
        if($this->configurator) return $this->configurator;
        $metaEntity = $this->mem->getMetaDataEntity($request->get('entity'));
        return  $metaEntity->getConfigurator($this->container);
    }


    protected function comunicateComponents(AbstractConfigurator $configurator,  $request, $entityId = null){
        $redirect = null;
        $componentResponses = $configurator->bindRequest($request);
        foreach($componentResponses as $componentResponse){
            if($componentResponse instanceof MessageComponentResponse) {
                if($componentResponse->hasRedirect()){
                    if($redirect != null){
                        throw new \Exception('Component Conflit: You have several redirection from your components');
                    }else{
                        $redirect = $componentResponse->getRedirect();
                    }
                }
                $this->addFlash($componentResponse->getType(),$componentResponse->getMessage());
            }
        }
        if($redirect){
            return $this->redirectToRoute($redirect['path'], $redirect['params']);
        }else{
            return null;
        }
    }

}