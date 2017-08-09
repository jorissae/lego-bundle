<?php

namespace Idk\LegoBundle\AdminList;

interface ExportableInterface
{
    public function getExportColumns();

    public function getAllIterator();

    public function getStringValue($item, $columnName);
}
