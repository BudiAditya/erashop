<!DOCTYPE HTML>
<html>
<?php
/** @var $invoice Invoice */ /** @var $sales Karyawan[] */
?>
<head>
<title>ERASYS - View Nota Penjualan (Invoicing)</title>
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

<script type="text/javascript" src="<?php print($helper->path("public/js/qz/dependencies/rsvp-3.1.0.min.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/qz/dependencies/sha-256.min.js")); ?>"></script>
<script type="text/javascript" src="<?php print($helper->path("public/js/qz/qz-tray.js")); ?>"></script>

<style scoped>
    .f1{
        width:200px;
    }
</style>
<script type="text/javascript">
    $( function() {
        $('#CustomerId').combogrid({
            panelWidth:600,
            url: "<?php print($helper->site_url("master.contacts/getjson_contacts/1"));?>",
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
            if (confirm('Buat invoice baru?')){
                location.href="<?php print($helper->site_url("ar.invoice/add/0")); ?>";
            }
        });

        $("#bEdit").click(function(){
            if (confirm('Anda yakin akan mengubah invoice ini?')){
                location.href="<?php print($helper->site_url("ar.invoice/add/").$invoice->Id); ?>";
            }
        });

        $("#bHapus").click(function(){
            if (confirm('Anda yakin akan membatalkan invoice ini?')){
                location.href="<?php print($helper->site_url("ar.invoice/void/").$invoice->Id); ?>";
            }
        });

        $("#bCetak").click(function(){
            var uip = '<?php print($userIpAdd);?>';
            var ptp = <?php print($userCabRpm);?>;
            var printCnt = <?php print($invoice->PrintCount);?>;
            if (printCnt > 0){
                alert('ER - Invoice/Struk sudah pernah di-print!');
            }else {
                if ((uip.substr(0, 3) == '127') || (uip.substr(0, 3) == '::1') && (ptp == 1 || ptp == 4)) {
                    if (confirm('Cetak Struk Invoice ini?')) {
                        $.get('<?php print($helper->site_url("ar.invoice/printdirect/").$invoice->Id."/".$userCabRpm."/".$userCabRpn); ?>', function (e) {
                            alert('OK - Send Data to Printer');
                        });
                    }
                } else {
                    if (confirm('Cetak invoice ini?')) {
                        printDirect()
                    }
                }
            }
        });

        $("#bCetakPdf").click(function(){
            if (confirm('Cetak PDF invoice ini?')){
                window.open("<?php print($helper->site_url("ar.invoice/invoice_print/invoice/?&id[]=").$invoice->Id); ?>");
            }
        });

        $("#bKembali").click(function(){
            location.href="<?php print($helper->site_url("ar.invoice")); ?>";
        });
    });

    function openWindow() {
       thisWindow = window.open('<?php print($helper->site_url("ar.invoice/printdirect/").$invoice->Id); ?>', "_blank", "toolbar=no,scrollbars=no,resizable=no,top=300,left=300,width=300,height=100");
    }

    function closeWindow(){
        thisWindow.close();
    }

    function printDirect() {
        qz.websocket.connect().then(function () {
            //alert("Not Connected!");
        });
        $.get('<?php print($helper->site_url("ar.invoice/printdirect/").$invoice->Id."/".$userCabRpm); ?>', function (ajson, status) {
            var config = qz.configs.create("<?php print($userCabRpn);?>");
            var data = JSON.parse(ajson);
            //var datx = ['\x1B' + '\x40','\x1B' + '\x67','\x1B' + '\x47'];  //cetak double-strike
            //var datx = ['\x1B' + '\x40','\x1B' + '\x67','\x1B' + '\x43' + '\x1E']; //cetak biasa 15 cpi 30 lines
            var datx = ['\x1B' + '\x40', '\x1B' + '\x67']; //cetak biasa 15 cpi
            for (var i = 0; i < data.length; i++) {
                datx.push(data[i] + '\n');
            }
            //datx.push('\x0C'); // form feed
            qz.print(config, datx).catch(function (e) {
                console.error(e);
            });
        });
    }

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
<div id="p" class="easyui-panel" title="View Nota Penjualan" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td>Cabang</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($invoice->CabangCode != null ? $invoice->CabangCode : $userCabCode); ?>" disabled/>
                <input type="hidden" id="CabangId" name="CabangId" value="<?php print($invoice->CabangId == null ? $userCabId : $invoice->CabangId);?>"/>
            </td>
            <td>Tanggal</td>
            <td><input type="text" size="12" id="InvoiceDate" name="InvoiceDate" value="<?php print($invoice->FormatInvoiceDate(JS_DATE));?>" readonly/></td>
            <td>No. Invoice</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="InvoiceNo" name="InvoiceNo" value="<?php print($invoice->InvoiceNo != null ? $invoice->InvoiceNo : '-'); ?>" readonly/></td>
        </tr>
        <tr>
            <td>Customer</td>
            <td><input class="easyui-combogrid" id="CustomerId" name="CustomerId" style="width: 250px" value="<?php print($invoice->CustomerId); ?>" disabled/></td>
            <td>Salesman</td>
            <td><select class="easyui-combobox" id="SalesId" name="SalesId" style="width: 150px" disabled>
                    <option value="">- Pilih Salesman -</option>
                    <?php
                    foreach ($sales as $salesman) {
                        if ($salesman->Id == $invoice->SalesId) {
                            printf('<option value="%d" selected="selected">%s</option>', $salesman->Id, $salesman->Nama);
                        } else {
                            printf('<option value="%d">%s</option>', $salesman->Id, $salesman->Nama);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>Status</td>
            <td><select class="easyui-combobox" id="InvoiceStatus" name="InvoiceStatus" style="width: 150px" disabled>
                    <option value="0" <?php print($invoice->InvoiceStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($invoice->InvoiceStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="2" <?php print($invoice->InvoiceStatus == 2 ? 'selected="selected"' : '');?>>2 - Approved</option>
                    <option value="3" <?php print($invoice->InvoiceStatus == 3 ? 'selected="selected"' : '');?>>3 - Terbayar</option>
                    <option value="4" <?php print($invoice->InvoiceStatus == 3 ? 'selected="selected"' : '');?>>4 - Batal</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td><b><input type="text" class="f1 easyui-textbox" id="InvoiceDescs" name="InvoiceDescs" style="width: 250px" value="<?php print($invoice->InvoiceDescs != null ? $invoice->InvoiceDescs : '-'); ?>" readonly/></b></td>
            <td>Gudang</td>
            <td><select class="easyui-combobox" id="GudangId" name="GudangId" style="width: 150px" disabled>
                    <?php
                    foreach ($gudangs as $gudang) {
                        if ($gudang->Id == $invoice->GudangId) {
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
                    <option value="1" <?php print($invoice->PaymentType == 1 ? 'selected="selected"' : '');?>>Kredit</option>
                    <option value="0" <?php print($invoice->PaymentType == 0 ? 'selected="selected"' : '');?>>Tunai</option>
                </select>
                &nbsp
                Kredit
                <input type="text" class="f1 easyui-textbox" id="CreditTerms" name="CreditTerms" size="2" maxlength="5" value="<?php print($invoice->CreditTerms != null ? $invoice->CreditTerms : 0); ?>" style="text-align: right" readonly/>&nbsphari</td>
        </tr>
        <tr>
            <td colspan="7">
                <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                    <tr>
                        <th colspan="11">DETAIL BARANG</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Notes</th>
                        <th>Qty</th>
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
                    foreach($invoice->Details as $idx => $detail) {
                        $counter++;
                        print("<tr>");
                        printf('<td class="right">%s.</td>', $counter);
                        printf('<td>%s</td>', $detail->ItemCode);
                        printf('<td>%s</td>', $detail->ItemDescs);
                        printf('<td>%s</td>', $detail->ItemNote);
                        printf('<td class="right">%s</td>', number_format($detail->Qty,1));
                        printf('<td>%s</td>', $detail->SatJual);
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
                        <td><input type="text" class="right bold" style="width: 150px" id="BaseAmount" name="BaseMount" value="<?php print($invoice->BaseAmount != null ? number_format($invoice->BaseAmount,0) : 0); ?>" readonly/></td>
                        <?php if ($acl->CheckUserAccess("ar.invoice", "edit")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Edit Invoice" title="Proses edit invoice" id="bEdit" style="cursor: pointer;"/>',$bedit);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="10" align="right">Diskon (%) :</td>
                        <td><input type="text" class="right bold" style="width: 30px" id="Disc1Pct" name="Disc1Pct" value="<?php print($invoice->Disc1Pct != null ? number_format($invoice->Disc1Pct,1) : 0); ?>"/>
                            <input type="text" class="right bold" style="width: 110px" id="Disc1Amount" name="Disc1Amount" value="<?php print($invoice->Disc1Amount != null ? number_format($invoice->Disc1Amount,0) : 0); ?>" readonly/></td>
                        <?php if ($acl->CheckUserAccess("ar.invoice", "add")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Invoice Baru" title="Buat invoice baru" id="bTambah" style="cursor: pointer;"/>',$baddnew);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="10" align="right">D P P :</td>
                        <td><input type="text" class="right bold" style="width: 150px" id="DppAmount" name="DppAmount" value="<?php print(number_format($invoice->BaseAmount - $invoice->Disc1Amount,0)); ?>" readonly/></td>
                        <?php if ($acl->CheckUserAccess("ar.invoice", "delete")) { ?>
                            <td class='center'><?php printf('<img src="%s" alt="Hapus Invoice" title="Proses hapus invoice" id="bHapus" style="cursor: pointer;"/>',$bdelete);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="10" align="right">Pajak (%) :</td>
                        <td><input type="text" class="right bold" style="width: 30px" id="TaxPct" name="TaxPct" value="<?php print($invoice->TaxPct != null ? $invoice->TaxPct : 0); ?>"/>
                            <input type="text" class="right bold" style="width: 110px" id="TaxAmount" name="TaxAmount" value="<?php print($invoice->TaxAmount != null ? number_format($invoice->TaxAmount,0) : 0); ?>"/></td>
                        <?php if ($acl->CheckUserAccess("ar.invoice", "print")) { ?>
                            <td class='center'><?php printf('<img src="%s" id="bCetakPdf" alt="Cetak PDF Invoice" title="Proses cetak PDF invoice" style="cursor: pointer;"/>',$bpdf);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="2" align="right">Biaya Lain :</td>
                        <td colspan="8"><b><input type="text" class="bold" id="OtherCosts" name="OtherCosts" size="60" maxlength="150" value="<?php print($invoice->OtherCosts != null ? $invoice->OtherCosts : '-'); ?>"/></b></td>
                        <td><input type="text" class="right bold" style="width: 150px" id="OtherCostsAmount" name="OtherCostsAmount" value="<?php print($invoice->OtherCostsAmount != null ? number_format($invoice->OtherCostsAmount,0) : 0); ?>"/></td>
                        <?php if ($acl->CheckUserAccess("ar.invoice", "print")) { ?>
                            <td class='center'><?php printf('<img src="%s" id="bCetak" alt="Cetak Invoice" title="Proses cetak invoice" style="cursor: pointer;"/>',$bcetak);?></td>
                        <?php }else{ ?>
                            <td>&nbsp</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="10" align="right">Grand Total :</td>
                        <td><input type="text" class="right bold" style="width: 150px;" id="TotalAmount" name="TotalAmount" value="<?php print($invoice->TotalAmount != null ? number_format($invoice->TotalAmount,0) : 0); ?>" readonly/></td>
                        <td class='center'><?php printf('<img src="%s" id="bKembali" alt="Daftar Invoice" title="Kembali ke daftar invoice" style="cursor: pointer;"/>',$bkembali);?></td>
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
