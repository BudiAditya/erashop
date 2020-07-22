<!DOCTYPE HTML>
<html>
<head>
    <title>ERASHOP - Entry Satuan Barang</title>
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
            $('#dg').datagrid({
                pageList: [10,15,30,50],
                height: 'auto',
                scrollbarSize: 0
            });
        });

        function newUom(){
            $('#dlg').dialog('open').dialog('setTitle','Tambah Data');
            $('#fm').form('clear');
            url= "<?php print($helper->site_url("master.itemuom/save"));?>";
        }
        function editUom(){
            var row = $('#dg').datagrid('getSelected');
            if (row){
                $('#dlg').dialog('open').dialog('setTitle','Edit Data');
                $('#fm').form('load',row);
                url= "<?php print($helper->site_url("master.itemuom/update/"));?>"+row.sid;
            }
        }
        function saveUom(){
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
                        $('#dlg').dialog('close');		// close the dialog
                        $('#dg').datagrid('reload');	// reload the user data
                    }
                }
            });
        }
        function destroyUom(){
            var row = $('#dg').datagrid('getSelected');
            var url= "<?php print($helper->site_url("master.itemuom/hapus/"));?>"+row.sid;
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
<?php include(VIEW . "main/menu.php"); ?>
<div align="center">
    <table id="dg" title="Daftar Satuan Barang" class="easyui-datagrid" style="width:700px;height:500px"
           url="<?php print($helper->site_url("master.itemuom/get_data"));?>"
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
            <th field="skode" width="50" sortable="true">Kode</th>
            <th field="snama" width="100" sortable="true">Satuan</th>
        </tr>
        </thead>
    </table>
</div>
<div id="toolbar" style="padding:3px">
    <?php
    if($acl->CheckUserAccess("master.itemuom", "add")){ ?>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newUom()">Baru</a>
    <?php }
    if($acl->CheckUserAccess("master.itemuom", "edit")){ ?>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editUom()">Ubah</a>
    <?php }
    if($acl->CheckUserAccess("master.itemuom", "delete")){ ?>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyUom()">Hapus</a>
        &nbsp|&nbsp
    <?php } ?>
    <span>Cari Data:</span>
    <select id="sfield" style="line-height:15px;border:1px solid #ccc">
        <option value=""></option>
        <option value="skode">Kode</option>
        <option value="snama">Satuan</option>
        </select>
    <span>Isi:</span>
    <input id="scontent" size="20" maxlength="50"  style="line-height:15px;border:1px solid #ccc">
    <a href="#" class="easyui-linkbutton" plain="true" onclick="doSearch()">Cari</a>
    <a href="#" class="easyui-linkbutton" plain="true" onclick="doClear()">Clear</a>
</div>

<div id="dlg" class="easyui-dialog" style="width:500px;height:280px;padding:10px 20px"
     closed="true" buttons="#dlg-buttons">
    <div class="ftitle">Data Satuan</div>
    <form id="fm" method="post" novalidate>
        <div class="fitem">
            <label>Kode:</label>
            <input name="skode" class="easyui-textbox" required="true">
        </div>
        <div class="fitem">
            <label>Satuan:</label>
            <input name="snama" class="easyui-textbox" required="true">
        </div>
    </form>
</div>
<div id="dlg-buttons">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveUom()" style="width:90px">Save</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>
<!-- </body> -->
</html>
