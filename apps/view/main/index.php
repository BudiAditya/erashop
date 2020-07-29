<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<?php /** @var $notifications NotificationGroup[] */ ?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ERASHOP - Integrated Sales System</title>

	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>" />

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>

<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br>
<div align="center">
    <table class="list" align="center">
        <thead>
        <td class='left subTitle' colspan=2>SELAMAT DATANG!</td>
        <td class='right' colspan=3><a href="<?php print($helper->site_url('main/aclview/0')); ?>">Klik disini untuk mengetahui <strong>Hak Akses Anda</strong></a></td>
        </thead>
        <tr height="100">
            <td>&nbsp;</td>
            <td width="150" align="center"><a href="master.items"><img src="<?php print(base_url('public/images/pics/barang.png'));?>" width="60px" height="60px"><br /><b>DAFTAR BARANG</b></a></td>
            <td width="150" align="center"><a href="inventory.stock"><img src="<?php print(base_url('public/images/pics/inventory.png'));?>" width="60px" height="60px"><br /><b>DAFTAR STOCK</b></a></td>
            <td width="150" align="center"><a href="master.pricelists"><img src="<?php print(base_url('public/images/pics/price-list.png'));?>" width="60px" height="60px"><br /><b>DAFTAR HARGA</b></a></td>
            <td>&nbsp;</td>
        </tr>
        <tr height="100">
            <td width="150" align="center"><a href="master.customer"><img src="<?php print(base_url('public/images/pics/pelanggan.png'));?>" width="60px" height="60px"><br /><b>CUSTOMER</b></a></td>
            <td width="150" align="center"><a href="ar.invoice"><img src="<?php print(base_url('public/images/pics/pos.png'));?>" width="70px" height="60px"><br /><b>PENJUALAN</b></a></td>
            <td width="150" align="center"><a href="ar.receipt"><img src="<?php print(base_url('public/images/pics/receivable.jpg'));?>" width="60px" height="60px"><br /><b>PENERIMAAN (A/R)</b></a></td>
            <td width="150" align="center"><a href="ar.arreturn"><img src="<?php print(base_url('public/images/pics/sales-return.jpg'));?>" width="80px" height="60px"><br /><b>RETUR PENJUALAN</b></a></td>
            <td width="150" align="center"><a href="ar.invoice/report"><img src="<?php print(base_url('public/images/pics/sales-report.png'));?>" width="60px" height="60px"><br /><b>LAPORAN PENJUALAN</b></a></td>
        </tr>
        <tr height="100">
            <td width="150" align="center"><a href="master.supplier"><img src="<?php print(base_url('public/images/pics/supplier.png'));?>" width="70px" height="60px"><br /><b>SUPPLIER</b></a></td>
            <td width="150" align="center"><a href="ap.purchase"><img src="<?php print(base_url('public/images/pics/purchase.jpg'));?>" width="90px" height="60px"><br /><b>PEMBELIAN</b></a></td>
            <td width="150" align="center"><a href="ap.payment"><img src="<?php print(base_url('public/images/pics/payable.jpg'));?>" width="60px" height="60px"><br /><b>PEMBAYARAN (A/P)</b></a></td>
            <td width="150" align="center"><a href="ap.apreturn"><img src="<?php print(base_url('public/images/pics/purchase-return.jpg'));?>" width="60px" height="60px"><br /><b>RETUR PEMBELIAN</b></a></td>
            <td width="150" align="center"><a href="ap.purchase/report"><img src="<?php print(base_url('public/images/pics/purchase-report.png'));?>" width="60px" height="60px"><br /><b>LAPORAN PEMBELIAN</b></a></td>
        </tr>
        <tr height="100">
            <td>&nbsp;</td>
            <td width="150" align="center"><a href="inventory.assembly"><img src="<?php print(base_url('public/images/pics/assembly.jpg'));?>" width="90px" height="60px"><br /><b>PRODUKSI</b></a></td>
            <td width="150" align="center"><a href="inventory.transfer"><img src="<?php print(base_url('public/images/pics/stock-transfer.jpg'));?>" width="80px" height="60px"><br /><b>STOCK TRANSFER</b></a></td>
            <td width="150" align="center"><a href="inventory.correction"><img src="<?php print(base_url('public/images/pics/stock-opname.jpg'));?>" width="60px" height="60px"><br /><b>STOCK OPNAME</b></a></td>
            <td>&nbsp;</td>
        </tr>
    </table>
