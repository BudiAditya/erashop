<?php
/** @var $invoice Invoice */ /** @var $sales Karyawan[] */
$userName = AclManager::GetInstance()->GetCurrentUser()->RealName;
$fontFamily = "helvetica";
$widths = $pdf->GetWidths();
$y = 0;
$y1 = 95;
$pdf->AddPage();
$pdf->SetY($y);
$pdf->SetFont("helvetica", "B", 11);
$pdf->Cell(1,5,$invoice->CompanyName, 0, 0, "L");
$pdf->SetFont("helvetica", "", 11);
$pdf->Cell($pdf->GetPaperWidth(), 5, "SURAT JALAN", 0, 0, "C");
$pdf->Ln(7);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Reff No.", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $invoice->InvoiceNo, 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Kepada Yth.", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $invoice->CustomerName, 0, 0, "L");
$pdf->Ln(5);
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(20, 5, "Tanggal", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $invoice->FormatInvoiceDate(JS_DATE), 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(20, 5, "Alamat", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $invoice->CustomerAddress, 0, 0, "L");
$pdf->Ln(6);
$pdf->SetFont("helvetica", "", 9);
$pdf->Cell(20, 5, "Mohon diterima dengan baik barang-barang pesanan bapak/ibu sebagai berikut:", 0, 0, "L");
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
$pdf->Line($x+135,$y+5,$x+135,$y+81);
$pdf->Line($x+195,$y+5,$x+195,$y+81);
//garis datar footer 1
$pdf->Line($x,$y+75,$x+195,$y+75);
//garis datar footer 2
$pdf->Line($x,$y+81,$x+195,$y+81);
$pdf->Ln(5);
//header barang
$pdf->SetFont("helvetica", "", 10);
$pdf->Cell(17,5,"  QTY",0,0,"C");
$pdf->Cell(85,5,"   KODE DAN NAMA BARANG",0,0,"C");
$pdf->Cell(115,5,"KETERANGAN",0,0,"C");
$pdf->Ln(2);
$y = $pdf->GetY();
//detail barang
$pdf->SetX(16);
$pdf->SetY($y);
$pdf->SetFont("helvetica", "", 9);
$qJenis = 0;
$qTotal = 0;
foreach($invoice->Details as $idx => $detail) {
	$pdf->Ln(5);
	$pdf->Cell(22,5,$detail->Qty.' '.strtolower($detail->SatBesar).' ',0,0,"R");
	$pdf->Cell(100,5,$detail->ItemCode.' - '.$detail->ItemDescs,0,0,"L");
	$qJenis++;
	$qTotal+= $detail->Qty;
}
$pdf->SetXY(6,$y1);
$pdf->SetFont("helvetica", "", 9);
$pdf->Cell(5,5,'Total: '.$qTotal.' satuan *'.$qJenis.' macam*',0,0,"L");
$pdf->SetX(10);
$pdf->Write(20,'Diterima oleh,');
$pdf->SetX(60);
$pdf->Write(20,'Diserahkan oleh,');
$pdf->SetX(110);
$pdf->Write(20,'Mengetahui,');
$pdf->SetX(160);
$pdf->Write(20,'Hormat kami,');
$pdf->SetY($pdf->GetY()+17);
$pdf->SetX(10);
$pdf->Write(20,'_________________');
$pdf->SetX(60);
$pdf->Write(20,'_________________');
$pdf->SetX(110);
$pdf->Write(20,'_________________');
$pdf->SetX(160);
$pdf->Write(20,'_________________');
$pdf->Ln(5);
$pdf->SetFont("helvetica", "", 8);
$pdf->SetX(10);
$pdf->Write(20,'Customer');
$pdf->SetX(60);
$pdf->Write(20,'Gudang');
$pdf->SetX(110);
$pdf->Write(20,'Ka. Gudang');
$pdf->SetX(160);
$pdf->Write(20,'Sales/Toko');
$pdf->Ln(13);
$pdf->SetFont("helvetica", "i", 7);
$pdf->Cell(5,5,'Admin: '.$invoice->AdminName.' - Printed by: '.$userName.'  - Time: '.date('d-m-Y h:i:s'),0,0,"L");
