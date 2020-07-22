<!DOCTYPE HTML>
<html>
<?php /** @var $customers Contacts[] */ ?>
<head>
	<title>ERASYS - Rekapitulasi Retur Penjualan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#StartDate").customDatePicker({ showOn: "focus" });
            $("#EndDate").customDatePicker({ showOn: "focus" });
        });
    </script>

</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th colspan="4"><b>Rekapitulasi Retur Penjualan</b></th>
            <th>Jenis Laporan:</th>
            <th colspan="2"><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>>1 - Rekap Per Bukti</option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>>2 - Rekap Retur Detail</option>
                    <option value="3" <?php print($JnsLaporan == 3 ? 'selected="selected"' : '');?>>3 - Rekap Item Retur</option>
                </select>
            </th>
        </tr>
        <tr class="center">
            <th>Cabang</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Dari Tanggal</th>
            <th>Sampai Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select name="CabangId" class="text2" id="CabangId" required>
                <?php if($userLevel > 3){ ?>
                    <option value="0">- Semua Cabang -</option>
                    <?php
                    foreach ($cabangs as $cab) {
                        if ($cab->Id == $CabangId) {
                            printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                        } else {
                            printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                        }
                    }
                    ?>
                <?php }else{
                        printf('<option value="%d">%s - %s</option>', $userCabId, $userCabCode, $userCabName);
                }?>
                </select>
            </td>
            <td>
                <select id="ContactsId" name="ContactsId" style="width: 150px" required>
                    <option value="0">- Semua Customer -</option>
                    <?php
                    foreach ($customers as $customer) {
                        if ($ContactsId == $customer->Id){
                            printf('<option value="%d" selected="selected">%s (%s)</option>',$customer->Id,$customer->ContactCode,$customer->ContactName);
                        }else{
                            printf('<option value="%d">%s (%s)</option>',$customer->Id,$customer->ContactCode,$customer->ContactName);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <select id="RjStatus" name="RjStatus" required>
                    <option value="-1" <?php print($Status == -1 ? 'selected="selected"' : '');?>> - Semua Status -</option>
                    <option value="0" <?php print($Status == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($Status == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="2" <?php print($Status == 2 ? 'selected="selected"' : '');?>>2 - Batal</option>                    
                </select>
            </td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="StartDate" name="StartDate" value="<?php printf(date('d-m-Y',$StartDate));?>"/></td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="EndDate" name="EndDate" value="<?php printf(date('d-m-Y',$EndDate));?>"/></td>
            <td>
                <select id="Output" name="Output" required>
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>>0 - Web Html</option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>>1 - Excel</option>
                </select>
            </td>
            <td><button type="submit" formaction="<?php print($helper->site_url("ar.arreturn/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
    if ($JnsLaporan < 3){
    ?>
    <h3>Rekapitulasi Retur Penjualan</h3>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Cabang</th>
            <th>Tanggal</th>
            <th>No. Bukti</th>
            <th>Nama Customer</th>
            <th>Keterangan</th>
            <th>Nilai Retur</th>
            <?php
            if ($JnsLaporan == 2){
                print("<th nowrap='nowrap'>Ex.Invoice No.</th>");
                print("<th nowrap='nowrap'>Kode Barang</th>");
                print("<th nowrap='nowrap'>Nama Barang</th>");
                print("<th>QTY</th>");
                print("<th>Harga</th>");
                print("<th>Jumlah</th>");
            }
            ?>
        </tr>
        <?php
            $nmr = 1;
            $total = 0;
            $subtotal = 0;
            $url = null;
            $sts = null;
            $ivn = null;
            $sma = false;
            while ($row = $Reports->FetchAssoc()) {
                if ($ivn <> $row["rj_no"]){
                    $nmr++;
                    $sma = false;
                }else{
                    $sma = true;
                }
                if (!$sma) {
                    $url = $helper->site_url("ar.arreturn/view/".$row["id"]);
                    print("<tr valign='Top'>");
                    printf("<td>%s</td>",$nmr);
                    printf("<td nowrap='nowrap'>%s</td>",$row["cabang_code"]);
                    printf("<td>%s</td>",date('d-m-Y',strtotime($row["rj_date"])));
                    printf("<td><a href= '%s' target='_blank'>%s</a></td>",$url ,$row["rj_no"]);
                    printf("<td nowrap='nowrap'>%s</td>",$row["customer_name"].' ('.$row["customer_code"].')');
                    printf("<td nowrap='nowrap'>%s</td>",$row["rj_descs"]);
                    printf("<td align='right'>%s</td>",number_format($row["rj_amount"],0));
                        if ($JnsLaporan == 1){
                            print("</tr>");
                        };
                    $nmr++;
                    $total+= $row["rj_amount"];
                }
                if ($JnsLaporan == 2){
                    if ($sma) {
                        print("</tr>");
                        print("<td colspan='7'>&nbsp;</td>");
                    }
                    printf("<td nowrap='nowrap'>%s</td>", $row['ex_invoice_no']);
                    printf("<td nowrap='nowrap'>%s</td>", $row['item_code']);
                    printf("<td nowrap='nowrap'>%s</td>", $row['item_descs']);
                    printf("<td align='right'>%s</td>", number_format($row['qty_retur'], 0));
                    printf("<td align='right' >%s</td>", number_format($row['price'], 0));
                    printf("<td align='right'>%s</td>", number_format($row['sub_total'], 0));
                    print("</tr>");
                    $subtotal+= $row['sub_total'];
                }
                $ivn = $row["rj_no"];
            }
        print("<tr>");
        print("<td colspan='6' align='right'>Total Retur</td>");
        printf("<td align='right'>%s</td>",number_format($total,0));
        if ($JnsLaporan == 2) {
            print("<td colspan='5'>&nbsp;</td>");
            printf("<td align='right'>%s</td>", number_format($subtotal, 0));
        }
        print("</tr>");
        ?>
    </table>
    <?php }else{ ?>
        <h3>Rekapitulasi Item Retur Penjualan</h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>QTY</th>
                <th>Nilai Retur</th>
            </tr>
            <?php
            $nmr = 0;
            $sqty = 0;
            $snilai = 0;
            while ($row = $Reports->FetchAssoc()) {
                $nmr++;
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>",$row['item_code']);
                printf("<td>%s</td>",$row['item_descs']);
                printf("<td>%s</td>",$row['satuan']);
                printf("<td align='right'>%s</td>",number_format($row['sum_qty'],0));
                printf("<td align='right'>%s</td>",number_format($row['sum_total'],0));
                print("</tr>");
                $sqty+= $row['sum_qty'];
                $snilai+= $row['sum_total'];
            }
            print("<tr>");
            print("<td colspan='4' align='right'>Total.....</td>");
            printf("<td align='right'>%s</td>",number_format($sqty,0));
            printf("<td align='right'>%s</td>",number_format($snilai,0));
            print("</tr>");
            ?>
        </table>
        <!-- end web report -->
<?php }} ?>
<!-- </body> -->
</html>
