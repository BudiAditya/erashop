<?php
/** @var $report Purchase[] */
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
$fileName = 'ap-purchase.pdf';
if ($doctype == 'grn'){
	foreach ($report as $idx => $grn) {
		require("grn_print_pdf.php");
	}
}
$pdf->Output($fileName,"D");
