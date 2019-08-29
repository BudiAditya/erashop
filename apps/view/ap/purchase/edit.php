<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $purchase Purchase */
?>
<head>
    <title>ERASYS - Edit Pembelian/Penerimaan Barang</title>
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
            var addetail = ["aItemCode", "aQty","aPrice", "aDiscFormula", "aDiscAmount", "aIsFree", "aSubTotal"];
            BatchFocusRegister(addetail);
            var addmaster = ["CabangId", "GrnDate","ReceiptDate","SupplierId", "SalesName", "GrnDescs", "PaymentType","CreditTerms","bUpdate", "bKembali"];
            BatchFocusRegister(addmaster);
            $("#GrnDate").customDatePicker({ showOn: "focus" });
            $("#ReceiptDate").customDatePicker({ showOn: "focus" });
            $('#SupplierId').combogrid({
                panelWidth:600,
                url: "<?php print($helper->site_url("master.contacts/getjson_contacts/2/".$userCompId));?>",
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
                url: "<?php print($helper->site_url("ap.purchase/getitemprices_json"));?>",
                idField:'item_id',
                textField:'item_name',
                mode:'remote',
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
                    //$('#aQty').focus();
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
                $('#aIsFree').val(0);
                $('#aSubTotal').val(0);
                newItem();
            });

            $("#aItemCode").change(function(e){
                //$ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$hrg_beli.'|'.$hrg_jual;
                var cbi = $("#CabangId").val();
                var itc = $("#aItemCode").val();
                var url = "<?php print($helper->site_url("ap.purchase/getitemprices_plain/"));?>"+cbi+"/"+itc;
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
                var totalDetail = subTotal - (discAmount * Number($("#aQty").val()));
                $('#aSubTotal').val(totalDetail);
            });

            $('#aIsFree').change(function () {
                if (this.checked){
                    $('#aIsFree').val(1);
                }else{
                    $('#aIsFree').val(0);
                }
                hitDetail();
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
                if (confirm('Buat GRN baru?')){
                    location.href="<?php print($helper->site_url("ap.purchase/add")); ?>";
                }
            });

            $("#bHapus").click(function(){
                if (confirm('Anda yakin akam membatalkan pembelian ini?')){
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

        function hitDetail(){
            //var subTotal = (Number($("#aQty").val()) * Number($("#aPrice").val()));
            //var discAmount = hitDiscFormula(Number($("#aPrice").val()),$("#aDiscFormula").val());
            //var totalDetail = subTotal - (discAmount * Number($("#aQty").val()));
            //$('#aDiscAmount').val(discAmount);
            //$('#aSubTotal').val(totalDetail);
            var isFree = Number($("#aIsFree").val());
            var subTotal = 0;
            var discAmount = 0;
            var totalDetail = 0;
            if (isFree == 0){
                subTotal = (Number($("#aQty").val()) * Number($("#aPrice").val()));
                discAmount = hitDiscFormula(Number($("#aPrice").val()),$("#aDiscFormula").val());
                totalDetail = subTotal - (discAmount * Number($("#aQty").val()));
            }
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
            var urx = '<?php print($helper->site_url("ap.purchase/delete_detail/"));?>'+id;
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
            $('#aIsFree').val(dtx[9]);
            if (Number(dtx[9]) == 0){
                $('#aIsFree').attr("checked",false);
            }else{
                $('#aIsFree').attr("checked",true);
            }
            hitDetail();
            $('#dlg').dialog('open').dialog('setTitle','Edit Detail Barang yang diterima');
            url= "<?php print($helper->site_url("ap.purchase/edit_detail/".$purchase->Id));?>";
        }

        function newItem(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Detail Barang yang diterima');
            $('#fm').form('clear');
            url= "<?php print($helper->site_url("ap.purchase/add_detail/".$purchase->Id));?>";
            $('#aItemCode').focus();
        }

        function saveDetail(){
            var aitd = Number($('#aItemId').val());
            var aqty = Number($('#aQty').val());
            var astt = Number($('#aSubTotal').val());
            var aisf = 0;
            if ($('#aIsFree').is(':checked')){
                aisf = 1;
            }
            if ((aitd > 0 && aqty > 0 && astt > 0) || (aitd > 0 && aqty > 0 && aisf > 0)){
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

        function hitDiscFormula(nAmount,dFormula) {
            var retVal = 0;
            if (nAmount > 0 && dFormula != '' && dFormula != '0') {
                var aFormula = dFormula.split('+');
                var nDiscount = 0;
                var pDiscount = 0;
                for (var i = 0; i < aFormula.length; i++) {
                    pDiscount = aFormula[i];
                    nDiscount = Math.round(nAmount * (pDiscount) / 100, 0);
                    retVal += nDiscount;
                    nAmount -= retVal;
                }
            }
            return retVal;
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
            bpurchase-bottom:1px solid #ccc;
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
<div id="p" class="easyui-panel" title="Edit Pembelian/Penerimaan Barang" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <form id="frmMaster" action="<?php print($helper->site_url("ap.purchase/edit/".$purchase->Id)); ?>" method="post">
        <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
            <tr>
                <td>Cabang</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($purchase->CabangCode != null ? $purchase->CabangCode : $userCabCode); ?>" disabled/>
                    <input type="hidden" id="CabangId" name="CabangId" value="<?php print($purchase->CabangId == null ? $userCabId : $purchase->CabangId);?>"/>
                </td>
                <td>Tanggal</td>
                <td><input type="text" size="12" id="GrnDate" name="GrnDate" value="<?php print($purchase->FormatGrnDate(JS_DATE));?>" <?php print($itemsCount == 0 ? 'required' : 'readonly');?>/></td>
                <td>Diterima</td>
                <td><input type="text" size="12" id="ReceiptDate" name="ReceiptDate" value="<?php print($purchase->FormatReceiptDate(JS_DATE));?>" <?php print($itemsCount == 0 ? 'required' : 'readonly');?>/></td>
                <td>No. GRN</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="GrnNo" name="GrnNo" value="<?php print($purchase->GrnNo != null ? $purchase->GrnNo : '-'); ?>" readonly/></td>
            </tr>
            <tr>
                <td>Supplier</td>
                <td><input class="easyui-combogrid" id="SupplierId" name="SupplierId" style="width: 250px" value="<?php print($purchase->SupplierId);?>" required/></td>
                <td>Salesman</td>
                <td><b><input type="text" class="f1 easyui-textbox" id="SalesName" name="SalesName" size="20" maxlength="50" value="<?php print($purchase->SalesName != null ? $purchase->SalesName : '-'); ?>"/></b></td>
                <td>Status</td>
                <td><select class="easyui-combobox" id="GrnStatus" name="GrnStatus" style="width: 150px">
                        <option value="0" <?php print($purchase->GrnStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                        <option value="1" <?php print($purchase->GrnStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                        <option value="2" <?php print($purchase->GrnStatus == 2 ? 'selected="selected"' : '');?>>2 - Closed</option>
                        <option value="3" <?php print($purchase->GrnStatus == 3 ? 'selected="selected"' : '');?>>3 - Batal</option>
                    </select>
                </td>
                <td>Ex. PO</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="ExPoNo" name="ExPoNo" value="<?php print($purchase->ExPoNo != null ? $purchase->ExPoNo : '-'); ?>"/></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td><b><input type="text" class="f1 easyui-textbox" id="GrnDescs" name="GrnDescs" style="width: 250px" value="<?php print($purchase->GrnDescs != null ? $purchase->GrnDescs : '-'); ?>" required/></b></td>
                <td>Gudang *</td>
                <td>
                    <?php if ($itemsCount == 0){?>
                    <select class="easyui-combobox" id="GudangId" name="GudangId" style="width: 150px">
                    <?php }else{ ?>
                    <input type="hidden" name="GudangId" id="GudangId" value="<?php print($purchase->GudangId);?>"/>
                    <select class="easyui-combobox" id="GudangId" name="GudangId" style="width: 150px" disabled>
                    <?php } ?>
                        <option value="">- Pilih Gudang -</option>
                        <?php
                        foreach ($gudangs as $gudang) {
                            if ($purchase->GudangId > 0){
                                if ($gudang->Id == $purchase->GudangId) {
                                    printf('<option value="%d" selected="selected">%s</option>', $gudang->Id, $gudang->Kode);
                                }else {
                                    printf('<option value="%d">%s</option>', $gudang->Id, $gudang->Kode);
                                }
                            }else{
                                if ($gudang->Id == $userCabId) {
                                    printf('<option value="%d" selected="selected">%s</option>', $gudang->Id, $gudang->Kode);
                                }else {
                                    printf('<option value="%d">%s</option>', $gudang->Id, $gudang->Kode);
                                }
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Cara Bayar</td>
                <td><select class="easyui-combobox" id="PaymentType" name="PaymentType" required>
                        <option value="1" <?php print($purchase->PaymentType == 1 ? 'selected="selected"' : '');?>>Kredit</option>
                        <option value="0" <?php print($purchase->PaymentType == 0 ? 'selected="selected"' : '');?>>Tunai</option>
                    </select>
                    &nbsp
                    Kredit
                    <input type="text" class="f1 easyui-textbox" id="CreditTerms" name="CreditTerms" size="2" maxlength="5" value="<?php print($purchase->CreditTerms != null ? $purchase->CreditTerms : 0); ?>" style="text-align: right" required/>&nbsphari</td>
                <td colspan="3" class="blink" style="color: orangered"><b>*Tanggal & Gudang tidak boleh diubah setelah detail diinput*</b></td>
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
                            print("<td class='center'>");
                            $dta = addslashes($detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs));
                            $dtx = $detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs).'|'.$detail->ItemId.'|'.$detail->PurchaseQty.'|'.$detail->ReturnQty.'|'.$detail->SatBesar.'|'.$detail->Price.'|'.$detail->DiscFormula.'|'.$detail->IsFree;
                            printf('&nbsp<img src="%s" alt="Edit barang" title="Edit barang" style="cursor: pointer" onclick="return feditdetail(%s);"/>',$bedit,"'".$dtx."'");
                            printf('&nbsp<img src="%s" alt="Hapus barang" title="Hapus barang" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bclose,"'".$dta."'");
                            print("</td>");
                            print("</tr>");
                            $total += $detail->SubTotal;
                        }
                        ?>
                        <tr>
                            <td colspan="10" align="right">Sub Total :</td>
                            <td><input type="text" class="right bold" style="width: 150px" id="BaseAmount" name="BaseMount" value="<?php print($purchase->BaseAmount != null ? number_format($purchase->BaseAmount,0) : 0); ?>" readonly/></td>
                            <td class='center'><?php printf('<img src="%s" alt="Tambah Barang" title="Tambah barang" id="bAdDetail" style="cursor: pointer;"/>',$badd);?></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="right">Diskon (%) :</td>
                            <td><input type="text" class="right bold" style="width: 30px" id="Disc1Pct" name="Disc1Pct" value="<?php print($purchase->Disc1Pct != null ? number_format($purchase->Disc1Pct,1) : 0); ?>"/>
                                <input type="text" class="right bold" style="width: 110px" id="Disc1Amount" name="Disc1Amount" value="<?php print($purchase->Disc1Amount != null ? number_format($purchase->Disc1Amount,0) : 0); ?>" readonly/></td>
                            <?php if ($acl->CheckUserAccess("ap.purchase", "edit")) { ?>
                                <td class='center'><?php printf('<img src="%s" alt="Simpan Data" title="Simpan data master" id="bUpdate" style="cursor: pointer;"/>',$bsubmit);?></td>
                            <?php }else{ ?>
                                <td>&nbsp</td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td colspan="10" align="right">D P P :</td>
                            <td><input type="text" class="right bold" style="width: 150px" id="DppAmount" name="DppAmount" value="<?php print(number_format($purchase->BaseAmount - $purchase->Disc1Amount,0)); ?>" readonly/></td>
                            <?php if ($acl->CheckUserAccess("ap.purchase", "add")) { ?>
                                <td class='center'><?php printf('<img src="%s" alt="GRN Baru" title="Buat GRN baru" id="bTambah" style="cursor: pointer;"/>',$baddnew);?></td>
                            <?php }else{ ?>
                                <td>&nbsp</td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td colspan="10" align="right">Pajak (%) :</td>
                            <td><input type="text" class="right bold" style="width: 30px" id="TaxPct" name="TaxPct" value="<?php print($purchase->TaxPct != null ? $purchase->TaxPct : 0); ?>"/>
                                <input type="text" class="right bold" style="width: 110px" id="TaxAmount" name="TaxAmount" value="<?php print($purchase->TaxAmount != null ? number_format($purchase->TaxAmount,0) : 0); ?>"/></td>
                            <?php if ($acl->CheckUserAccess("ap.purchase", "delete")) { ?>
                                <td class='center'><?php printf('<img src="%s" alt="Hapus Grn" title="Proses hapus GRN" id="bHapus" style="cursor: pointer;"/>',$bdelete);?></td>
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
                            <td class='center'><?php printf('<img src="%s" id="bKembali" alt="Daftar Grn" title="Kembali ke daftar GRN" style="cursor: pointer;"/>',$bkembali);?></td>
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
<!-- Form Add Grn Detail -->
<div id="dlg" class="easyui-dialog" style="width:1100px;height:200px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="right bold">Cari Data Barang:</td>
                <td colspan="7"><input id="aItemSearch" name="aItemSearch" style="width: 500px"/></td>
            </tr>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Diskon (Per Item)</th>
                <th>Gratis</th>
                <th>Jumlah</th>
            </tr>
            <tr>
                <td>
                    <input type="text" id="aItemCode" name="aItemCode" size="20" value="" required/>
                    <input type="hidden" id="aItemId" name="aItemId" value="0"/>
                    <input type="hidden" id="aId" name="aId" value="0"/>
                    <input type="hidden" id="aQtyStock" name="aQtyStock" value="0"/>
                </td>
                <td>
                    <input type="text" id="aItemDescs" name="aItemDescs" size="50" value="" disabled/>
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
                    <input class="right" type="text" id="aDiscFormula" name="aDiscFormula" size="5" value="0"/>% =
                    <input class="right" type="text" id="aDiscAmount" name="aDiscAmount" size="10" value="0"/>
                </td>
                <td>
                    <input class="right" type="checkbox" id="aIsFree" name="aIsFree" value="0"/>
                </td>
                <td>
                    <input class="right" type="text" id="aSubTotal" name="aSubTotal" size="12" value="0" readonly/>
                </td>
            </tr>
        </table>
    </form>
    <span style="color: red" class="blink"><b>**Ketik Kode Barang atau Scan BarCode agar lebih cepat**</b></span>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDetail()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
</body>
</html>
