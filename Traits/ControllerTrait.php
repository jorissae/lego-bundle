<?php
namespace Idk\LegoBundle\Traits;

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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Idk\LegoBundle\Service\MetaEntityManager;

trait ControllerTrait
{
    


    protected function getConfigurator(){
        return $this->container->get('lego.service.configurator.builder')->getConfigurator(self::LEGO_CONFIGURATOR);
    }

    protected function getResponse(string $action, Request $request): Response{
        return $this->get(ActionChain::class)->getResponse($action, $this->getConfigurator(), $request);
    }


    /**
     * The index action
     *
     * @Route("/")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        return $this->getResponse(IndexAction::class, $request);
    }

    /**
     * The show action
     *
     * @param int $id
     *
     * @Route("/{id}/show", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function showAction(Request $request)
    {
        return $this->getResponse(ShowAction::class, $request);
    }

    /**
     * The add action
     *
     * @Route("/add")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function addAction(Request $request)
    {
        return $this->getResponse(AddAction::class, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        return $this->getResponse(EditAction::class, $request);
    }

    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request)
    {
        return $this->getResponse(DeleteAction::class, $request);
    }

    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/export.{format}", requirements={"format" = "csv|xlsx"})
     * @Method({"GET", "POST"})
     * @return array
     */
    public function exportAction(Request $request)
    {
        return $this->getResponse(ExportAction::class, $request);
    }


    /**
     * The edit in place action
     *
     * @Route("/eip")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function editInPlaceAction(Request $request)
    {
        return $this->getResponse(EditInPlaceAction::class, $request);
    }

    /**
     * The edit in place action
     *
     * @Route("/entity/reload/{id}/{cid}/{suffix_route}")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function entityReloadAction(Request $request)
    {
        return $this->getResponse(EntityReloadAction::class, $request);
    }

    /**
     * The edit in place action
     *
     * @Route("/eip/choice/{cid}/{suffix_route}")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function editInPlaceEntityChoiceAction(Request $request)
    {
        return $this->getResponse(EditInPlaceEntityChoiceAction::class, $request);
    }

    /**
     * sort components
     *
     * @Route("/sc/{suffix_route}")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function sortComponentsAction(Request $request)
    {
        return $this->getResponse(SortComponentsAction::class, $request);
    }

    /**
     * sort components reset
     *
     * @Route("/screset/{suffix_route}")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function sortComponentsResetAction(Request $request)
    {
        return $this->getResponse(SortComponentsResetAction::class, $request);
    }

    /**
     * The auto completion action
     *
     * @Route("/ac")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function autoCompletionAction(Request $request)
    {
        return $this->getResponse(AutoCompletionAction::class, $request);
    }

    /**
     * The auto completion action
     *
     * @Route("/component/{cid}/{suffix_route}")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function componentAction(Request $request)
    {
        return $this->getResponse(ComponentAction::class, $request);
    }

    /**
     * The auto completion action
     *
     * @Route("/bulk/{cid}/{ida}")
     * @Method({"POST"})
     * @return array
     */
    public function bulkAction(Request $request)
    {
        return $this->getResponse(BulkAction::class, $request);
    }

}