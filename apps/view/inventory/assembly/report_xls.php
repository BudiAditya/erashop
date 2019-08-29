<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Eraditya Inc
 * Date: 16/01/15
 * Time: 7:42
 * To change this template use File | Settings | File Templates.
 */
$phpExcel = new PHPExcel();
$headers = array(
    'Content-Type: application/vnd.ms-excel'
, 'Content-Disposition: attachment;filename="print-rekap-produksi.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erasystem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erasystem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Rekapitulasi Produksi");
//helper for styling
$center = array("alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
$right = array("alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
$allBorders = array("borders" => array("allborders" => array("style" => PHPExcel_Style_Border::BORDER_THIN)));
$idrFormat = array("numberformat" => array("code" => '_([$-421]* #,##0_);_([$-421]* (#,##0);_([$-421]* "-"??_);_(@_)'));
// OK mari kita bikin ini cuma bisa di read-only
//$password = "" . time();
//$sheet->getProtection()->setSheet(true);
//$sheet->getProtection()->setPassword($password);

// FORCE Custom Margin for continous form
/*
$sheet->getPageMargins()->setTop(0)
    ->setRight(0.2)
    ->setBottom(0)
    ->setLeft(0.2)
    ->setHeader(0)
    ->setFooter(0);
*/
$row = 1;
$sheet->setCellValue("A$row",$company_name);
// Hmm Reset Pointer
$sheet->getStyle("A1");
$sheet->setShowGridlines(false);
$row++;
if ($JnsLaporan == 1) {
    $sheet->setCellValue("A$row", "LAPORAN PRODUKSI");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Cabang");
    $sheet->setCellValue("C$row", "Tanggal");
    $sheet->setCellValue("D$row", "No. Produksi");
    $sheet->setCellValue("E$row", "Kode Barang");
    $sheet->setCellValue("F$row", "Nama Barang");
    $sheet->setCellValue("G$row", "Satuan");
    $sheet->setCellValue("H$row", "Q T Y");
    $sheet->setCellValue("I$row", "Nilai Produksi");
    $sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($center, $allBorders));
    $nmr = 0;
    $str = $row;
    if ($Reports != null) {
        $ivn = null;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            $nmr++;
            $sheet->setCellValue("A$row", $nmr);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", $rpt["cabang_code"]);
            $sheet->setCellValue("C$row", date('d-m-Y', strtotime($rpt["assembly_date"])));
            $sheet->setCellValue("D$row", $rpt["assembly_no"]);
            $sheet->setCellValue("E$row", $rpt["item_code"]);
            $sheet->setCellValue("F$row", $rpt["item_name"]);
            $sheet->setCellValue("G$row", $rpt["bsatkecil"]);
            $sheet->setCellValue("H$row", $rpt["qty"]);
            $sheet->setCellValue("I$row", round($rpt["qty"]*$rpt["price"],0));
            $sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($allBorders));
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "TOTAL...");
        $sheet->mergeCells("A$row:G$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("H$row", "=SUM(H$str:H$edr)");
        $sheet->setCellValue("I$row", "=SUM(I$str:I$edr)");
        $sheet->getStyle("H$str:I$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($allBorders));
        $row++;
    }
}elseif ($JnsLaporan == 2){
    $sheet->setCellValue("A$row", "REKAPITULASI HASIL PRODUKSI");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Kode Barang");
    $sheet->setCellValue("C$row", "Nama Barang");
    $sheet->setCellValue("D$row", "Satuan");
    $sheet->setCellValue("E$row", "Q T Y");
    $sheet->setCellValue("F$row", "Nilai Produksi");
    $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($center, $allBorders));
    $nmr = 0;
    $str = $row;
    if ($Reports != null) {
        $ivn = null;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            $nmr++;
            $sheet->setCellValue("A$row", $nmr);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", $rpt["item_code"]);
            $sheet->setCellValue("C$row", $rpt["item_name"]);
            $sheet->setCellValue("D$row", $rpt["satuan"]);
            $sheet->setCellValue("E$row", $rpt["sum_qty"]);
            $sheet->setCellValue("F$row", $rpt["sum_total"],0);
            $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "TOTAL...");
        $sheet->mergeCells("A$row:D$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("E$row", "=SUM(E$str:E$edr)");
        $sheet->setCellValue("F$row", "=SUM(F$str:F$edr)");
        $sheet->getStyle("E$str:F$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
        $row++;
    }
}elseif ($JnsLaporan == 3){
    $sheet->setCellValue("A$row", "LAPORAN PEMAKAIAN MATERIAL");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Cabang");
    $sheet->setCellValue("C$row", "Tanggal");
    $sheet->setCellValue("D$row", "No. Produksi");
    $sheet->setCellValue("E$row", "Kode Barang");
    $sheet->setCellValue("F$row", "Nama Barang");
    $sheet->setCellValue("G$row", "Satuan");
    $sheet->setCellValue("H$row", "Q T Y");
    $sheet->setCellValue("I$row", "Nilai Material");
    $sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($center, $allBorders));
    $nmr = 0;
    $str = $row;
    if ($Reports != null) {
        $ivn = null;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            $nmr++;
            $sheet->setCellValue("A$row", $nmr);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", $rpt["cabang_code"]);
            $sheet->setCellValue("C$row", date('d-m-Y', strtotime($rpt["assembly_date"])));
            $sheet->setCellValue("D$row", $rpt["assembly_no"]);
            $sheet->setCellValue("E$row", $rpt["item_code"]);
            $sheet->setCellValue("F$row", $rpt["item_name"]);
            $sheet->setCellValue("G$row", $rpt["satuan"]);
            $sheet->setCellValue("H$row", $rpt["qty"]);
            $sheet->setCellValue("I$row", round($rpt["qty"]*$rpt["price"],0));
            $sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($allBorders));
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "TOTAL...");
        $sheet->mergeCells("A$row:G$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("H$row", "=SUM(H$str:H$edr)");
        $sheet->setCellValue("I$row", "=SUM(I$str:I$edr)");
        $sheet->getStyle("H$str:I$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($allBorders));
        $row++;
    }
}elseif ($JnsLaporan == 4){
    $sheet->setCellValue("A$row", "REKAPITULASI PEMAKAIAN MATERIAL");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Kode Barang");
    $sheet->setCellValue("C$row", "Nama Barang");
    $sheet->setCellValue("D$row", "Satuan");
    $sheet->setCellValue("E$row", "Q T Y");
    $sheet->setCellValue("F$row", "Nilai Material");
    $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($center, $allBorders));
    $nmr = 0;
    $str = $row;
    if ($Reports != null) {
        $ivn = null;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            $nmr++;
            $sheet->setCellValue("A$row", $nmr);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", $rpt["item_code"]);
            $sheet->setCellValue("C$row", $rpt["item_name"]);
            $sheet->setCellValue("D$row", $rpt["satuan"]);
            $sheet->setCellValue("E$row", $rpt["sum_qty"]);
            $sheet->setCellValue("F$row", $rpt["sum_total"],0);
            $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "TOTAL...");
        $sheet->mergeCells("A$row:D$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("E$row", "=SUM(E$str:E$edr)");
        $sheet->setCellValue("F$row", "=SUM(F$str:F$edr)");
        $sheet->getStyle("E$str:F$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
        $row++;
    }
}
// Flush to client

foreach ($headers as $header) {
    header($header);
}
// Hack agar client menutup loading dialog box... (Ada JS yang checking cookie ini pada common.js)
$writer->save("php://output");

// Garbage Collector
$phpExcel->disconnectWorksheets();
unset($phpExcel);
ob_flush();
