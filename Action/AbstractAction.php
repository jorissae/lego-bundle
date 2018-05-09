<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;



use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\ComponentResponse\MessageComponentResponse;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractAction
{

    protected $configuratorBuilder;
    protected $configurator = null;
    protected $mem;

    public function __construct(ConfiguratorBuilder $configuratorBuilder)
    {
        $this->configuratorBuilder = $configuratorBuilder;
        $this->mem = $this->configuratorBuilder->getMetaEntityManager();
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
        return $this->configuratorBuilder>trans($str, $vars);
    }

    public function getConfigurator(Request $request){
        if($this->configurator) return $this->configurator;
        $metaEntity = $this->mem->getMetaDataEntity($request->get('entity'));
        return  $metaEntity->getConfigurator($this->configuratorBuilder);
    }

    protected function renderView(string $view, array $parameters = array()): string
    {
        return $this->configuratorBuilder->render($view, $parameters);
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

    protected function addFlash(string $type, ?string $message)
    {
        $this->configuratorBuilder->getSession()->getFlashBag()->add($type, $message);
    }

    protected function redirectToRoute(string $route, array $parameters = array(), int $status = 302): RedirectResponse
    {
        return new RedirectResponse($this->generateUrl($route, $parameters), $status);
    }

    protected function generateUrl(string $route, array $parameters = array(), int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->configuratorBuilder->getRouter()->generate($route, $parameters, $referenceType);
    }

}