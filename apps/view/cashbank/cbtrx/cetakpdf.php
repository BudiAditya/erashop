<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Eraditya Inc
 * Date: 16/01/15
 * Time: 7:44
 * To change this template use File | Settings | File Templates.
 */
/** @var $cbtrx CbTrx */
require_once(LIBRARY . "tabular_pdf.php");
require_once(LIBRARY . "gen_functions.php");
// array(612,396) => array(215.9, 139.7)

$pdf = new TabularPdf("P", "mm", "letter");
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetMargins(15, 5);

$widths = $pdf->GetColumnWidths();
$sumWidths = array_sum($widths);

$pdf->Open();
$pdf->AddFont("Tahoma");
$pdf->AddFont("Tahoma", "B");
$pdf->AddPage();
$output = "bukti-".$cbtrx->DocNo;
$logo = 'logo-as.jpg';
$counter = 0;
$pdf->Image($logo,15,1,25,18);
$pdf->SetLineWidth(0.4);
$pdf->Line(15,20,200,20);
$pdf->SetFont("Tahoma", "B", 18);
$pdf->Cell(30,5,'');
$pdf->Cell(30,5, $company_name, 0, 0, "L");
$pdf->Ln(8);
$pdf->SetFont("Tahoma", "", 12);
$pdf->Cell(30,5,'');
$pdf->Cell(30, 5, 'Jl. Ring Road, Kel. Bumi Nyiur Kec. Wanea, Manado - Tel: (0431) 864081', 0, 0, "L");
$pdf->Ln(10);
$pdf->SetFont("Tahoma", "U", 14);
if($cbtrx->TrxMode == 1){
    $pdf->Cell(190, 5, "K W I T A N S I", 0, 0, "C");
}else{
    $pdf->Cell(190, 5, "BUKTI PEMBAYARAN", 0, 0, "C");
}
$pdf->Ln();
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(190, 5, "No. ".$cbtrx->DocNo, 0, 0, "C");
$pdf->Ln(10);
$pdf->SetFont("Tahoma", "", 10);
if($cbtrx->TrxMode == 1){
    $pdf->Cell(43, 5, "Sudah diterima dari", 0, 0, "L");
    $pdf->Cell(3, 5, ":", 0, 0, "L");
    $pdf->SetFont("Tahoma", "B", 10);
    $pdf->Cell(50, 5, strtoupper($customer_name), 0, 0, "L");
}else{
    $pdf->Cell(43, 5, "Sudah dibayarkan kepada", 0, 0, "L");
    $pdf->Cell(3, 5, ":", 0, 0, "L");
    $pdf->SetFont("Tahoma", "B", 10);
    $pdf->Cell(50, 5, strtoupper($supplier_name), 0, 0, "L");
}
$pdf->Ln(5);
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(43, 5, "Uang Sejumlah (Rupiah)", 0, 0, "L");
$pdf->Cell(3, 5, ":", 0, 0, "L");
$pdf->Cell(50, 5,'#'.trim(strtoupper(terbilang($cbtrx->TotalAmount))).'#', 0, 0, "L");
$pdf->Ln(10);
$pdf->Cell(43, 5, "Untuk Pembayaran", 0, 0, "L");
$pdf->Cell(3, 5, ":", 0, 0, "L");
$pdf->SetFont("Tahoma", "B", 10);
$pdf->Cell(50, 5, $cbtrx->TrxDescs, 0, 0, "L");
$pdf->Ln();
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(43, 5, "Sesuai Reff. No.", 0, 0, "L");
$pdf->Cell(3, 5, ":", 0, 0, "L");
$pdf->SetFont("Tahoma", "B", 10);
$pdf->Cell(50, 5, $cbtrx->ReffNo, 0, 0, "L");
$pdf->Ln(5);
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(-60, 5, "Manado, ".$cbtrx->FormatTrxDate(), 0, 0, "L");
$pdf->Ln(5);
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(43, 5, "Jumlah Terbilang", 0, 0, "L");
$pdf->Cell(3, 5, ":", 0, 0, "L");
$pdf->SetFont("Tahoma", "B", 10);
$pdf->Cell(50, 5, 'Rp. '.number_format($cbtrx->TotalAmount,0).',-', 0, 0, "L");
$pdf->Ln(20);
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(-60, 5, "(                              )", 0, 0, "L");
$pdf->Ln();
$pdf->SetFont("Tahoma", "", 5);
$pdf->Cell(-60, 5, "*Tidak sah tanpa stempel perusahaaan*", 0, 0, "L");

//print to file
$pdf->Output($output.".pdf", "D");