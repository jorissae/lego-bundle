<?php

namespace Idk\LegoBundle\Service;

use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    private $renderer;
    private $serviceCsv;
    private $mem;

    const EXT_CSV = 'csv';
    const EXT_EXCEL = 'xlsx';

    public function __construct($renderer, $serviceCsv, MetaEntityManager $mem)
    {
        $this->renderer = $renderer;
        $this->serviceCsv = $serviceCsv;
        $this->mem = $mem;
    }

    public function getExportFields($entityName, array $columns = null){
        return $this->mem->generateExportFields($entityName, $columns);
    }

    public function getDownloadableResponse(AbstractConfigurator $configurator, $format)
    {
        switch ($format) {
            case self::EXT_EXCEL:
                $writer = $this->createExcelSheet($configurator);
                $response = $this->createResponseForExcel($writer);
                break;
            default:
                $response = $this->createCsvResponse($configurator);
                break;
        }
        return $response;
    }

    public function createCsvResponse(AbstractConfigurator $configurator){
        $allIterator = $configurator->getAllIterator();
        foreach($this->getExportFields($configurator->getEntityName()) as $field){
            $csv[0][] = $field->getHeader();
        }
        $i=1;
        foreach($allIterator as $entity){
            foreach($this->getExportFields($configurator->getEntityName()) as $field) {
                $csv[$i][] = $configurator->getStringValue($entity, $field->getName());
            }
                $i++;
        }
        return  $this->serviceCsv->arrayToCsvResponse($csv);
    }

    /**
     * @param $configurator
     * @return \PHPExcel_Writer_Excel2007
     * @throws \Exception
     * @throws \PHPExcel_Exception
     */
    public function createExcelSheet(AbstractConfigurator $configurator)
    {
        $objPHPExcel = new \PHPExcel();

        $objWorksheet = $objPHPExcel->getActiveSheet();

        $number = 1;

        $row = [];
        foreach ($this->getExportFields($configurator->getEntityName()) as $field) {
            $row[] = $field->getHeader();
        }
        $objWorksheet->fromArray($row, null, 'A' . $number++);

        $allIterator = $configurator->getAllIterator();
        foreach($allIterator as $entity) {

            $row = [];
            foreach ($this->getExportFields($configurator->getEntityName()) as $field) {
                $coordinate = $objWorksheet->getCellByColumnAndRow(count($row), $number)->getCoordinate();
                $data = $configurator->getStringValue($entity, $field->getName());
                if (is_object($data)) {
                    if (!$this->renderer->exists($field->getTemplate())) {
                        $data = $data->__toString();
                    }else{
                        $data = $this->renderer->render($field->getTemplate(), array("entity" => $entity, 'configurator' => $configurator, 'field' => $field));
                    }
                }
                if($field->is('string')){
                    $objWorksheet->getStyle($coordinate)
                        ->getNumberFormat()
                        ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                }
                $row[] = $data;


            }
            $objWorksheet->fromArray($row, null, 'A' . $number++);

        }

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);

        if (ob_get_length()) ob_end_clean();
        return $objWriter;
    }

    public function createResponseForExcel($writer)
    {
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        $response->headers->set('Content-Type', 'application/download');
        $filename = 'export.xlsx';
        $response->headers->set('Content-Disposition', sprintf('attachment; filename=%s', $filename));
        return $response;
    }
}
