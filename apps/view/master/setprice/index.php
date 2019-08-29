<!DOCTYPE HTML>
<html>
<head>
    <title>ERASHOP - Daftar Harga Barang</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/default/easyui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/icon.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/color.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-demo/demo.css")); ?>"/>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>
    <script type="text/javascript">
        $(function(){
            var addetail = ["aItemCode", "aPriceDate","aMaxDisc", "aHrgBeli", "aMarkup1", "aHrgJual1", "aMarkup2", "aHrgJual2", "aMarkup3", "aHrgJual3", "aMarkup4", "aHrgJual4", "aMarkup5", "aHrgJual5", "aMarkup6", "aHrgJual6"];
            BatchFocusRegister(addetail);
            $("#aPriceDate").customDatePicker({ showOn: "focus" });
            $('#dg').datagrid({
                url: "<?php print($helper->site_url("master.setprice/get_data"));?>",
                pageList: [10,15,30,50],
                height: 'auto',
                scrollbarSize: 0
            });
            $('#aItemSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("master.items/getjson_items"));?>",
                idField:'bid',
                textField:'bid',
                mode:'get',
                fitColumns:true,
                columns:[[
                    {field:'bkode',title:'Kode',width:50,sortable:true},
                    {field:'bnama',title:'Nama Barang',sortable:true,width:150},
                    {field:'bsatbesar',title:'Satuan',width:40}
                ]],
                onSelect: function(index,row){
                    var bid = row.bid;
                    console.log(bid);
                    var bkode = row.bkode;
                    console.log(bkode);
                    var bnama = row.bnama;
                    console.log(bnama);
                    var satuan = row.bsatbesar;
                    console.log(satuan);
                    $('#aItemId').val(bid);
                    $('#aItemCode').val(bkode);
                    $('#aItemDescs').val(bnama);
                    $('#aSatuan').val(satuan);
                }
            });

            $("#aItemCode").change(function(e){
                //$ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$items->Bqtystock.'|'.$items->Bhargabeli.'|'.$items->Bhargajual;
                var itc = $("#aItemCode").val();
                var url = "<?php print($helper->site_url("master.items/getplain_items/"));?>"+itc;
                if (itc != ''){
                    $.get(url, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#aItemId').val(dtx[1]);
                                $('#aItemDescs').val(dtx[2]);
                                $('#aSatuan').val(dtx[3]);
                            }
                        }
                    });
                }
            });

            $("#aHrgBeli").change(function(e){hitMarkup();});
            $("#aMarkup1").change(function(e){hitMarkup();});
            $("#aMarkup2").change(function(e){hitMarkup();});
            $("#aMarkup3").change(function(e){hitMarkup();});
            $("#aMarkup4").change(function(e){hitMarkup();});
            $("#aMarkup5").change(function(e){hitMarkup();});
            $("#aMarkup6").change(function(e){hitMarkup();});
        });

        function newPrice(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Data Harga Barang');
            $('#fm').form('clear');
            url= "<?php print($helper->site_url("master.setprice/save"));?>";
        }

        function copyPrice(){
            $('#dlg1').dialog('open').dialog('setTitle','Proses Salin Data Harga Barang');
            $('#fm1').form('clear');
            url= "<?php print($helper->site_url("master.setprice/copy_data"));?>";
        }

        function editPrice(){
            var row = $('#dg').datagrid('getSelected');
            if (row){
                $('#dlg').dialog('open').dialog('setTitle','Edit Data Harga Barang');
                $('#fm').form('load',row);
                url= "<?php print($helper->site_url("master.setprice/update/"));?>"+row.id;
                var itc = $("#aItemCode").val();
                var urx = "<?php print($helper->site_url("master.items/getplain_items/"));?>"+itc;
                if (itc != ''){
                    $.get(urx, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#aItemDescs').val(dtx[2]);
                                $('#aSatuan').val(dtx[3]);
                            }
                        }
                    });
                }
            }
        }
        function savePrice(){
            var aitd = Number($('#aItemId').val());
            if (aitd > 0 ){
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
                            $('#dg').datagrid('reload');	// reload the user data
                        }
                    }
                });
            }else{
                alert('Data tidak valid!');
            }
        }

        function processCopy(){
            var fcbi = $('#frCabangId').val();
            var tcbi = $('#toCabangId').val();
            //alert('Dari: '+fcbi+' Ke: '+tcbi);
            if (fcbi != tcbi && (fcbi > 0 && tcbi >0)){
                if (confirm('PERHATIAN: Data harga dicabang tujuan akan ditimpa!\n\nApakah Anda akan melakukan proses ini?')){
                    $('#fm1').form('submit',{
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
                                $('#dg').datagrid('reload');	// reload the user data
                                alert('Proses Copy harga berhasil!')
                                $('#dlg1').dialog('close');		// close the dialog
                            }
                        }
                    });
                }
            }else{
                alert('Data tidak valid!');
            }
        }

        function destroyPrice(){
            var row = $('#dg').datagrid('getSelected');
            var url= "<?php print($helper->site_url("master.setprice/hapus/"));?>"+row.id;
            if (row){
                $.messager.confirm('Confirm','Anda yakin akan menghapus data ini?',function(r){
                    if (r){
                        $.post(url,{id:row.id},function(result){
                            if (result.success){
                                $('#dg').datagrid('reload');	// reload the user data
                            } else {
                                $.messager.show({	// show error message
                                    title: 'Error',
                                    msg: result.errorMsg
                                });
                            }
                        },'json');
                    }
                });
            }
        }
        function doSearch(){
            $('#dg').datagrid('load',{
                sfield: $('#sfield').val(),
                scontent: $('#scontent').val()
            });
        }
        function doClear(){
            $('#sfield').val('');
            $('#scontent').val('');
            doSearch();
        }

        function formatPrice(num,row){
            return Number(num).toLocaleString();
        }

        function hitMarkup(){
            var hBeli = Number($("#aHrgBeli").val());
            var mUp1 = Number($("#aMarkup1").val());
            var mUp2 = Number($("#aMarkup2").val());
            var mUp3 = Number($("#aMarkup3").val());
            var mUp4 = Number($("#aMarkup4").val());
            var mUp5 = Number($("#aMarkup5").val());
            var mUp6 = Number($("#aMarkup6").val());
            if(mUp1 > 0){
              $("#aHrgJual1").val(hBeli + Math.ceil(hBeli * (mUp1/100)));
            }else{
              $("#aHrgJual1").val(hBeli);
            }
            if(mUp2 > 0){
                $("#aHrgJual2").val(hBeli + Math.ceil(hBeli * (mUp2/100)));
            }else{
                $("#aHrgJual2").val(hBeli);
            }
            if(mUp3 > 0){
                $("#aHrgJual3").val(hBeli + Math.ceil(hBeli * (mUp3/100)));
            }else{
                $("#aHrgJual3").val(hBeli);
            }
            if(mUp4 > 0){
                $("#aHrgJual4").val(hBeli + Math.ceil(hBeli * (mUp4/100)));
            }else{
                $("#aHrgJual4").val(hBeli);
            }
            if(mUp5 > 0){
                $("#aHrgJual5").val(hBeli + Math.ceil(hBeli * (mUp5/100)));
            }else{
                $("#aHrgJual5").val(hBeli);
            }
            if(mUp6 > 0){
                $("#aHrgJual6").val(hBeli + Math.ceil(hBeli * (mUp6/100)));
            }else{
                $("#aHrgJual6").val(hBeli);
            }
        }

    </script>
    <style type="text/css">
        #fm{
            margin:0;
            padding:10px 30px;
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
            width:80px;
        }
        .fitem input{
            width:160px;
        }
    </style>
