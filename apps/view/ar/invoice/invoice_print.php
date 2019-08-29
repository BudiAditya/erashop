<?php
/** @var $report Invoice[] */
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
$fileName = 'ar-invoice.pdf';
if ($doctype == 'invoice'){
	foreach ($report as $idx => $invoice) {
		require("invoice_print_pdf.php");
	}
	$fileName = 'ar-invoice.pdf';
}elseif ($doctype == 'do'){
	foreach ($report as $idx => $invoice) {
		require("do_print_pdf.php");
	}
	$fileName = 'ar-deliveryorder.pdf';
}elseif ($doctype == 'suratjalan') {
	foreach ($report as $idx => $invoice) {
		require("suratjalan_print_pdf.php");
	}
	$fileName = 'ar-suratjalan.pdf';
}

$pdf->Output($fileName,"D");
