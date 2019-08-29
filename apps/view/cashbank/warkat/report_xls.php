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
, 'Content-Disposition: attachment;filename="print-rekap-warkat-transaction.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erashoptem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erashoptem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Rekapitulasi Transaksi Warkat");
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
$sheet->setCellValue("A$row","REKAPITULASI TRANSAKSI WARKAT");
$row++;
$sheet->setCellValue("A$row","Dari Tgl. ".date('d-m-Y',$StartDate)." - ".date('d-m-Y',$EndDate));
$row++;
$sheet->setCellValue("A$row","No.");
$sheet->setCellValue("B$row","Cabang");
$sheet->setCellValue("C$row","Tanggal");
$sheet->setCellValue("D$row","No. Warkat");
$sheet->setCellValue("E$row","Mode");
$sheet->setCellValue("F$row","Bank");
$sheet->setCellValue("G$row","Relasi");
$sheet->setCellValue("H$row","Keterangan");
$sheet->setCellValue("I$row","Refferensi");
if ($TrxMode == 0){
    $sheet->setCellValue("J$row","Debet");
    $sheet->setCellValue("K$row","Kredit");
    $sheet->setCellValue("L$row","Saldo");
    $sheet->setCellValue("M$row","Admin");
    $sheet->setCellValue("N$row","Status");
    $sheet->getStyle("A$row:N$row")->applyFromArray(array_merge($center, $allBorders));
}else{
    $sheet->setCellValue("J$row","Jumlah");
    $sheet->setCellValue("K$row","Tgl. Cair");
    $sheet->setCellValue("L$row","Status");
    $sheet->getStyle("A$row:L$row")->applyFromArray(array_merge($center, $allBorders));
}
$nmr = 0;
$str = $row;
if ($Reports != null){
    $debet = 0;
    $kredit = 0;
    $saldo = 0;
    $xmode = null;
    while ($rpt = $Reports->FetchAssoc()) {
        $row++;
        $nmr++;
        $debet = 0;
        $kredit = 0;
        if ($TrxMode == 0){
            if ($rpt["warkat_mode"] == 1){
                $debet = $rpt["warkat_amount"];
            }else{
                $kredit = $rpt["warkat_amount"];
            }
            $saldo = $saldo + $debet - $kredit;
        }else{
            $saldo+= $rpt["warkat_amount"];
        }
        if ($row["warkat_mode"] == 1){
            $xmode = "Masuk";
        }else{
            $xmode = "Masuk";
        }
        $sheet->setCellValue("A$row",$nmr);
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("B$row",$rpt["kd_cabang"]);
        $sheet->setCellValue("C$row",date('d-m-Y',strtotime($rpt["warkat_date"])));
        $sheet->setCellValue("D$row",$rpt["warkat_no"]);
        $sheet->setCellValue("E$row",$xmode);
        $sheet->setCellValue("F$row",$rpt["bank_name"]);
        $sheet->setCellValue("G$row",$rpt["contact_name"]);
        $sheet->setCellValue("H$row",$rpt["warkat_descs"]);
        $sheet->setCellValue("I$row",$rpt["reff_no"]);
        if ($TrxMode == 0){
            $sheet->setCellValue("J$row",$debet);
            $sheet->setCellValue("K$row",$kredit);
            $sheet->setCellValue("L$row",$saldo);
            $sheet->setCellValue("M$row",date('d-m-Y',strtotime($rpt["process_date"])));
            if ($rpt["warkat_status"] == 1) {
                $sheet->setCellValue("N$row", "Cair");
            }elseif ($rpt["warkat_status"] == 2) {
                $sheet->setCellValue("N$row", "Batal");
            }else{
                $sheet->setCellValue("N$row","Baru");
            }
            $sheet->getStyle("A$row:N$row")->applyFromArray(array_merge($allBorders));
        }else{
            $sheet->setCellValue("J$row",$rpt["warkat_amount"]);
            $sheet->setCellValue("K$row",date('d-m-Y',strtotime($rpt["process_date"])));
            if ($rpt["warkat_status"] == 1) {
                $sheet->setCellValue("L$row", "Cair");
            }elseif ($rpt["warkat_status"] == 2) {
                $sheet->setCellValue("L$row", "Batal");
            }else{
                $sheet->setCellValue("L$row","Baru");
            }
            $sheet->getStyle("A$row:L$row")->applyFromArray(array_merge($allBorders));
        }
    }
    $edr = $row;
    $row++;
    $sheet->setCellValue("A$row","TOTAL TRANSAKSI");
    $sheet->mergeCells("A$row:I$row");
    $sheet->getStyle("A$row")->applyFromArray($center);
    if ($TrxMode == 0){
        $sheet->setCellValue("J$row","=SUM(J$str:J$edr)");
        $sheet->setCellValue("K$row","=SUM(K$str:K$edr)");
        $sheet->setCellValue("L$row","=J$row-K$row");
        $sheet->mergeCells("M$row:N$row");
        $sheet->getStyle("J$str:L$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$row:N$row")->applyFromArray(array_merge($allBorders));
    }else{
        $sheet->setCellValue("J$row","=SUM(J$str:J$edr)");
        $sheet->mergeCells("K$row:L$row");
        $sheet->getStyle("J$str:J$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$row:L$row")->applyFromArray(array_merge($allBorders));
    }
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
