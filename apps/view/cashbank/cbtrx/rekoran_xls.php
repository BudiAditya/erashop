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
, 'Content-Disposition: attachment;filename="print-rekoran-cashbank.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erashoptem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erashoptem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Rekening Koran Kas-Bank");
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
$sheet->setCellValue("A$row","REKENING KORAN KAS/BANK: ".$BankKode.' - '.$BankName);
$row++;
$sheet->setCellValue("A$row","Dari Tgl. ".date('d-m-Y',$StartDate)." - ".date('d-m-Y',$EndDate));
$row++;
$sheet->setCellValue("A$row","No.");
$sheet->setCellValue("B$row","Cabang");
$sheet->setCellValue("C$row","Tanggal");
$sheet->setCellValue("D$row","No. Bukti");
$sheet->setCellValue("E$row","Keterangan");
$sheet->setCellValue("F$row","Asuransi / Relasi");
$sheet->setCellValue("G$row","Refferensi");
$sheet->setCellValue("H$row","Debet");
$sheet->setCellValue("I$row","Kredit");
$sheet->setCellValue("J$row","Saldo");
$sheet->setCellValue("K$row","Admin");
$sheet->getStyle("A$row:K$row")->applyFromArray(array_merge($center, $allBorders));
$nmr = 0;
$str = $row;
if ($Reports != null){
    $debet = 0;
    $kredit = 0;
    $saldo = $SaldoAwal;
    $row++;
    $nmr++;
    $sheet->setCellValue("A$row",$nmr);
    $sheet->getStyle("A$row")->applyFromArray($center);
    $sheet->setCellValue("B$row","-");
    $sheet->setCellValue("C$row",date('d-m-Y',strtotime($StartDate)));
    $sheet->setCellValue("D$row","");
    $sheet->setCellValue("E$row","Saldo Awal");
    $sheet->setCellValue("F$row","");
    $sheet->setCellValue("G$row","");
    $sheet->setCellValue("H$row",0);
    $sheet->setCellValue("I$row",0);
    $sheet->setCellValue("J$row",$saldo);
    $sheet->setCellValue("K$row","");
    $sheet->getStyle("A$row:K$row")->applyFromArray(array_merge($allBorders));
    while ($rpt = $Reports->FetchAssoc()) {
        $row++;
        $nmr++;
        $debet = $rpt["db_amount"];
        $kredit = $rpt["cr_amount"];
        $saldo = $saldo + $debet - $kredit;
        $sheet->setCellValue("A$row",$nmr);
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("B$row",$rpt["kode_cabang"]);
        $sheet->setCellValue("C$row",date('d-m-Y',strtotime($rpt["trx_date"])));
        $sheet->setCellValue("D$row",$rpt["doc_no"]);
        $sheet->setCellValue("E$row",$rpt["trx_descs"]);
        $sheet->setCellValue("F$row",$rpt["customer_name"]);
        $sheet->setCellValue("G$row",$rpt["reff_no"]);
        $sheet->setCellValue("H$row",$debet);
        $sheet->setCellValue("I$row",$kredit);
        $sheet->setCellValue("J$row",$saldo);
        $sheet->setCellValue("K$row",$rpt["user_id"]);
        $sheet->getStyle("A$row:K$row")->applyFromArray(array_merge($allBorders));
    }
    $edr = $row;
    $row++;
    $sheet->setCellValue("A$row","TOTAL TRANSAKSI");
    $sheet->mergeCells("A$row:G$row");
    $sheet->getStyle("A$row")->applyFromArray($center);
    $sheet->setCellValue("H$row","=SUM(H$str:H$edr)");
    $sheet->setCellValue("I$row","=SUM(I$str:I$edr)");
    $sheet->getStyle("H$str:J$row")->applyFromArray($idrFormat);
    $sheet->getStyle("A$row:K$row")->applyFromArray(array_merge($allBorders));
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
