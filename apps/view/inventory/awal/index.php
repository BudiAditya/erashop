<!DOCTYPE HTML>
<html>
<head>
    <title>ERASHOP - Entry Stock Awal Barang</title>
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
            //var addetail = ["aItemCode", "aOpDate","aMaxDisc", "aHrgBeli", "aMarkup1", "aHrgJual1", "aMarkup2", "aHrgJual2", "aMarkup3", "aHrgJual3", "aMarkup4", "aHrgJual4", "aMarkup5", "aHrgJual5", "aMarkup6", "aHrgJual6"];
            //BatchFocusRegister(addetail);
            $("#aOpDate").customDatePicker({ showOn: "focus" });
            $('#dg').datagrid({
                url: "<?php print($helper->site_url("inventory.awal/get_data"));?>",
                pageList: [10,15,30,50],
                height: 'auto',
                scrollbarSize: 0
            });
            $('#aItemSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("inventory.awal/getjson_items"));?>",
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
                var url = "<?php print($helper->site_url("inventory.awal/getplain_items/"));?>"+itc;
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

        function newOpBal(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Data Stock Awal');
            //$('#fm').form('clear');
            url= "<?php print($helper->site_url("inventory.awal/save"));?>";
        }

        function saveOpBal(){
            var aitd = Number($('#aItemId').val());
            if (aitd > 0 ){
                $('#fm').form('submit',{
                    url: url,
                    onSubmit: function(){
                        return $(this).form('validate');
                    },
                    success: function(result){
                        //var retval = eval('('+result+')');
                        //if (retval.errorMsg){
                        //    $.messager.show({
                        //        title: 'Error',
                        //        msg: retval.errorMsg
                        //    });
                        //} else {
                        //    alert(retval);
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

        function destroyOpBal(){
            var row = $('#dg').datagrid('getSelected');
            var url= "<?php print($helper->site_url("inventory.awal/hapus/"));?>"+row.id;
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
    <table id="dg" title="Stock Awal Barang" class="easyui-datagrid" style="width:800px;height:500px"
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
            <th field="op_date" width="20">Tanggal</th>
            <th field="item_code" width="30" sortable="true">Kode Barang</th>
            <th field="bnama" width="55" sortable="true">Nama Barang</th>
            <th field="bsatbesar" width="15">Satuan</th>
            <th field="op_qty" width="20" sortable="true" align="right">Qty</th>
        </tr>
        </thead>
    </table>
</div>
<div id="toolbar" style="padding:3px">
    <?php
    if($acl->CheckUserAccess("inventory.awal", "add")){
    ?>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newOpBal()">Baru</a>
    <?php }
    if($acl->CheckUserAccess("inventory.awal", "delete")){
    ?>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyOpBal()">Hapus</a>
    &nbsp|&nbsp
    <?php } ?>
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

<div id="dlg" class="easyui-dialog" style="width:700px;height:250px;padding:5px 5px"
     closed="true" buttons="#dlg-buttons">
    <form id="fm" method="post" novalidate>
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="bold right">Cari Data</td>
                <td colspan="5"><input id="aItemSearch" name="ItemSearch" style="width: 350px"/></td>
            </tr>
            <tr>
                <td class="bold right">Kode</td>
                <td>
                    <input type="text" class="bold" id="aItemCode" name="item_code" size="20" required/>
                    <input type="hidden" id="aItemId" name="item_id" value="0"/>
                    <input type="hidden" id="aId" name="id" value="0"/>
                </td>
                <td class="bold right">Tanggal</td>
                <td><input type="text" class="bold" size="10" id="aOpDate" name="op_date" value="<?php print($crDate);?>" required/></td>
            </tr>
            <tr>
                <td class="bold right">Nama</td>
                <td colspan="3"><input type="text" class="bold" id="aItemDescs" name="item_name" size="50" readonly/></td>
                <td class="bold right">Satuan</td>
                <td><input type="text" class="bold" id="aSatuan" name="item_uom" size="5" readonly/></td>
            </tr>
            <tr>
                <td class="bold right">Qty</td>
                <td><input class="bold right" type="text" id="aOpQty" name="op_qty" size="5" value="0"/></td>
            </tr>
        </table>
    </form>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveOpBal()" style="width:90px">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Batal</a>
</div>
<!-- </body> -->
</html>
