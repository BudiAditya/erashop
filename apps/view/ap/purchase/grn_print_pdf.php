<?php
/** @var $grn Purchase */
$userName = AclManager::GetInstance()->GetCurrentUser()->RealName;
$fontFamily = "helvetica";
$widths = $pdf->GetWidths();

//$logo = 'public/images/company/mtc.jpg';
$y = 0;
$y1 = 0;
//if ($idx % 2 == 0) {
//	$y = 0;
$pdf->AddPage();
//} else {
//	$y = 140;
//}
$pdf->SetY($y);
$pdf->SetFont("helvetica", "B", 11);
$pdf->Cell(1,5,$grn->CompanyName, 0, 0, "L");
$pdf->SetFont("helvetica", "", 11);
$pdf->Cell($pdf->GetPaperWidth(), 5, "BUKTI PEMBELIAN", 0, 0, "C");
$pdf->Ln(7);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Nomor", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
//$pdf->SetFont("helvetica", "B", 10);
$pdf->Cell(4, 5, $grn->GrnNo, 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Supplier", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
//$pdf->SetFont("helvetica", "B", 10);
$pdf->Cell(4, 5, $grn->SupplierName, 0, 0, "L");
$pdf->Ln(5);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Tanggal", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $grn->FormatGrnDate(JS_DATE), 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(20, 5, "Gudang", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $grn->GudangCode, 0, 0, "L");
$pdf->Ln(5);
$pdf->Cell(20, 5, "Cabang", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $grn->CabangCode, 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(20, 5, "JTP", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
if($grn->CreditTerms > 0){
	$pdf->Cell(4, 5, $grn->FormatDueDate(JS_DATE).' ('.$grn->CreditTerms.' hari)', 0, 0, "L");
}else{
	$pdf->Cell(4, 5, 'Cash', 0, 0, "L");
}
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
//uraian
$pdf->Line($x+115,$y+5,$x+115,$y+105);
//harga
$pdf->Line($x+135,$y+5,$x+135,$y+75);
//discount
$pdf->Line($x+167,$y+5,$x+167,$y+105);
//subtotal
$pdf->Line($x+195,$y+5,$x+195,$y+105);
//garis datar footer 1
$pdf->Line($x,$y+75,$x+195,$y+75);
//garis datar footer 2
$pdf->Line($x,$y+81,$x+195,$y+81);
//garis datar footer 3
$pdf->Line($x+115,$y+87,$x+195,$y+87);
//garis datar footer 4
$pdf->Line($x+115,$y+93,$x+195,$y+93);
//garis datar footer 5
$pdf->Line($x+115,$y+99,$x+195,$y+99);
//garis datar footer 5
$pdf->Line($x+115,$y+105,$x+195,$y+105);
$pdf->Ln(6);
//header barang
$pdf->Cell(17,5,"  QTY",0,0,"C");
$pdf->Cell(85,5,"   KODE DAN NAMA BARANG",0,0,"C");
$pdf->Cell(48,5,"HARGA",0,0,"C");
$pdf->Cell(1,5," DISCOUNT",0,0,"C");
$pdf->Cell(55,5,"  J U M L A H",0,0,"C");
$y = $pdf->GetY();
//footer barang
$pdf->Ln(70);
$pdf->Cell(167,5,"Sub Total",0,0,"R");
$pdf->Cell(28,5,number_format($grn->BaseAmount,0,',','.'),0,0,"R");
$y1 = $pdf->GetY();
$pdf->Ln(6);
if ($grn->Disc1Pct > 0){
	$pdf->Cell(167,5,"Discount (".number_format($grn->Disc1Pct,1,',','.').' %)',0,0,"R");
	$pdf->Cell(28,5,'-'.number_format($grn->Disc1Amount,0,',','.'),0,0,"R");
}else{
	$pdf->Cell(167,5,"Discount (0 %)",0,0,"R");
	$pdf->Cell(28,5,0,0,0,"R");
}

$pdf->Ln(6);
$pdf->Cell(167,5,"Pajak (".$grn->TaxPct.' %)',0,0,"R");
if($grn->TaxAmount > 0){
	$pdf->Cell(28,5,'+'.number_format($grn->TaxAmount,0,',','.'),0,0,"R");
}else{
	$pdf->Cell(28,5,number_format($grn->TaxAmount,0,',','.'),0,0,"R");

}
$pdf->Ln(6);
$pdf->Cell(167,5,$grn->OtherCosts,0,0,"R");
if($grn->OtherCostsAmount > 0){
	$pdf->Cell(28,5,'+'.number_format($grn->OtherCostsAmount,0,',','.'),0,0,"R");
}else{
	$pdf->Cell(28,5,number_format($grn->OtherCostsAmount,0,',','.'),0,0,"R");
}

$pdf->Ln(6);
$pdf->Cell(167,5,"Grand Total",0,0,"R");
$pdf->Cell(28,5,number_format($grn->TotalAmount,0,',','.'),0,0,"R");
//detail barang
$pdf->SetX(16);
$pdf->SetY($y);
$pdf->SetFont("helvetica", "", 9);
$qJenis = 0;
$qTotal = 0;
foreach($grn->Details as $idx => $detail) {
	$pdf->Ln(5);
	$pdf->Cell(22,5,$detail->PurchaseQty.' '.strtolower($detail->SatBesar).' ',0,0,"R");
	$pdf->Cell(100,5,$detail->ItemCode.' - '.$detail->ItemDescs,0,0,"L");
	$pdf->Cell(13,5,number_format($detail->Price,0,',','.'),0,0,"R");
	if ($detail->DiscAmount > 0){
		$pdf->Cell(32,5,number_format($detail->DiscAmount,0,',','.').' ('.$detail->DiscFormula.'%)',0,0,"R");
	}else{
		$pdf->Cell(32,5,' ',0,0,"R");
	}
	$pdf->Cell(28,5,number_format($detail->SubTotal,0,',','.'),0,0,"R");
	$qJenis++;
	$qTotal+= $detail->PurchaseQty;
}
$pdf->SetXY(6,$y1);
$pdf->SetFont("helvetica", "", 9);
$pdf->Cell(5,5,'Total: '.$qTotal.' satuan *'.$qJenis.' macam*',0,0,"L");
$pdf->SetX(25);
$pdf->Write(20,'Diterima oleh,');
$pdf->SetX(80);
$pdf->Write(20,'Mengetahui,');
$pdf->SetY($pdf->GetY()+17);
$pdf->SetX(25);
$pdf->Write(20,'_________________');
$pdf->SetX(80);
$pdf->Write(20,'_________________');
$pdf->Ln(15);
$pdf->SetFont("helvetica", "i", 7);
$pdf->Cell(5,5,'Admin: '.$grn->AdminName.' - Printed by: '.$userName.'  - Time: '.date('d-m-Y h:i:s'),0,0,"L");
