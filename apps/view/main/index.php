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
