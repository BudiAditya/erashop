<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>ERASHOP - Rekapitulasi Nota/Invoice/Tagihan</title>
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
            <th colspan="10"><b>Rekapitulasi Nota/Invoice/Tagihan</b></th>
        </tr>
        <tr class="center">
            <th>Cabang</th>
            <th>Jenis Barang</th>
            <th>Customer</th>
            <th>Salesman</th>
            <th>Invoice Status</th>
            <th>Status Lunas</th>
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
                    foreach ($cabangs as $cab) {
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
                <select id="JnsBarangId" name="JnsBarangId" style="width: 150px" required>
                    <option value="0">- Semua Jenis Barang -</option>
                    <?php
                    foreach ($jnsbarang as $jbarang) {
                        if ($jbarang->Id == $JnsBarangId) {
                            printf('<option value="%d" selected="selected">%s</option>', $jbarang->Id, $jbarang->JnsBarang);
                        } else {
                            printf('<option value="%d">%s</option>', $jbarang->Id, $jbarang->JnsBarang);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <select id="CustomerId" name="CustomerId" style="width: 150px" required>
                    <option value="0">- Semua Customer -</option>
                    <?php
                    foreach ($customers as $customer) {
                        if ($CustomerId == $customer->Id){
                            printf('<option value="%d" selected="selected">%s (%s)</option>',$customer->Id,$customer->CustomerName,$customer->CustomerCd);
                        }else{
                            printf('<option value="%d">%s (%s)</option>',$customer->Id,$customer->CustomerName,$customer->CustomerCd);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <select id="SalesId" name="SalesId" style="width: 150px" required>
                    <option value="0">- Semua Salesman -</option>
                    <?php
                    foreach ($sales as $salesman) {
                        if ($salesman->Id == $SalesId) {
                            printf('<option value="%d" selected="selected">%s</option>', $salesman->Id, $salesman->Nama);
                        } else {
                            printf('<option value="%d">%s</option>', $salesman->Id, $salesman->Nama);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <select id="Status" name="Status" required>
                    <option value="-1" <?php print($Status == -1 ? 'selected="selected"' : '');?>> - Semua Status -</option>
                    <option value="0" <?php print($Status == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($Status == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="2" <?php print($Status == 2 ? 'selected="selected"' : '');?>>2 - Penagihan</option>
                    <option value="3" <?php print($Status == 3 ? 'selected="selected"' : '');?>>3 - Terbayar</option>
                    <option value="4" <?php print($Status == 3 ? 'selected="selected"' : '');?>>4 - Batal</option>
                </select>
            </td>
            <td>
                <select id="PaymentStatus" name="PaymentStatus" required>
                    <option value="-1" <?php print($PaymentStatus == -1 ? 'selected="selected"' : '');?>> - Semua Status -</option>
                    <option value="0" <?php print($PaymentStatus == 0 ? 'selected="selected"' : '');?>>0 - Belum Lunas</option>
                    <option value="1" <?php print($PaymentStatus == 1 ? 'selected="selected"' : '');?>>1 - Lunas</option>
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
            <td><button type="submit" formaction="<?php print($helper->site_url("ar.invoice/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){ ?>
    <h3>Rekapitulasi A/R Invoice</h3>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Cabang</th>
            <th>Tanggal</th>
            <th>No. Invoice</th>
            <th>Nama Customer</th>
            <th>Nama Salesman</th>
            <th>Jenis Barang</th>
            <th>Keterangan</th>
            <th>Jatuh Tempo</th>
            <th>Jumlah</th>
            <th>Terbayar</th>
            <th>Outstanding</th>
            <th>Status</th>
        </tr>
        <?php
            $nmr = 1;
            $tDpp = 0;
            $tPpn = 0;
            $tTerbayar = 0;
            $tSisa = 0;
            $url = null;
            while ($row = $Reports->FetchAssoc()) {
                $url = $helper->site_url("ar.invoice/view/".$row["id"]);
                print("<tr valign='Top'>");
                printf("<td>%s</td>",$nmr);
                printf("<td>%s</td>",$row["kd_cabang"]);
                printf("<td>%s</td>",date('d-m-Y',strtotime($row["invoice_date"])));
                printf("<td><a href= '%s' target='_blank'>%s</a></td>",$url ,$row["invoice_no"]);
                printf("<td nowrap='nowrap'>%s</td>",$row["customer_name"]);
                printf("<td nowrap='nowrap'>%s</td>",$row["nm_sales"]);
                printf("<td>%s</td>",$row["jns_barang"]);
                printf("<td nowrap='nowrap'>%s</td>",$row["invoice_descs"]);
                printf("<td>%s</td>",date('d-m-Y',strtotime($row["due_date"])));
                printf("<td align='right'>%s</td>",number_format($row["base_amount"]+$row["tax_amount"],0));
                printf("<td align='right'>%s</td>",number_format($row["paid_amount"],0));
                printf("<td align='right'>%s</td>",number_format($row["base_amount"]+$row["tax_amount"]-$row["paid_amount"],0));
                printf("<td>%s</td>",$row["status_desc"]);
                print("</tr>");
                $nmr++;
                $tDpp+= $row["base_amount"];
                $tPpn+= $row["tax_amount"];
                $tTerbayar+= $row["paid_amount"];
                $tSisa+= $row["base_amount"]+$row["tax_amount"]-$row["paid_amount"];
            }
        print("<tr>");
        print("<td colspan='9' align='right'>Total Invoice</td>");
        printf("<td align='right'>%s</td>",number_format($tDpp+$tPpn,0));
        printf("<td align='right'>%s</td>",number_format($tTerbayar,0));
        printf("<td align='right'>%s</td>",number_format($tSisa,0));
        printf("<td colspan='2'>&nbsp</td>");
        print("</tr>");
        ?>
    </table>
<!-- end web report -->
<?php } ?>
</body>
</html>
