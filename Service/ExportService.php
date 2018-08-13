<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service;

use Idk\LegoBundle\Component\Component;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
    private $renderer;
    private $serviceCsv;
    private $mem;

    const EXT_CSV = 'csv';
    const EXT_EXCEL = 'xlsx';

    public function __construct(\Twig_Environment $renderer, $serviceCsv, MetaEntityManager $mem)
    {
        $this->renderer = $renderer;
        $this->serviceCsv = $serviceCsv;
        $this->mem = $mem;
    }

    public function getExportFields($entityName, array $columns = null){
        return $this->mem->generateExportFields($entityName, $columns);
    }

    public function getDownloadableResponse(AbstractConfigurator $configurator, Component $component ,$format)
    {
        switch ($format) {
            case self::EXT_EXCEL:
                $writer = $this->createExcelSheet($configurator, $component);
                $response = $this->createResponseForExcel($writer);
                break;
            default:
                $response = $this->createCsvResponse($configurator, $component);
                break;
        }
        return $response;
    }

    public function createCsvResponse(AbstractConfigurator $configurator, $component){
        $allIterator = $configurator->getItems($component);
        $csv = [];
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
    public function createExcelSheet(AbstractConfigurator $configurator, $component)
    {

        $objPHPExcel = new Spreadsheet();

        $objWorksheet = $objPHPExcel->getActiveSheet();

        $number = 1;

        $row = [];
        foreach ($this->getExportFields($configurator->getEntityName()) as $field) {
            $row[] = $field->getHeader();
        }
        $objWorksheet->fromArray($row, null, 'A' . $number++);

        $allIterator = $configurator->getItems($component);
        foreach($allIterator as $entity) {

            $row = [];
            foreach ($this->getExportFields($configurator->getEntityName()) as $field) {
                $coordinate = $objWorksheet->getCellByColumnAndRow(count($row), $number)->getCoordinate();
                $data = $configurator->getStringValue($entity, $field->getName());
                if (is_object($data)) {
                    if (!$this->renderer->getLoader()->exists($field->getTemplate())) {
                        $data = $data->__toString();
                    }else{
                        $data = $this->renderer->render($field->getTemplate(), array("entity" => $entity, 'configurator' => $configurator, 'field' => $field));
                    }
                }
                if($field->is('string')){
                    $objWorksheet->getStyle($coordinate)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_TEXT);
                }
                $row[] = $data;


            }
            $objWorksheet->fromArray($row, null, 'A' . $number++);

        }

        $objWriter = new Xlsx($objPHPExcel);


        if (ob_get_length()) ob_end_clean();
        return $objWriter;
    }

    public function createResponseForExcel(Xlsx $writer)
    {
        if(!class_exists('\ZipArchive')){
            throw new \Exception('ZipArchive not found install it apt-get install php-zip or http://php.net/manual/fr/zip.installation.php');
        }
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        //$response = new Response($writer->save('php://output'));

        $response->headers->set('Content-Type', 'application/download');
        $filename = 'export.xlsx';
        $response->headers->set('Content-Disposition', sprintf('attachment; filename=%s', $filename));
        return $response;
    }
}