</head>

<body>
<?php include(VIEW . "main/menu.php");
$crDate = date(JS_DATE, strtotime(date('Y-m-d')));
?>
<div align="left">
    <table id="dg" title="Daftar Harga Barang" class="easyui-datagrid" style="width:100%;height:500px"
           toolbar="#toolbar"
           pagination="true"
           rownumbers="true"
           fitColumns="true"
           striped="true"
           singleSelect="true"
           showHeader="true"
           showFooter="true"
        >
        <thead>
        <tr>
            <th field="cabang_code" width="20">Sumber</th>
            <th field="price_date" width="15">Tanggal</th>
            <th field="item_code" width="20" sortable="true">Kode Barang</th>
            <th field="item_name" width="50" sortable="true">Nama Barang</th>
            <th field="satuan" width="10">Satuan</th>
            <?php if ($userLevel > 1){ ?>
            <th field="hrg_beli" width="15" sortable="true" align="right" formatter="formatPrice">Hrg Beli</th>
            <th field="hrg_jual1" width="15" sortable="true" align="right" formatter="formatPrice">Hrg Jual1</th>
            <th field="hrg_jual2" width="15" align="right" formatter="formatPrice">Hrg Jual2</th>
            <th field="hrg_jual3" width="15" align="right" formatter="formatPrice">Hrg Jual3</th>
            <th field="hrg_jual4" width="15" align="right" formatter="formatPrice">Hrg Jual4</th>
            <th field="hrg_jual5" width="15" align="right" formatter="formatPrice">Hrg Jual5</th>
            <th field="hrg_jual6" width="15" align="right" formatter="formatPrice">Hrg Jual6</th>
            <?php }else{ ?>
            <th field="hrg_jual1" width="15" sortable="true" align="right" formatter="formatPrice">Harga Jual</th>
            <?php } ?>
            <th field="supplier_name" width="20">Supplier</th>
        </tr>
        </thead>
    </table>
