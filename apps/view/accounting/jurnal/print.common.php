<?php
function CreateCommon(TabularPdf $pdf, Jurnal $rs, $counter, $subPage, $totalPage) {
    /* yg dipake adalah counter dan bukan idx, krn detail voucher tdk bisa diprediksi */
    if ($counter % 2 == 0) {
        $pdf->AddPage();
    } else {
        $pdf->SetY(140);
    }

	$widths = $pdf->GetColumnWidths();
	$sumWidths = array_sum($widths);

    $pdf->SetFont("Tahoma", "", 14);
    if (!in_array($rs->EntityId, array(3, 4, 5))) {
        $pdf->Cell(100, 5, "CV. SUMA TIRTA KENCANA", 0, 0, "L");
    } else {
        $pdf->Cell(100, 5, "CV. SUMA TIRTA KENCANA", 0, 0, "L");
    }

    $pdf->Ln(7);

    $pdf->SetFont("Tahoma", "", 14);
    $pdf->Cell($pdf->GetPaperWidth(), 5, strtoupper($rs->VoucherTypes->VoucherDesc), 0, 0, "C");
    $pdf->SetFont("Tahoma", "", 10);
	$pdf->SetX(-55, true);
    $pdf->Cell(15, 5, "No.Bukti", 0, 0, "L");
    $pdf->Cell(3, 5, ":", 0, 0, "C");
    $pdf->Cell(50, 5, $rs->NoVoucher, 0, 0, "L");
    $pdf->Ln();

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->SetX(-55, true);
    $pdf->Cell(15, 5, "Tanggal", 0, 0, "L");
    $pdf->Cell(3, 5, ":", 0, 0, "C");
    $pdf->Cell(50, 5, long_date(date("Y-m-d", $rs->TglVoucher)), 0, 0, "L");
    $pdf->Ln(7);

    $pdf->Line(5, $pdf->GetY(), $sumWidths + 5, $pdf->GetY()); //top
    $pdf->Line(5, $pdf->GetY(), 5, 22 + $pdf->GetY()); //left
    $pdf->Line(140, ($pdf->GetY()), 140, (22 + $pdf->GetY())); //center vertikal
    $pdf->Line($sumWidths + 5, $pdf->GetY(), $sumWidths + 5, 22 + $pdf->GetY()); //right

    $pdf->SetY($pdf->GetY() + 2);
    $pdf->Cell(30, 5, "Dibayar Kepada", 0, 0, "L");
    $pdf->Cell(3, 5, ":", 0, 0, "C");
    $pdf->SetX(140);
    $pdf->Cell(28, 5, "Rekening Bank", 0, 0, "L");
    $pdf->Cell(3, 5, ":", 0, 0, "L");
    $pdf->Ln(8);

    $pdf->Cell(30, 5, "Terbilang", 0, 0, "L");
    $pdf->Cell(3, 5, ":", 0, 0, "C");

    $x1 = $pdf->GetX();
    $y1 = $pdf->GetY();

    $pdf->SetX(140);
    $pdf->Cell(28, 5, "No. Chq/Giro/Trf", 0, 0, "L");
    $pdf->Cell(3, 5, ":", 0, 0, "L");
    $pdf->Ln();

    $pdf->SetY($pdf->GetY() + 7);
    $pdf->SetFont("Tahoma", "", 10);
	$pdf->RowHeader(5, array('TRBL', 'TRB', 'TRB', 'TRB'), null, array('C', 'C', 'C', 'C'));

    $pdf->Line(5, $pdf->GetY(), 5, (45 + $pdf->GetY()));
    $pdf->Line(5 + $widths[0], $pdf->GetY(), 5 + $widths[0], (45 + $pdf->GetY()));
    $pdf->Line(5 + $widths[0] + $widths[1], $pdf->GetY(), 5 + $widths[0] + $widths[1], (45 + $pdf->GetY()));
    $pdf->Line(5 + $widths[0] + $widths[1] + $widths[2], $pdf->GetY(), 5 + $widths[0] + $widths[1] + $widths[2], (45 + $pdf->GetY()));
    $pdf->Line(5 + $sumWidths, $pdf->GetY(), 5 + $sumWidths, (45 + $pdf->GetY()));
    $y3 = 45 + $pdf->GetY();

    $subTotal = 0;
    $coa = null;
    $coaHeader = array();
    //foreach ($rs->Details as $row) {
    $start = ($subPage - 1) * 9;
    $end = min($start + 9, count($rs->Details));
    for ($i = $start; $i < $end; $i++) {
        $row = $rs->Details[$i];

        $subTotal += $row->Jumlah;
        $coaDetail = null;

        if ($rs->VoucherTypes->Id == 1 || $rs->VoucherTypes->Id == 3) {
            $coaDetail = $row->AcKreditNo;

            if (!in_array($row->AcDebetNo, $coaHeader)) {
                $coaHeader[] = $row->AcDebetNo;
            }
        } elseif ($rs->VoucherTypes->Id == 2 || $rs->VoucherTypes->Id == 6) {
            $coaDetail = $row->AcDebetNo;

            if (!in_array($row->AcKreditNo, $coaHeader)) {
                $coaHeader[] = $row->AcKreditNo;
            }
        }

        $pdf->RowData(array($coaDetail, "", $row->Uraian, "Rp. " . number_format($row->Jumlah, 2,",",".")),
            5, null, 0, array("L", "L", "L", "R"));
    }

    $header = implode(",", $coaHeader);

    $pdf->SetX($x);
    $pdf->SetY($y);
    $pdf->Cell($sumWidths, 5,"(".$header.")", 0, 0, "C");

    $pdf->SetFont("Tahoma", "", 10);
    $pdf->SetY($y3);
    $pdf->Cell($widths[0] + $widths[1] + $widths[2], 5, 'SUB TOTAL', 'LTR', 0, 'R');
    $pdf->Cell($widths[3], 5, "Rp. " . number_format($subTotal, 2,",","."), 'BTR', 0, 'R');
    $pdf->Ln();

    if ($subPage == $totalPage) {
        $grandTotal = 0;
        foreach ($rs->Details as $detail) {
            $grandTotal += $detail->Jumlah;
        }

        $pdf->Cell($widths[0] + $widths[1] + $widths[2], 5, 'GRAND TOTAL', 'LTR', 0, 'R');
        $pdf->Cell($widths[3], 5, "Rp. " . number_format($grandTotal, 2,",","."), 'BTR', 0, 'R');
        $pdf->Ln();

        $pdf->SetXY($x1, $y1);
        $pdf->SetFont("Tahoma", "", 10);
        $pdf->MultiCell(90, 5, "#" . terbilang($grandTotal) . " #");

        $pdf->SetY($y3);
        $pdf->Ln(10);
    }

	$cellWidth1 = floor($sumWidths / 5);
	$cellWidth2 = $sumWidths - (4 * $cellWidth1);
    //$pdf->SetY(100 + $coordinat);
    $pdf->SetFont("Tahoma", "", 10);
    $pdf->Cell($cellWidth1, 5, 'Dibayar', 'LRBT', 0, 'C');
    $pdf->Cell($cellWidth1, 5, 'Diperiksa', 'BTR', 0, 'C');
    $pdf->Cell($cellWidth1, 5, 'Dibukukan', 'BTR', 0, 'C');
    $pdf->Cell($cellWidth1, 5, 'Disetujui', 'BTR', 0, 'C');
    $pdf->Cell($cellWidth2, 5, 'Diterima', 'BTR', 0, 'C');
    $pdf->Ln();
    $pdf->Cell($cellWidth1, 15, '', 'LRBT', 0, 'C');
    $pdf->Cell($cellWidth1, 15, '', 'BTR', 0, 'C');
    $pdf->Cell($cellWidth1, 15, '', 'BTR', 0, 'C');
    $pdf->Cell($cellWidth1, 15, '', 'BTR', 0, 'C');
    $pdf->Cell($cellWidth2, 15, '', 'BTR', 0, 'C');
    $pdf->Ln();

    $pdf->SetY($pdf->GetY() - 5);
    $pdf->Cell($cellWidth1, 5, '(Nama Jelas)', 0, 0, 'C');
    $pdf->Cell($cellWidth1, 5, '(Nama Jelas)', 0, 0, 'C');
    $pdf->Cell($cellWidth1, 5, '(Nama Jelas)', 0, 0, 'C');
    $pdf->Cell($cellWidth1, 5, '(Nama Jelas)', 0, 0, 'C');
    $pdf->Cell($cellWidth2, 5, '(Nama Jelas)', 0, 0, 'C');
    $pdf->Ln();
    if ($totalPage > 1) {
        $pdf->SetFont("Tahoma", "", 8);
        $pdf->Cell(-100, 5, sprintf("%s Halaman: %s dari %d", $rs->DocumentNo, $subPage, $totalPage), "", 0, "R");
    }
}

