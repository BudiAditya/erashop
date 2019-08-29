<?php
/** @var $report Transfer[] */
require_once(LIBRARY . 'tabular_pdf.php');
define('FPDF_FONTPATH','font/');

$pdf = new TabularPdf("P", "mm", "halfletter");
$pdf->SetAutoPageBreak(true, 2);
$pdf->SetMargins(5,5);
$pdf->SetDefaultAlignments(array("R", "L", "R"));

$pdf->Open();
$pdf->AddFont("helvetica");
$pdf->AddFont("helvetica", "B");
$fontFamily = "helvetica";
$fileName = 'ivt-transfer.pdf';
foreach ($report as $idx => $trx) {
		require("transfer_print_pdf.php");
}
$pdf->Output($fileName,"D");
