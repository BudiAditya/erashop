<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $payment Payment */ /** @var $banks Bank[] */
?>
<head>
    <title>ERASYS - Entry Pembayaran Hutang</title>
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
            //var addetail = ["aGrnNo", "aQty","aGrnOutstanding", "aAllocateAmount", "aDiscAmount", "aBalanceAmount"];
            //BatchFocusRegister(addetail);
            //var addmaster = ["CabangId", "PaymentDate","CreditorId", "SalesId", "PaymentDescs", "PaymentType","CreditTerms","BaseAmount","Disc1Pct","Disc1Amount","TaxPct","TaxAmount","OtherCosts","OtherCostsAmount","TotalAmount","bUpdate","bKembali"];
            //BatchFocusRegister(addmaster);
            $("#PaymentDate").customDatePicker({ showOn: "focus" });

            $('#CreditorId').combogrid({
                panelWidth:600,
                url: "<?php print($helper->site_url("master.contacts/getjson_contacts/2/".$userCompId));?>",
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

            // getoutstandinggrn_plain($cabangId,$customerId,$grnNo){
            // $ret = 'OK|'.$grn->Id.'|'.$grn->GrnDate.'|'.$grn->DueDate.'|'.$grn->BalanceAmount;
            $("#aGrnNo").change(function(e){
                var gno = $("#aGrnNo").val();
                var cdi = "<?php print($payment->CreditorId);?>";
                var cbi = "<?php print($payment->CabangId);?>";
                var url = "<?php print($helper->site_url("ap.payment/getoutstandinggrns_plain/"));?>"+cbi+"/"+cdi+"/"+gno;
                if (gno != ''){
                    $.get(url, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#aGrnId').val(dtx[1]);
                                $('#aGrnSearch').val(dtx[1]);
                                $('#aGrnDate').val(dtx[2]);
                                $('#aDueDate').val(dtx[3]);
                                $('#aGrnOutStanding').val(Number(dtx[4]));
                                $('#aAllocateAmount').val(Number(dtx[4]));
                                $('#aBalanceAmount').val(0);
                            }else{
                                alert('Data GRN Hutang tidak ditemukan atau sudah lunas!');
                            }
                        }else{
                            alert('Data GRN Hutang tidak ditemukan!');
                        }
                    });
                }
            });

            $('#aGrnSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("ap.payment/getoutstandinggrns_json/".$payment->CabangId."/".$payment->CreditorId));?>",
                idField:'id',
                textField:'id',
                mode:'get',
                fitColumns:true,
                columns:[[
                    {field:'grn_no',title:'No. GRN',width:50},
                    {field:'grn_date',title:'Tanggal',width:50},
                    {field:'due_date',title:'J T P',width:50},
                    {field:'balance_amount',title:'Outstanding',width:50,align:'right'}
                ]],
                onSelect: function(index,row){
                    var id = row.id;
                    console.log(id);
                    var gno = row.grn_no;
                    console.log(gno);
                    var ivd = row.grn_date;
                    console.log(ivd);
                    var due = row.due_date;
                    console.log(due);
                    var bal = row.balance_amount;
                    console.log(bal);
                    $('#aGrnId').val(id);
                    $('#aGrnNo').val(gno);
                    $('#aGrnDate').val(ivd);
                    $('#aDueDate').val(due);
                    $('#aGrnOutStanding').val(bal);
                    $('#aAllocateAmount').val(bal);
                    $('#aBalanceAmount').val(0);
                }
            });

            $("#aAllocateAmount").change(function(e){
                var out = Number($('#aGrnOutStanding').val());
                var alo = Number($("#aAllocateAmount").val());
                $('#aBalanceAmount').val(out-alo);
            });

            $("#bAdDetail").click(function(e){
                $('#aGrnId').val('');
                $('#aGrnNo').val('');
                $('#aGrnDate').val('');
                $('#aDueDate').val('');
                $('#aGrnOutStanding').val(0);
                $('#aAllocateAmount').val(0);
                $('#aBalanceAmount').val(0);
                newItem();
            });

            $("#bTambah").click(function(){
                if (confirm('Buat Payment baru?')){
                    location.href="<?php print($helper->site_url("ap.payment/add")); ?>";
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

        function fdeldetail(dta){
            var dtz = dta.replace(/\"/g,"\\\"")
            var dtx = dtz.split('|');
            var id = dtx[0];
            var gno = dtx[1];
            var bal = dtx[2];
            var urx = '<?php print($helper->site_url("ap.payment/delete_detail/"));?>'+id;
            if (confirm('Hapus Detail Pembayaran \nGRN No: '+gno+ '\nNilai: '+bal+' ?')) {
                $.get(urx, function(data){
                    alert(data);
                    location.reload();
                });
            }
        }

        function newItem(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Detail Pembayaran');
            //$('#fm').form('clear');
            url= "<?php print($helper->site_url("ap.payment/add_detail/".$payment->Id));?>";
            $('#aGrnNo').focus();
        }

        function saveDetail(){
            var aivi = Number($('#aGrnId').val());
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
<div id="p" class="easyui-panel" title="Entry Pembayaran Hutang" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <form id="frmMaster" action="<?php print($helper->site_url("ap.payment/edit/".$payment->Id)); ?>" method="post">
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
                <td><select class="easyui-combobox" id="PaymentMode" name="PaymentMode" style="width: 100px">
                        <option value="0" <?php print($payment->PaymentMode == 0 ? 'selected="selected"' : '');?>>0 - Tunai</option>
                        <option value="1" <?php print($payment->PaymentMode == 1 ? 'selected="selected"' : '');?>>1 - Transfer</option>
                        <option value="2" <?php print($payment->PaymentMode == 2 ? 'selected="selected"' : '');?>>2 - Cheque/BG</option>
                        <option value="3" <?php print($payment->PaymentMode == 3 ? 'selected="selected"' : '');?>>3 - Slip</option>
                        <option value="4" <?php print($payment->PaymentMode == 4 ? 'selected="selected"' : '');?>>4 - Lain</option>
                    </select>
                </td>
                <td>Kas/Bank</td>
                <td><select class="easyui-combobox" id="BankId" name="BankId" style="width: 150px">
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
                <td><select class="easyui-combobox" id="PaymentStatus" name="PaymentStatus" style="width: 100px">
                        <option value="0" <?php print($payment->PaymentStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                        <option value="1" <?php print($payment->PaymentStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                        <option value="2" <?php print($payment->PaymentStatus == 2 ? 'selected="selected"' : '');?>>2 - Batal</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                        <tr>
                            <th colspan="7">DETAIL PEMBAYARAN HUTANG</th>
                            <th rowspan="2">Action</th>
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
                            print("<td class='center'>");
                            $dta = addslashes($detail->Id.'|'.$detail->GrnNo.'|'.$detail->GrnOutstanding);
                            printf('&nbsp<img src="%s" alt="Hapus Detail" title="Hapus Detail" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bclose,"'".$dta."'");
                            print("</td>");
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
                            <td class='center'><?php printf('<img src="%s" alt="Tambah Detail" title="Tambah Detail" id="bAdDetail" style="cursor: pointer;"/>',$badd);?></td>
                        </tr>
                        <tr>
                            <td colspan="8" class="right">
                                <?php
                                if ($acl->CheckUserAccess("ap.payment", "edit")) {
                                    printf('<img src="%s" alt="Simpan Data" title="Simpan data master" id="bUpdate" style="cursor: pointer;"/> &nbsp',$bsubmit);
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
    </form>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016  CV. Erasystem Infotama
</div>
<!-- Form Add Payment Detail -->
<div id="dlg" class="easyui-dialog" style="width:750px;height:150px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" style="font-size: 12px;font-family: tahoma">
            <tr>
                <th>No. GRN</th>
                <th>Tanggal</th>
                <th>J T P</th>
                <th>Outstanding</th>
                <th>Dibayar</th>
                <th>Sisa</th>
            </tr>
            <tr>
                <td>
                    <input type="text" id="aGrnNo" name="aGrnNo" size="15" value="" required/>
                    <input id="aGrnSearch" name="aGrnSearch" style="width: 20px"/>
                    <input type="hidden" id="aGrnId" name="aGrnId" value="0"/>
                </td>
                <td>
                    <input type="text" id="aGrnDate" name="aGrnDate" size="10" value="" disabled/>
                </td>
                <td>
                    <input type="text" id="aDueDate" name="aDueDate" size="10" value="" disabled/>
                </td>
                <td>
                    <input class="right" type="text" id="aGrnOutStanding" name="aGrnOutStanding" size="15" value="0" readonly/>
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
