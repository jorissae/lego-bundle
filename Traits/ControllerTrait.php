<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Traits;

use Idk\LegoBundle\Action\DefaultAction;
use Idk\LegoBundle\Action\EditInPlaceEntityChoiceAction;
use Idk\LegoBundle\Action\EntityReloadAction;
use Idk\LegoBundle\Service\Tag\ActionChain;
use Idk\LegoBundle\Action\AutoCompletionAction;
use Idk\LegoBundle\Action\EditInPlaceAction;
use Idk\LegoBundle\Action\IndexAction;
use Idk\LegoBundle\Action\SortComponentsAction;
use Idk\LegoBundle\Action\SortComponentsResetAction;
use Idk\LegoBundle\Action\ExportAction;
use Idk\LegoBundle\Action\ShowAction;
use Idk\LegoBundle\Action\ComponentAction;
use Idk\LegoBundle\Action\AddAction;
use Idk\LegoBundle\Action\BulkAction;
use Idk\LegoBundle\Action\DeleteAction;
use Idk\LegoBundle\Action\EditAction;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Idk\LegoBundle\Service\MetaEntityManager;

trait ControllerTrait
{


    protected function getConfigurator()
    {
        return $this->container->get('lego.service.configurator.builder')->getConfigurator(self::LEGO_CONFIGURATOR);
    }

    protected function getResponse(string $action, Request $request): Response
    {
        return $this->get(ActionChain::class)->getResponse($action, $this->getConfigurator(), $request);
    }


    /**
     * The index action
     *
     * @Route("/", methods={"GET", "POST"})
     */
    public function indexAction(Request $request): Response
    {
        return $this->getResponse(IndexAction::class, $request);
    }

    /**
     * The show action
     *
     * @param int $id
     *
     * @Route("/{id}/show", requirements={"id" = "\d+"}, methods={"GET"})
     *
     * @return Response
     */
    public function showAction(Request $request): Response
    {
        return $this->getResponse(ShowAction::class, $request);
    }

    /**
     * The add action
     *
     * @Route("/add", methods={"GET", "POST"})
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        return $this->getResponse(AddAction::class, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     *
     * @return Response
     */
    public function editAction(Request $request, $id): Response
    {
        return $this->getResponse(EditAction::class, $request);
    }

    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     *
     * @return Response
     */
    public function deleteAction(Request $request): Response
    {
        return $this->getResponse(DeleteAction::class, $request);
    }

    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/{cid}/{suffix_route}/export.{format}", defaults={"cid"=0}, requirements={"format" = "csv|xlsx"}, methods={"GET", "POST"})
     * @return Response
     */
    public function exportAction(Request $request): Response
    {
        return $this->getResponse(ExportAction::class, $request);
    }


    /**
     * The edit in place action
     *
     * @Route("/eip", methods={"GET", "POST"})
     * @return Response
     */
    public function editInPlaceAction(Request $request): Response
    {
        return $this->getResponse(EditInPlaceAction::class, $request);
    }

    /**
     * The edit in place action
     *
     * @Route("/entity/reload/{id}/{cid}/{suffix_route}", methods={"GET", "POST"})
     * @return Response
     */
    public function entityReloadAction(Request $request): Response
    {
        return $this->getResponse(EntityReloadAction::class, $request);
    }

    /**
     * The edit in place action
     *
     * @Route("/eip/choice/{cid}/{suffix_route}", methods={"GET", "POST"})
     * @return Response
     */
    public function editInPlaceEntityChoiceAction(Request $request): Response
    {
        return $this->getResponse(EditInPlaceEntityChoiceAction::class, $request);
    }

    /**
     * sort components
     *
     * @Route("/sc/{suffix_route}", methods={"GET", "POST"})
     * @return Response
     */
    public function sortComponentsAction(Request $request): Response
    {
        return $this->getResponse(SortComponentsAction::class, $request);
    }

    /**
     * sort components reset
     *
     * @Route("/screset/{suffix_route}", methods={"GET", "POST"})
     * @return Response
     */
    public function sortComponentsResetAction(Request $request): Response
    {
        return $this->getResponse(SortComponentsResetAction::class, $request);
    }

    /**
     * The auto completion action
     *
     * @Route("/ac", methods={"GET", "POST"})
     * @return Response
     */
    public function autoCompletionAction(Request $request): Response
    {
        return $this->getResponse(AutoCompletionAction::class, $request);
    }

    /**
     * The auto completion action
     *
     * @Route("/component/{cid}/{suffix_route}", methods={"GET", "POST"})
     * @return Response
     */
    public function componentAction(Request $request): Response
    {
        return $this->getResponse(ComponentAction::class, $request);
    }

    /**
     * The auto completion action
     *
     * @Route("/bulk/{cid}/{ida}", methods={"POST"})
     * @return Response
     */
    public function bulkAction(Request $request): Response
    {
        return $this->getResponse(BulkAction::class, $request);
    }

    /**
     * The index action
     *
     * @Route("/{suffix_route}", methods={"GET", "POST"})
     * @return Response
     */
    public function defaultAction(Request $request): Response
    {
        return $this->getResponse(DefaultAction::class, $request);
    }

}