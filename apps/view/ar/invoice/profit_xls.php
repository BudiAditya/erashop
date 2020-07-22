<?php
$phpExcel = new PHPExcel();
$headers = array(
  'Content-Type: application/vnd.ms-excel'
, 'Content-Disposition: attachment;filename="profit-report.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erasystem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erasystem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Laporan Profit");
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
    $sheet->setCellValue("A$row", "PROFIT PER TRANSAKSI/INVOICE");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Cabang");
    $sheet->setCellValue("C$row", "Tanggal");
    $sheet->setCellValue("D$row", "No. Invoice");
    $sheet->setCellValue("E$row", "Customer");
    $sheet->setCellValue("F$row", "Penjualan");
    $sheet->setCellValue("G$row", "Retur");
    $sheet->setCellValue("H$row", "Pokok");
    $sheet->setCellValue("I$row", "Profit");
    $sheet->getStyle("A$row:I$row")->applyFromArray(array_merge($center, $allBorders));
    $nmr = 1;
    $str = $row;
    if ($Reports != null) {
        $ivn = null;
        $sma = false;
        $tTotal = 0;
        $tPaid = 0;
        $tBalance = 0;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            $sheet->setCellValue("A$row", $nmr++);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", $rpt["cabang_code"]);
            $sheet->setCellValue("C$row", date('d-m-Y', strtotime($rpt["invoice_date"])));
            $sheet->setCellValue("D$row", $rpt["invoice_no"]);
            $sheet->setCellValue("E$row", $rpt["customer_name"]);
            $sheet->setCellValue("F$row", $rpt["total_amount"]);
            $sheet->setCellValue("G$row", $rpt["total_return"]);
            $sheet->setCellValue("H$row", $rpt["total_hpp"]);
            $sheet->setCellValue("I$row", $rpt["total_amount"]-$rpt["total_return"]-$rpt["total_hpp"]);
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "GRAND TOTAL INVOICE");
        $sheet->mergeCells("A$row:E$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("F$row", "=SUM(F$str:F$edr)");
        $sheet->setCellValue("G$row", "=SUM(G$str:G$edr)");
        $sheet->setCellValue("H$row", "=SUM(H$str:H$edr)");
        $sheet->setCellValue("I$row", "=SUM(I$str:I$edr)");
        $sheet->getStyle("F$str:I$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$str:I$row")->applyFromArray(array_merge($allBorders));
        $row++;
    }
}elseif($JnsLaporan == 2) {
    $sheet->setCellValue("A$row", "PROFIT PER TANGGAL");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Cabang");
    $sheet->setCellValue("C$row", "Tanggal");
    $sheet->setCellValue("D$row", "Penjualan");
    $sheet->setCellValue("E$row", "Retur");
    $sheet->setCellValue("F$row", "Pokok");
    $sheet->setCellValue("G$row", "Profit");
    $sheet->getStyle("A$row:G$row")->applyFromArray(array_merge($center, $allBorders));
    $nmr = 1;
    $str = $row;
    if ($Reports != null) {
        $ivn = null;
        $sma = false;
        $tTotal = 0;
        $tPaid = 0;
        $tBalance = 0;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            $sheet->setCellValue("A$row", $nmr++);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", $rpt["cabang_code"]);
            $sheet->setCellValue("C$row", date('d-m-Y', strtotime($rpt["invoice_date"])));
            $sheet->setCellValue("D$row", $rpt["sumSale"]);
            $sheet->setCellValue("E$row", $rpt["sumReturn"]);
            $sheet->setCellValue("F$row", $rpt["sumHpp"]);
            $sheet->setCellValue("G$row", $rpt["sumSale"] - $rpt["sumReturn"] - $rpt["sumHpp"]);
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "GRAND TOTAL");
        $sheet->mergeCells("A$row:C$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("D$row", "=SUM(D$str:D$edr)");
        $sheet->setCellValue("E$row", "=SUM(E$str:E$edr)");
        $sheet->setCellValue("F$row", "=SUM(F$str:F$edr)");
        $sheet->setCellValue("G$row", "=SUM(G$str:G$edr)");
        $sheet->getStyle("D$str:G$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$str:G$row")->applyFromArray(array_merge($allBorders));
        $row++;
    }
}else{
        $sheet->setCellValue("A$row", "PROFIT PER BULAN");
        $row++;
        $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
        $row++;
        $sheet->setCellValue("A$row", "No.");
        $sheet->setCellValue("B$row", "Cabang");
        $sheet->setCellValue("C$row", "Bulan");
        $sheet->setCellValue("D$row", "Penjualan");
        $sheet->setCellValue("E$row", "Retur");
        $sheet->setCellValue("F$row", "Pokok");
        $sheet->setCellValue("G$row", "Profit");
        $sheet->getStyle("A$row:G$row")->applyFromArray(array_merge($center, $allBorders));
        $nmr = 1;
        $str = $row;
        if ($Reports != null) {
            $ivn = null;
            $sma = false;
            $tTotal = 0;
            $tPaid = 0;
            $tBalance = 0;
            while ($rpt = $Reports->FetchAssoc()) {
                $row++;
                $sheet->setCellValue("A$row", $nmr++);
                $sheet->getStyle("A$row")->applyFromArray($center);
                $sheet->setCellValue("B$row", $rpt["cabang_code"]);
                $sheet->setCellValue("C$row", $rpt["tahun"].'-'.$rpt["bulan"]);
                $sheet->setCellValue("D$row", $rpt["sumSale"]);
                $sheet->setCellValue("E$row", $rpt["sumReturn"]);
                $sheet->setCellValue("F$row", $rpt["sumHpp"]);
                $sheet->setCellValue("G$row", $rpt["sumSale"]-$rpt["sumReturn"]-$rpt["sumHpp"]);
            }
            $edr = $row;
            $row++;
            $sheet->setCellValue("A$row", "GRAND TOTAL");
            $sheet->mergeCells("A$row:C$row");
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("D$row", "=SUM(D$str:D$edr)");
            $sheet->setCellValue("E$row", "=SUM(E$str:E$edr)");
            $sheet->setCellValue("F$row", "=SUM(F$str:F$edr)");
            $sheet->setCellValue("G$row", "=SUM(G$str:G$edr)");
            $sheet->getStyle("D$str:G$row")->applyFromArray($idrFormat);
            $sheet->getStyle("A$str:G$row")->applyFromArray(array_merge($allBorders));
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
