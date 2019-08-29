<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Eraditya Inc
 * Date: 26/01/15
 * Time: 16:12
 * To change this template use File | Settings | File Templates.
 */
require_once(LIBRARY . "PHPExcel.php");

// Ini file pure akan membuat file excel dan tidak ada HTML fragment
$reader = new PHPExcel_Reader_Excel5(); // Report Template
$phpExcel = $reader->load(APPS . "templates/upload-item-prices.xls");
$filename = "template-item-pricelist-upload.xls";
$headers = array(
    'Content-Type: application/vnd.ms-excel'
, sprintf('Content-Disposition: attachment;filename="%s"', $filename)
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);

// Tulis data Harga Barang Barang
$sheet = $phpExcel->setActiveSheetIndexByName("Data Harga");
$sheet->getColumnDimension("B")->setAutoSize(true);
$brs = 3;
$nmr = 0;
/** @var  $prices SetPrice[]*/
foreach ($prices as $bprices) {
    $brs++;
    $nmr++;
    $sheet->setCellValue("A" . $brs, $nmr);
    $sheet->setCellValue("B" . $brs, $bprices->ItemId);
    $sheet->setCellValue("C" . $brs, date('d-m-Y',$bprices->PriceDate));
    $sheet->setCellValueExplicit("D" . $brs, $bprices->ItemCode,PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValueExplicit("E" . $brs, $bprices->ItemName,PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValue("F" . $brs, $bprices->Satuan);
    $sheet->setCellValue("G" . $brs, $bprices->HrgBeli);
    $sheet->setCellValue("H" . $brs, $bprices->MaxDisc);
    $sheet->setCellValue("I" . $brs, $bprices->HrgJual1);
    $sheet->setCellValue("J" . $brs, $bprices->HrgJual2);
    $sheet->setCellValue("K" . $brs, $bprices->HrgJual3);
    $sheet->setCellValue("L" . $brs, $bprices->HrgJual4);
    $sheet->setCellValue("M" . $brs, $bprices->HrgJual5);
    $sheet->setCellValue("N" . $brs, $bprices->HrgJual6);
}
// Hmm Reset Pointer
$sheet->getStyle("A1");

// Flush to client
foreach ($headers as $header) {
    header($header);
}
// Hack agar client menutup loading dialog box... (Ada JS yang checking cookie ini pada common.js)
setcookie("startDownload", 1);
$writer->save("php://output");

// Garbage Collector
$phpExcel->disconnectWorksheets();
unset($phpExcel);
ob_flush();
exit();
