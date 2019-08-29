<?php
/** @var $items Stock[] */
$phpExcel = new PHPExcel();
$headers = array(
    'Content-Type: application/vnd.ms-excel'
, 'Content-Disposition: attachment;filename="print-daftar-stock-barang.xls"'
, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);
// Excel MetaData
$phpExcel->getProperties()->setCreator("Erashoptem Infotama Inc (c) Budi Aditya")->setTitle("Print Laporan")->setCompany("Erashoptem Infotama Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Daftar Stock Barang");
//helper for styling
$center = array("alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
$right = array("alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
$allBorders = array("borders" => array("allborders" => array("style" => PHPExcel_Style_Border::BORDER_THIN)));
$idrFormat = array("numberformat" => array("code" => '_([$-421]* #,##0_);_([$-421]* (#,##0);_([$-421]* "-"??_);_(@_)'));
$row = 1;
$sheet->setCellValue("A$row",$company_name);
// Hmm Reset Pointer
$sheet->getStyle("A1");
$sheet->setShowGridlines(false);
$row++;
$sheet->setCellValue("A$row","DAFTAR STOCK BARANG");
$row++;
$sheet->setCellValue("A$row","No.");
$sheet->setCellValue("B$row","Cabang/Gudang");
$sheet->setCellValue("C$row","Kode");
$sheet->setCellValue("D$row","Nama Barang");
$sheet->setCellValue("E$row","Satuan");
$sheet->setCellValue("F$row","Qty Stock");
$sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($center, $allBorders));
$nmr = 0;
$str = $row;
if ($items != null){
    foreach ($items as $item){
        $row++;
        $nmr++;
        $sheet->setCellValue("A$row",$nmr);
        $sheet->getStyle("A$row")->applyFromArray($center);
        $sheet->setCellValue("B$row",$item->KdCabang);
        $sheet->setCellValue("C$row",$item->ItemCode);
        $sheet->setCellValue("D$row",$item->ItemName);
        $sheet->setCellValue("E$row",$item->SatBesar);
        $sheet->setCellValue("F$row",$item->QtyStock);
        $sheet->getStyle("A$row:F$row")->applyFromArray(array_merge($allBorders));
    }
    $sheet->getStyle("F$str:F$row")->applyFromArray($idrFormat);
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
