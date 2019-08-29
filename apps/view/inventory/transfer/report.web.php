<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>ERASYS - Rekapitulasi Transfer Stock</title>
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
            <th colspan="2"><b>Rekapitulasi Transfer Stock Barang</b></th>
            <th>Jenis Laporan:</th>
            <th colspan="2"><select name="JnsLaporan" id="JnsLaporan">
                    <option value="1" <?php print($JnsLaporan == 1 ? 'selected="selected"' : '');?>>1 - Laporan Transfer</option>
                    <option value="2" <?php print($JnsLaporan == 2 ? 'selected="selected"' : '');?>>2 - Rekap Barang Transfer</option>
                </select>
            </th>
        </tr>
        <tr class="center">
            <th>Dari Cabang/Gudang</th>
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
            <td><button type="submit" formaction="<?php print($helper->site_url("inventory.transfer/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){
    if ($JnsLaporan == 1){
    ?>
    <h3>Laporan Transfer Stock Barang</h3>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Dari Cabang</th>
            <th>Ke Cabang</th>
            <th>Tanggal</th>
            <th>No.Transfer</th>
            <th>Keterangan</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Q T Y</th>
        </tr>
        <?php
        $nmr = 0;
        $tQty = 0;
        $npn = null;
        while ($row = $Reports->FetchAssoc()) {
            if ($npn <> $row["npb_no"]) {
                $nmr++;
                $url = $helper->site_url("inventory.transfer/view/" . $row["id"]);
                print("<tr valign='Top'>");
                printf("<td>%s</td>", $nmr);
                printf("<td>%s</td>", $row["cabang_code"]);
                printf("<td>%s</td>", $row["to_cabang_code"]);
                printf("<td>%s</td>", date('d-m-Y', strtotime($row["npb_date"])));
                printf("<td><a href= '%s' target='_blank'>%s</a></td>", $url, $row["npb_no"]);
                printf("<td nowrap='nowrap'>%s</td>", $row["npb_descs"]);
            }else{
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
            }
            printf("<td nowrap='nowrap'>%s</td>", $row["item_code"]);
            printf("<td nowrap='nowrap'>%s</td>", $row["item_name"]);
            printf("<td nowrap='nowrap'>%s</td>", $row["satuan"]);
            printf("<td align='right'>%s</td>", number_format($row["qty"], 0));
            print("</tr>");
            $tQty+= $row["qty"];
            $npn = $row["npb_no"];
        }
        print("<tr>");
        print("<td colspan='9' align='right'>Total</td>");
        printf("<td align='right'>%s</td>",number_format($tQty,0));
        print("</tr>");
        ?>
    </table>
<?php }else{ ?>
        <h3>Rekapitulasi Transfer Stock Barang</h3>
        <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
        <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
            <tr>
                <th>No.</th>
                <th>Dari Cabang</th>
                <th>Ke Cabang</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Qty</th>
            </tr>
            <?php
            $nmr = 0;
            $sqty = 0;
            $cbs = null;
            while ($row = $Reports->FetchAssoc()) {
                if ($cbs <> $row['cabang_code'].$row['to_cabang_code']) {
                    $nmr++;
                    print("<tr valign='Top'>");
                    printf("<td>%s</td>", $nmr);
                    printf("<td>%s</td>", $row['cabang_code']);
                    printf("<td>%s</td>", $row['to_cabang_code']);
                }else{
                    print("<td>&nbsp;</td>");
                    print("<td>&nbsp;</td>");
                    print("<td>&nbsp;</td>");
                }
                printf("<td>%s</td>",$row['item_code']);
                printf("<td>%s</td>",$row['item_name']);
                printf("<td>%s</td>",$row['satuan']);
                printf("<td align='right'>%s</td>",number_format($row['sum_qty'],0));
                print("</tr>");
                $sqty+= $row['sum_qty'];
                $cbs = $row['cabang_code'].$row['to_cabang_code'];
            }
            print("<tr>");
            print("<td colspan='6' align='right'>Total.....</td>");
            printf("<td align='right'>%s</td>",number_format($sqty,0));
            print("</tr>");
            ?>
        </table>
<?php }} ?>
</body>
</html>
