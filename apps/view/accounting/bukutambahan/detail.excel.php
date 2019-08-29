<?php
/** @var $accountId int */ /** @var $accounts array */ /** @var $start int */ /** @var $end int */ /** @var $openingBalance null|OpeningBalance */
/** @var int $status */ /** @var string $statusName */ /** @var $cabangList Cabang[] */ /** @var $cabangId int */
/** @var $transaction null|array */ /** @var $report null|ReaderBase */ /** @var $output string */ /** @var $company Company */

$haveData = $openingBalance != null;
// OK mari kita buat PDF nya (selectedAccountnya... harus dicari manual)
/** @var $selectedAccount CoaDetail|null */
$selectedAccount = null;
foreach ($accounts as $account) {
    if ($account->Id == $accountId) {
        $selectedAccount = $account;
    }
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
$phpExcel = new PHPExcel();
$phpExcel->getProperties()->setCreator("Erashop System (c) Budi Aditya")->setTitle("Buku Tambahan")->setCompany("Erashoptem Infotama Inc");

$sheet = $phpExcel->getActiveSheet();
$sheet->setTitle("Buku Tambahan");

$sheet->setCellValue("A1", sprintf("%s - %s", $company->EntityCd, $company->CompanyName.$strCabang));
$sheet->mergeCells("A1:H1");
$sheet->getStyle("A1")->applyFromArray(array(
	"font" => array("bold" => true, "size" => 18)
));
$sheet->setCellValue("A2", "Buku Tambahan");
$sheet->mergeCells("A2:H2");
$sheet->setCellValue("A3", sprintf("Periode: %s s.d. %s", date(HUMAN_DATE, $start), date(HUMAN_DATE, $end)));
$sheet->mergeCells("A3:H3");
$sheet->setCellValue("A4", sprintf("Akun: %s - %s (Status: %s)", $selectedAccount->Kode, $selectedAccount->Perkiraan, $statusName));
$sheet->mergeCells("A4:H4");
$sheet->getStyle("A2:A4")->applyFromArray(array(
	"font" => array("size" => 14)
));

// Column Header
$sheet->setCellValue("A6", "Tgl.");
$sheet->setCellValue("B6", "No. Voucher");
$sheet->setCellValue("C6", "Uraian");
$sheet->setCellValue("D6", "SBU");
$sheet->setCellValue("E6", "Dept");
$sheet->setCellValue("F6", "Debet");
$sheet->setCellValue("G6", "Kredit");
$sheet->getStyle("A6:G6")->applyFromArray(array(
	"font" => array("bold" => true),
	"alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
	"borders" => array(
		"top" => array("style" => PHPExcel_Style_Border::BORDER_THIN),
		"bottom" => array("style" => PHPExcel_Style_Border::BORDER_THIN)
	)
));
for ($i = 0; $i < 7; $i++) {
	$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
}

// Saldo awal
$sheet->setCellValue("C7", "Saldo Awal " . date(HUMAN_DATE, $start));
$sheet->setCellValue("F7", ($haveData && $openingBalance->GetCoa()->PosisiSaldo == "DK") ? $transaction["saldo"] : 0);
$sheet->setCellValue("G7", ($haveData && $openingBalance->GetCoa()->PosisiSaldo == "KD") ? $transaction["saldo"] : 0);

// Tulis Data
$row = 7;
$flagDate = true;
$flagVoucherNo = true;
$flagSbu = true;
$prevDate = null;
$prevVoucherNo = null;
while ($data = $report->FetchAssoc()) {
	$row++;
	// Convert datetime jadi native format
	$data["tgl_voucher"] = strtotime($data["tgl_voucher"]);

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

	$debit = $data["acdebet_id"] == $accountId ? $data["jumlah"] : 0;
	$credit = $data["ackredit_id"] == $accountId ? $data["jumlah"] : 0;

	$sheet->setCellValue("A$row", $flagDate ? date("d", $prevDate) : "");
	$sheet->setCellValue("B$row", $flagVoucherNo ? $prevVoucherNo : "");
	$sheet->setCellValue("C$row", $data["uraian"]);
	$sheet->setCellValue("D$row", $data["entity_cd"]);
	$sheet->setCellValue("E$row", $data["kd_cabang"].' - '.$data["cabang"]);
	$sheet->setCellValue("F$row", $debit);
	$sheet->setCellValue("G$row", $credit);
}

// Grand Total
$row++;
$sheet->setCellValue("A$row", "GRAND TOTAL: ");
$sheet->mergeCells("A$row:E$row");
$sheet->setCellValue("F$row", "=SUM(F7:F" . ($row - 1) . ")");
$sheet->setCellValue("G$row", "=SUM(G7:G" . ($row - 1) . ")");
$sheet->getStyle("A$row:G$row")->applyFromArray(array(
	"font" => array("bold" => true),
	"borders" => array(
		"top" => array("style" => PHPExcel_Style_Border::BORDER_THIN),
		"bottom" => array("style" => PHPExcel_Style_Border::BORDER_THIN)
	)
));

// Saldo Akhir
$row++;
$sheet->setCellValue("A$row", "SALDO AKHIR: ");
$sheet->mergeCells("A$row:E$row");
$sheet->setCellValue("F$row", $selectedAccount->PosisiSaldo == "DK" ? "=F" . ($row - 1) . "-G" . ($row - 1) : "");
$sheet->setCellValue("G$row", $selectedAccount->PosisiSaldo == "KD" ? "=G" . ($row - 1) . "-F" . ($row - 1) : "");
$sheet->getStyle("A$row:G$row")->applyFromArray(array(
	"font" => array("bold" => true),
	"borders" => array(
		"bottom" => array("style" => PHPExcel_Style_Border::BORDER_THIN)
	)
));

// Border Styling
$sheet->getStyle("A6:A$row")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("A6:A$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("B6:B$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("C6:C$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("D6:D$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("E6:E$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("F6:F$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("G6:G$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle("F6:F$row")->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle("G6:G$row")->getNumberFormat()->setFormatCode('#,##0.00');

// Hmm Reset Pointer
$sheet->getStyle("A1");
$sheet->setShowGridlines(false);

// Sent header
header('Content-Type: application/vnd.ms-excel');
header(sprintf('Content-Disposition: attachment;filename="buku-tambahan-%s.xls"', $selectedAccount->Kode));
header('Cache-Control: max-age=0');

// Write to php output
$writer = new PHPExcel_Writer_Excel5($phpExcel);
$writer->save("php://output");

// Garbage Collector
$phpExcel->disconnectWorksheets();
unset($phpExcel);
ob_flush();

// EoF: detail.excel.php