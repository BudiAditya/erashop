<?php
$phpExcel = new PHPExcel();
$headers = array(
  'Content-Type: application/vnd.ms-excel'
, 'Content-Disposition: attachment;filename="print-rekap-ar-invoice.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erasystem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erasystem Infotama Inc");
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
if ($JnsLaporan < 3) {
    $sheet->setCellValue("A$row", "REKAPITULASI A/R INVOICE");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Cabang");
    $sheet->setCellValue("C$row", "Tanggal");
    $sheet->setCellValue("D$row", "No. Invoice");
    $sheet->setCellValue("E$row", "Customer");
    if ($JnsLaporan == 1) {
        $sheet->setCellValue("F$row", "Keterangan");
        $sheet->setCellValue("G$row", "Salesman");
        $sheet->setCellValue("H$row", "JTP");
        $sheet->setCellValue("I$row", "Jumlah");
        $sheet->setCellValue("J$row", "Terbayar");
        $sheet->setCellValue("K$row", "Outstanding");
        $sheet->getStyle("A$row:K$row")->applyFromArray(array_merge($center, $allBorders));
    }else{
        $sheet->setCellValue("F$row", 'Kode Barang');
        $sheet->setCellValue("G$row", 'Nama Barang');
        $sheet->setCellValue("H$row", 'QTY');
        $sheet->setCellValue("I$row", 'Harga');
        $sheet->setCellValue("J$row", 'Disc(%)');
        $sheet->setCellValue("K$row", 'Discount');
        $sheet->setCellValue("L$row", 'Jumlah');
        $sheet->getStyle("A$row:L$row")->applyFromArray(array_merge($center, $allBorders));
    }
    $nmr = 0;
    $str = $row;
    if ($Reports != null) {
        $ivn = null;
        $sma = false;
        $tTotal = 0;
        $tPaid = 0;
        $tBalance = 0;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            if ($ivn <> $rpt["invoice_no"]) {
                $nmr++;
                $sma = false;
            } else {
                $sma = true;
            }
            //if (!$sma) {
            $sheet->setCellValue("A$row", $nmr);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->setCellValue("B$row", $rpt["cabang_code"]);
            $sheet->setCellValue("C$row", date('d-m-Y', strtotime($rpt["invoice_date"])));
            $sheet->setCellValue("D$row", $rpt["invoice_no"]);
            $sheet->setCellValue("E$row", $rpt["customer_name"]);
            if ($JnsLaporan == 1) {
                $sheet->setCellValue("F$row", $rpt["invoice_descs"]);
                $sheet->setCellValue("G$row", $rpt["sales_name"]);
                $sheet->setCellValue("H$row", date('d-m-Y', strtotime($rpt["due_date"])));
                $sheet->setCellValue("I$row", $rpt["total_amount"]);
                $sheet->setCellValue("J$row", $rpt["paid_amount"]);
                $sheet->setCellValue("K$row", $rpt["balance_amount"]);
                $sheet->getStyle("A$row:K$row")->applyFromArray(array_merge($allBorders));
            }else{
                $sheet->setCellValueExplicit("F$row", $rpt['item_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("G$row", $rpt['item_descs'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("H$row", $rpt['qty']);
                $sheet->setCellValue("I$row", $rpt['price']);
                $sheet->setCellValue("J$row", $rpt['disc_formula']);
                $sheet->setCellValue("K$row", $rpt['disc_amount']);
                $sheet->setCellValue("L$row", $rpt['sub_total']);
                $sheet->getStyle("A$row:L$row")->applyFromArray(array_merge($allBorders));
            }
            $ivn = $rpt["invoice_no"];
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "GRAND TOTAL INVOICE");
        $sheet->mergeCells("A$row:H$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        if ($JnsLaporan == 1) {
            $sheet->setCellValue("I$row", "=SUM(I$str:I$edr)");
            $sheet->setCellValue("J$row", "=SUM(J$str:J$edr)");
            $sheet->setCellValue("K$row", "=SUM(K$str:K$edr)");
            $sheet->getStyle("A$row:K$row")->applyFromArray(array_merge($allBorders));
            $sheet->getStyle("I$str:K$row")->applyFromArray($idrFormat);
        }else{
            $sheet->setCellValue("L$row", "=SUM(L$str:L$edr)");
            $sheet->getStyle("A$row:L$row")->applyFromArray(array_merge($allBorders));
            $sheet->getStyle("I$str:L$row")->applyFromArray($idrFormat);
        }
        $row++;
    }
}else{
    // rekap item yang terjual
    $sheet->setCellValue("A$row", "REKAPITULASI ITEM TERJUAL");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Kode Barang");
    $sheet->setCellValue("C$row", "Nama Barang");
    $sheet->setCellValue("D$row", "Satuan");
    $sheet->setCellValue("E$row", "Q T Y");
    $sheet->setCellValue("F$row", "Nilai Penjualan");
    $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($center, $allBorders));
    $nmr = 0;
    $str = $row;
    if ($Reports != null) {
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            $nmr++;
            $sheet->setCellValue("A$row", $nmr);
            $sheet->setCellValue("B$row", $rpt['item_code']);
            $sheet->setCellValue("C$row", $rpt['item_descs']);
            $sheet->setCellValue("D$row", $rpt['satuan']);
            $sheet->setCellValue("E$row", $rpt['sum_qty']);
            $sheet->setCellValue("F$row", $rpt['sum_total']);
            $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "T O T A L");
        $sheet->mergeCells("A$row:D$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("E$row","=SUM(E$str:E$edr)");
        $sheet->setCellValue("F$row","=SUM(F$str:F$edr)");
        $sheet->getStyle("E$str:F$row")->applyFromArray($idrFormat);
        $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
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
