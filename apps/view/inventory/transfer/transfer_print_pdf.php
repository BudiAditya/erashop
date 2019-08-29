<?php
/** @var $trx Transfer */
$userName = AclManager::GetInstance()->GetCurrentUser()->RealName;
$fontFamily = "helvetica";
$widths = $pdf->GetWidths();

$y = 0;
$y1 = 90;
//if ($idx % 2 == 0) {
//	$y = 0;
$pdf->AddPage();
//} else {
//	$y = 140;
//}
$pdf->SetY($y);
$pdf->SetFont("helvetica", "B", 11);
$pdf->Cell(1,5,$trx->CompanyName, 0, 0, "L");
$pdf->SetFont("helvetica", "", 11);
$pdf->Cell($pdf->GetPaperWidth(), 5, "BUKTI STOCK TRANSFER", 0, 0, "C");
$pdf->Ln(7);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Nomor", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $trx->NpbNo, 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(20, 5, "Dari Cabang", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $trx->CabangCode, 0, 0, "L");
$pdf->Ln(5);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Tanggal", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $trx->FormatNpbDate(JS_DATE), 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(20, 5, "Ke Cabang", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $trx->ToCabangCode, 0, 0, "L");
$pdf->Ln(2);
$x = 6;
$y = $pdf->GetY();
//garis datar header
$pdf->SetLineWidth(0.4);
$pdf->Line($x,$y+5,$x+195,$y+5);
$pdf->SetLineWidth(0.2);
$pdf->Line($x,$y+11,$x+195,$y+11);
//haris vertical header
$pdf->Line($x,$y+5,$x,$y+81);
//qty
$pdf->Line($x+20,$y+5,$x+20,$y+75);
//vertical paling kanan
$pdf->Line($x+195,$y+5,$x+195,$y+81);
//garis datar footer 1
$pdf->Line($x,$y+75,$x+195,$y+75);
//garis datar footer 2
$pdf->Line($x,$y+81,$x+195,$y+81);
$pdf->Ln(6);
//header barang
$pdf->Cell(17,5,"  QTY",0,0,"C");
$pdf->Cell(125,5,"   KODE DAN NAMA BARANG",0,0,"C");
$y = $pdf->GetY();
//detail barang
$pdf->SetX(16);
$pdf->SetY($y);
$pdf->SetFont("helvetica", "", 9);
$qJenis = 0;
$qTotal = 0;
foreach($trx->Details as $idx => $detail) {
	$pdf->Ln(5);
	$pdf->Cell(22,5,$detail->Qty.' '.strtolower($detail->SatBesar).' ',0,0,"R");
	$pdf->Cell(100,5,$detail->ItemCode.' - '.$detail->ItemDescs,0,0,"L");
	$qJenis++;
	$qTotal+= $detail->Qty;
}
$pdf->SetXY(6,$y1);
$pdf->SetFont("helvetica", "", 9);
$pdf->Cell(5,5,'Total: '.$qTotal.' satuan *'.$qJenis.' macam*',0,0,"L");
$pdf->SetX(25);
$pdf->Write(20,'Dikirim oleh,');
$pdf->SetX(80);
$pdf->Write(20,'Diterima oleh,');
$pdf->SetX(135);
$pdf->Write(20,'Mengetahui,');
$pdf->SetY($pdf->GetY()+17);
$pdf->SetX(25);
$pdf->Write(20,'_________________');
$pdf->SetX(80);
$pdf->Write(20,'_________________');
$pdf->SetX(135);
$pdf->Write(20,'_________________');
$pdf->Ln(20);
$pdf->SetFont("helvetica", "i", 7);
$pdf->Cell(5,5,'Admin: '.$trx->AdminName.' - Printed by: '.$userName.'  - Time: '.date('d-m-Y h:i:s'),0,0,"L");
