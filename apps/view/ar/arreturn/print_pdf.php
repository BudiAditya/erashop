<?php
require_once(LIBRARY . "tabular_pdf.php");
// array(612,396) => array(215.9, 139.7)
/** @var $arreturn ArReturn */ /** @var $sales Karyawan[] */
$pdf = new TabularPdf("P", "mm", "a5l");
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetMargins(5, 5);
$widths = $pdf->GetColumnWidths();
$sumWidths = array_sum($widths);

$pdf->Open();
$pdf->AddFont("Tahoma");
$pdf->AddFont("Tahoma", "B");
$pdf->AddPage();
$output = $arreturn->RjNo;
$logo = 'smk-logo.jpg';
//$pdf->Image($logo,15,1,25,18);
//$pdf->SetLineWidth(0.4);
//$pdf->Line(15,20,200,20);
$pdf->SetFont("Tahoma", "U", 11);
$pdf->Cell(1,5,$arreturn->CompanyName, 0, 0, "L");
$pdf->SetFont("Tahoma", "", 11);
$pdf->Cell($pdf->GetPaperWidth(), 5, "RETUR PENJUALAN", 0, 0, "C");
$pdf->Ln(7);
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(20, 5, "Nomor", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->SetFont("Tahoma", "B", 10);
$pdf->Cell(4, 5, $arreturn->RjNo, 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(20, 5, "Customer", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->SetFont("Tahoma", "B", 10);
$pdf->Cell(4, 5, $arreturn->CustomerName, 0, 0, "L");
$pdf->Ln(5);
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(20, 5, "Tanggal", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $arreturn->FormatRjDate(JS_DATE), 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(20, 5, "Alamat", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $arreturn->CustomerAddress, 0, 0, "L");
$pdf->Ln(5);
$pdf->Cell(20, 5, "Ex.Gudang", 0, 0, "L");
$pdf->Cell(4, 5, ":", 0, 0, "L");
$pdf->Cell(4, 5, $arreturn->CabangCode, 0, 0, "L");
$pdf->Ln(2);
$x = 6;
$y = $pdf->GetY();
//garis datar header
$pdf->SetLineWidth(0.4);
$pdf->Line($x,$y+5,$x+195,$y+5);
$pdf->SetLineWidth(0.2);
$pdf->Line($x,$y+11,$x+195,$y+11);
//haris vertical header
$pdf->Line($x,$y+5,$x,$y+85);
//qty
$pdf->Line($x+27,$y+5,$x+27,$y+80);
//uraian
$pdf->Line($x+125,$y+5,$x+125,$y+80);
//harga
$pdf->Line($x+142,$y+5,$x+142,$y+85);
//discount
$pdf->Line($x+167,$y+5,$x+167,$y+85);
//subtotal
$pdf->Line($x+195,$y+5,$x+195,$y+85);
//garis datar footer 1
$pdf->Line($x,$y+80,$x+195,$y+80);
$pdf->Line($x,$y+85,$x+195,$y+85);
$pdf->Ln(6);
//header barang
$pdf->Cell(25,5,"Ex. Invoice",0,0,"C");
$pdf->Cell(85,5,"Kode dan Nama Barang",0,0,"C");
$pdf->Cell(45,5,"QTY",0,0,"C");
$pdf->Cell(1,5,"Harga Satuan",0,0,"C");
$pdf->Cell(55,5,"Jumlah",0,0,"C");
//footer barang
$pdf->Ln(74);
$pdf->Cell(167,5,"Total Retur",0,0,"R");
$pdf->Cell(28,5,number_format($arreturn->RjAmount,0,',','.'),0,0,"R");
//detail barang
$pdf->SetX(16);
$pdf->SetY(31);
$pdf->SetFont("Tahoma", "", 9);
$qJenis = 0;
$qTotal = 0;
foreach($arreturn->Details as $idx => $detail) {
    $pdf->Ln(5);
    $pdf->Cell(28,5,'- '.$detail->ExInvoiceNo,0,0,"L");
    $pdf->Cell(95,5,$detail->ItemCode.' - '.$detail->ItemDescs,0,0,"L");
    $pdf->Cell(22,5,$detail->QtyRetur.' '.strtolower($detail->SatBesar).' ',0,0,"R");
    $pdf->Cell(23,5,number_format($detail->Price,0,',','.'),0,0,"R");
    $pdf->Cell(27,5,number_format($detail->SubTotal,0,',','.'),0,0,"R");
    $qJenis++;
    $qTotal+= $detail->QtyRetur;
}
$pdf->SetXY(6,104);
$pdf->SetFont("Tahoma", "", 9);
$pdf->Cell(5,5,'Total: '.$qTotal.' satuan *'.$qJenis.' jenis*',0,0,"L");
$pdf->SetX(25);
$pdf->Write(20,'Diterima oleh,');
$pdf->SetX(80);
$pdf->Write(20,'Yang menyerahkan,');
$pdf->SetY($pdf->GetY()+17);
$pdf->SetX(25);
$pdf->Write(20,'_________________');
$pdf->SetX(80);
$pdf->Write(20,'_________________');
$pdf->Ln(13);
$pdf->SetFont("Times", "i", 7);
$pdf->Cell(195,5,'Printed by: '.$userName.' time: '.date('d-m-Y h:ia'),0,0,"R");
//print to file
$pdf->Output($output.".pdf", "D");