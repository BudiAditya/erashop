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
, 'Content-Disposition: attachment;filename="print-rekap-ar-invoice.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erashoptem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erashoptem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Rekapitulasi AR Invoice");
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
$sheet->setCellValue("A$row","REKAPITULASI A/R INVOICE");
$row++;
$sheet->setCellValue("A$row","Dari Tgl. ".date('d-m-Y',$StartDate)." - ".date('d-m-Y',$EndDate));
$row++;
$sheet->setCellValue("A$row","No.");
$sheet->setCellValue("B$row","Cabang");
$sheet->setCellValue("C$row","Tanggal");
$sheet->setCellValue("D$row","No. Invoice");
$sheet->setCellValue("E$row","Nama Customer");
$sheet->setCellValue("F$row","Nama Salesman");
$sheet->setCellValue("G$row","Jenis Barang");
$sheet->setCellValue("H$row","Keterangan");
$sheet->setCellValue("I$row","Jth. Tempo");
$sheet->setCellValue("J$row","Jumlah");
$sheet->setCellValue("K$row","Terbayar");
$sheet->setCellValue("L$row","Outstanding");
$sheet->setCellValue("M$row","Status");
$sheet->getStyle("A$row:M$row")->applyFromArray(array_merge($center, $allBorders));
$nmr = 0;
$str = $row;
if ($Reports != null){
    while ($rpt = $Reports->FetchAssoc()) {
        $row++;
        $nmr++;
        $sheet->setCellValue("A$row",$nmr);
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("B$row",$rpt["kd_cabang"]);
        $sheet->setCellValue("C$row",date('d-m-Y',strtotime($rpt["invoice_date"])));
        $sheet->setCellValue("D$row",$rpt["invoice_no"]);
        $sheet->setCellValue("E$row",$rpt["customer_name"]);
        $sheet->setCellValue("F$row",$rpt["nm_sales"]);
        $sheet->setCellValue("G$row",$rpt["jns_barang"]);
        $sheet->setCellValue("H$row",$rpt["invoice_descs"]);
        $sheet->setCellValue("I$row",date('d-m-Y',strtotime($rpt["due_date"])));
        $sheet->setCellValue("J$row",$rpt["base_amount"] + $rpt["tax_amount"]);
        $sheet->setCellValue("K$row",$rpt["paid_amount"]);
        $sheet->setCellValue("L$row",($rpt["base_amount"] + $rpt["tax_amount"]) - $rpt["paid_amount"]);
        $sheet->setCellValue("M$row",$rpt["status_desc"]);
        $sheet->getStyle("A$row:m$row")->applyFromArray(array_merge($allBorders));
    }
    $edr = $row;
    $row++;
    $sheet->setCellValue("A$row","TOTAL INVOICE");
    $sheet->mergeCells("A$row:I$row");
    $sheet->getStyle("A$row")->applyFromArray($center);
    $sheet->setCellValue("J$row","=SUM(J$str:J$edr)");
    $sheet->setCellValue("K$row","=SUM(K$str:K$edr)");
    $sheet->setCellValue("L$row","=SUM(L$str:L$edr)");
    $sheet->getStyle("J$str:L$row")->applyFromArray($idrFormat);
    $sheet->getStyle("A$row:M$row")->applyFromArray(array_merge($allBorders));
    $row++;
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
