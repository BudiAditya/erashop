<?php
/** @var $assembly Assembly */
$userName = AclManager::GetInstance()->GetCurrentUser()->RealName;
$fontFamily = "helvetica";
$widths = $pdf->GetWidths();

//$logo = 'public/images/company/mtc.jpg';
$y = 0;
$y1 = 108;
//if ($idx % 2 == 0) {
//	$y = 0;
$pdf->AddPage();
//} else {
//	$y = 140;
//}
$pdf->SetY($y);
$pdf->SetFont("helvetica", "B", 11);
$pdf->Cell(1,5,$assembly->CompanyName, 0, 0, "L");
$pdf->SetFont("helvetica", "", 11);
$pdf->Cell($pdf->GetPaperWidth(), 5, "BUKTI PRODUKSI", 0, 0, "C");
$pdf->Ln(7);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Nomor", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
//$pdf->SetFont("helvetica", "B", 10);
$pdf->Cell(4, 5, $assembly->AssemblyNo, 0, 0, "L");
$pdf->Ln(5);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Tanggal", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $assembly->FormatAssemblyDate(JS_DATE), 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(20, 5, "Cabang", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $assembly->CabangCode, 0, 0, "L");
//hasil produksi
$pdf->Ln(5);
$pdf->Cell(20, 5, "Hasil Produksi:", 0, 0, "L");
$x = 6;
$y = $pdf->GetY();
//garis datar header
$pdf->SetLineWidth(0.4);
$pdf->Line($x,$y+5,$x+195,$y+5);
$pdf->SetLineWidth(0.2);
$pdf->Line($x,$y+10,$x+195,$y+10);
//haris vertical header
$pdf->Line($x,$y+5,$x,$y+15);
//qty
$pdf->Line($x+20,$y+5,$x+20,$y+15);
//uraian
$pdf->Line($x+125,$y+5,$x+125,$y+15);
//harga
$pdf->Line($x+150,$y+5,$x+150,$y+15);
//subtotal
$pdf->Line($x+195,$y+5,$x+195,$y+15);
//garis datar footer 1
$pdf->Line($x,$y+15,$x+195,$y+15);
$pdf->Ln(5);
//header barang
$pdf->Cell(17,5,"  QTY",0,0,"C");
$pdf->Cell(100,5,"   KODE DAN NAMA BARANG",0,0,"C");
$pdf->Cell(45,5,"HARGA",0,0,"C");
$pdf->Cell(35,5,"NILAI PRODUKSI",0,0,"C");
$pdf->Ln(5);
$pdf->Cell(22,5,$assembly->Qty.' '.strtolower($assembly->ItemSatuan).' ',0,0,"R");
$pdf->Cell(112,5,$assembly->ItemCode.' - '.$assembly->ItemName,0,0,"L");
$pdf->Cell(14,5,number_format($assembly->Price,0,',','.'),0,0,"R");
$pdf->Cell(45,5,number_format(round($assembly->Qty * $assembly->Price,0),0,',','.'),0,0,"R");
$pdf->Ln(6);
//bahan baku
$pdf->Cell(20, 5, "Bahan Baku:", 0, 0, "L");
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
$pdf->Line($x+125,$y+5,$x+125,$y+75);
//harga
$pdf->Line($x+150,$y+5,$x+150,$y+81);
//subtotal
$pdf->Line($x+195,$y+5,$x+195,$y+81);
//garis datar footer 1
$pdf->Line($x,$y+75,$x+195,$y+75);
//garis datar footer 2
$pdf->Line($x,$y+81,$x+195,$y+81);
$pdf->Ln(6);
//header barang
$pdf->Cell(17,5,"  QTY",0,0,"C");
$pdf->Cell(100,5,"   KODE DAN NAMA BARANG",0,0,"C");
$pdf->Cell(45,5,"HARGA",0,0,"C");
$pdf->Cell(32,5,"NILAI BAHAN BAKU",0,0,"C");
$y = $pdf->GetY();
//detail barang
$pdf->SetX(16);
$pdf->SetY($y);
$pdf->SetFont("helvetica", "", 9);
$qJenis = 0;
$qTotal = 0;
$nTotal = 0;
foreach($assembly->Details as $idx => $detail) {
	$pdf->Ln(5);
	$pdf->Cell(22,5,$detail->Qty.' '.strtolower($detail->SatBesar).' ',0,0,"R");
	$pdf->Cell(112,5,$detail->ItemCode.' - '.$detail->ItemDescs,0,0,"L");
	$pdf->Cell(14,5,number_format($detail->Price,0,',','.'),0,0,"R");
	$pdf->Cell(45,5,number_format(round($detail->Qty * $detail->Price,0),0,',','.'),0,0,"R");
	$qJenis++;
	$qTotal+= $detail->Qty;
	$nTotal+= round($detail->Qty * $detail->Price,0);
}
$pdf->SetXY(6,$y1);
$pdf->Cell(5,5,'Total: '.$qTotal.' satuan *'.$qJenis.' macam*',0,0,"L");
$pdf->Cell(143,5,'Jumlah..',0,0,"R");
$pdf->Cell(45,5,number_format($nTotal,0,',','.'),0,0,"R");
$pdf->SetX(25);
$pdf->Write(20,'Bagian Produksi,');
$pdf->SetX(80);
$pdf->Write(20,'Bagian Gudang,');
$pdf->SetX(135);
$pdf->Write(20,'Mengetahui,');
$pdf->SetY($pdf->GetY()+9);
$pdf->SetX(25);
$pdf->Write(20,'_________________');
$pdf->SetX(80);
$pdf->Write(20,'_________________');
$pdf->SetX(135);
$pdf->Write(20,'_________________');
$pdf->Ln(13);
$pdf->SetFont("helvetica", "i", 7);
$pdf->Cell(5,5,'Admin: '.$assembly->AdminName.' - Printed by: '.$userName.'  - Time: '.date('d-m-Y h:i:s'),0,0,"L");
