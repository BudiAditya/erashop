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
$phpExcel->getProperties()->setCreator("Erasystem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erasystem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Rekapitulasi AP Purchase");
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
    $sheet->setCellValue("A$row", "REKAPITULASI PEMBELIAN & HUTANG");
    $row++;
    $sheet->setCellValue("A$row", "Dari Tgl. " . date('d-m-Y', $StartDate) . " - " . date('d-m-Y', $EndDate));
    $row++;
    $sheet->setCellValue("A$row", "No.");
    $sheet->setCellValue("B$row", "Cabang");
    $sheet->setCellValue("C$row", "Tanggal");
    $sheet->setCellValue("D$row", "No. GRN");
    $sheet->setCellValue("E$row", "Nama Supplier");
    $sheet->setCellValue("F$row", "Keterangan");
    $sheet->setCellValue("G$row", "Jth. Tempo");
    $sheet->setCellValue("H$row", "Jumlah");
    $sheet->setCellValue("I$row", "Terbayar");
    $sheet->setCellValue("J$row", "Outstanding");
    $sheet->setCellValue("J$row", "Outstanding");
    if ($JnsLaporan == 2) {
        $sheet->setCellValue("K$row", 'Kode Barang');
        $sheet->setCellValue("L$row", 'Nama Barang');
        $sheet->setCellValue("M$row", 'QTY');
        $sheet->setCellValue("N$row", 'Harga');
        $sheet->setCellValue("O$row", 'Disc(%)');
        $sheet->setCellValue("P$row", 'Discount');
        $sheet->setCellValue("Q$row", 'Jumlah');
        $sheet->getStyle("A$row:Q$row")->applyFromArray(array_merge($center, $allBorders));
    }else {
        $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($center, $allBorders));
    }
    $nmr = 0;
    $str = $row;
    if ($Reports != null) {
        $ivn = null;
        $sma = false;
        while ($rpt = $Reports->FetchAssoc()) {
            $row++;
            if ($ivn <> $rpt["grn_no"]) {
                $nmr++;
                $sma = false;
            } else {
                $sma = true;
            }
            if (!$sma) {
                $sheet->setCellValue("A$row", $nmr);
                $sheet->getStyle("A$row")->applyFromArray($center);
                $sheet->setCellValue("B$row", $rpt["cabang_code"]);
                $sheet->setCellValue("C$row", date('d-m-Y', strtotime($rpt["grn_date"])));
                $sheet->setCellValue("D$row", $rpt["grn_no"]);
                $sheet->setCellValue("E$row", $rpt["supplier_name"]);
                $sheet->setCellValue("F$row", $rpt["grn_descs"]);
                $sheet->setCellValue("G$row", date('d-m-Y', strtotime($rpt["due_date"])));
                $sheet->setCellValue("H$row", $rpt["total_amount"]);
                $sheet->setCellValue("I$row", $rpt["paid_amount"]);
                $sheet->setCellValue("J$row", $rpt["balance_amount"]);
                $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($allBorders));
            }
            if ($JnsLaporan == 2) {
                $sheet->setCellValueExplicit("K$row", $rpt['item_code'],PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("L$row", $rpt['item_descs']);
                $sheet->setCellValue("M$row", $rpt['qty']);
                $sheet->setCellValue("N$row", $rpt['price']);
                $sheet->setCellValue("O$row", $rpt['disc_formula']);
                $sheet->setCellValue("P$row", $rpt['disc_amount']);
                $sheet->setCellValue("Q$row", $rpt['sub_total']);
                $sheet->getStyle("K$row:Q$row")->applyFromArray(array_merge($allBorders));
            }
            $ivn = $rpt["grn_no"];
        }
        $edr = $row;
        $row++;
        $sheet->setCellValue("A$row", "GRAND TOTAL INVOICE");
        $sheet->mergeCells("A$row:G$row");
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("H$row", "=SUM(H$str:H$edr)");
        $sheet->setCellValue("I$row", "=SUM(I$str:I$edr)");
        $sheet->setCellValue("J$row", "=SUM(J$str:J$edr)");
        $sheet->setCellValue("Q$row", "=SUM(Q$str:Q$edr)");
        $sheet->getStyle("H$str:J$row")->applyFromArray($idrFormat);
        $sheet->getStyle("M$str:Q$row")->applyFromArray($idrFormat);
        if ($JnsLaporan == 1) {
            $sheet->getStyle("A$row:J$row")->applyFromArray(array_merge($allBorders));
        }else{
            $sheet->getStyle("A$row:Q$row")->applyFromArray(array_merge($allBorders));
        }
        $row++;
    }
}else{
    // rekap item yang dibeli
    $sheet->setCellValue("A$row", "REKAPITULASI ITEM PEMBELIAN");
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
