<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $order Order */
?>
<head>
    <title>ERASYS - Entry Order Pembelian (PO)</title>
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
            var addetail = ["aItemCode", "aQty","aPrice", "aDiscFormula", "aDiscAmount", "aSubTotal"];
            BatchFocusRegister(addetail);
            var addmaster = ["CabangId", "PoDate","RequestDate","SupplierId", "SalesName", "PoDescs", "PaymentType","CreditTerms","bUpdate", "bKembali"];
            BatchFocusRegister(addmaster);
            $("#PoDate").customDatePicker({ showOn: "focus" });
            $("#RequestDate").customDatePicker({ showOn: "focus" });

            $('#SupplierId').combogrid({
                panelWidth:600,
                url: "<?php print($helper->site_url("master.contacts/getjson_contacts"));?>",
                idField:'id',
                textField:'contact_name',
                mode:'remote',
                fitColumns:true,
                columns:[[
                    {field:'contact_code',title:'Kode',width:30},
                    {field:'contact_name',title:'Nama Supplier',width:100},
                    {field:'address',title:'Alamat',width:100},
                    {field:'city',title:'Kota',width:60}
                ]]
            });

            $('#aItemSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("master.setprice/getitemprices_json"));?>",
                idField:'item_id',
                textField:'item_id',
                mode:'get',
                fitColumns:true,
                columns:[[
                    {field:'item_code',title:'Kode',width:50},
                    {field:'item_name',title:'Nama Barang',width:150},
                    {field:'sat_besar',title:'Satuan',width:40},
                    {field:'hrg_beli',title:'Harga',width:40,align:'right'}
                ]],
                onSelect: function(index,row){
                    var bid = row.item_id;
                    console.log(bid);
                    var bkode = row.item_code;
                    console.log(bkode);
                    var bnama = row.item_name;
                    console.log(bnama);
                    var satuan = row.sat_besar;
                    console.log(satuan);
                    var harga = row.hrg_beli;
                    console.log(harga);
                    $('#aItemId').val(bid);
                    $('#aItemCode').val(bkode);
                    $('#aItemDescs').val(bnama);
                    $('#aSatuan').val(satuan);
                    $('#aPrice').val(harga);
                    $('#aDiscFormula').val(0);
                    $('#aDiscAmount').val(0);
                    $('#aQty').val(1);
                    hitDetail();
                    $('#aQty').focus();
                }
            });

            $("#bAdDetail").click(function(e){
                $('#aItemId').val('');
                $('#aItemCode').val('');
                $('#aItemDescs').val('');
                $('#aSatuan').val('');
                $('#aPrice').val(0);
                $('#aQty').val(0);
                $('#aDiscFormula').val('0');
                $('#aDiscAmount').val(0);
                $('#aSubTotal').val(0);
                newItem();
            });

            $("#aItemCode").change(function(e){
                //$ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$hrg_beli;
                var itc = $("#aItemCode").val();
                var url = "<?php print($helper->site_url("master.setprice/getitemprices_plain/"));?>"+itc;
                if (itc != ''){
                    $.get(url, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#aItemId').val(dtx[1]);
                                $('#aItemDescs').val(dtx[2]);
                                $('#aSatuan').val(dtx[3]);
                                $('#aPrice').val(dtx[4]);
                                $('#aDiscFormula').val(0);
                                $('#aDiscAmount').val(0);
                                $('#aQty').val(1);
                                hitDetail();
                                $('#aQty').focus();
                            }
                        }else{
                            alert('Data Harga Barang ini tidak ditemukan!');
                        }
                    });
                }
            });

            $("#aQty").change(function(e){
                var stk = Number($('#aQtyStock').val());
                var qty = $('#aQty').val();
                hitDetail();
            });

            $("#aPrice").change(function(e){
                hitDetail();
            });

            $("#aDiscFormula").change(function(e){
                hitDetail();
            });

            $("#aDiscAmount").change(function(e){
                var subTotal = (Number($("#aQty").val()) * Number($("#aPrice").val()));
                var discAmount = Number($('#aDiscAmount').val());
                var totalDetail = subTotal - discAmount;
                $('#aSubTotal').val(totalDetail);
            });

            $("#Disc1Pct").change(function(e){
                hitMaster();
            });

            $("#TaxPct").change(function(e){
                hitMaster();
            });

            $("#OtherCostsAmount").change(function(e){
                hitMaster();
            });

            $("#bUpdate").click(function(){
                if (confirm('Apakah data input sudah benar?')){
                    $('#frmMaster').submit();
                }
            });

            $("#bTambah").click(function(){
                if (confirm('Buat order baru?')){
                    location.href="<?php print($helper->site_url("ap.order/add")); ?>";
                }
            });

            $("#bHapus").click(function(){
                if (confirm('Anda yakin akam menghapus order ini?')){
                    location.href="<?php print($helper->site_url("ap.order/delete/").$order->Id); ?>";
                }
            });

            $("#bCetak").click(function(){
                if (confirm('Cetak order ini?')){
                    alert('Proses cetak..');
                }
            });

            $("#bKembali").click(function(){
                location.href="<?php print($helper->site_url("ap.order")); ?>";
            });
        });

        function hitDetail(){
            var subTotal = (Number($("#aQty").val()) * Number($("#aPrice").val()));
            var discAmount = Math.round(Number($("#aDiscFormula").val())/100 * subTotal);
            var totalDetail = subTotal - discAmount;
            $('#aDiscAmount').val(discAmount);
            $('#aSubTotal').val(totalDetail);
        }

        function hitMaster(){
            var bam = Number($("#BaseAmount").val().replace(/,/g,""));
            var dpc = Number($("#Disc1Pct").val().replace(/,/g,""));
            var tpc = Number($("#TaxPct").val().replace(/,/g,""));
            var oca = Number($("#OtherCostsAmount").val().replace(/,/g,""));
            var dam = 0;
            var tam = 0;
            var dpp = 0;
            if (bam > 0 && dpc > 0 ){
                dam = Math.round(bam * (dpc/100),0);
                $("#Disc1Amount").val(dam);
            }else{
                $("#Disc1Amount").val(0);
            }
            dpp = bam - dam;
            $("#DppAmount").val(dpp);
            if (dpp > 0 && tpc > 0 ){
                tam = Math.round(dpp * (tpc/100),0);
                $("#TaxAmount").val(tam);
            }else{
                $("#TaxAmount").val(0);
            }
            $("#TotalAmount").val(dpp+tam+oca);
        }
        function fdeldetail(dta){
            var dtz = dta.replace(/\"/g,"\\\"")
            var dtx = dtz.split('|');
            var id = dtx[0];
            var kode = dtx[1];
            var barang = dtx[2];
            var urx = '<?php print($helper->site_url("ap.order/delete_detail/"));?>'+id;
            if (confirm('Hapus Data Detail Barang \nKode: '+kode+ '\nNama: '+barang+' ?')) {
                $.get(urx, function(data){
                    //alert(data);
                    location.reload();
                });
            }
        }

        function feditdetail(dta){
            //$dtx = $detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs).'|'.$detail->ItemId.'|'.$detail->Qty.'|'.$detail->SatBesap.'|'.$detail->Price.'|'.$detail->DiscFormula;
            var dtx = dta.split('|');
            $('#aId').val(dtx[0]);
            $('#aItemId').val(dtx[3]);
            $('#aItemCode').val(dtx[1]);
            $('#aItemDescs').val(dtx[2]);
            $('#aSatuan').val(dtx[6]);
            $('#aPrice').val(dtx[7]);
            $('#aQty').val(dtx[4]);
            $('#aDiscFormula').val(dtx[8]);
            hitDetail();
            $('#dlg').dialog('open').dialog('setTitle','Edit Detail Barang yang dipesan');
            url= "<?php print($helper->site_url("ap.order/edit_detail/".$order->Id));?>";
        }

        function newItem(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Detail Barang yang dipesan');
            $('#fm').form('clear');
            url= "<?php print($helper->site_url("ap.order/add_detail/".$order->Id));?>";
            $('#aItemCode').focus();
        }

        function saveDetail(){
            var aitd = Number($('#aItemId').val());
            var aqty = Number($('#aQty').val());
            var astt = Number($('#aSubTotal').val());
            if (aitd > 0 && aqty > 0 && astt > 0){
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
                alert('Data tidak valid!');
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
<div id="p" class="easyui-panel" title="Entry Order Pembelian (PO)" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <form id="frmMaster" action="<?php print($helper->site_url("ap.order/edit/".$order->Id)); ?>" method="post">
        <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
            <tr>
                <td>Cabang</td>
                <td><select name="CabangId" class="easyui-combobox" id="CabangId" style="width: 250px" required>
                        <option value=""></option>
                        <?php
                        foreach ($cabangs as $cab) {
                            if ($cab->Id == $order->CabangId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Tanggal</td>
                <td><input type="text" size="12" id="PoDate" name="PoDate" value="<?php print($order->FormatPoDate(JS_DATE));?>" required/></td>
                <td>Dibutuhkan</td>
                <td><input type="text" size="12" id="RequestDate" name="RequestDate" value="<?php print($order->FormatRequestDate(JS_DATE));?>" /></td>
                <td>No. Order</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="PoNo" name="PoNo" value="<?php print($order->PoNo != null ? $order->PoNo : '-'); ?>" readonly/></td>
            </tr>
            <tr>
                <td>Supplier</td>
                <td><input class="easyui-combogrid" id="SupplierId" name="SupplierId" style="width: 250px" value="<?php print($order->SupplierId);?>" required/></td>
                <td>Salesman</td>
                <td><b><input type="text" class="f1 easyui-textbox" id="SalesName" name="SalesName" size="20" maxlength="50" value="<?php print($order->SalesName != null ? $order->SalesName : '-'); ?>"/></b></td>
                <td>Status</td>
                <td><select class="easyui-combobox" id="PoStatus" name="PoStatus" style="width: 150px">
                        <option value="0" <?php print($order->PoStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                        <option value="1" <?php print($order->PoStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                        <option value="2" <?php print($order->PoStatus == 2 ? 'selected="selected"' : '');?>>2 - Closed</option>
                        <option value="3" <?php print($order->PoStatus == 3 ? 'selected="selected"' : '');?>>3 - Batal</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td colspan="3"><b><input type="text" class="f1 easyui-textbox" id="PoDescs" name="PoDescs" size="86" maxlength="150" value="<?php print($order->PoDescs != null ? $order->PoDescs : '-'); ?>" /></b></td>
                <td>Cara Bayar</td>
                <td><select class="easyui-combobox" id="PaymentType" name="PaymentType" required>
                        <option value="1" <?php print($order->PaymentType == 1 ? 'selected="selected"' : '');?>>Kredit</option>
                        <option value="0" <?php print($order->PaymentType == 0 ? 'selected="selected"' : '');?>>Tunai</option>
                    </select>
                    &nbsp
                    Kredit
                    <input type="text" class="f1 easyui-textbox" id="CreditTerms" name="CreditTerms" size="2" maxlength="5" value="<?php print($order->CreditTerms != null ? $order->CreditTerms : 0); ?>" style="text-align: right" required/>&nbsphari</td>
            </tr>
            <tr>
                <td colspan="7">
                    <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                        <tr>
                            <th colspan="10">DETAIL BARANG YANG DIPESAN</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Order</th>
                            <th>Terima</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Disc (%)</th>
                            <th>Diskon</th>
                            <th>Jumlah</th>
                        </tr>
                        <?php
                        $counter = 0;
                        $total = 0;
                        $dta = null;
                        $dtx = null;
                        foreach($order->Details as $idx => $detail) {
                            $counter++;
                            print("<tr>");
                            printf('<td class="right">%s.</td>', $counter);
                            printf('<td>%s</td>', $detail->ItemCode);
                            printf('<td>%s</td>', $detail->ItemDescs);
                            printf('<td class="right">%s</td>', number_format($detail->OrderQty,0));
                            printf('<td class="right">%s</td>', number_format($detail->ReceiptQty,0));
                            printf('<td>%s</td>', $detail->SatBesar);
                            printf('<td class="right">%s</td>', number_format($detail->Price,0));
                            printf('<td class="right">%s</td>', $detail->DiscFormula);
                            printf('<td class="right">%s</td>', number_format($detail->DiscAmount,0));
                            printf('<td class="right">%s</td>', number_format($detail->SubTotal,0));
                            print("<td class='center'>");
                            $dta = addslashes($detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs));
                            $dtx = $detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs).'|'.$detail->ItemId.'|'.$detail->OrderQty.'|'.$detail->ReceiptQty.'|'.$detail->SatBesar.'|'.$detail->Price.'|'.$detail->DiscFormula;
                            printf('&nbsp<img src="%s" alt="Edit barang" title="Edit barang" style="cursor: pointer" onclick="return feditdetail(%s);"/>',$bedit,"'".$dtx."'");
                            printf('&nbsp<img src="%s" alt="Hapus barang" title="Hapus barang" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bclose,"'".$dta."'");
                            print("</td>");
                            print("</tr>");
                            $total += $detail->SubTotal;
                        }
                        ?>
                        <tr>
                            <td colspan="9" align="right">Sub Total :</td>
                            <td><input type="text" class="right bold" style="width: 150px" id="BaseAmount" name="BaseMount" value="<?php print($order->BaseAmount != null ? number_format($order->BaseAmount,0) : 0); ?>" readonly/></td>
                            <td class='center'><?php printf('<img src="%s" alt="Tambah Barang" title="Tambah barang" id="bAdDetail" style="cursor: pointer;"/>',$badd);?></td>
                        </tr>
                        <tr>
                            <td colspan="9" align="right">Diskon (%) :</td>
                            <td><input type="text" class="right bold" style="width: 30px" id="Disc1Pct" name="Disc1Pct" value="<?php print($order->Disc1Pct != null ? number_format($order->Disc1Pct,0) : 0); ?>"/>
                                <input type="text" class="right bold" style="width: 110px" id="Disc1Amount" name="Disc1Amount" value="<?php print($order->Disc1Amount != null ? number_format($order->Disc1Amount,0) : 0); ?>" readonly/></td>
                                <td class='center'><?php printf('<img src="%s" alt="Simpan Data" title="Simpan data master" id="bUpdate" style="cursor: pointer;"/>',$bsubmit);?></td>
                        </tr>
                        <tr>
                            <td colspan="9" align="right">D P P :</td>
                            <td><input type="text" class="right bold" style="width: 150px" id="DppAmount" name="DppAmount" value="<?php print(number_format($order->BaseAmount - $order->Disc1Amount,0)); ?>" readonly/></td>
                            <td class='center'><?php printf('<img src="%s" alt="Invoie Baru" title="Buat order baru" id="bTambah" style="cursor: pointer;"/>',$baddnew);?></td>
                        </tr>
                        <tr>
                            <td colspan="9" align="right">Pajak (%) :</td>
                            <td><input type="text" class="right bold" style="width: 30px" id="TaxPct" name="TaxPct" value="<?php print($order->TaxPct != null ? $order->TaxPct : 0); ?>"/>
                                <input type="text" class="right bold" style="width: 110px" id="TaxAmount" name="TaxAmount" value="<?php print($order->TaxAmount != null ? number_format($order->TaxAmount,0) : 0); ?>"/></td>
                                <td class='center'><?php printf('<img src="%s" alt="Hapus Po" title="Proses hapus order" id="bHapus" style="cursor: pointer;"/>',$bdelete);?></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right">Biaya Lain :</td>
                            <td colspan="7"><b><input type="text" class="bold" id="OtherCosts" name="OtherCosts" size="60" maxlength="150" value="<?php print($order->OtherCosts != null ? $order->OtherCosts : '-'); ?>"/></b></td>
                            <td><input type="text" class="right bold" style="width: 150px" id="OtherCostsAmount" name="OtherCostsAmount" value="<?php print($order->OtherCostsAmount != null ? number_format($order->OtherCostsAmount,0) : 0); ?>"/></td>
                            <td class='center'><?php printf('<img src="%s" id="bCetak" alt="Cetak Po" title="Proses cetak order" style="cursor: pointer;"/>',$bcetak);?></td>
                        </tr>
                        <tr>
                            <td colspan="9" align="right">Grand Total :</td>
                            <td><input type="text" class="right bold" style="width: 150px;" id="TotalAmount" name="TotalAmount" value="<?php print($order->TotalAmount != null ? number_format($order->TotalAmount,0) : 0); ?>" readonly/></td>
                            <td class='center'><?php printf('<img src="%s" id="bKembali" alt="Daftar Po" title="Kembali ke daftar order" style="cursor: pointer;"/>',$bkembali);?></td>
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
<!-- Form Add Po Detail -->
<div id="dlg" class="easyui-dialog" style="width:1000px;height:150px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" style="font-size: 12px;font-family: tahoma">
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Diskon</th>
                <th>Jumlah</th>
            </tr>
            <tr>
                <td>
                    <input type="text" id="aItemCode" name="aItemCode" size="15" value="" required/>
                    <input id="aItemSearch" name="aItemSearch" style="width: 20px"/>
                    <input type="hidden" id="aItemId" name="aItemId" value="0"/>
                    <input type="hidden" id="aId" name="aId" value="0"/>
                    <input type="hidden" id="aQtyStock" name="aQtyStock" value="0"/>
                </td>
                <td>
                    <input type="text" id="aItemDescs" name="aItemDescs" size="38" value="" disabled/>
                </td>
                <td>
                    <input class="right" type="text" id="aQty" name="aQty" size="5" value="0"/>
                </td>
                <td>
                    <input type="text" id="aSatuan" name="aSatuan" size="5" value="" disabled/>
                </td>
                <td>
                    <input class="right" type="text" id="aPrice" name="aPrice" size="10" value="0"/>
                </td>
                <td>
                    <input class="right" type="text" id="aDiscFormula" name="aDiscFormula" size="3" value="0"/>% =
                    <input class="right" type="text" id="aDiscAmount" name="aDiscAmount" size="10" value="0"/>
                </td>
                <td>
                    <input class="right" type="text" id="aSubTotal" name="aSubTotal" size="12" value="0"/>
                </td>
            </tr>
        </table>
    </form>
    <br>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDetail()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
</body>
</html>
