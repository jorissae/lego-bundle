<?php
namespace Idk\LegoBundle\Traits;

use Idk\LegoBundle\Service\MetaEntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

trait Controller
{
    


    protected function getConfigurator(){
        $class = self::LEGO_CONFIGURATOR;
        return new $class($this->container);
    }


    /**
     * The index action
     *
     * @Route("/")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getConfigurator(), $request);
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
    public function showAction(Request $request, $id)
    {
        return parent::doShowAction($this->getConfigurator(), $id, $request);
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
        return parent::doAddAction($this->getConfigurator(), $request);
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
        return parent::doEditAction($this->getConfigurator(), $id, $request);
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
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getConfigurator(), $id, $request);
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
        return parent::doExportAction($this->getConfigurator(), $request);
    }


    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/logs")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function logsAction(Request $request)
    {
        return parent::doLogsAction($this->getConfigurator(), $request);
    }

    /**
     * The log action
     *
     * @Route("/log/{id}")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function logAction(Request $request, $id)
    {
        return parent::doLogAction($this->getConfigurator(), $id, $request);
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
        return parent::doEditInPlaceAction($this->getConfigurator(), $request);
    }

    /**
 * order components
 *
 * @Route("/oc/{suffix_route}")
 * @Method({"GET", "POST"})
 * @return array
 */
    public function orderComponentsAction(Request $request)
    {
        return parent::doOrderComponents($this->getConfigurator(), $request);
    }

    /**
     * order components
     *
     * @Route("/ocreset/{suffix_route}")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function orderComponentsResetAction(Request $request)
    {
        return parent::doOrderComponentsReset($this->getConfigurator(), $request);
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
        return parent::doAutoCompleteAction($this->getConfigurator(), $request);
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
        return parent::doComponentAction($this->getConfigurator(), $request);
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
        return parent::doBulkAction($this->getConfigurator(), $request);
    }

}