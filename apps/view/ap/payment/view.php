<!DOCTYPE HTML>
<html>
<?php
/** @var $payment Payment */ /** @var $banks Bank[] */
?>
<head>
<title>ERASYS - View Pembayaran Hutang</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>

<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/default/easyui.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/icon.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/color.css")); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-demo/demo.css")); ?>"/>

<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>

<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>

<style scoped>
    .f1{
        width:200px;
    }
</style>
<script type="text/javascript">

    $( function() {
        $('#CreditorId').combogrid({
            panelWidth:600,
            url: "<?php print($helper->site_url("master.contacts/getjson_contacts/2"));?>",
            idField:'id',
            textField:'contact_name',
            mode:'remote',
            fitColumns:true,
            columns:[[
                {field:'contact_code',title:'Kode',width:30},
                {field:'contact_name',title:'Nama Customer',width:100},
                {field:'address',title:'Alamat',width:100},
                {field:'city',title:'Kota',width:60}
            ]],
            onSelect: function(index,row){
                //var lvl = row.contactlevel;
                //console.log(lvl);
            }
        });

        $("#bTambah").click(function(){
            if (confirm('Buat Payment baru?')){
                location.href="<?php print($helper->site_url("ap.payment/add")); ?>";
            }
        });

        $("#bEdit").click(function(){
            if (confirm('Anda akan mengubah data payment ini?')){
                location.href="<?php print($helper->site_url("ap.payment/edit/").$payment->Id); ?>";
            }
        });

        $("#bHapus").click(function(){
            if (confirm('Anda yakin akan membatalkan data payment ini?')){
                location.href="<?php print($helper->site_url("ap.payment/void/").$payment->Id); ?>";
            }
        });

        $("#bCetak").click(function(){
            if (confirm('Cetak payment ini?')){
                //location.href="<?php //print($helper->site_url("ap.payment/print_pdf/").$payment->Id); ?>";
                alert('Proses cetak belum siap..');
            }
        });

        $("#bKembali").click(function(){
            location.href="<?php print($helper->site_url("ap.payment")); ?>";
        });
    });

</script>
<style type="text/css">
    #fd{
        margin:0;
        padding:5px 10px;
    }
    .ftitle{
        font-size:14px;
        font-weight:bold;
        padding:5px 0;
        margin-bottom:10px;
        border-bottom:1px solid #ccc;
    }
    .fitem{
        margin-bottom:5px;
    }
    .fitem label{
        display:inline-block;
        width:100px;
    }
    .numberbox .textbox-text{
        text-align: right;
        color: blue;
    }
