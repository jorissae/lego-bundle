<?php
namespace Idk\LegoBundle\Traits;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

trait Controller
{
    


    protected function getConfigurator(){
        $class = self::LEGO_CONFIGURATOR;
        return new $class($this->container);
    }


    public function getAdminListConfigurator()
    {
        return $this->getConfigurator();
    }

    /**
     * The index action
     *
     * @Route("/")
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

        return parent::doShowAction($this->getAdminListConfigurator(), $id, $request);
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
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
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
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
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
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/export.{_format}", requirements={"_format" = "csv|xlsx"})
     * @Method({"GET", "POST"})
     * @return array
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
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
        return parent::doLogsAction($this->getAdminListConfigurator(), $request);
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
        return parent::doLogAction($this->getAdminListConfigurator(), $id, $request);
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
        return parent::doEditInPlaceAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The edit in place action
     *
     * @Route("/autocomplete")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function autoCompleteAction(Request $request)
    {
        return parent::doAutoCompleteAction($this->getAdminListConfigurator(), $request);
    }


}