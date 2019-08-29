<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php /** @var $suppliers Contacts[] */ /** @var $banks Bank[] */ ?>
<head>
	<title>ERASHOP - Rekapitulasi Pembayaran Hutang</title>
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
            <th colspan="10"><b>Rekapitulasi Pembayaran Hutang</b></th>
        </tr>
        <tr class="center">
            <th>Cabang</th>
            <th>Supplier</th>
            <th>Cara Bayar</th>
            <th>Kas/Bank</th>
            <th>Status</th>
            <th>Dari Tanggal</th>
            <th>Sampai Tanggal</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select name="CabangId" class="text2" id="CabangId" style="width: 100px" required>
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
                <select id="ContactsId" name="ContactsId" style="width: 100px" required>
                    <option value="0">- Semua Supplier -</option>
                    <?php
                    foreach ($suppliers as $supplier) {
                        if ($ContactsId == $supplier->Id){
                            printf('<option value="%d" selected="selected">%s (%s)</option>',$supplier->Id,$supplier->ContactName,$supplier->ContactCode);
                        }else{
                            printf('<option value="%d">%s (%s)</option>',$supplier->Id,$supplier->ContactName,$supplier->ContactCode);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <select id="PaymentMode" name="PaymentMode" style="width: 100px" required>
                    <option value="-1">- Semua Cara Bayar -</option>
                    <option value="0" <?php print($PaymentMode == 0 ? 'selected="selected"' : '');?>>0 - Tunai</option>
                    <option value="1" <?php print($PaymentMode == 1 ? 'selected="selected"' : '');?>>1 - Transfer</option>
                    <option value="2" <?php print($PaymentMode == 2 ? 'selected="selected"' : '');?>>2 - Cheque/BG</option>
                    <option value="3" <?php print($PaymentMode == 3 ? 'selected="selected"' : '');?>>3 - Slip</option>
                    <option value="4" <?php print($PaymentMode == 4 ? 'selected="selected"' : '');?>>4 - Lain</option>
                </select>
            </td>
            <td>
                <select id="BankId" name="BankId" style="width: 100px" required>
                    <option value="0">- Semua Kas/Bank -</option>
                    <?php
                    foreach ($banks as $bank) {
                        if ($BankId == $bank->Id){
                            printf('<option value="%d" selected="selected">%s - %s</option>',$bank->Id,$bank->Id,$bank->Name);
                        }else{
                            printf('<option value="%d">%s - %s</option>',$bank->Id,$bank->Id,$bank->Name);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <select id="PaymentStatus" name="PaymentStatus" style="100px" required>
                    <option value="-1" <?php print($PaymentStatus == -1 ? 'selected="selected"' : '');?>> - Semua Status -</option>
                    <option value="0" <?php print($PaymentStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($PaymentStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="2" <?php print($PaymentStatus == 2 ? 'selected="selected"' : '');?>>2 - Batal</option>
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
            <td><button type="submit" formaction="<?php print($helper->site_url("ap.payment/report")); ?>"><b>Proses</b></button></td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($Reports != null){ ?>
    <h3>Rekapitulasi Pembayaran Hutang</h3>
    <?php printf("Dari Tgl. %s - %s",date('d-m-Y',$StartDate),date('d-m-Y',$EndDate));?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Cabang</th>
            <th>Tanggal</th>
            <th>No. Payment</th>
            <th>Nama Supplier</th>
            <th>Cara Bayar</th>
            <th>Kas / Bank</th>
            <th>Jumlah</th>
            <th>Status</th>
        </tr>
        <?php
            $nmr = 1;
            $total = 0;
            $url = null;
            while ($row = $Reports->FetchAssoc()) {
                $url = $helper->site_url("ap.payment/view/".$row["id"]);
                print("<tr valign='Top'>");
                printf("<td>%s</td>",$nmr);
                printf("<td>%s</td>",$row["cabang_code"]);
                printf("<td>%s</td>",date('d-m-Y',strtotime($row["payment_date"])));
                printf("<td><a href= '%s' target='_blank'>%s</a></td>",$url ,$row["payment_no"]);
                printf("<td nowrap='nowrap'>%s</td>",$row["supplier_name"].' ('.$row["supplier_code"].')');
                printf("<td nowrap='nowrap'>%s</td>",$row["cara_bayar"]);
                printf("<td nowrap='nowrap'>%s</td>",$row["bank_name"]);
                printf("<td align='right'>%s</td>",number_format($row["payment_amount"],0));
                printf("<td>%s</td>",$row["status_desc"]);
                print("</tr>");
                $nmr++;
                $total+= $row["payment_amount"];
            }
        print("<tr>");
        print("<td colspan='7' align='right'>Total Pembayaran</td>");
        printf("<td align='right'>%s</td>",number_format($total,0));
        printf("<td>&nbsp</td>");
        print("</tr>");
        ?>
    </table>
<!-- end web report -->
<?php } ?>
</body>
</html>
