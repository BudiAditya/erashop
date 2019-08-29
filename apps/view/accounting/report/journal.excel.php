<?php
/** @var $start int */ /** @var $end int */ /** @var $docTypes DocType[] */ /** @var $showNo bool */ /** @var $showCol bool */ /** @var $docIds int[] */ /** @var $vocTypes VoucherType[] */
/** @var $report ReaderBase */ /** @var $output string */ /** @var $company Company */ /** @var $orientation string */ /** @var $status int */
/** @var $cabangList Cabang[] */ /** @var $cabangId int */

$phpExcel = new PHPExcel();
$headers = array(
	'Content-Type: application/vnd.ms-excel'
	, 'Content-Disposition: attachment;filename="laporan-jurnal.xls"'
	, 'Cache-Control: max-age=0'
);
$writer = new PHPExcel_Writer_Excel5($phpExcel);

// Excel MetaData
$phpExcel->getProperties()->setCreator("Erashop System (c) Budiasa Wayan")->setTitle("Laporan Jurnal")->setCompany("Eraditya Inc");
$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Laporan Jurnal");

// Bikin Header
$buff = array();
foreach ($docTypes as $docType) {
	if (in_array($docType->Id, $docIds)) {
		$buff[] = strtoupper($docType->DocCode);
	}
}
switch ($status) {
	case 0:
		$subTitle = "JURNAL: " . implode(", ", $buff) . " status: DRAFT";
		break;
	case 1:
		$subTitle = "JURNAL: " . implode(", ", $buff) . " status: APPROVED";
		break;
	case 2:
		$subTitle = "JURNAL: " . implode(", ", $buff) . " status: VERIFIED";
		break;
	default:
		$subTitle = "JURNAL: " . implode(", ", $buff) . " status: SEMUA";
		break;
}
$selectedCabang = null;
$strCabang = null;
foreach ($cabangList as $cabang) {
    if ($cabang->Id == $idCabang) {
        $selectedCabang = $cabang;
        $strCabang = " (Cabang: ".$cabang->Kode." - ".$cabang->Cabang.")";
        break;
    }
}
$sheet->setCellValue("A1", sprintf("Laporan Jurnal : %s - %s", $company->EntityCd, $company->CompanyName.$strCabang));
$sheet->mergeCells("A1:H1");
$sheet->getStyle("A1")->applyFromArray(array(
	"font" => array("size" => 20)
));
$sheet->setCellValue("A2", $subTitle);
$sheet->mergeCells("A2:H2");
$sheet->getStyle("A2")->applyFromArray(array(
	"font" => array("size" => 14)
));
$sheet->setCellValue("A3", sprintf("Periode: %s s.d. %s", date(HUMAN_DATE, $start), date(HUMAN_DATE, $end)));
$sheet->mergeCells("A3:H3");
$sheet->getStyle("A3")->applyFromArray(array(
	"font" => array("size" => 14)
));
for ($i = 0; $i < 13; $i++) {
	$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
}

// Bikin Kolom
$sheet->setCellValue("A5", "Tgl");
$sheet->setCellValue("B5", "No. Voucher");
$sheet->setCellValue("C5", "Cabang");
$sheet->setCellValue("D5", "Uraian Transaksi");
$sheet->setCellValue("E5", "Relasi / Customer");
$sheet->setCellValue("F5", "Debet");
$sheet->setCellValue("H5", "Kredit");
$sheet->setCellValue("F6", "Akun");
$sheet->setCellValue("G6", "Jumlah");
$sheet->setCellValue("H6", "Akun");
$sheet->setCellValue("I6", "Jumlah");
$sheet->mergeCells("A5:A6");
$sheet->mergeCells("B5:B6");
$sheet->mergeCells("C5:C6");
$sheet->mergeCells("D5:D6");
$sheet->mergeCells("E5:E6");
$sheet->mergeCells("F5:G5");
$sheet->mergeCells("H5:I5");
$range = "A5:I6";
$sheet->getStyle($range)->applyFromArray(array(
	"font" => array("bold" => true),
	"alignment" => array(
		"horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
	),
	"borders" => array("allborders" => array(
		"style" => PHPExcel_Style_Border::BORDER_THIN
	))
));

// Tulis data
$row = 6;
$prevDate = null;
$prevVoucherNo = null;
$prevSbu = null;

$flagDate = true;
$flagVoucherNo = true;
$flagSbu = true;
$sums = 0;
while ($data = $report->FetchAssoc()) {
	$row++;
	// Convert datetime jadi native format
	//$data["tgl_voucher"] = strtotime($data["tgl_voucher"]);
	if ($prevDate != $data["tgl_voucher"]) {
		$prevDate = $data["tgl_voucher"];
		$flagDate = true;
	} else {
		$flagDate = false;
	}

	if ($prevVoucherNo != $data["no_voucher"]) {
		$prevVoucherNo = $data["no_voucher"];
		$flagVoucherNo = true;
	} else {
		$flagVoucherNo = false;
	}

	if ($prevSbu != $data["kd_cabang"]) {
		$prevSbu = $data["kd_cabang"];
		$flagSbu = true;
	} else {
		$flagSbu = false;
	}

	$sums += $data["jumlah"];
	$sheet->setCellValue("A$row", $flagDate ? $prevDate : "");
	$sheet->setCellValue("B$row", $flagVoucherNo ? $prevVoucherNo : "");
	$sheet->setCellValue("C$row", $flagSbu ? $prevSbu : "");
	$sheet->setCellValue("D$row", $data["uraian"]);
    $sheet->setCellValue("E$row", $data["customer_name"]);
    $sheet->setCellValue("F$row", $showNo ? $data["acc_no_debit"] : $data["acc_no_debit"].' - '.$data["acc_debit"]);
    $sheet->setCellValue("G$row", $data["jumlah"]);
    $sheet->setCellValue("H$row", $showNo ? $data["acc_no_credit"] : $data["acc_no_credit"].' - '.$data["acc_credit"]);
    $sheet->setCellValue("I$row", $data["jumlah"]);
}

// SUM
$row++;
$flagCyclic = ($row == 7);
$sheet->setCellValue("A$row", "GRAND TOTAL : ");
$sheet->mergeCells("A$row:E$row");
$sheet->setCellValue("G$row", $flagCyclic ? "0" : "=SUM(G7:G".($row-1).")");
$sheet->setCellValue("I$row", $flagCyclic ? "0" : "=SUM(I7:I".($row-1).")");
$sheet->getStyle("A$row:I$row")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("A$row:I$row")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// Styling
$sheet->getStyle("G7:G$row")->getNumberFormat()->setFormatCode("#,##0.00");
$sheet->getStyle("I7:I$row")->getNumberFormat()->setFormatCode("#,##0.00");

$sheet->getStyle("A7:A$row")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("A7:A$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("B7:B$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("C7:C$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("D7:D$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("E7:E$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("F7:F$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("G7:G$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("H7:H$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("I7:I$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// Hmm Reset Pointer
$sheet->getStyle("A1");
$sheet->setShowGridlines(false);

// Flush to client
foreach ($headers as $header) {
	header($header);
}
$writer->save("php://output");

// Garbage Collector
$phpExcel->disconnectWorksheets();
unset($phpExcel);
ob_flush();

// EoF: journal.excel.php