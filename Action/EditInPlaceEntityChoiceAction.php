<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;

use Idk\LegoBundle\Component\EditInPlaceInterface;
use Idk\LegoBundle\Component\FieldsInterface;
use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Idk\LegoBundle\Service\MetaEntityManager;
use Idk\LegoBundle\Service\Tag\WidgetChain;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class EditInPlaceEntityChoiceAction extends AbstractAction
{

    private $security;

    public function __construct(ConfiguratorBuilder $builder,AuthorizationCheckerInterface $security){
        parent::__construct($builder);
        $this->security = $security;
    }

    public function __invoke(Request $request): Response
    {
        $em = $this->mem->getEntityManager();
        $configurator = $this->getConfigurator($request);
        $this->denyAccessUnlessGranted($configurator->getEntityName(), 'edit');
        $this->denyAccessUnlessGranted($configurator->getEntityName(), 'edit_in_place');
        $component = $configurator->getComponent($request->get('suffix_route'),$request->get('cid'));
        if($component instanceof EditInPlaceInterface) {

            $configurator->bindRequestCurrentComponents($request, $component);
            $entitySource = str_replace('/', '\\', $request->get('entity_source'));
            $entitySourceId = $request->get('entity_source_id');
            $entityTarget = str_replace('/', '\\', $request->get('entity_target'));
            $field = $component->getField($request->get('fieldname'));
            $item = $em->getRepository($entitySource)->find($entitySourceId);
            if ($field->isEditInPlace($item) and (!$field->getEditInPlaceRole() or $this->security->isGranted($field->getEditInPlaceRole()))) {
                $edit = $field->getEditInPlace();
                $method = (isset($edit['method'])) ? $edit['method'] : 'findAll';
                if (isset($edit['object-in-argument']) and $edit['object-in-argument']) {
                    $list = $em->getRepository($entityTarget)->$method($object);
                } else {
                    $list = $em->getRepository($entityTarget)->$method();
                }
                $return = array();
                if ($list) {
                    foreach ($list as $entity) {
                        $return[$entity->getId()] = (string)$entity;
                    }
                }
                return new JsonResponse($return);
            }
        }
        return new JsonResponse([]);
    }

}