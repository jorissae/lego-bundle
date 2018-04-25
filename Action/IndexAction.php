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

final class IndexAction
{

    use ControllerTrait;
    private $mem;
    private $container;

    public function __construct(Container $container, MetaEntityManager $mem){
        $this->mem = $mem;
        $this->container = $container;
    }


    public function __invoke(Request $request, $entity, $index): Response
    {
        $metaEntity = $this->mem->getMetaDataEntitie($entity);
        $configurator = $metaEntity->getConfigurator($this->container);
        $response = $this->comunicateComponents($configurator, $request);
        if($response) return $response;
        return new Response($this->renderView($configurator->getIndexTemplate(), [ 'configurator' => $configurator ]));
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