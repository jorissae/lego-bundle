<?php

namespace Idk\LegoBundle\AdminList;

use Idk\LegoBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Idk\LegoBundle\AdminList\Configurator\ExportListConfiguratorInterface;

/**
 * AdminListFactory
 */
class AdminListFactory
{

    /**
     * @param AdminListConfiguratorInterface $configurator The configurator
     *
     * @return AdminList
     */
    public function createList(AdminListConfiguratorInterface $configurator)
    {
        return new AdminList($configurator);
    }

    /**
     * @param $configurator
     * @return ExportList
     */
    public function createExportList(ExportListConfiguratorInterface $configurator)
    {
        return new ExportList($configurator);
    }
}
