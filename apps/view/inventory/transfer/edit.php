<!DOCTYPE HTML>
<html>
<?php
/** @var $transfer Transfer */
?>
<head>
    <title>ERASYS - Edit Pengiriman Barang Antar Cabang</title>
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
            var addetail = ["aItemCode", "aQty"];
            BatchFocusRegister(addetail);
            //var addmaster = ["CabangId", "NpbDate","ToCabangId","NpbStatus","NpbDescs", "btSubmit", "btKembali"];
            //BatchFocusRegister(addmaster);
            $("#NpbDate").customDatePicker({ showOn: "focus" });

            $('#aItemSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("inventory.stock/getitemstock_json/".$transfer->CabangId));?>",
                idField:'item_id',
                textField:'item_name',
                mode:'remote',
                fitColumns:true,
                columns:[[
                    {field:'item_code',title:'Kode',width:50},
                    {field:'item_name',title:'Nama Barang',width:150},
                    {field:'sat_besar',title:'Satuan',width:40},
                    {field:'qty_stock',title:'Stock',width:40,align:'right'}
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
                    var stock = row.qty_stock;
                    console.log(stock);
                    $('#aQtyStock').val(stock);
                    $('#aItemId').val(bid);
                    $('#aItemCode').val(bkode);
                    $('#aItemDescs').val(bnama);
                    $('#aSatuan').val(satuan);
                    if(stock > 0){
                       $('#aQty').val(1);
                    }else{
                       alert('Maaf, Stock barang ini kosong!');
                        $('#aQty').val(0);
                    }
                    //$('#aQty').focus();
                }
            });

            $("#bAdDetail").click(function(e){
                $('#aItemId').val('');
                $('#aItemCode').val('');
                $('#aItemDescs').val('');
                $('#aSatuan').val('');
                $('#aQty').val(0);
                newItem();
            });

            $("#aItemCode").change(function(e){
                //$ret = "OK|".$stock->ItemId.'|'.$stock->ItemName.'|'.$stock->SatBesar.'|'.$stock->QtyStock;
                var itc = $("#aItemCode").val();
                var url = '<?php print($helper->site_url("inventory.stock/getitemstock_plain/".$transfer->CabangId."/"));?>'+itc;
                if (itc != ''){
                    $.get(url, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#aItemId').val(dtx[1]);
                                $('#aItemDescs').val(dtx[2]);
                                $('#aSatuan').val(dtx[3]);
                                if (Number(dtx[4])>0){
                                    $('#aQtyStock').val(dtx[4]);
                                    $('#aQty').val(1);
                                }else{
                                    alert('Maaf, Stock barang ini kosong!');
                                    $('#aQty').val(0);
                                }
                                $('#aQty').focus();
                            }
                        }else{
                            alert('Maaf, Stock Harga Barang ini tidak ditemukan!');
                        }
                    });
                }
            });

            $("#aQty").change(function(e){
                var stk = Number($('#aQtyStock').val());
                var qty = $('#aQty').val();
                if (stk < qty && stk > 0){
                    alert('Maaf, Stock barang ini tidak cukup!\nSisa : '+stk);
                    $('#aQty').val(stk);
                    $('#aQty').focus();
                }else if($stk < 1){
                    alert('Maaf, Stock barang ini kosong!');
                    $('#aQty').val(0);
                    $('#aQty').focus();
                }
            });

            $("#bUpdate").click(function(){
                if (confirm('Apakah data input sudah benar?')){
                    $('#frmMaster').submit();
                }
            });

            $("#bTambah").click(function(){
                if (confirm('Buat NPB baru?')){
                    location.href="<?php print($helper->site_url("inventory.transfer/add")); ?>";
                }
            });

            $("#bHapus").click(function(){
                if (confirm('Anda yakin akam menghapus NPB ini?')){
                    location.href="<?php print($helper->site_url("inventory.transfer/delete/").$transfer->Id); ?>";
                }
            });

            $("#bCetak").click(function(){
                if (confirm('Cetak NPB ini?')){
                    alert('Proses cetak..');
                }
            });

            $("#bKembali").click(function(){
                location.href="<?php print($helper->site_url("inventory.transfer")); ?>";
            });
        });

         function fdeldetail(dta){
            var dtz = dta.replace(/\"/g,"\\\"")
            var dtx = dtz.split('|');
            var id = dtx[0];
            var kode = dtx[1];
            var barang = dtx[2];
            var urx = '<?php print($helper->site_url("inventory.transfer/delete_detail/"));?>'+id;
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
            $('#aQty').val(dtx[4]);
            $('#dlg').dialog('open').dialog('setTitle','Edit Detail Barang yang dikirim');
            url= "<?php print($helper->site_url("inventory.transfer/edit_detail/".$transfer->Id));?>";
        }

        function newItem(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Detail Barang yang dikirim');
            $('#fm').form('clear');
            url= "<?php print($helper->site_url("inventory.transfer/add_detail/".$transfer->Id));?>";
            $('#aItemCode').focus();
        }

        function saveDetail(){
            var aitd = Number($('#aItemId').val());
            var aqty = Number($('#aQty').val());
            var aqts = Number($('#aQtyStock').val());
            if (aitd > 0 && aqty > 0){
                if (aqty > aqts){
                    alert('Qty kirim melebihi stock!');
                }else {
                    $('#fm').form('submit', {
                        url: url,
                        onSubmit: function () {
                            return $(this).form('validate');
                        },
                        success: function (result) {
                            var result = eval('(' + result + ')');
                            if (result.errorMsg) {
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
                }
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
            btransfer-bottom:1px solid #ccc;
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
<div id="p" class="easyui-panel" title="Edit Pengiriman Barang Antar Cabang" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <form id="frmMaster" action="<?php print($helper->site_url("inventory.transfer/edit/".$transfer->Id)); ?>" method="post">
        <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
            <tr>
                <td>Dari Cabang</td>
                <td><select name="CabangId" class="easyui-combobox" id="CabangId" style="width: 250px">
                        <option value=""></option>
                        <?php
                        foreach ($cabangs as $cab) {
                            if ($cab->Id == $transfer->CabangId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Tanggal</td>
                <td><input type="text" size="12" id="NpbDate" name="NpbDate" value="<?php print($transfer->FormatNpbDate(JS_DATE));?>" required/></td>
                <td>No. NPB</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="NpbNo" name="NpbNo" value="<?php print($transfer->NpbNo != null ? $transfer->NpbNo : '-'); ?>" readonly/></td>
            </tr>
            <tr>
                <td>Ke Cabang</td>
                <td><select name="ToCabangId" class="easyui-combobox" id="ToCabangId" style="width: 250px">
                        <option value=""></option>
                        <?php
                        foreach ($cabangs as $cab) {
                            if ($cab->Id == $transfer->ToCabangId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Status</td>
                <td><select class="easyui-combobox" id="NpbStatus" name="NpbStatus" style="width: 100px">
                        <option value="0" <?php print($transfer->NpbStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                        <option value="1" <?php print($transfer->NpbStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                        <option value="2" <?php print($transfer->NpbStatus == 2 ? 'selected="selected"' : '');?>>2 - Closed</option>
                        <option value="3" <?php print($transfer->NpbStatus == 3 ? 'selected="selected"' : '');?>>3 - Batal</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td colspan="3"><b><input type="text" class="f1 easyui-textbox" id="NpbDescs" name="NpbDescs" size="75" maxlength="150" value="<?php print($transfer->NpbDescs != null ? $transfer->NpbDescs : '-'); ?>" /></b></td>
            </tr>
            <tr>
                <td colspan="7">
                    <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
                        <tr>
                            <th colspan="5">DETAIL BARANG YANG DIKIRIM</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                        </tr>
                        <?php
                        $counter = 0;
                        $dta = null;
                        $dtx = null;
                        $tqy = 0;
                        foreach($transfer->Details as $idx => $detail) {
                            $counter++;
                            print("<tr>");
                            printf('<td class="right">%s.</td>', $counter);
                            printf('<td>%s</td>', $detail->ItemCode);
                            printf('<td>%s</td>', $detail->ItemDescs);
                            printf('<td class="right">%s</td>', number_format($detail->Qty,0));
                            printf('<td>%s</td>', $detail->SatBesar);
                            print("<td class='center'>");
                            $dta = addslashes($detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs));
                            $dtx = $detail->Id.'|'.$detail->ItemCode.'|'.str_replace('"',' in',$detail->ItemDescs).'|'.$detail->ItemId.'|'.$detail->Qty;
                            printf('&nbsp<img src="%s" alt="Edit barang" title="Edit barang" style="cursor: pointer" onclick="return feditdetail(%s);"/>',$bedit,"'".$dtx."'");
                            printf('&nbsp<img src="%s" alt="Hapus barang" title="Hapus barang" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bclose,"'".$dta."'");
                            print("</td>");
                            print("</tr>");
                            $tqy+= $detail->Qty;
                        }
                        ?>
                        <tr>
                            <td colspan="3" align="right">Total :</td>
                            <td class="right bold"><?php print(number_format($tqy,0));?></td>
                            <td>item(s)</td>
                            <td class='center'><?php printf('<img src="%s" alt="Tambah Barang" title="Tambah barang" id="bAdDetail" style="cursor: pointer;"/>',$badd);?></td>
                        </tr>
                        <tr>
                            <td colspan="6" align="right">
                                <?php printf('<img src="%s" alt="Simpan Data" title="Simpan data master" id="bUpdate" style="cursor: pointer;"/>',$bsubmit);?>
                                &nbsp&nbsp
                                <?php printf('<img src="%s" alt="Npb Baru" title="Buat NPB baru" id="bTambah" style="cursor: pointer;"/>',$baddnew);?>
                                &nbsp&nbsp
                                <?php printf('<img src="%s" alt="Hapus Npb" title="Proses hapus NPB" id="bHapus" style="cursor: pointer;"/>',$bdelete);?>
                                &nbsp&nbsp
                                <?php printf('<img src="%s" id="bCetak" alt="Cetak Npb" title="Proses cetak NPB" style="cursor: pointer;"/>',$bcetak);?>
                                &nbsp&nbsp
                                <?php printf('<img src="%s" id="bKembali" alt="Daftar Npb" title="Kembali ke daftar NPB" style="cursor: pointer;"/>',$bkembali);?>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </form>
</div>
<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016 - 2020 <a href='http://rekasys.com'>Rekasys Inc</a>
</div>
<!-- Form Add Npb Detail -->
<div id="dlg" class="easyui-dialog" style="width:700px;height:200px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td>Cari Data:</td>
                <td colspan="3"><input id="aItemSearch" name="aItemSearch" style="width: 350px"/></td>
            </tr>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Satuan</th>
            </tr>
            <tr>
                <td>
                    <input type="text" id="aItemCode" name="aItemCode" size="15" value="" required/>
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
            </tr>
        </table>
    </form>
    <br>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveDetail()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
<!-- </body> -->
</html>
