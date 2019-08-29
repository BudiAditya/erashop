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
, 'Content-Disposition: attachment;filename="print-rekap-stock-transfer.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erasystem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erasystem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Rekapitulasi Stock Transfer");
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
    $sheet->setCellValue("A$row", "LAPORAN STOCK TRANSFER");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Dari");
    $sheet->setCellValue("C$row", "Ke");
    $sheet->setCellValue("D$row", "Tanggal");
    $sheet->setCellValue("E$row", "No. Bukti");
    $sheet->setCellValue("F$row", "Keterangan");
    $sheet->setCellValue("G$row", "Kode Barang");
    $sheet->setCellValue("H$row", "Nama Barang");
    $sheet->setCellValue("I$row", "Satuan");
    $sheet->setCellValue("J$row", "QTY");
    $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($center, $allBorders));
    $nmr = 0;
    $str = $row;
    $npn = null;
    if ($Reports != null) {
        $ivn = null;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            if ($npn <> $rpt["npb_no"]) {
                $nmr++;
                $sheet->setCellValue("A$row", $nmr);
                $sheet->getStyle("A$row")->applyFromArray($center);
                $sheet->setCellValue("B$row", $rpt["cabang_code"]);
                $sheet->setCellValue("C$row", $rpt["to_cabang_code"]);
                $sheet->setCellValue("D$row", date('d-m-Y', strtotime($rpt["npb_date"])));
                $sheet->setCellValue("E$row", $rpt["npb_no"]);
                $sheet->setCellValue("F$row", $rpt["npb_descs"]);
            }
            $sheet->setCellValue("G$row", $rpt["item_code"]);
            $sheet->setCellValue("H$row", $rpt["item_name"]);
            $sheet->setCellValue("I$row", $rpt["satuan"]);
            $sheet->setCellValue("J$row", $rpt["qty"]);
            $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($allBorders));
            $npn = $rpt["npb_no"];
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "TOTAL...");
        $sheet->mergeCells("A$row:G$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("J$row", "=SUM(J$str:J$edr)");
        $sheet->getStyle("J$str:J$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($allBorders));
        $row++;
    }
}elseif ($JnsLaporan == 2){
    $sheet->setCellValue("A$row", "REKAPITULASI STOCK TRANSFER");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Cabang");
    $sheet->setCellValue("C$row", "Kode Barang");
    $sheet->setCellValue("D$row", "Nama Barang");
    $sheet->setCellValue("E$row", "Satuan");
    $sheet->setCellValue("F$row", "Q T Y");
    $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($center, $allBorders));
    $nmr = 0;
    $str = $row;
    $cbs = null;
    if ($Reports != null) {
        $ivn = null;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            if ($cbs <> $rpt['cabang_code'].$rpt['to_cabang_code']) {
                $nmr++;
                $sheet->setCellValue("A$row", $nmr);
                $sheet->getStyle("A$row")->applyFromArray($center);
                $sheet->setCellValue("B$row", $rpt["cabang_code"] . ' -> ' . $rpt["to_cabang_code"]);
            }
            $sheet->setCellValue("C$row", $rpt["item_code"]);
            $sheet->setCellValue("D$row", $rpt["item_name"]);
            $sheet->setCellValue("E$row", $rpt["satuan"]);
            $sheet->setCellValue("F$row", $rpt["sum_qty"]);
            $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
            $cbs = $rpt['cabang_code'].$rpt['to_cabang_code'];
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "TOTAL...");
        $sheet->mergeCells("A$row:D$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("F$row", "=SUM(F$str:F$edr)");
        $sheet->getStyle("F$str:F$row")->applyFromArray($idrFormat);
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