</div>
<div id="mainPanel" title="A/R & Sales Statistic" style="width:100%;height:100%;padding:5px;">
    <table border="1" cellspacing="1" style="width: 100%">
        <tr>
            <td colspan="2" style="width: 80%;height: 250px">
                <canvas id="myLineChart"></canvas>
            </td>
            <td align="center" style="width: 20%;height: 250px">
                <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
                    <tr>
                        <th>No.</th>
                        <th>Bulan</th>
                        <th>OMSET (Rp)</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>January</td>
                        <td align="right"><?=number_format($dataInvMonthly["January"],0);?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>February</td>
                        <td align="right"><?=number_format($dataInvMonthly["February"],0);?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Maret</td>
                        <td align="right"><?=number_format($dataInvMonthly["March"],0);?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>April</td>
                        <td align="right"><?=number_format($dataInvMonthly["April"],0);?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Mei</td>
                        <td align="right"><?=number_format($dataInvMonthly["May"],0);?></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Juni</td>
                        <td align="right"><?=number_format($dataInvMonthly["June"],0);?></td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Juli</td>
                        <td align="right"><?=number_format($dataInvMonthly["July"],0);?></td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Agustus</td>
                        <td align="right"><?=number_format($dataInvMonthly["August"],0);?></td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>September</td>
                        <td align="right"><?=number_format($dataInvMonthly["September"],0);?></td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Oktober</td>
                        <td align="right"><?=number_format($dataInvMonthly["October"],0);?></td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>Nopember</td>
                        <td align="right"><?=number_format($dataInvMonthly["November"],0);?></td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>Desember</td>
                        <td align="right"><?=number_format($dataInvMonthly["December"],0);?></td>
                    </tr>
                    <tr>
                        <td colspan="2">Total</td>
                        <td align="right"><?=number_format($dataInvMonthly["January"]+$dataInvMonthly["February"]+$dataInvMonthly["March"]+$dataInvMonthly["April"]+$dataInvMonthly["May"]+$dataInvMonthly["June"]+$dataInvMonthly["July"]+$dataInvMonthly["August"]+$dataInvMonthly["September"]+$dataInvMonthly["October"]+$dataInvMonthly["November"]+$dataInvMonthly["December"],0);?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 70%;height: 250px">
                <canvas id="myItemChart"></canvas>
            </td>
            <td colspan="2" align="center" style="width: 30%;height: 250px">
                <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
                    <tr>
                        <th>No.</th>
                        <th>KODE</th>
                        <th>TOP 10 PRODUK</th>
                        <th>NILAI (Rp)</th>
                    </tr>
                    <?php
                    $nmr = 1;
                    while ($row = $dataOmsetItem->FetchAssoc()) {
                        print("<tr>");
                        printf("<td>%s</td>",$nmr++);
                        printf("<td>%s</td>",$row["item_code"]);
                        printf("<td>%s</td>",$row["item_name"]);
                        printf("<td align='right'>%s</td>",number_format($row["nilai"],0));
                        print("</tr>");
                    }
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 70%;height: 250px">
                <canvas id="myCustomerChart"></canvas>
            </td>
            <td colspan="2" align="center" style="width: 30%;height: 250px">
                <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
                    <tr>
                        <th>No.</th>
                        <th>KODE</th>
                        <th>TOP 10 CUSTOMER</th>
                        <th>NILAI (Rp)</th>
                    </tr>
                    <?php
                    $nmr = 1;
                    while ($row = $dataOmsetCustomer->FetchAssoc()) {
                        print("<tr>");
                        printf("<td>%s</td>",$nmr++);
                        printf("<td>%s</td>",$row["customer_code"]);
                        printf("<td>%s</td>",$row["customer_name"]);
                        printf("<td align='right'>%s</td>",number_format($row["omset"],0));
                        print("</tr>");
                    }
                    ?>
                </table>
            </td>
        </tr>
    </table>
