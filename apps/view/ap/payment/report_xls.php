<?php
$phpExcel = new PHPExcel();
$headers = array(
    'Content-Type: application/vnd.ms-excel'
, 'Content-Disposition: attachment;filename="print-rekap-ap-payment.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erashoptem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erashoptem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Rekapitulasi AP Payment");
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
$sheet->setCellValue("A$row","REKAPITULASI PEMBAYARAN HUTANG");
$row++;
$sheet->setCellValue("A$row","Dari Tgl. ".date('d-m-Y',$StartDate)." - ".date('d-m-Y',$EndDate));
$row++;
$sheet->setCellValue("A$row","No.");
$sheet->setCellValue("B$row","Cabang");
$sheet->setCellValue("C$row","Tanggal");
$sheet->setCellValue("D$row","No. Bukti");
$sheet->setCellValue("E$row","Nama Supplier");
$sheet->setCellValue("F$row","Cara Bayar");
$sheet->setCellValue("G$row","Kas / Bank");
$sheet->setCellValue("H$row","Jumlah");
$sheet->setCellValue("I$row","Status");
$sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($center, $allBorders));
$nmr = 0;
$str = $row;
if ($Reports != null){
    while ($rpt = $Reports->FetchAssoc()) {
        $row++;
        $nmr++;
        $sheet->setCellValue("A$row",$nmr);
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("B$row",$rpt["cabang_code"]);
        $sheet->setCellValue("C$row",date('d-m-Y',strtotime($rpt["payment_date"])));
        $sheet->setCellValue("D$row",$rpt["payment_no"]);
        $sheet->setCellValue("E$row",$rpt["supplier_name"].' ('.$rpt["supplier_code"].')');
        $sheet->setCellValue("F$row",$rpt["cara_bayar"]);
        $sheet->setCellValue("G$row",$rpt["bank_name"]);
        $sheet->setCellValue("H$row",$rpt["payment_amount"]);
        $sheet->setCellValue("I$row",$rpt["status_desc"]);
        $sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($allBorders));
    }
    $edr = $row;
    $row++;
    $sheet->setCellValue("A$row","TOTAL PEMBAYARAN");
    $sheet->mergeCells("A$row:G$row");
    $sheet->getStyle("A$row")->applyFromArray($center);
    $sheet->setCellValue("H$row","=SUM(H$str:H$edr)");
    $sheet->getStyle("H$str:H$row")->applyFromArray($idrFormat);
    $sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($allBorders));
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