</style>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php }
$badd = base_url('public/images/button/').'add.png';
$bsave = base_url('public/images/button/').'accept.png';
$bcancel = base_url('public/images/button/').'cancel.png';
$bview = base_url('public/images/button/').'view.png';
$bedit = base_url('public/images/button/').'edit.png';
$bdelete = base_url('public/images/button/').'delete.png';
$bclose = base_url('public/images/button/').'close.png';
$bsearch = base_url('public/images/button/').'search.png';
$bkembali = base_url('public/images/button/').'back.png';
$bcetak = base_url('public/images/button/').'printer.png';
$bsubmit = base_url('public/images/button/').'ok.png';
$baddnew = base_url('public/images/button/').'create_new.png';
?>
<br />
<div id="p" class="easyui-panel" title="View Pembayaran Hutang" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td>Cabang</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($payment->CabangCode != null ? $payment->CabangCode : $userCabCode); ?>" disabled/>
                <input type="hidden" id="CabangId" name="CabangId" value="<?php print($payment->CabangId == null ? $userCabId : $payment->CabangId);?>"/>
            </td>
            <td>Tanggal</td>
            <td><input type="text" size="12" id="PaymentDate" name="PaymentDate" value="<?php print($payment->FormatPaymentDate(JS_DATE));?>" readonly/></td>
            <td>No. Payment</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="PaymentNo" name="PaymentNo" value="<?php print($payment->PaymentNo != null ? $payment->PaymentNo : '-'); ?>" readonly/></td>
        </tr>
        <tr>
            <td>Supplier</td>
            <td><input class="easyui-combogrid" id="CreditorId" name="CreditorId" style="width: 250px" value="<?php print($payment->CreditorId);?>" readonly/></td>
            <td>Cara Bayar</td>
            <td><select class="easyui-combobox" id="PaymentMode" name="PaymentMode" style="width: 100px" disabled>
                    <option value="0" <?php print($payment->PaymentMode == 0 ? 'selected="selected"' : '');?>>0 - Tunai</option>
                    <option value="1" <?php print($payment->PaymentMode == 1 ? 'selected="selected"' : '');?>>1 - Transfer</option>
                    <option value="2" <?php print($payment->PaymentMode == 2 ? 'selected="selected"' : '');?>>2 - Cheque/BG</option>
                    <option value="3" <?php print($payment->PaymentMode == 3 ? 'selected="selected"' : '');?>>3 - Slip</option>
                    <option value="4" <?php print($payment->PaymentMode == 4 ? 'selected="selected"' : '');?>>4 - Lain</option>
                </select>
            </td>
            <td>Kas/Bank</td>
            <td><select class="easyui-combobox" id="BankId" name="BankId" style="width: 150px" disabled>
                    <?php
                    foreach ($banks as $bank) {
                        if ($bank->Id == $payment->BankId) {
                            printf('<option value="%d" selected="selected">%s - %s</option>', $bank->Id, $bank->Id, $bank->Name);
                        } else {
                            printf('<option value="%d">%s - %s</option>', $bank->Id, $bank->Id, $bank->Name);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td colspan="3"><b><input type="text" class="f1 easyui-textbox" id="PaymentDescs" name="PaymentDescs" size="80" maxlength="150" value="<?php print($payment->PaymentDescs != null ? $payment->PaymentDescs : '-'); ?>" required/></b></td>
            <td>Status</td>
            <td><select class="easyui-combobox" id="PaymentStatus" name="PaymentStatus" style="width: 100px" disabled>
                    <option value="0" <?php print($payment->PaymentStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($payment->PaymentStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="3" <?php print($payment->PaymentStatus == 3 ? 'selected="selected"' : '');?>>3 - Void</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="7">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th colspan="7">DETAIL PEMBAYARAN HUTANG</th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>No. GRN</th>
                        <th>Tanggal</th>
                        <th>J T P</th>
                        <th>Nilai Hutang</th>
                        <th>Dibayar</th>
                        <th>Sisa</th>
                    </tr>
                    <?php
                    $counter = 0;
                    $tout = 0;
                    $tall = 0;
                    $tbal = 0;
                    $dta = null;
                    $url = null;
                    foreach($payment->Details as $idx => $detail) {
                        $url = $helper->site_url("ap.purchase/view/".$detail->GrnId);
                        $counter++;
                        print("<tr>");
                        printf('<td class="right">%s.</td>', $counter);
                        printf("<td><a href= '%s' target='_blank'>%s</a></td>",$url ,$detail->GrnNo);
                        printf('<td>%s</td>', $detail->GrnDate);
                        printf('<td>%s</td>', $detail->DueDate);
                        printf('<td class="right">%s</td>', number_format($detail->GrnOutstanding,0));
                        printf('<td class="right">%s</td>', number_format($detail->AllocateAmount,0));
                        printf('<td class="right">%s</td>', number_format(($detail->GrnOutstanding - $detail->AllocateAmount),0));
                        print("</tr>");
                        $tall += $detail->AllocateAmount;
                        $tout += $detail->GrnOutstanding;
                        $tbal += ($detail->GrnOutstanding - $detail->AllocateAmount);
                    }
                    ?>
                    <tr>
                        <td colspan="4" class="bold right">Sub Total :</td>
                        <td class="bold right"><?php print(number_format($tout,0,',','.')) ?></td>
                        <td class="bold right"><?php print(number_format($tall,0,',','.')) ?></td>
                        <td class="bold right"><?php print(number_format($tbal,0,',','.')) ?></td>
                    </tr>
                    <tr>
                        <td colspan="7" class="right">
                            <?php
                            if ($acl->CheckUserAccess("ap.payment", "edit")) {
                                printf('<img src="%s" alt="Edit Data" title="Edit Data" id="bEdit" style="cursor: pointer;"/> &nbsp',$bedit);
                            }
                            if ($acl->CheckUserAccess("ap.payment", "add")) {
                                printf('<img src="%s" alt="Data Baru" title="Buat Data Baru" id="bTambah" style="cursor: pointer;"/> &nbsp',$baddnew);
                            }
                            if ($acl->CheckUserAccess("ap.payment", "delete")) {
                                printf('<img src="%s" alt="Hapus Data" title="Hapus Data" id="bHapus" style="cursor: pointer;"/> &nbsp',$bdelete);
                            }
                            if ($acl->CheckUserAccess("ap.payment", "print")) {
                                printf('<img src="%s" alt="Cetak Payment" title="Cetak Payment" id="bCetak" style="cursor: pointer;"/> &nbsp',$bcetak);
                            }
                            printf('<img src="%s" id="bKembali" alt="Daftar Pembayaran" title="Kembali ke daftar penerimaan" style="cursor: pointer;"/>',$bkembali);
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016 - 2020 <a href='http://rekasys.com'>Rekasys Inc</a>
</div>
<!-- </body> -->
</html>