</div>
<script>
    var ctxLine = document.getElementById('myLineChart').getContext('2d');
    var myLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels  : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label               : 'Penjualan',
                backgroundColor     : 'rgba(139, 29, 65, 0.8)',
                borderColor			: 'rgba(139, 29, 65, 0.8)',
                border              : 1,
                fill				: false,
                data                : [<?= $dataInvoices?>]
            },
                {
                    label               : 'Penerimaan Piutang',
                    backgroundColor     : 'rgba(105, 120, 12, 0.8)',
                    borderColor			: 'rgba(105, 120, 12, 0.8)',
                    border              : 1,
                    fill				: false,
                    data                : [<?= $dataReceipts?>]
                }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: 'GRAFIK PENJUALAN TAHUN <?=$dataTahun?>'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });

    //grafik penjualan top 10 customer
    $.ajax({
        url: "<?php print($helper->site_url("main/top10customerdata"));?>",
        method: "GET",
        success: function(response) {
            console.log(response);
            data = JSON.parse(response);
            console.log(data);
            var label = [];
            var nilai = [];
            var warna = [];

            for(var i=0; i<data.length;i++) {
                label.push(data[i].kode);
                nilai.push(data[i].nilai);
                warna.push(data[i].warna);
            }

            var ctx = document.getElementById('myCustomerChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label,
                    datasets: [{
                        label: 'Omset Customer',
                        backgroundColor     : warna,
                        borderColor			: 'rgba(139, 29, 65, 0.8)',
                        data: nilai
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: true,
                        text: 'TOP 10 CUSTOMER <?=$dataTahun?>'
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        }
    });

    //grafik penjualan top 10 customer
    $.ajax({
        url: "<?php print($helper->site_url("main/top10itemdata"));?>",
        method: "GET",
        success: function(response) {
            console.log(response);
            data = JSON.parse(response);
            console.log(data);
            var label = [];
            var nilai = [];
            var warna = [];

            for(var i=0; i<data.length;i++) {
                label.push(data[i].kode);
                nilai.push(data[i].nilai);
                warna.push(data[i].warna);
            }

            var ctx = document.getElementById('myItemChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label,
                    datasets: [{
                        label: 'Omset Produk',
                        backgroundColor     : warna,
                        borderColor			: 'rgba(139, 29, 65, 0.8)',
                        data: nilai
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: true,
                        text: 'TOP 10 PRODUCT <?=$dataTahun?>'
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        }
    });
</script>
<!-- ingat div notifikasi nanti disini tempatnya -->
<div id="notifications" class="subTitle" style="border: dotted #000000 1px; margin: 10px 20px; padding: 10px;">
    <div class="bold"><u>Pengumuman:</u></div>
    <ul>
        <?php

        foreach ($attentions as $atts) {
            print("<li>");
            printf("<div class='bold'>%s</div>",$atts->AttHeader);
            printf("%s",$atts->AttContent);
            print("</li>");
        }

        ?>
    </ul>
    <div class="bold" style="text-decoration: blink"><u>Notifikasi:</u></div>
    <ul>
        <?php
/*
        foreach ($notifications as $group) {
            $buff = sprintf("<li>%s<ol>", $group->Name);
            foreach ($group->UserNotifications as $notification) {
                $buff .= sprintf('<li>%s&nbsp<a href="%s">%s</a></li>',$notification->Text,$helper->site_url($notification->Url),$notification->Status);
            }
            $buff .= "</ol></li>";
            print($buff);
        }
*/
        ?>
    </ul>
</div>
<!-- </body> -->
</html>