</div>
<div id="toolbar" style="padding:3px">
    <?php if ($userLevel > 1){ ?>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newPrice()">Baru</a>
   <!-- <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="copyPrice()">Copy</a> -->
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editPrice()">Ubah</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyPrice()">Hapus</a>
    &nbsp|&nbsp
    <?php } ?>
    <span>Cari Data:</span>
    <select id="sfield" style="line-height:15px;border:1px solid #ccc">
        <option value=""></option>
        <option value="item_code">Kode Barang</option>
        <option value="item_name">Nama Barang</option>
        <option value="supplier_name">Supplier</option>
        </select>
    <span>Isi:</span>
    <input id="scontent" size="20" maxlength="50"  style="line-height:15px;border:1px solid #ccc">
    <a href="#" class="easyui-linkbutton" plain="true" onclick="doSearch()">Cari</a>
    <a href="#" class="easyui-linkbutton" plain="true" onclick="doClear()">Clear</a>
</div>

<div id="dlg" class="easyui-dialog" style="width:700px;height:400px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="bold right">Cabang</td>
                <td colspan="3"><select name="CabangId" class="easyui-combobox" id="CabangId" style="width: 150px" required>
                        <?php
                        if($userLevel > 3){
                            foreach ($cabangs as $cab) {
                                if ($cab->Id == $userCabId) {
                                    printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                                } else {
                                    printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                                }
                            }
                        }else{
                            printf('<option value="%d">%s - %s</option>', $userCabId, $userCabCode, $userCabName);
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="bold right">Kode</td>
                <td>
                    <input type="text" class="bold" id="aItemCode" name="bkode" size="15" required/>
                    <input id="aItemSearch" name="ItemSearch" style="width: 20px"/>
                    <input type="hidden" id="aItemId" name="bid" value="0"/>
                    <input type="hidden" id="aId" name="id" value="0"/>
                </td>
                <td class="bold right">Tanggal</td>
                <td><input type="text" class="bold" size="10" id="aPriceDate" name="price_date" value="<?php print($crDate);?>" required/></td>
            </tr>
            <tr>
                <td class="bold right">Nama</td>
                <td colspan="3"><input type="text" class="bold" id="aItemDescs" name="bnama" size="50" readonly/></td>
                <td class="bold right">Satuan</td>
                <td><input type="text" class="bold" id="aSatuan" name="item_uom" size="5" readonly/></td>
            </tr>
            <tr>
                <td class="bold right">Max Discount</td>
                <td><input class="bold right" type="text" id="aMaxDisc" name="max_disc" size="3" value="0"/>%</td>
                <td class="bold right">Harga Beli</td>
                <td><input class="bold right" type="text" id="aHrgBeli" name="hrg_beli" size="10" value="0"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up1</td>
                <td><input class="bold right" type="text" id="aMarkup1" name="markup1" size="3" value="0"/>%</td>
                <td class="bold right">Harga Jual1</td>
                <td><input class="bold right" type="text" id="aHrgJual1" name="hrg_jual1" size="10" value="0"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up2</td>
                <td><input class="bold right" type="text" id="aMarkup2" name="markup2" size="3" value="0"/>%</td>
                <td class="bold right">Harga Jual2</td>
                <td><input class="bold right" type="text" id="aHrgJual2" name="hrg_jual2" size="10" value="0"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up3</td>
                <td><input class="bold right" type="text" id="aMarkup3" name="markup3" size="3" value="0"/>%</td>
                <td class="bold right">Harga Jual3</td>
                <td><input class="bold right" type="text" id="aHrgJual3" name="hrg_jual3" size="10" value="0"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up4</td>
                <td><input class="bold right" type="text" id="aMarkup4" name="markup4" size="3" value="0"/>%</td>
                <td class="bold right">Harga Jual4</td>
                <td><input class="bold right" type="text" id="aHrgJual4" name="hrg_jual4" size="10" value="0"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up5</td>
                <td><input class="bold right" type="text" id="aMarkup5" name="markup5" size="3" value="0"/>%</td>
                <td class="bold right">Harga Jual5</td>
                <td><input class="bold right" type="text" id="aHrgJual5" name="hrg_jual5" size="10" value="0"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up6</td>
                <td><input class="bold right" type="text" id="aMarkup6" name="markup6" size="3" value="0"/>%</td>
                <td class="bold right">Harga Jual6</td>
                <td><input class="bold right" type="text" id="aHrgJual6" name="hrg_jual6" size="10" value="0"/></td>
            </tr>
        </table>
    </form>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="savePrice()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
<div id="dlg1" class="easyui-dialog" style="width:570px;height:130px;padding:5px 5px"
     closed="true" buttons="#dlg1-buttons">
    <form id="fm1" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="bold right">Dari Cabang</td>
                <td><select name="frCabangId" id="frCabangId" style="width: 150px" required>
                        <?php
                        if($userLevel > 3){
                            foreach ($cabangs as $cab) {
                                if ($cab->Id == $userCabId) {
                                    printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                                } else {
                                    printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                                }
                            }
                        }else{
                            printf('<option value="%d">%s - %s</option>', $userCabId, $userCabCode, $userCabName);
                        }
                        ?>
                    </select>
                </td>
                <td class="bold right">Ke Cabang</td>
                <td><select name="toCabangId" id="toCabangId" style="width: 150px" required>
                        <?php
                        if($userLevel > 3){
                            print('<option value="0"></option>');
                            foreach ($cabangs as $cab) {
                                if ($cab->Id == $userCabId) {
                                    printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                                } else {
                                    printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                                }
                            }
                        }else{
                            printf('<option value="%d">%s - %s</option>', $userCabId, $userCabCode, $userCabName);
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</div>
<div id="dlg1-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="processCopy()" style="width:90px">Proses</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg1').dialog('close')" style="width:90px">Tutup</a>
</div>
</body>
</html>
