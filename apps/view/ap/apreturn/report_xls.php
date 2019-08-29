<?php
$phpExcel = new PHPExcel();
$headers = array(
    'Content-Type: application/vnd.ms-excel'
, 'Content-Disposition: attachment;filename="print-rekap-ap-return.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erasystem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erasystem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Rekapitulasi Retur Pembelian");
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
    $sheet->setCellValue("A$row","REKAPITULASI RETUR PEMBELIAN");
    $row++;
    $sheet->setCellValue("A$row","Dari Tgl. ".date('d-m-Y',$StartDate)." - ".date('d-m-Y',$EndDate));
    $row++;
    $sheet->setCellValue("A$row","No.");
    $sheet->setCellValue("B$row","Cabang");
    $sheet->setCellValue("C$row","Tanggal");
    $sheet->setCellValue("D$row","No. Bukti");
    $sheet->setCellValue("E$row","Nama Supplier");
    $sheet->setCellValue("F$row","Keterangan");
    $sheet->setCellValue("G$row","Nilai Retur");
    if ($JnsLaporan == 2) {
        $sheet->setCellValue("H$row", 'Ex.Invoice');
        $sheet->setCellValue("I$row", 'Kode Barang');
        $sheet->setCellValue("J$row", 'Nama Barang');
        $sheet->setCellValue("K$row", 'QTY');
        $sheet->setCellValue("L$row", 'Harga');
        $sheet->setCellValue("M$row", 'Jumlah');
        $sheet->getStyle("A$row:M$row")->applyFromArray(array_merge($center, $allBorders));
    }else {
        $sheet->getStyle("A$row:G$row")->applyFromArray(array_merge($center, $allBorders));
    }
    $nmr = 0;
    $str = $row;
    if ($Reports != null) {
        $sts = null;
        $ivn = null;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            if ($ivn <> $rpt["rb_no"]) {
                $nmr++;
                $sma = false;
            } else {
                $sma = true;
            }
            if (!$sma) {
                $sheet->setCellValue("A$row", $nmr);
                $sheet->getStyle("A$row")->applyFromArray($center);
                $sheet->setCellValue("B$row", $rpt["cabang_code"]);
                $sheet->setCellValue("C$row", date('d-m-Y', strtotime($rpt["rb_date"])));
                $sheet->setCellValue("D$row", $rpt["rb_no"]);
                $sheet->setCellValue("E$row", $rpt["supplier_name"]);
                $sheet->setCellValue("F$row", $rpt["rb_descs"]);
                $sheet->setCellValue("G$row", $rpt["rb_amount"]);
                $sheet->getStyle("A$row:G$row")->applyFromArray(array_merge($allBorders));
            }
            if ($JnsLaporan == 2) {
                $sheet->setCellValue("H$row", $rpt['ex_grn_no']);
                $sheet->setCellValueExplicit("I$row", $rpt['item_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("J$row", $rpt['item_descs']);
                $sheet->setCellValue("K$row", $rpt['qty_retur']);
                $sheet->setCellValue("L$row", $rpt['price']);
                $sheet->setCellValue("M$row", $rpt['sub_total']);
                $sheet->getStyle("H$row:M$row")->applyFromArray(array_merge($allBorders));
            }
            $ivn = $rpt["rb_no"];
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "TOTAL RETUR");
        $sheet->mergeCells("A$row:F$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("G$row", "=SUM(G$str:G$edr)");
        $sheet->getStyle("G$str:G$row")->applyFromArray($idrFormat);
        if ($JnsLaporan == 2) {
            $sheet->mergeCells("H$row:L$row");
            $sheet->getStyle("A$row:M$row")->applyFromArray(array_merge($allBorders));
            $sheet->setCellValue("M$row", "=SUM(M$str:M$edr)");
            $sheet->getStyle("K$str:M$row")->applyFromArray($idrFormat);
        } else {
            $sheet->getStyle("A$row:G$row")->applyFromArray(array_merge($allBorders));
        }
        $row++;
    }
}else{
        // rekap item yang terjual
        $sheet->setCellValue("A$row", "REKAPITULASI ITEM RETUR PENJUALAN");
        $row++;
        $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
        $row++;
        $sheet->setCellValue("A$row", "No.");
        $sheet->setCellValue("B$row", "Kode Barang");
        $sheet->setCellValue("C$row", "Nama Barang");
        $sheet->setCellValue("D$row", "Satuan");
        $sheet->setCellValue("E$row", "Q T Y");
        $sheet->setCellValue("F$row", "Nilai Retur");
        $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($center, $allBorders));
        $nmr = 0;
        $str = $row;
        if ($Reports != null) {
            while ($rpt = $Reports->FetchAssoc()) {
                $row++;
                $nmr++;
                $sheet->setCellValue("A$row", $nmr);
                $sheet->setCellValueExplicit("B$row", $rpt['item_code'],PHPExcel_Cell_DataType::TYPE_STRING);
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
