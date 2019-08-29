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

        $(document).ready(function() {

            var addmaster = ["CabangId", "PoDate","RequestDate","SupplierId", "SalesName", "PoDescs", "PaymentType","CreditTerms","btSubmit", "btKembali"];
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
        });

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
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<div id="p" class="easyui-panel" title="Entry Order Pembelian (PO)" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <form id="frmMaster" action="<?php print($helper->site_url("ap.order/add")); ?>" method="post">
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
                <td><input class="easyui-combogrid" id="SupplierId" name="SupplierId" style="width: 250px"/></td>
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
                <td colspan="6" align="right">
                    <a id="btKembali" href="<?php print($helper->site_url("ap.order")); ?>" class="button">Kembali</a>
                    <button id="btSubmit" type="submit">Berikutnya &gt;</button>
                </td>
            </tr>
        </table>
    </form>
</div>

<div id="ft" style="padding:5px; text-align: center; font-family: verdana; font-size: 9px" >
    Copyright &copy; 2016  CV. Erasystem Infotama
</div>
</body>
</html>
