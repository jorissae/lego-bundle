<?php

namespace {{ namespace }}\Controller;

use {{ namespace }}\AdminList\{{ entity_class }}AdminListConfigurator;
use {{ namespace }}\Entity\{{ entity_class }};
use Idk\LegoBundle\Controller\AdminListController;
use Idk\LegoBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The admin list controller for {{ entity_class }}
 * @Route("/{{ entity_class|lower }}")
 */
class {{ entity_class }}AdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (!isset($this->configurator)) {
            $this->configurator = new {{ entity_class }}AdminListConfigurator($this->container);
        }

        return $this->configurator;
    }

    /**
     * The index action
     *
     * @Route("/", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}")
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_LIST')")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The add action
     *
     * @Route("/add", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_add")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_ADD')")
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
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_EDIT')")
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
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_DELETE')")
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }


    /**
     * The show action
     *
     * @param int $id
     *
     * @Route("/{id}/show", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_show")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_LIST')")
     * @return array
     */
    public function showAction(Request $request, $id)
    {
        return parent::doShowAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/export.{_format}", requirements={"_format" = "{{ export_extensions }}"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_export")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_EXPORT')")
     * @return array
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }

    /**
     * The edit in place action
     *
     * @Route("/eip", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_edit_in_place")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_EDIT')")
     * @return array
     */
    public function editInPlaceAction(Request $request)
    {
        return parent::doEditInPlaceAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/eipattr", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_edit_in_place_attribut")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_EDIT')")
     * @return array
     */
    public function editInPlaceAttributAction(Request $request)
    {
        return parent::doEditInPlaceAttributAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The edit in place action
     *
     * @Route("/autocomplete", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_auto_complete")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function autoCompleteAction(Request $request)
    {
        return parent::doAutoCompleteAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The log action
     *
     *
     * @Route("/logs", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_logs")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_LOGS')")
     * @return array
     */
    public function logsAction(Request $request)
    {
        return parent::doLogsAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The log action
     *
     *
     * @Route("/log/{id}", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_log")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_LOGS')")
     * @return array
     */
    public function logAction(Request $request,$id)
    {
        return parent::doLogAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The workflow action
     *
     *
     * @Route("/ajax/workflow/{id}", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_wf")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_WF')")
     * @return array
     */
    public function workflowAction({{ entity_class }} $entity)
    {
        return parent::doWorkflowAction($this->getAdminListConfigurator(), $entity);
    }

    /**
     * The item action
     *
     *
     * @Route("/ajax/item/{id}/{ida}/{type}", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_item")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_ITEM')")
     * @return array
     */
    public function itemAction({{ entity_class }} $entity, $ida, $type)
    {
        return parent::doItemAction($this->getAdminListConfigurator(), $entity, $ida, $type);
    }

    /**
     * The bulk action
     *
     *
     * @Route("/action/bulk/{ida}/{type}", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_bulk")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_BULK')")
     * @return array
     */
    public function bulkAction($ida, $type)
    {
        return parent::doBulkAction($this->getAdminListConfigurator(), $ida, $type);
    }

    /**
     * The list action
     *
     *
     * @Route("/action/list/{ida}/{type}", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_alist")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN_{{ entity_class|upper }}_ALIST')")
     * @return array
     */
    public function alistAction($ida, $type)
    {
        return parent::doAlistAction($this->getAdminListConfigurator(), $ida, $type);
    }
}
