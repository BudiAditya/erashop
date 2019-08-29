<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Eraditya Inc
 * Date: 16/01/15
 * Time: 7:44
 * To change this template use File | Settings | File Templates.
 */
/** @var $rekonsil Rekonsil */ /** @var $estimasi Estimasi */ /** @var $plservices PlService[] */ /** @var $spareparts Sparepart[] */ /** @var $asuransi Asuransi */
/** @var $customer Customer */
require_once(LIBRARY . "tabular_pdf.php");
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
$output = "estimasi-".$rekonsil->RegNo.'-'.$estimasi->EstNo;
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
$pdf->Ln(15);
$pdf->SetFont("Tahoma", "U", 10);
$pdf->Cell(30, 5, "Kepada Yth.", 0, 0, "L");
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(-30, 5, "Manado, ".$estimasi->FormatTglEstimasi(), 0, 0, "R");
$pdf->Ln();
if($rekonsil->InsName == ""){
    $pdf->SetFont("Tahoma", "B", 10);
    $pdf->Cell(30, 5, $customer->CustomerName, 0, 0, "L");
    $pdf->SetFont("Tahoma", "", 10);
    $pdf->Ln();
    $pdf->Cell(30, 5, $customer->Address, 0, 0, "L");
    $pdf->Ln();
    $pdf->Cell(30, 5, $customer->City, 0, 0, "L");
}else{
    $pdf->SetFont("Tahoma", "B", 10);
    $pdf->Cell(30, 5, $rekonsil->InsName, 0, 0, "L");
    $pdf->SetFont("Tahoma", "", 10);
    $pdf->Ln();
    $pdf->Cell(30, 5, $asuransi->Address, 0, 0, "L");
    $pdf->Ln();
    $pdf->Cell(30, 5, $asuransi->City, 0, 0, "L");
}
$pdf->Ln(8);
$pdf->SetFont("Tahoma", "B", 11);
$pdf->Cell($pdf->GetPaperWidth(), 5, "ESTIMASI BIAYA PERBAIKAN", 0, 0, "C");
$pdf->Ln(8);
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(30, 5, "Nama Tertanggung", 0, 0, "L");
$pdf->Cell(5, 5, ":", 0, 0, "C");
$pdf->Cell(5, 5, $rekonsil->CustomerName, 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(30, 5, "No. Polis", 0, 0, "L");
$pdf->Cell(5, 5, ":", 0, 0, "C");
$pdf->Cell(5, 5, $rekonsil->NoPolis, 0, 0, "L");
$pdf->Ln();
$pdf->Cell(30, 5, "Merk / Type", 0, 0, "L");
$pdf->Cell(5, 5, ":", 0, 0, "C");
$pdf->Cell(5, 5, $rekonsil->Merk.' / '.$rekonsil->Type, 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(30, 5, "No. SPK", 0, 0, "L");
$pdf->Cell(5, 5, ":", 0, 0, "C");
$pdf->Cell(5, 5, $rekonsil->NoSpk, 0, 0, "L");
$pdf->Ln();
$pdf->Cell(30, 5, "No. Polisi", 0, 0, "L");
$pdf->Cell(5, 5, ":", 0, 0, "C");
$pdf->Cell(5, 5, $rekonsil->NoPolisi, 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(30, 5, "No. LK", 0, 0, "L");
$pdf->Cell(5, 5, ":", 0, 0, "C");
$pdf->Cell(5, 5, $estimasi->RegNo, 0, 0, "L");
$pdf->Ln();
$pdf->Cell(30, 5, "Resiko Sendiri", 0, 0, "L");
$pdf->Cell(5, 5, ":", 0, 0, "C");
$pdf->Cell(5, 5, 'Rp. '.number_format($estimasi->NilOwnrisk,0), 0, 0, "L");
$pdf->SetX(-80,true);
$pdf->Cell(30, 5, "No. Estimasi", 0, 0, "L");
$pdf->Cell(5, 5, ":", 0, 0, "C");
$pdf->Cell(5, 5, "#".$estimasi->EstNo, 0, 0, "L");
$pdf->Ln(8);
//bof table
if($qcrepair > 0){
    $pdf->Cell(30, 5, "PERBAIKAN :", 0, 0, "L");
    $x = 16;
    $y = $pdf->GetY();
    $pdf->SetFont("Tahoma", "", 10);
    //$pdf->RowHeader(5, array('TRBL', 'TRB', 'TRB'), null, array('C', 'C', 'C'));
    //garis datar header
    $pdf->SetLineWidth(0.4);
    $pdf->Line($x,$y+5,$x+174,$y+5);
    $pdf->SetLineWidth(0.2);
    $pdf->Line($x,$y+11,$x+174,$y+11);
    //haris vertical header
    $pdf->Line($x,$y+5,$x,$y+11);
    $pdf->Line($x+10,$y+5,$x+10,$y+11);
    $pdf->Line($x+143,$y+5,$x+143,$y+11);
    $pdf->Line($x+174,$y+5,$x+174,$y+11);
    $pdf->Ln(6);
    //$pdf->SetFont("Tahoma", "B", 10);
    $pdf->Cell(10,5,"NO",0,0,"C");
    $pdf->Cell(124,5,"URAIAN PEKERJAAN",0,0,"C");
    $pdf->Cell(47,5,"HARGA",0,0,"C");
    $pdf->SetFont("Tahoma", "", 10);
    $counter = 0;
    $total = 0;
    foreach($estimasi->Details as $idx => $detail) {
        if ($detail->EstType == 1) {
            $counter++;
            /** @var $svcs PlService */
            $svcs = $services[$detail->ServiceId];
            $kode = $svcs->Kode;
            $service = $svcs->Pekerjaan;
            $y+=5;
            $pdf->Line($x,$y+5,$x,$y+11);
            $pdf->Line($x+10,$y+5,$x+10,$y+11);
            $pdf->Line($x+143,$y+5,$x+143,$y+11);
            $pdf->Line($x+174,$y+5,$x+174,$y+11);
            $pdf->Ln(5);
            $pdf->Cell(12,5,$counter,0,0,"C");
            $pdf->Cell(120,5,$service,0,0,"L");
            $pdf->Cell(43,5,'Rp. '.number_format($detail->Price,0),0,0,"R");
            $total += $detail->Qty * $detail->Price;
        }
    }
    $pdf->Line($x,$y+11,$x+174,$y+11);
    $y+=5;
    $pdf->Line($x,$y+5,$x,$y+11);
    $pdf->Line($x+143,$y+5,$x+143,$y+11);
    $pdf->Line($x+174,$y+5,$x+174,$y+11);
    $pdf->Ln(5);
    $pdf->Cell(132,5,"TOTAL ESTIMASI PERBAIKAN",0,0,"R");
    $pdf->Cell(43,5,'Rp. '.number_format($total,0),0,0,"R");
    $pdf->Line($x,$y+11,$x+174,$y+11);
    $pdf->Ln(7);
}
if($qcpart > 0){
    $pdf->Cell(30, 5, "PENGGANTIAN :", 0, 0, "L");
    $x = 16;
    $y = $pdf->GetY();
    $pdf->SetFont("Tahoma", "", 10);
    //$pdf->RowHeader(5, array('TRBL', 'TRB', 'TRB'), null, array('C', 'C', 'C'));
    //garis datar header
    $pdf->SetLineWidth(0.4);
    $pdf->Line($x,$y+5,$x+174,$y+5);
    $pdf->SetLineWidth(0.2);
    $pdf->Line($x,$y+11,$x+174,$y+11);
    //haris vertical header
    $pdf->Line($x,$y+5,$x,$y+11);
    $pdf->Line($x+10,$y+5,$x+10,$y+11);
    $pdf->Line($x+100,$y+5,$x+100,$y+11);
    $pdf->Line($x+143,$y+5,$x+143,$y+11);
    $pdf->Line($x+174,$y+5,$x+174,$y+11);
    $pdf->Ln(6);
    //$pdf->SetFont("Tahoma", "B", 10);
    $pdf->Cell(10,5,"NO",0,0,"C");
    $pdf->Cell(90,5,"PART NAME",0,0,"C");
    $pdf->Cell(40,5,"PART NO",0,0,"C");
    $pdf->Cell(35,5,"HARGA",0,0,"C");
    $pdf->SetFont("Tahoma", "", 10);
    $counter = 0;
    $total = 0;
    foreach($estimasi->Details as $idx => $detail) {
        if ($detail->EstType == 2) {
            $counter++;
            /** @var $part Sparepart */
            $part = $parts[$detail->PartId];
            $pno = $part->PartNo;
            $pnm = $part->PartName;
            $y+=5;
            $pdf->Line($x,$y+5,$x,$y+11);
            $pdf->Line($x+10,$y+5,$x+10,$y+11);
            $pdf->Line($x+100,$y+5,$x+100,$y+11);
            $pdf->Line($x+143,$y+5,$x+143,$y+11);
            $pdf->Line($x+174,$y+5,$x+174,$y+11);
            $pdf->Ln(5);
            $pdf->Cell(12,5,$counter,0,0,"C");
            $pdf->Cell(90,5,$pnm,0,0,"L");
            $pdf->Cell(40,5,$pno,0,0,"L");
            $pdf->Cell(33,5,'Rp. '.number_format($detail->Price,0),0,0,"R");
            $total += $detail->Qty * $detail->Price;
        }
    }
    $pdf->Line($x,$y+11,$x+174,$y+11);
    $y+=5;
    $pdf->Line($x,$y+5,$x,$y+11);
    $pdf->Line($x+143,$y+5,$x+143,$y+11);
    $pdf->Line($x+174,$y+5,$x+174,$y+11);
    $pdf->Ln(5);
    $pdf->Cell(132,5,"TOTAL ESTIMASI PENGGANTIAN",0,0,"R");
    $pdf->Cell(43,5,'Rp. '.number_format($total,0),0,0,"R");
    $pdf->Line($x,$y+11,$x+174,$y+11);
}
$y+=3;
$pdf->Line($x,$y+11,$x+174,$y+11);
$y+=6;
$pdf->Line($x,$y+5,$x,$y+10);
$pdf->Line($x+143,$y+5,$x+143,$y+10);
$pdf->Line($x+174,$y+5,$x+174,$y+10);
$pdf->Ln(8);
$pdf->Cell(132,5,"GRAND TOTAL",0,0,"R");
$pdf->Cell(43,5,'Rp. '.number_format($estimasi->NilRepair+$estimasi->NilExchange-$estimasi->NilOwnrisk,0),0,0,"R");
$pdf->Line($x,$y+10,$x+174,$y+10);
//eof table
$pdf->Ln(8);
$pdf->SetFont("Tahoma", "U", 9);
$pdf->Cell(10,5,"Catatan:",0,0,"L");
$pdf->Ln();
$pdf->SetFont("Tahoma", "", 9);
$pdf->Cell(160,5,"- Jika disetujui mohon Estimasi ini ditandatangani lalu dikirim kembali ke ".$company_name,0,0,"L");
$pdf->Ln();
$pdf->Cell(160,5,"- Estimasi ini bukan merupakan Bukti Pembayaran",0,0,"L");
$pdf->Ln();
$pdf->Cell(160,5,"- Harga Estimasi dapat berubah sewaktu-waktu tanpa pemberitahuan terlebih dahulu",0,0,"L");
$pdf->Ln();
$pdf->Cell(160,5,"- Estimasi Parts di atas belum termasuk Parts yang tidak kelihatan",0,0,"L");
$pdf->Ln();
$pdf->Cell(160,5,"- Kendaraan dikerjakan setelah Pembayaran Uang Muka sebesar 50% dari jumlah Estimasi",0,0,"L");
$pdf->Ln();
$pdf->Cell(160,5,"- Apabila Estimasi ini dibatalkan, maka dikenakan Biaya Estimasi & Parkir selama dalam Outlet",0,0,"L");
$pdf->Ln();
$pdf->Cell(160,5,"- Jika ada pesanan Parts di luar kota akan di tambahkan Ongkos Kirim",0,0,"L");
$pdf->Ln(10);
$pdf->SetFont("Tahoma", "", 10);
$pdf->Cell(30,5,"",0,0,"C");
$pdf->Cell(70,5,"Hormat kami,",0,0,"C");
$pdf->Cell(30,5,"Disetujui oleh :",0,0,"R");
$pdf->Ln(7);
$pdf->Cell(100,5,"",0,0,"C");
$pdf->Cell(30,5,"Tanda Tangan :",0,0,"R");
$pdf->Ln(7);
$pdf->Cell(100,5,"",0,0,"C");
$pdf->Cell(30,5,"Tanggal :",0,0,"R");
$pdf->Ln(9);
$pdf->Cell(30,5,"",0,0,"C");
$pdf->Cell(70,5,"( ".$rekonsil->AdminName." )",0,0,"C");
//print to file
$pdf->Output($output.".pdf", "D");