<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Idk\LegoBundle\Service\EditInPlaceFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditInPlaceAction extends AbstractFormAction
{

    private $eipFactory;

    public function __construct(ConfiguratorBuilder $builder, FormFactoryInterface $formFactory, EditInPlaceFactory $eipFactory){
        parent::__construct($builder,$formFactory);
        $this->eipFactory = $eipFactory;
    }

    public function __invoke(Request $request): Response
    {
        $configurator = $this->getConfigurator($request);
        $component = $configurator->getComponent($request->get('suffix_route'), $request->get('cid'));
        $field = $component->getField($request->request->get('fieldName'));


        $this->createFormBuilder();
        $em = $this->getEntityManager();
        $reload = $request->request->get('reload');
        $entity = $em->getRepository($component->getConfigurator()->getRepositoryName())->findOneById($request->request->get('id'));
        $fieldName = $field->getName();
        $class = $request->request->get('cls');
        $type = $this->eipFactory->getEditInPlaceType(
            $component->getConfigurator()->getType($entity, $field->getName()),
            $field->getValue($component->getConfigurator(),$entity),
            $field->getName());

        $value = $type->getValueFromAction($request, $this);

        $persist = $field->setValue($component->getConfigurator(), $entity, $value);
        $em->persist($persist);
        $em->flush();
        $stringValue = $configurator->getStringValue($entity,$fieldName);
        if($reload == 'entity') {

            $configurator->bindRequestCurrentComponents($request, $component);
            $component->xhrBindRequest($request);
            $return = array('code' => 'OK', 'val' => (string)html_entity_decode($component->renderEntity($entity)));
        }elseif($reload == 'field'){
            $component = $configurator->getComponent($request->get('suffix_route'), $request->get('cid'));
            $configurator->bindRequestCurrentComponents($request, $component);
            $component->xhrBindRequest($request);
            $template = $this->configuratorBuilder->getTwig()->createTemplate('{{ render_field_value(component, field, item) }}');
            $html = $template->render(['component'=>$component,'item'=>$entity, 'field'=> $component->getField($fieldName)]);
            $return = array('code' => 'OK', 'val' => (string)html_entity_decode($html));
        }else{
            $return = array('code'=>'OK','val'=>(string)$stringValue);
        }
        return new Response(json_encode($return));
    }

}