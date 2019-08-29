<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>ERASHOP - Rekapitulasi Produksi</title>
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
<br/>
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th colspan="2"><b>Rekapitulasi Produksi</b></th>
            <th>Jenis Laporan:</th>
            <th colspan="2"><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>>1 - Laporan Produksi</option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>>2 - Rekapitulasi Hasil Produksi</option>
                    <option value="3" <?php print($JnsLaporan == 3 ? 'selected="selected"' : '');?>>3 - Laporan Pemakaian Material</option>
                    <option value="4" <?php print($JnsLaporan == 4 ? 'selected="selected"' : '');?>>4 - Rekapitulasi Pemakaian Material</option>
                </select>
            </th>
        </tr>
        <tr class="center">
            <th>Cabang</th>
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
            <td><input type="text" class="text2" maxlength="10" size="10" id="StartDate" name="StartDate" value="<?php printf(date('d-m-Y',$StartDate));?>"/></td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="EndDate" name="EndDate" value="<?php printf(date('d-m-Y',$EndDate));?>"/></td>
            <td>
                <select id="Output" name="Output" required>
                    <option value="0" <?php print($Output == 0 ? 'selected="selected"' : '');?>>0 - Web Html</option>
                    <option value="1" <?php print($Output == 1 ? 'selected="selected"' : '');?>>1 - Excel</option>
                </select>
            </td>
            <td><button type="submit" formaction="<?php print($helper->site_url("inventory.assembly/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
    if ($JnsLaporan == 1){
    ?>
    <h3>Laporan Produksi</h3>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Cabang</th>
            <th>Tanggal</th>
            <th>No. Produksi</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Q T Y</th>
            <th>Nilai Produksi</th>
        </tr>
        <?php
        $nmr = 0;
        $tQty = 0;
        $tNilai = 0;
        $sma = false;
        $ivn = null;
        while ($row = $Reports->FetchAssoc()) {
            $nmr++;
            $url = $helper->site_url("inventory.assembly/view/" . $row["id"]);
            print("<tr valign='Top'>");
            printf("<td>%s</td>", $nmr);
            printf("<td>%s</td>", $row["cabang_code"]);
            printf("<td>%s</td>", date('d-m-Y', strtotime($row["assembly_date"])));
            printf("<td><a href= '%s' target='_blank'>%s</a></td>", $url, $row["assembly_no"]);
            printf("<td nowrap='nowrap'>%s</td>", $row["item_code"]);
            printf("<td nowrap='nowrap'>%s</td>", $row["item_name"]);
            printf("<td nowrap='nowrap'>%s</td>", $row["bsatkecil"]);
            printf("<td align='right'>%s</td>", number_format($row["qty"], 0));
            printf("<td align='right'>%s</td>", number_format(round($row["qty"] * $row["price"],0), 0));
            print("</tr>");
            $tQty+= $row["qty"];
            $tNilai+= round($row["qty"] * $row["price"],0);
        }
        print("<tr>");
        print("<td colspan='7' align='right'>Total</td>");
        printf("<td align='right'>%s</td>",number_format($tQty,0));
        printf("<td align='right'>%s</td>",number_format($tNilai,0));
        print("</tr>");
        ?>
    </table>
<?php }elseif ($JnsLaporan == 2){ ?>
        <h3>Rekapitulasi Hasil Produksi</h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Qty</th>
                <th>Nilai Produksi</th>
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
                printf("<td>%s</td>",$row['item_name']);
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
<?php }elseif ($JnsLaporan == 3){ ?>
        <h3>Laporan Pemakaian Material</h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Cabang</th>
                <th>Tanggal</th>
                <th>No. Produksi</th>
                <th>Kode Material</th>
                <th>Nama Material</th>
                <th>Satuan</th>
                <th>Q T Y</th>
                <th>Nilai Material</th>
            </tr>
            <?php
            $nmr = 0;
            $tQty = 0;
            $tNilai = 0;
            $sma = false;
            $ivn = null;
            while ($row = $Reports->FetchAssoc()) {
                $nmr++;
                $url = $helper->site_url("inventory.assembly/view/" . $row["id"]);
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>", $row["cabang_code"]);
                printf("<td>%s</td>", date('d-m-Y', strtotime($row["assembly_date"])));
                printf("<td><a href= '%s' target='_blank'>%s</a></td>", $url, $row["assembly_no"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["item_code"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["item_name"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["satuan"]);
                printf("<td align='right'>%s</td>", number_format($row["qty"], 0));
                printf("<td align='right'>%s</td>", number_format(round($row["qty"] * $row["price"],0), 0));
                print("</tr>");
                $tQty+= $row["qty"];
                $tNilai+= round($row["qty"] * $row["price"],0);
            }
            print("<tr>");
            print("<td colspan='7' align='right'>Total</td>");
            printf("<td align='right'>%s</td>",number_format($tQty,0));
            printf("<td align='right'>%s</td>",number_format($tNilai,0));
            print("</tr>");
            ?>
        </table>
<?php }else{ ?>
        <h3>Rekapitulasi Pemakaian Material</h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Qty</th>
                <th>Nilai Material</th>
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
                printf("<td>%s</td>",$row['item_name']);
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
<?php }} ?>
</body>
</html>
