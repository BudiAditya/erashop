<!DOCTYPE HTML>
<html>
<head>
    <title>ERASHOP - Koreksi Stock Barang</title>
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
            //var addetail = ["aItemCode", "aCorrDate","aMaxDisc", "aHrgBeli", "aMarkup1", "aHrgJual1", "aMarkup2", "aHrgJual2", "aMarkup3", "aHrgJual3", "aMarkup4", "aHrgJual4", "aMarkup5", "aHrgJual5", "aMarkup6", "aHrgJual6"];
            //BatchFocusRegister(addetail);
            $("#aCorrDate").customDatePicker({ showOn: "focus" });
            $('#dg').datagrid({
                url: "<?php print($helper->site_url("inventory.correction/get_data"));?>",
                pageList: [10,15,30,50],
                height: 'auto',
                scrollbarSize: 0
            });
            $('#aItemSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("master.items/getjson_items"));?>",
                idField:'bid',
                textField:'bnama',
                mode:'remote',
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
        });

        function newCorrection(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Data Koreksi Stock');
            //$('#fm').form('clear');
            url= "<?php print($helper->site_url("inventory.correction/save"));?>";
        }

        function saveCorrection(){
            var aitd = Number($('#aItemId').val());
            if (aitd > 0 ){
                $('#fm').form('submit',{
                    url: url,
                    onSubmit: function(){
                        return $(this).form('validate');
                    },
                    success: function(result){
                        //var result = eval('('+result+')');
                        //if (result.errorMsg){
                        //    $.messager.show({
                        //        title: 'Error',
                        //        msg: result.errorMsg
                        //    });
                        //} else {
                            location.reload();
                            $('#dlg').dialog('close');		// close the dialog
                            $('#dg').datagrid('reload');	// reload the user data
                        //}
                    }
                });
            }else{
                alert('Data tidak valid!');
            }
        }

        function destroyCorrection(){
            var row = $('#dg').datagrid('getSelected');
            var url= "<?php print($helper->site_url("inventory.correction/hapus/"));?>"+row.id;
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
    <table id="dg" title="Daftar Opname & Koreksi Stock Barang" class="easyui-datagrid" style="width:1000px;height:500px"
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
            <th field="cabang_code" width="20">Cabang</th>
            <th field="corr_date" width="20">Tanggal</th>
            <th field="corr_no" width="25">No. Bukti</th>
            <th field="item_code" width="30" sortable="true">Kode Barang</th>
            <th field="bnama" width="55" sortable="true">Nama Barang</th>
            <th field="bsatbesar" width="15">Satuan</th>
            <th field="corr_qty" width="20" sortable="true" align="right">Koreksi</th>
        </tr>
        </thead>
    </table>
</div>
<div id="toolbar" style="padding:3px">
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newCorrection()">Baru</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyCorrection()">Hapus</a>
    &nbsp|&nbsp
    <span>Cari Data:</span>
    <select id="sfield" style="line-height:15px;border:1px solid #ccc">
        <option value=""></option>
        <option value="item_code">Kode</option>
        <option value="bnama">Nama</option>
        </select>
    <span>Isi:</span>
    <input id="scontent" size="20" maxlength="50"  style="line-height:15px;border:1px solid #ccc">
    <a href="#" class="easyui-linkbutton" plain="true" onclick="doSearch()">Cari</a>
    <a href="#" class="easyui-linkbutton" plain="true" onclick="doClear()">Clear</a>
</div>

<div id="dlg" class="easyui-dialog" style="width:900px;height:250px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="bold right">Cabang</td>
                <td><select name="aCabangId" class="easyui-combobox" id="aCabangId" style="width: 150px" required>
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
                <td class="bold right">Cari Data:</td>
                <td colspan="3"><input id="aItemSearch" name="ItemSearch" style="width: 350px"/></td>
            </tr>
            <tr>
                <td class="bold right">Kode Barang</td>
                <td>
                    <input type="text" class="bold" id="aItemCode" name="aItemCode" size="15" required/>
                    <input type="hidden" id="aItemId" name="aItemId" value="0"/>
                    <input type="hidden" id="aId" name="aId" value="0"/>
                </td>
                <td class="bold right">Tanggal</td>
                <td><input type="text" class="bold" size="10" id="aCorrDate" name="aCorrDate" value="<?php print($crDate);?>" required/></td>
            </tr>
            <tr>
                <td class="bold right">Nama Barang</td>
                <td colspan="3"><input type="text" class="bold" id="aItemDescs" name="aItemDescs" size="50" readonly/></td>
                <td class="bold right">Satuan</td>
                <td><input type="text" class="bold" id="aSatuan" name="aSatuan" size="5" readonly/></td>
            </tr>
            <tr>
                <td class="bold right">Qty Koreksi</td>
                <td class="bold"><input class="bold right" type="text" id="aCorrQty" name="aCorrQty" size="5" value="0"/>&nbsp&nbspAlasan Koreksi</td>
                <td colspan="4"><input type="text" class="bold" id="aCorrReason" name="aCorrReason" size="43" value="Selisih Stock"/></td>
            </tr>
        </table>
    </form>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveCorrection()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
<!-- </body> -->
</html>
