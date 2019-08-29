<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>ERASHOP - Laporan Rekening Koran Kas/Bank</title>
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
            <th colspan="6"><b>Laporan Rekening Koran Kas/Bank</b></th>
        </tr>
        <tr class="center">
            <th>Cabang/Outlet</th>
            <th>Kas/Bank</th>
            <th>Dari Tanggal</th>
            <th>Sampai Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select name="CabangId" class="text2" id="CabangId" required>
                    <option value="0">- Semua Cabang -</option>
                    <?php
                    foreach ($Cabangs as $cab) {
                        if ($cab->Id == $CabangId) {
                            printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                        } else {
                            printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <select id="CoaBankId" name="CoaBankId" required>
                    <?php
                    foreach ($CoaBanks as $coabank) {
                        if($coabank->Id == $CoaBankId){
                            printf('<option value="%d" selected="selected">%s</option>',$coabank->Id, $coabank->Perkiraan);
                        }else{
                            printf('<option value="%d">%s</option>',$coabank->Id, $coabank->Perkiraan);
                        }
                    }
                    ?>
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
            <td><button type="submit" formaction="<?php print($helper->site_url("cashbank.cbtrx/rekoran")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){ ?>
    <h3>Laporan Rekening Koran Kas/Bank</h3>
    <?php printf("<h3>%s - %s</h3>",$BankKode,$BankName) ?>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Cabang</th>
            <th>Tanggal</th>
            <th>No. Bukti</th>
            <th>Keterangan</th>
            <th>Relasi / Asuransi</th>
            <th>Refferensi</th>
            <th>Debet</th>
            <th>Kredit</th>
            <th>Saldo</th>
            <th>Admin</th>
        </tr>
        <?php
            $nmr = 1;
            $tdebet = 0;
            $tkredit = 0;
            $debet = 0;
            $kredit = 0;
            $saldo = $SaldoAwal;
            $url = null;
            print("<tr valign='Top'>");
            printf("<td>%s</td>",$nmr);
            printf("<td>&nbsp</td>");
            printf("<td>%s</td>",date('d-m-Y',$StartDate));
            printf("<td>&nbsp</a></td>");
            printf("<td>Saldo Awal</td>");
            printf("<td>&nbsp</td>");
            printf("<td>&nbsp</td>");
            printf("<td align='right'>%s</td>",number_format($debet,0));
            printf("<td align='right'>%s</td>",number_format($kredit,0));
            printf("<td align='right'>%s</td>",number_format($saldo,0));
            printf("<td>&nbsp</td>");
            print("</tr>");
            $nmr++;
            while ($row = $Reports->FetchAssoc()) {
                $url = $helper->site_url("cashbank.cbtrx/view/".$row["id"]);
                $debet = $row["db_amount"];
                $kredit = $row["cr_amount"];
                $saldo = $saldo + $debet - $kredit;
                $tdebet+= $debet;
                $tkredit+= $kredit;
                print("<tr valign='Top'>");
                printf("<td>%s</td>",$nmr);
                printf("<td>%s</td>",$row["kode_cabang"]);
                printf("<td>%s</td>",date('d-m-Y',strtotime($row["trx_date"])));
                printf("<td><a href= '%s' target='_blank'>%s</a></td>",$url ,$row["doc_no"]);
                printf("<td>%s</td>",$row["trx_descs"]);
                printf("<td>%s</td>",$row["customer_name"]);
                printf("<td>%s</td>",$row["reff_no"]);
                printf("<td align='right'>%s</td>",number_format($debet,0));
                printf("<td align='right'>%s</td>",number_format($kredit,0));
                printf("<td align='right'>%s</td>",number_format($saldo,0));
                printf("<td>%s</td>",$row["user_id"]);
                print("</tr>");
                $nmr++;
            }
        print("<tr>");
        print("<td colspan='7' align='right'>Total Transaksi</td>");
        printf("<td align='right'>%s</td>",number_format($tdebet,0));
        printf("<td align='right'>%s</td>",number_format($tkredit,0));
        printf("<td>&nbsp</td>");
        printf("<td>&nbsp</td>");
        print("</tr>");
        ?>
    </table>
<!-- end web report -->
<?php } ?>
</body>
</html>
