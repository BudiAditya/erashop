<!DOCTYPE HTML>
<html>
<?php
/** @var $purchase Purchase */
?>
<head>
<title>ERASYS - View Pembelian/Penerimaan Barang</title>
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
        $('#SupplierId').combogrid({
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
            ]]
        });

        $("#bTambah").click(function(){
            if (confirm('Buat GRN baru?')){
                location.href="<?php print($helper->site_url("ap.purchase/add")); ?>";
            }
        });

        $("#bEdit").click(function(){
            if (confirm('Anda yakin akan mengubah GRN ini?')){
                location.href="<?php print($helper->site_url("ap.purchase/edit/").$purchase->Id); ?>";
            }
        });

        $("#bHapus").click(function(){
            if (confirm('Anda yakin akan membatalkan pembelian ini?')){
                location.href="<?php print($helper->site_url("ap.purchase/void/").$purchase->Id); ?>";
            }
        });

        $("#bCetakPdf").click(function(){
            if (confirm('Cetak PDF Bukti Pembelian ini?')){
                window.open("<?php print($helper->site_url("ap.purchase/grn_print/grn/?&id[]=").$purchase->Id); ?>");
            }
        });

        $("#bKembali").click(function(){
            location.href="<?php print($helper->site_url("ap.purchase")); ?>";
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
$bpdf = base_url('public/images/button/').'pdf.png';
?>
<br />
<div id="p" class="easyui-panel" title="View Pembelian/Penerimaan Barang" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td>Cabang</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($purchase->CabangCode != null ? $purchase->CabangCode : $userCabCode); ?>" disabled/>
                <input type="hidden" id="CabangId" name="CabangId" value="<?php print($purchase->CabangId == null ? $userCabId : $purchase->CabangId);?>"/>
            </td>
            <td>Tanggal</td>
            <td><input type="text" size="12" id="GrnDate" name="GrnDate" value="<?php print($purchase->FormatGrnDate(JS_DATE));?>" disabled/></td>
            <td>Diterima</td>
            <td><input type="text" size="12" id="ReceiptDate" name="ReceiptDate" value="<?php print($purchase->FormatReceiptDate(JS_DATE));?>" /></td>
            <td>No. GRN</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="GrnNo" name="GrnNo" value="<?php print($purchase->GrnNo != null ? $purchase->GrnNo : '-'); ?>" readonly/></td>
        </tr>
        <tr>
            <td>Supplier</td>
            <td><input class="easyui-combogrid" id="SupplierId" name="SupplierId" style="width: 250px" value="<?php print($purchase->SupplierId);?>" disabled/></td>
            <td>Salesman</td>
            <td><b><input type="text" class="f1 easyui-textbox" id="SalesName" name="SalesName" size="20" maxlength="50" value="<?php print($purchase->SalesName != null ? $purchase->SalesName : '-'); ?>"/></b></td>
            <td>Status</td>
            <td><select class="easyui-combobox" id="GrnStatus" name="GrnStatus" style="width: 150px" disabled>
                    <option value="0" <?php print($purchase->GrnStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($purchase->GrnStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="2" <?php print($purchase->GrnStatus == 2 ? 'selected="selected"' : '');?>>2 - Closed</option>
                    <option value="3" <?php print($purchase->GrnStatus == 3 ? 'selected="selected"' : '');?>>3 - Void</option>
                </select>
            </td>
            <td>Ex. PO</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="ExPoNo" name="ExPoNo" value="<?php print($purchase->ExPoNo != null ? $purchase->ExPoNo : '-'); ?>"/></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td><b><input type="text" class="f1 easyui-textbox" id="GrnDescs" name="GrnDescs" style="width: 250px" value="<?php print($purchase->GrnDescs != null ? $purchase->GrnDescs : '-'); ?>" readonly/></b></td>
            <td>Gudang</td>
            <td><select class="easyui-combobox" id="GudangId" name="GudangId" style="width: 150px" disabled>
                    <?php
                    foreach ($gudangs as $gudang) {
                        if ($gudang->Id == $purchase->GudangId) {
                            printf('<option value="%d" selected="selected">%s</option>', $gudang->Id, $gudang->Kode);
                        } else {
                            printf('<option value="%d">%s</option>', $gudang->Id, $gudang->Kode);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>Cara Bayar</td>
            <td><select class="easyui-combobox" id="PaymentType" name="PaymentType" disabled>
                    <option value="1" <?php print($purchase->PaymentType == 1 ? 'selected="selected"' : '');?>>Kredit</option>
                    <option value="0" <?php print($purchase->PaymentType == 0 ? 'selected="selected"' : '');?>>Tunai</option>
                </select>
                &nbsp
                Kredit
                <input type="text" class="f1 easyui-textbox" id="CreditTerms" name="CreditTerms" size="2" maxlength="5" value="<?php print($purchase->CreditTerms != null ? $purchase->CreditTerms : 0); ?>" style="text-align: right" disabled/>&nbsphari</td>
        </tr>
        <tr>
            <td colspan="7">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th colspan="11">DETAIL BARANG YANG DIBELI/DITERIMA</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Terima</th>
                        <th>Return</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Disc (%)</th>
                        <th>Diskon</th>
                        <th>Gratis</th>
                        <th>Jumlah</th>
                    </tr>
                    <?php
                    $counter = 0;
                    $total = 0;
                    $dta = null;
                    $dtx = null;
                    foreach($purchase->Details as $idx => $detail) {
                        $counter++;
                        print("<tr>");
                        printf('<td class="right">%s.</td>', $counter);
                        printf('<td>%s</td>', $detail->ItemCode);
                        printf('<td>%s</td>', $detail->ItemDescs);
                        printf('<td class="right">%s</td>', number_format($detail->PurchaseQty,0));
                        printf('<td class="right">%s</td>', number_format($detail->ReturnQty,0));
                        printf('<td>%s</td>', $detail->SatBesar);
                        printf('<td class="right">%s</td>', number_format($detail->Price,0));
                        printf('<td class="right">%s</td>', $detail->DiscFormula);
                        printf('<td class="right">%s</td>', number_format($detail->DiscAmount,0));
                        if($detail->IsFree == 0){
                            print("<td class='center'><input type='checkbox' disabled></td>");
                        }else{
                            print("<td class='center'><input type='checkbox' checked='checked' disabled></td>");
                        }
                        printf('<td class="right">%s</td>', number_format($detail->SubTotal,0));
                        print("<td class='center'>&nbsp</td>");
                        print("</tr>");
                        $total += $detail->SubTotal;
                    }
                    ?>
                    <tr>
                        <td colspan="10" align="right">Sub Total :</td>
                        <td><input type="text" class="right bold" style="width: 150px" id="BaseAmount" name="BaseMount" value="<?php print($purchase->BaseAmount != null ? number_format($purchase->BaseAmount,0) : 0); ?>" readonly/></td>
                        <td class='center'>&nbsp</td>
                    </tr>
                    <tr>
                        <td colspan="10" align="right">Diskon (%) :</td>
                        <td><input type="text" class="right bold" style="width: 30px" id="Disc1Pct" name="Disc1Pct" value="<?php print($purchase->Disc1Pct != null ? number_format($purchase->Disc1Pct,1) : 0); ?>"/>
                            <input type="text" class="right bold" style="width: 110px" id="Disc1Amount" name="Disc1Amount" value="<?php print($purchase->Disc1Amount != null ? number_format($purchase->Disc1Amount,0) : 0); ?>" readonly/></td>
                        <?php if ($acl->CheckUserAccess("ap.purchase", "edit")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Edit Grn" title="Proses edit invoice" id="bEdit" style="cursor: pointer;"/>',$bedit);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="10" align="right">D P P :</td>
                        <td><input type="text" class="right bold" style="width: 150px" id="DppAmount" name="DppAmount" value="<?php print(number_format($purchase->BaseAmount - $purchase->Disc1Amount,0)); ?>" readonly/></td>
                        <?php if ($acl->CheckUserAccess("ap.purchase", "add")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="GRN Baru" title="Buat invoice baru" id="bTambah" style="cursor: pointer;"/>',$baddnew);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="10" align="right">Pajak (%) :</td>
                        <td><input type="text" class="right bold" style="width: 30px" id="TaxPct" name="TaxPct" value="<?php print($purchase->TaxPct != null ? $purchase->TaxPct : 0); ?>"/>
                            <input type="text" class="right bold" style="width: 110px" id="TaxAmount" name="TaxAmount" value="<?php print($purchase->TaxAmount != null ? number_format($purchase->TaxAmount,0) : 0); ?>"/></td>
                        <?php if ($acl->CheckUserAccess("ap.purchase", "delete")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Hapus Grn" title="Proses hapus invoice" id="bHapus" style="cursor: pointer;"/>',$bdelete);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="2" align="right">Biaya Lain :</td>
                        <td colspan="8"><b><input type="text" class="bold" id="OtherCosts" name="OtherCosts" size="60" maxlength="150" value="<?php print($purchase->OtherCosts != null ? $purchase->OtherCosts : '-'); ?>"/></b></td>
                        <td><input type="text" class="right bold" style="width: 150px" id="OtherCostsAmount" name="OtherCostsAmount" value="<?php print($purchase->OtherCostsAmount != null ? number_format($purchase->OtherCostsAmount,0) : 0); ?>"/></td>
                        <?php if ($acl->CheckUserAccess("ap.purchase", "print")) { ?>
                            <td class='center'><?php printf('<img src="%s" id="bCetakPdf" alt="Cetak Bukti Pembelian" title="Proses cetak bukti pembelian" style="cursor: pointer;"/>',$bpdf);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="10" align="right">Grand Total :</td>
                        <td><input type="text" class="right bold" style="width: 150px;" id="TotalAmount" name="TotalAmount" value="<?php print($purchase->TotalAmount != null ? number_format($purchase->TotalAmount,0) : 0); ?>" readonly/></td>
                        <td class='center'><?php printf('<img src="%s" id="bKembali" alt="Daftar Grn" title="Kembali ke daftar invoice" style="cursor: pointer;"/>',$bkembali);?></td>
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
