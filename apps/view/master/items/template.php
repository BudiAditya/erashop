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
$phpExcel = $reader->load(APPS . "templates/upload-items.xls");
$filename = "template-itemlist-upload.xls";
$headers = array(
    'Content-Type: application/vnd.ms-excel'
, sprintf('Content-Disposition: attachment;filename="%s"', $filename)
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);

// Tulis data Jenis Barang
$sheet = $phpExcel->setActiveSheetIndexByName("Jenis Barang");
$sheet->getColumnDimension("B")->setAutoSize(true);
$brs = 3;
/** @var  $ijenis ItemJenis[]*/
foreach ($ijenis as $bjenis) {
    $brs++;
    $sheet->setCellValue("A" . $brs, $bjenis->Id);
    $sheet->setCellValue("B" . $brs, $bjenis->JnsBarang);
    $sheet->setCellValue("C" . $brs, $bjenis->Keterangan);
}
// Tulis data Divisi Barang
$sheet = $phpExcel->setActiveSheetIndexByName("Divisi Barang");
$sheet->getColumnDimension("B")->setAutoSize(true);
$brs = 3;
/** @var $idivisi ItemDivisi[] */
foreach ($idivisi as $bdivisi) {
    $brs++;
    $sheet->setCellValue("A" . $brs, $bdivisi->Id);
    $sheet->setCellValue("B" . $brs, $bdivisi->Divisi);
    $sheet->setCellValue("C" . $brs, $bdivisi->Keterangan);
}
// Tulis data Kelompok Barang
$sheet = $phpExcel->setActiveSheetIndexByName("Kelompok Barang");
$sheet->getColumnDimension("B")->setAutoSize(true);
$brs = 3;
/** @var $ikelompok ItemKelompok[] */
foreach ($ikelompok as $bkelompok) {
    $brs++;
    $sheet->setCellValue("A" . $brs, $bkelompok->Id);
    $sheet->setCellValue("B" . $brs, $bkelompok->Kelompok);
    $sheet->setCellValue("C" . $brs, $bkelompok->Keterangan);
}
// Tulis data Satuan Barang
$sheet = $phpExcel->setActiveSheetIndexByName("Satuan Barang");
$sheet->getColumnDimension("B")->setAutoSize(true);
$brs = 3;
/** @var $isatuan ItemUom[] */
foreach ($isatuan as $bsatuan) {
    $brs++;
    $sheet->setCellValue("A" . $brs, $bsatuan->Sid);
    $sheet->setCellValue("B" . $brs, $bsatuan->Skode);
    $sheet->setCellValue("C" . $brs, $bsatuan->Snama);
}
// Tulis data Supplier Barang
$sheet = $phpExcel->setActiveSheetIndexByName("Daftar Supplier");
$sheet->getColumnDimension("B")->setAutoSize(true);
$brs = 3;
/** @var $isupplier Contacts[] */
foreach ($isupplier as $bsupplier) {
    $brs++;
    $sheet->setCellValue("A" . $brs, $bsupplier->Id);
    $sheet->setCellValue("B" . $brs, $bsupplier->ContactCode);
    $sheet->setCellValue("C" . $brs, $bsupplier->ContactName);
}
// Hmm Reset Pointer
$sheet = $phpExcel->setActiveSheetIndexByName("Data Barang");
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
