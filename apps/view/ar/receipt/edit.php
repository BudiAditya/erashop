<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $receipt Receipt */ /** @var $banks Bank[] */
?>
<head>
    <title>ERASYS - Entry Penerimaan Piutang</title>
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
            //var addetail = ["aInvoiceNo", "aQty","aInvoiceOutstanding", "aAllocateAmount", "aDiscAmount", "aBalanceAmount"];
            //BatchFocusRegister(addetail);
            //var addmaster = ["CabangId", "ReceiptDate","DebtorId", "SalesId", "ReceiptDescs", "PaymentType","CreditTerms","BaseAmount","Disc1Pct","Disc1Amount","TaxPct","TaxAmount","OtherCosts","OtherCostsAmount","TotalAmount","bUpdate","bKembali"];
            //BatchFocusRegister(addmaster);
            $("#ReceiptDate").customDatePicker({ showOn: "focus" });

            $('#DebtorId').combogrid({
                panelWidth:600,
                url: "<?php print($helper->site_url("master.contacts/getjson_contacts/1/".$userCompId));?>",
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

            // getoutstandinginvoice_plain($cabangId,$customerId,$invoiceNo){
            // $ret = 'OK|'.$invoice->Id.'|'.$invoice->InvoiceDate.'|'.$invoice->DueDate.'|'.$invoice->BalanceAmount;
            $("#aInvoiceNo").change(function(e){
                var ivn = $("#aInvoiceNo").val();
                var dbi = "<?php print($receipt->DebtorId);?>";
                var cbi = "<?php print($receipt->CabangId);?>";
                var url = "<?php print($helper->site_url("ar.receipt/getoutstandinginvoices_plain/"));?>"+cbi+"/"+dbi+"/"+ivn;
                if (ivn != ''){
                    $.get(url, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#aInvoiceId').val(dtx[1]);
                                $('#aInvoiceSearch').val(dtx[1]);
                                $('#aInvoiceDate').val(dtx[2]);
                                $('#aDueDate').val(dtx[3]);
                                $('#aInvoiceOutStanding').val(Number(dtx[4]));
                                if (Number($('#BalanceAmount').val()) >= $('#aInvoiceOutStanding').val()) {
                                    $('#aAllocateAmount').val(Number(dtx[4]));
                                    $('#aBalanceAmount').val(0);
                                }else{
                                    $('#aAllocateAmount').val($('#BalanceAmount').val());
                                    $('#aBalanceAmount').val(Number(dtx[4])-$('#BalanceAmount').val());
                                }
                            }else{
                                alert('Data Invoice Piutang tidak ditemukan!');
                            }
                        }else{
                            alert('Data Invoice Piutang tidak ditemukan!');
                        }
                    });
                }
            });

            $('#aInvoiceSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("ar.receipt/getoutstandinginvoices_json/".$receipt->CabangId."/".$receipt->DebtorId));?>",
                idField:'id',
                textField:'id',
                mode:'get',
                fitColumns:true,
                columns:[[
                    {field:'invoice_no',title:'No. Invoice',width:50},
                    {field:'invoice_date',title:'Tanggal',width:50},
                    {field:'due_date',title:'J T P',width:50},
                    {field:'balance_amount',title:'Outstanding',width:50,align:'right'}
                ]],
                onSelect: function(index,row){
                    var id = row.id;
                    console.log(id);
                    var ivn = row.invoice_no;
                    console.log(ivn);
                    var ivd = row.invoice_date;
                    console.log(ivd);
                    var due = row.due_date;
                    console.log(due);
                    var bal = row.balance_amount;
                    console.log(bal);
                    $('#aInvoiceId').val(id);
                    $('#aInvoiceNo').val(ivn);
                    $('#aInvoiceDate').val(ivd);
                    $('#aDueDate').val(due);
                    $('#aInvoiceOutStanding').val(bal);
                    if (Number($('#BalanceAmount').val()) >= $('#aInvoiceOutStanding').val()) {
                        $('#aAllocateAmount').val(bal);
                        $('#aBalanceAmount').val(0);
                    }else{
                        $('#aAllocateAmount').val($('#BalanceAmount').val());
                        $('#aBalanceAmount').val(bal - $('#aAllocateAmount').val());
                    }
                }
            });

            $("#aAllocateAmount").change(function(e){
                var out = Number($('#aInvoiceOutStanding').val());
                var alo = Number($("#aAllocateAmount").val());
                $('#aBalanceAmount').val(out-alo);
            });

            $("#bAdDetail").click(function(e){
                if ($('#BalanceAmount').val() > 0) {
                    $('#aInvoiceId').val('');
                    $('#aInvoiceNo').val('');
                    $('#aInvoiceDate').val('');
                    $('#aDueDate').val('');
                    $('#aInvoiceOutStanding').val(0);
                    $('#aAllocateAmount').val(0);
                    $('#aBalanceAmount').val(0);
                    newItem();
                }else{
                    alert('Pembayaran sudah dialokasikan semuanya!');
                }
            });

            $("#bTambah").click(function(){
                if (confirm('Buat Receipt baru?')){
                    location.href="<?php print($helper->site_url("ar.receipt/add")); ?>";
                }
            });

            $("#bHapus").click(function(){
                if (confirm('Anda yakin akan membatalkan data receipt ini?')){
                    location.href="<?php print($helper->site_url("ar.receipt/void/").$receipt->Id); ?>";
                }
            });

            $("#bCetak").click(function(){
                if (confirm('Cetak receipt ini?')){
                    //location.href="<?php //print($helper->site_url("ar.receipt/print_pdf/").$receipt->Id); ?>";
                    alert('Proses cetak belum siap..');
                }
            });

            $("#bKembali").click(function(){
                location.href="<?php print($helper->site_url("ar.receipt")); ?>";
            });
        });

        function fdeldetail(dta){
            var dtz = dta.replace(/\"/g,"\\\"")
            var dtx = dtz.split('|');
            var id = dtx[0];
            var ivn = dtx[1];
            var bal = dtx[2];
            var urx = '<?php print($helper->site_url("ar.receipt/delete_detail/"));?>'+id;
            if (confirm('Hapus Detail penerimaan \nInvoice No: '+ivn+ '\nNilai: '+bal+' ?')) {
                $.get(urx, function(data){
                    alert(data);
                    location.reload();
                });
            }
        }

        function newItem(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Detail Penerimaan');
            //$('#fm').form('clear');
            url= "<?php print($helper->site_url("ar.receipt/add_detail/".$receipt->Id));?>";
            $('#aInvoiceNo').focus();
        }

        function saveDetail(){
            var aivi = Number($('#aInvoiceId').val());
            var aalo = Number($('#aAllocateAmount').val());
            if (aivi > 0 && aalo > 0){
                $('#fm').form('submit',{
                    url: url,
                    onSubmit: function(){
                        return $(this).form('validate');
                    },
                    success: function(result){
                        var result = eval('('+result+')');
                        if (result.errorMsg){
                            $.messager.show({
                                title: 'Error',
                                msg: result.errorMsg
                            });
                        } else {
                            location.reload();
                            $('#dlg').dialog('close');		// close the dialog
                        }
                    }
                });
            }else{
                alert('Maaf, Data input tidak valid!');
            }
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
?>
<br />
<div id="p" class="easyui-panel" title="Entry Penerimaan Piutang" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <form id="frmMaster" action="<?php print($helper->site_url("ar.receipt/edit/".$receipt->Id)); ?>" method="post">
        <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
            <tr>
                <td>Cabang</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($receipt->CabangCode != null ? $receipt->CabangCode : $userCabCode); ?>" disabled/>
                    <input type="hidden" id="CabangId" name="CabangId" value="<?php print($receipt->CabangId == null ? $userCabId : $receipt->CabangId);?>"/>
                </td>
                <td>Tanggal</td>
                <td><input type="text" size="12" id="ReceiptDate" name="ReceiptDate" value="<?php print($receipt->FormatReceiptDate(JS_DATE));?>" readonly/></td>
                <td>No. Receipt</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="ReceiptNo" name="ReceiptNo" value="<?php print($receipt->ReceiptNo != null ? $receipt->ReceiptNo : '-'); ?>" readonly/></td>
            </tr>
            <tr>
                <td>Customer</td>
                <td><input class="easyui-combogrid" id="DebtorId" name="DebtorId" style="width: 250px" value="<?php print($receipt->DebtorId);?>"/></td>
                <td>Cara Bayar</td>
                <td><select class="easyui-combobox" id="WarkatTypeId" name="WarkatTypeId" style="width: 100px">
                        <?php
                        foreach ($warkattypes as $wti) {
                            if ($wti->Id == $receipt->WarkatTypeId) {
                                printf('<option value="%d" selected="selected">%s</option>', $wti->Id, $wti->Type);
                            } else {
                                printf('<option value="%d">%s</option>', $wti->Id, $wti->Type);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Bank</td>
                <td><select class="easyui-combobox" id="WarkatBankId" name="WarkatBankId" style="width: 150px">
                        <?php
                        foreach ($banks as $bank) {
                            if ($bank->Id == $receipt->WarkatBankId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $bank->Id, $bank->Id, $bank->Name);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $bank->Id, $bank->Id, $bank->Name);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>No. Retur</td>
                <td><input class="easyui-combogrid" style="width: 150px" id="ReturnNo" name="ReturnNo" value="<?php print($receipt->ReturnNo); ?>"/></td>
            </tr>
            <tr>
                <td>No. Warkat</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="WarkatNo" name="WarkatNo" value="<?php print($receipt->WarkatNo);?>"/></td>
                <td>Tgl. Warkat</td>
                <td><input type="text" size="12" id="WarkatDate" name="WarkatDate" value="<?php print($receipt->FormatWarkatDate(JS_DATE));?>"/></td>
                <td>Jumlah</td>
                <td><input type="text" class="right bold" style="width: 120px" id="ReceiptAmount" name="ReceiptAmount" value="<?php print($receipt->ReceiptAmount != null ? $receipt->ReceiptAmount : 0); ?>"/></td>
                <td>Alokasi</td>
                <td><input type="text" class="right bold" style="width: 120px" id="AllocateAmount" name="AllocateAmount" value="<?php print($receipt->AllocateAmount != null ? $receipt->AllocateAmount : 0); ?>" readonly/></td>
                <td>Sisa</td>
                <td><input type="text" class="right bold" style="width: 120px" id="BalanceAmount" name="BalanceAmount" value="<?php print($receipt->BalanceAmount != null ? $receipt->BalanceAmount : 0); ?>" readonly/></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td colspan="3"><b><input type="text" class="f1 easyui-textbox" id="ReceiptDescs" name="ReceiptDescs" size="80" maxlength="150" value="<?php print($receipt->ReceiptDescs != null ? $receipt->ReceiptDescs : '-'); ?>" required/></b></td>
                <td>Status</td>
                <td><select class="easyui-combobox" id="ReceiptStatus" name="ReceiptStatus" style="width: 100px">
                        <option value="0" <?php print($receipt->ReceiptStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                        <option value="1" <?php print($receipt->ReceiptStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                        <option value="2" <?php print($receipt->ReceiptStatus == 2 ? 'selected="selected"' : '');?>>2 - Approved</option>
                        <option value="3" <?php print($receipt->ReceiptStatus == 3 ? 'selected="selected"' : '');?>>3 - Batal</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                        <tr>
                            <th colspan="7">DETAIL PENERIMAAN PIUTANG</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>No.</th>
                            <th>No. Invoice</th>
                            <th>Tanggal</th>
                            <th>J T P</th>
                            <th>Nilai Piutang</th>
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
                        foreach($receipt->Details as $idx => $detail) {
                            $url = $helper->site_url("ar.invoice/view/".$detail->InvoiceId);
                            $counter++;
                            print("<tr>");
                            printf('<td class="right">%s.</td>', $counter);
                            printf("<td><a href= '%s' target='_blank'>%s</a></td>",$url ,$detail->InvoiceNo);
                            printf('<td>%s</td>', $detail->InvoiceDate);
                            printf('<td>%s</td>', $detail->DueDate);
                            printf('<td class="right">%s</td>', number_format($detail->InvoiceOutstanding,0));
                            printf('<td class="right">%s</td>', number_format($detail->AllocateAmount,0));
                            printf('<td class="right">%s</td>', number_format(($detail->InvoiceOutstanding - $detail->AllocateAmount),0));
                            print("<td class='center'>");
                            $dta = addslashes($detail->Id.'|'.$detail->InvoiceNo.'|'.$detail->InvoiceOutstanding);
                            printf('&nbsp<img src="%s" alt="Hapus Detail" title="Hapus Detail" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bclose,"'".$dta."'");
                            print("</td>");
                            print("</tr>");
                            $tall += $detail->AllocateAmount;
                            $tout += $detail->InvoiceOutstanding;
                            $tbal += ($detail->InvoiceOutstanding - $detail->AllocateAmount);
                        }
                        ?>
                        <tr>
                            <td colspan="4" class="bold right">Sub Total :</td>
                            <td class="bold right"><?php print(number_format($tout,0,',','.')) ?></td>
                            <td class="bold right"><?php print(number_format($tall,0,',','.')) ?></td>
                            <td class="bold right"><?php print(number_format($tbal,0,',','.')) ?></td>
                            <td class='center'><?php printf('<img src="%s" alt="Tambah Detail" title="Tambah Detail" id="bAdDetail" style="cursor: pointer;"/>',$badd);?></td>
                        </tr>
                        <tr>
                            <td colspan="8" class="right">
                                <?php
                                if ($acl->CheckUserAccess("ar.receipt", "edit")) {
                                    printf('<img src="%s" alt="Simpan Data" title="Simpan data master" id="bUpdate" style="cursor: pointer;"/> &nbsp',$bsubmit);
                                }
                                if ($acl->CheckUserAccess("ar.receipt", "add")) {
                                    printf('<img src="%s" alt="Data Baru" title="Buat Data Baru" id="bTambah" style="cursor: pointer;"/> &nbsp',$baddnew);
                                }
                                if ($acl->CheckUserAccess("ar.receipt", "delete")) {
                                    printf('<img src="%s" alt="Hapus Data" title="Hapus Data" id="bHapus" style="cursor: pointer;"/> &nbsp',$bdelete);
                                }
                                if ($acl->CheckUserAccess("ar.receipt", "print")) {
                                    printf('<img src="%s" alt="Cetak Receipt" title="Cetak Receipt" id="bCetak" style="cursor: pointer;"/> &nbsp',$bcetak);
                                }
                                printf('<img src="%s" id="bKembali" alt="Daftar Penerimaan" title="Kembali ke daftar penerimaan" style="cursor: pointer;"/>',$bkembali);
                                ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016  CV. Erasystem Infotama
</div>
<!-- Form Add Receipt Detail -->
<div id="dlg" class="easyui-dialog" style="width:750px;height:150px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" style="font-size: 12px;font-family: tahoma">
            <tr>
                <th>No.Invoice</th>
                <th>Tgl.Invoice</th>
                <th>J T P</th>
                <th>Outstanding</th>
                <th>Dibayar</th>
                <th>Sisa</th>
            </tr>
            <tr>
                <td>
                    <input type="text" id="aInvoiceNo" name="aInvoiceNo" size="15" value="" required/>
                    <input id="aInvoiceSearch" name="aInvoiceSearch" style="width: 20px"/>
                    <input type="hidden" id="aInvoiceId" name="aInvoiceId" value="0"/>
                </td>
                <td>
                    <input type="text" id="aInvoiceDate" name="aInvoiceDate" size="10" value="" disabled/>
                </td>
                <td>
                    <input type="text" id="aDueDate" name="aDueDate" size="10" value="" disabled/>
                </td>
                <td>
                    <input class="right" type="text" id="aInvoiceOutStanding" name="aInvoiceOutStanding" size="15" value="0" readonly/>
                </td>
                <td>
                    <input class="right" type="text" id="aAllocateAmount" name="aAllocateAmount" size="15" value="0"/>
                </td>
                <td>
                    <input class="right" type="text" id="aBalanceAmount" name="aBalanceAmount" size="15" value="0" readonly/>
                </td>
            </tr>
        </table>
    </form>
    <br>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDetail()" style="width:90px">Proses</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
</body>
</html>
