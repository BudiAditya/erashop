<!DOCTYPE HTML>
<html>
<?php
/** @var $assembly Assembly */ 
?>
<head>
    <title>ERASYS - Entry Proses Produksi</title>
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
            var userCabId,assemblyId,userCompId;
            userCabId = "<?php print($assembly->CabangId > 0 ? $assembly->CabangId : $userCabId);?>";
            assemblyId = "<?php print($assembly->Id);?>";
            userCompId = "<?php print($assembly->EntityId > 0 ? $assembly->EntityId : $userCompId);?>";
            var addetail = ["aItemSearch","aItemCode","aQty","aPrice","aSubTotal","bSaveDetail"];
            BatchFocusRegister(addetail);
            //var addmaster = ["AssemblyDate","aItemMasterSearch","aItemMasterCode","aItemMasterQty"];
            //BatchFocusRegister(addmaster);
            $("#AssemblyDate").customDatePicker({ showOn: "focus" });

            $('#aItemMasterSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("inventory.assembly/getitemprices_json"));?>",
                idField:'item_id',
                textField:'item_name',
                mode:'remote',
                fitColumns:true,
                columns:[[
                    {field:'item_code',title:'Kode',width:60},
                    {field:'item_name',title:'Nama Barang',width:200},
                    {field:'sat_besar',title:'Satuan',width:40},
                    {field:'hrg_jual1',title:'Harga',width:40,align:'right'}
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
                    var harga = row.hrg_jual1;
                    console.log(harga);
                    $('#aItemMasterId').val(bid);
                    $('#aItemMasterCode').val(bkode);
                    $('#aItemMasterName').val(bnama);
                    $('#aItemMasterSatuan').val(satuan);
                }
            });

            $("#aItemMasterCode").change(function(e){
                //$ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$hrg_beli.'|'.$hrg_jual;
                var itc = $("#aItemMasterCode").val();
                var lvl = -1;
                var cbi = userCabId;
                var url = "<?php print($helper->site_url("inventory.assembly/getitemprices_plain/"));?>"+cbi+"/"+itc;
                if (itc != ''){
                    $.get(url, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#aItemMasterId').val(dtx[1]);
                                $('#aItemMasterName').val(dtx[2]);
                                $('#aItemMasterSatuan').val(dtx[3]);
                                $('#aItemMasterPrice').val(dtx[5]);
                            }else{
                                alert('ER1 - Data Barang ini tidak ditemukan!');
                            }
                        }else{
                            alert('ER2 - Data Barang ini tidak ditemukan!');
                        }
                    });
                }
            });

            $('#aItemSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("inventory.assembly/getitempricestock_json/0/"));?>"+userCabId,
                idField:'item_id',
                textField:'item_name',
                mode:'remote',
                fitColumns:true,
                columns:[[
                    {field:'item_id',title:'ID',width:50},
                    {field:'item_code',title:'Kode',width:50},
                    {field:'item_name',title:'Nama Barang',width:150},
                    {field:'bsatbesar',title:'Satuan',width:40},
                    {field:'qty_stock',title:'Stock',width:40,align:'right'},
                    {field:'hrg_jual',title:'Harga',width:40,align:'right'}
                ]],
                onSelect: function(index,row){
                    var bid = row.item_id;
                    console.log(bid);
                    var bkode = row.item_code;
                    console.log(bkode);
                    var bnama = row.item_name;
                    console.log(bnama);
                    var satuan = row.bsatbesar;
                    console.log(satuan);
                    var harga = row.hrg_jual;
                    console.log(harga);
                    var bqstock = row.qty_stock;
                    console.log(bqstock);
                    var hbeli = row.hrg_beli;
                    console.log(hbeli);
                    $('#aItemId').val(bid);
                    $('#aItemCode').val(bkode);
                    $('#aItemDescs').val(bnama);
                    $('#aSatuan').val(satuan);
                    $('#aPrice').val(harga);
                    $('#aQtyStock').val(bqstock);
                    if(bqstock > 0){
                        $('#aQty').val(1);
                        hitDetail();
                    }else{
                        $('#aQty').val(0);
                        alert('Maaf, Stock tidak cukup!');
                    }
                }
            });

            $("#aItemCode").change(function(e){
                //$ret = "OK|".$setprice->ItemId.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$setprice->QtyStock.'|'.$setprice->HrgBeli.'|'.$setprice->HrgJual1;
                var itc = $("#aItemCode").val();
                var lvl = -1;
                var cbi = userCabId;
                var url = "<?php print($helper->site_url("inventory.assembly/getitempricestock_plain/"));?>"+cbi+"/"+itc+"/"+lvl;
                if (itc != ''){
                    $.get(url, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#aItemId').val(dtx[1]);
                                $('#aItemDescs').val(dtx[2]);
                                $('#aSatuan').val(dtx[3]);
                                $('#aPrice').val(dtx[5]);
                                $('#aQtyStock').val(Number(dtx[4]));
                                if (Number(dtx[4]) > 0){
                                    if ($('#aQty').val()=='' || Number($('#aQty').val())==0){
                                        $('#aQty').val(1);
                                    }
                                    hitDetail();
                                    $('#aQty').focus();
                                }else{
                                    $('#aQty').val(0);
                                    alert('Maaf, Stock tidak cukup!');
                                    $('#aQty').focus();
                                }
                            }else{
                                alert('ER1 - Data Stock Barang ini tidak ditemukan!');
                            }
                        }else{
                            alert('ER2 - Data Stock Barang ini tidak ditemukan!');
                        }
                    });
                }
            });

            $("#aSubTotal").keyup(function(event){
                if(event.keyCode == 13){
                    $("#bSaveDetail").click();
                }
            });

            $("#bSaveDetail").click(function(){
                //validasi master
                assemblyId = "<?php print($assembly->Id == null ? 0 : $assembly->Id);?>";
                var aitd = Number($('#aItemId').val());
                var aitc = $('#aItemCode').val();
                var aqty = Number($('#aQty').val());
                var aprc = Number($('#aPrice').val());
                var astt = Number($('#aSubTotal').val());
                if ($("#aItemMasterId").val() > 0 && $("#aItemMasterQty").val() && $("#aItemMasterCode").val() != '') {
                    if ((userCabId > 0) && (aitd > 0 && aqty > 0 && astt > 0)) {
                        if (confirm('Apakah data input sudah benar?')) {
                            var url = "<?php print($helper->site_url("inventory.assembly/proses_master/")); ?>" + assemblyId;
                            //proses simpan dan update master
                            $.post(url, {
                                CabangId: userCabId,
                                AssemblyDate: $("#AssemblyDate").val(),
                                AssemblyNo: $("#AssemblyNo").val(),
                                aItemMasterId: $("#aItemMasterId").val(),
                                aItemMasterCode: $("#aItemMasterCode").val(),
                                aItemMasterQty: $("#aItemMasterQty").val(),
                                AssemblyStatus: $("#AssemblyStatus").val()
                            }).done(function (data) {
                                var rst = data.split('|');
                                if (rst[0] == 'OK') {
                                    //validasi detail
                                    var aisi = rst[2];
                                    if (aitd > 0 && aqty > 0 && astt > 0) {
                                        //proses simpan detail
                                        var urz = "<?php print($helper->site_url("inventory.assembly/add_detail/")); ?>" + aisi;
                                        $.post(urz, {
                                            aItemId: aitd,
                                            aItemCode: aitc,
                                            aQty: aqty,
                                            aPrice: aprc
                                        }).done(function (data) {
                                            var rsx = data.split('|');
                                            if (rsx[0] == 'OK') {
                                                location.href = "<?php print($helper->site_url("inventory.assembly/add/")); ?>" + aisi;
                                            } else {
                                                alert(data);
                                            }
                                        });
                                    } else {
                                        alert('Data Detail tidak valid!');
                                        location.href = "<?php print($helper->site_url("inventory.assembly/add/")); ?>" + aisi;
                                    }
                                }
                            });
                        }
                    } else {
                        alert('Data Input tidak valid!');
                    }
                }else{
                    alert('Data Hasil Produksi tidak valid!');
                }
            });

            $("#aQty").change(function(e){
                var stk = Number($('#aQtyStock').val());
                var qty = $('#aQty').val();
                if (stk > 0 && stk >= qty ){
                    hitDetail();
                }else{
                    alert('Maaf, Stock tidak cukup!\nSisa: '+stk);
                    $('#aQty').val(stk);
                    $('#aQty').focus();
                    hitDetail();
                }
            });

            $("#bUpdate").click(function(){
                //if (confirm('Apakah data input sudah benar?')){
                //    $('#frmMaster').submit();
                //}
            });

            $("#bTambah").click(function(){
                if (confirm('Buat Proses Produksi baru?')){
                    location.href="<?php print($helper->site_url("inventory.assembly/add/0")); ?>";
                }
            });

            $("#bHapus").click(function(){
                if (confirm('Anda yakin akan menghapus data produksi ini?')){
                    location.href="<?php print($helper->site_url("inventory.assembly/delete/").$assembly->Id); ?>";
                }
            });

            $("#bCetak").click(function(){
                if (confirm('Cetak bukti produksi ini?')){
                    alert('Maaf, Proses cetak belum tersedia..');
                }
            });

            $("#bKembali").click(function(){
                location.href="<?php print($helper->site_url("inventory.assembly")); ?>";
            });

        });

        function hitDetail(){
            var subTotal = (Number($("#aQty").val()) * Number($("#aPrice").val()));
            $('#aSubTotal').val(subTotal);
        }

        function fdeldetail(dta){
            var dtz = dta.replace(/\"/g,"\\\"")
            var dtx = dtz.split('|');
            var id = dtx[0];
            var kode = dtx[1];
            var barang = dtx[2];
            var urx = '<?php print($helper->site_url("inventory.assembly/delete_detail/"));?>'+id;
            if (confirm('Hapus Data Detail Barang \nKode: '+kode+ '\nNama: '+barang+' ?')) {
                $.get(urx, function(data){
                    alert(data);
                    location.reload();
                });
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
<div id="p" class="easyui-panel" title="Entry Proses Produksi" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td>Cabang</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($assembly->CabangCode != null ? $assembly->CabangCode : $userCabCode); ?>" disabled/></td>
            <td>Tanggal</td>
            <td><input type="text" size="12" id="AssemblyDate" name="AssemblyDate" value="<?php print($assembly->FormatAssemblyDate(JS_DATE));?>"  <?php print($assembly->AssemblyStatus > 0 ? 'disabled' : 'required');?>/></td>
            <td>No. Produksi</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="AssemblyNo" name="AssemblyNo" value="<?php print($assembly->AssemblyNo != null ? $assembly->AssemblyNo : '-'); ?>" readonly/></td>
            <td>Status</td>
            <td><select id="xAssemblyStatus" name="xAssemblyStatus" disabled>
                    <option value="0" <?php print($assembly->AssemblyStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                    <option value="1" <?php print($assembly->AssemblyStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                    <option value="2" <?php print($assembly->AssemblyStatus == 2 ? 'selected="selected"' : '');?>>2 - Batal</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="bold">BARANG HASIL PRODUKSI:</td>
        </tr>
        <?php if ($assembly->AssemblyStatus == null || $assembly->AssemblyStatus == 0){?>
        <tr>
            <td class="right">Cari Data:</td>
            <td colspan="3"><input class="easyui-combogrid" id="aItemMasterSearch" name="aItemMasterSearch" style="width:500px"/></td>
        </tr>
        <?php }?>
        <tr>
            <td>Kode Barang
                <input type="hidden" id="aItemMasterId" name="aItemMasterId" value="<?php print($assembly->ItemId == null ? 0 : $assembly->ItemId);?>"/>
                <input type="hidden" id="CabangId" name="CabangId" value="<?php print($assembly->CabangId == null ? $userCabId : $assembly->CabangId);?>"/>
                <input type="hidden" id="AssemblyStatus" name="AssemblyStatus" value="<?php print($assembly->AssemblyStatus == null ? 0 : $assembly->AssemblyStatus);?>"/>
            </td>
            <td colspan="9"><input class="bold" type="text" id="aItemMasterCode" name="aItemMasterCode" size="15" value="<?php print($assembly->ItemCode);?>"  <?php print($assembly->AssemblyStatus > 0 ? 'readonly' : 'required');?>/>
                &nbsp;
                Nama Barang
                <input class="bold" type="text" id="aItemMasterName" name="aItemMasterName" size="40" value="<?php print($assembly->ItemName);?>" disabled/>
                &nbsp;
                QTY :
                <input class="bold right" type="text" id="aItemMasterQty" name="aItemMasterQty" size="5" value="<?php print($assembly->Qty);?>" <?php print($assembly->AssemblyStatus > 0 ? 'readonly' : 'required');?>/>
                <input class="bold" type="text" id="aItemMasterSatuan" name="aItemMasterSatuan" size="3" value="<?php print($assembly->ItemSatuan);?>" disabled/>
                HPP :
                <input class="bold right" type="text" id="aItemMasterPrice" name="aItemMasterPrice" size="8" value="<?php print($assembly->Price == '' ? 0 : number_format($assembly->Price,0));?>" readonly/>
                Total :
                <input class="bold right" type="text" id="aItemMasterTotalPrice" name="aItemMasterTotalPrice" size="10" value="<?php print($assembly->Price == '' ? 0 : number_format(round($assembly->Price * $assembly->Qty,0),0));?>" readonly/>
            </td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
        <tr>
            <th colspan="7">DETAIL BARANG YANG DIPAKAI</th>
            <th rowspan="2">Action</th>
        </tr>
        <tr>
            <th>No.</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Harga</th>
            <th>Jumlah</th>
        </tr>
        <tr>
            <td colspan="2" class="right">Cari Data Barang -->></td>
            <td colspan="9"><input class="easyui-combogrid" id="aItemSearch" name="aItemSearch" style="width:500px"/></td>
        </tr>
        <tr class="bold">
            <td>&nbsp;</td>
            <td>
                <input type="text" id="aItemCode" name="aItemCode" size="15" value="" required/>
                <input type="hidden" id="aItemId" name="aItemId" value="0"/>
                <input type="hidden" id="aId" name="aId" value="0"/>
                <input type="hidden" id="aQtyStock" name="aQtyStock" value="0"/>
                <input type="hidden" id="aItemHpp" name="aItemHpp" value="0"/>
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
                <input class="right" type="text" id="aPrice" name="aPrice" size="10" value="0" readonly/>
            </td>
            <td>
                <input class="right" type="text" id="aSubTotal" name="aSubTotal" style="width:100px" value="0" readonly/>
            </td>
            <td class='center'><?php printf('<img src="%s" alt="Simpan" title="Simpan" id="bSaveDetail" style="cursor: pointer;"/>',$badd);?></td>
        </tr>
        <?php
        $counter = 0;
        $total = 0;
        $dta = null;
        $dtx = null;
        foreach($assembly->Details as $idx => $detail) {
            $counter++;
            print("<tr class='bold'>");
            printf('<td class="right">%s.</td>', $counter);
            printf('<td>%s</td>', $detail->ItemCode);
            printf('<td>%s</td>', $detail->ItemDescs);
            printf('<td class="right">%s</td>', number_format($detail->Qty,0));
            printf('<td>%s</td>', $detail->SatBesar);
            printf('<td class="right">%s</td>', number_format($detail->Price,0));
            printf('<td class="right">%s</td>', number_format(round($detail->Qty * $detail->Price,0),0));
            print("<td class='center'>");
            $dta = addslashes($detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs));
            printf('&nbsp<img src="%s" alt="Hapus barang" title="Hapus barang" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bclose,"'".$dta."'");
            print("</td>");
            print("</tr>");
            $total += round($detail->Qty * $detail->Price,0);
        }
        ?>
        <tr>
            <td colspan="6" class="bold right">Nilai Produksi</td>
            <td class="bold right"><?php print(number_format($total,0));?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="8" class="right">
                <?php
                if ($acl->CheckUserAccess("inventory.assembly", "edit")) {
                    printf('<img src="%s" alt="Simpan Data" title="Simpan data master" id="bUpdate" style="cursor: pointer;"/> &nbsp',$bsubmit);
                }
                if ($acl->CheckUserAccess("inventory.assembly", "add")) {
                    printf('<img src="%s" alt="Data Baru" title="Buat Data Baru" id="bTambah" style="cursor: pointer;"/> &nbsp',$baddnew);
                }
                if ($acl->CheckUserAccess("inventory.assembly", "delete")) {
                    printf('<img src="%s" alt="Hapus Data" title="Hapus Data" id="bHapus" style="cursor: pointer;"/> &nbsp',$bdelete);
                }
                if ($acl->CheckUserAccess("inventory.assembly", "print")) {
                    printf('<img src="%s" alt="Cetak Bukti" title="Cetak Bukti" id="bCetak" style="cursor: pointer;"/> &nbsp',$bcetak);
                }
                printf('<img src="%s" id="bKembali" alt="Daftar Produksi" title="Kembali ke Daftar Produksi" style="cursor: pointer;"/>',$bkembali);
                ?>
            </td>
        </tr>
    </table>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016 - 2020  <a href = 'http://rekasys.com'>Rekasys Inc</a>
</div>
<!-- </body> -->
</html>
