<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $receipt Receipt */ /** @var $banks Bank[] */ /** @var $warkattypes WarkatType[] */
?>
<head>
	<title>ERASYS - Entry Penerimaan Piutang</title>
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

            //var addmaster = ["CabangId", "ReceiptDate","DebtorId", "SalesId", "ReceiptDescs", "PaymentType","CreditTerms","btSubmit", "btKembali"];
            //BatchFocusRegister(addmaster);

            $("#ReceiptDate").customDatePicker({ showOn: "focus" });
            $("#WarkatDate").customDatePicker({ showOn: "focus" });
            $('#DebtorId').combogrid({
                panelWidth:600,
                url: "<?php print($helper->site_url("master.contacts/getjson_contacts/1/".$userCompId));?>",
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
                    var dbi = row.id;
                    console.log(dbi);
                    $("#WarkatDate").val('');
                    $("#ReceiptAmount").val(0);
                    var urz = "<?php print($helper->site_url("ar.arreturn/getjson_returnlists/".$receipt->CabangId.'/'));?>"+dbi;
                    $('#ReturnNo').combogrid('grid').datagrid('load',urz);
                    $("#ReturnNo").val('');
                }
            });

            $('#ReturnNo').combogrid({
                panelWidth:350,
                url: "<?php print($helper->site_url("ar.arreturn/getjson_returnlists/".$receipt->CabangId.'/0'));?>",
                idField:'rj_no',
                textField:'rj_no',
                mode:'remote',
                fitColumns:true,
                columns:[[
                    {field:'rj_no',title:'No. Retur',width:60},
                    {field:'rj_date',title:'Tanggal',width:50},
                    {field:'rj_balance',title:'Nilai',width:50}
                ]],
                onSelect: function(index,row){
                    var rjd = row.rj_date;
                    console.log(rjd);
                    var rjb = row.rj_balance;
                    console.log(rjb);
                    $("#WarkatDate").val(rjd);
                    $("#ReceiptAmount").val(rjb);
                    $("#BalanceAmount").val(rjb);
                }
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
<div id="p" class="easyui-panel" title="Entry Penerimaan Piutang" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <form id="frmMaster" action="<?php print($helper->site_url("ar.receipt/add")); ?>" method="post">
        <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
            <tr>
                <td>Cabang</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($receipt->CabangCode != null ? $receipt->CabangCode : $userCabCode); ?>" disabled/>
                    <input type="hidden" id="CabangId" name="CabangId" value="<?php print($receipt->CabangId == null ? $userCabId : $receipt->CabangId);?>"/>
                </td>
                <td>Tanggal</td>
                <td><input type="text" size="12" id="ReceiptDate" name="ReceiptDate" value="<?php print($receipt->FormatReceiptDate(JS_DATE));?>" required/></td>
                <td>No. Receipt</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="ReceiptNo" name="ReceiptNo" value="<?php print($receipt->ReceiptNo != null ? $receipt->ReceiptNo : '-'); ?>" readonly/></td>
            </tr>
            <tr>
                <td>Customer</td>
                <td><input class="easyui-combogrid" id="DebtorId" name="DebtorId" style="width: 250px" value="<?php print($receipt->DebtorId);?>"/></td>
                <td>Cara Bayar</td>
                <td><select class="easyui-combobox" id="WarkatTypeId" name="WarkatTypeId" style="width: 100px">
                        <?php
                        foreach ($warkattypes as $wti) {
                            if ($wti->Id == $receipt->WarkatTypeId) {
                                printf('<option value="%d" selected="selected">%s</option>', $wti->Id, $wti->Type);
                            } else {
                                printf('<option value="%d">%s</option>', $wti->Id, $wti->Type);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Kas/Bank</td>
                <td><select class="easyui-combobox" id="WarkatBankId" name="WarkatBankId" style="width: 150px">
                        <?php
                        foreach ($banks as $bank) {
                            if ($bank->Id == $receipt->WarkatBankId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $bank->Id, $bank->Id, $bank->Name);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $bank->Id, $bank->Id, $bank->Name);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>No. Retur</td>
                <td><input class="easyui-combogrid" style="width: 150px" id="ReturnNo" name="ReturnNo" value="<?php print($receipt->ReturnNo); ?>"/></td>
            </tr>
            <tr>
                <td>No. Warkat</td>
                <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="WarkatNo" name="WarkatNo" value="<?php print($receipt->WarkatNo); ?>"/></td>
                <td>Tgl. Warkat</td>
                <td><input type="text" size="12" id="WarkatDate" name="WarkatDate" value="<?php print($receipt->FormatWarkatDate(JS_DATE));?>"/></td>
                <td>Jumlah</td>
                <td><input type="text" class="right bold" style="width: 120px" id="ReceiptAmount" name="ReceiptAmount" value="<?php print($receipt->ReceiptAmount != null ? $receipt->ReceiptAmount : 0); ?>"/></td>
                <td>Alokasi</td>
                <td><input type="text" class="right bold" style="width: 120px" id="AllocateAmount" name="AllocateAmount" value="<?php print($receipt->AllocateAmount != null ? $receipt->AllocateAmount : 0); ?>" readonly/></td>
                <td>Sisa</td>
                <td><input type="text" class="right bold" style="width: 120px" id="BalanceAmount" name="BalanceAmount" value="<?php print($receipt->BalanceAmount != null ? $receipt->BalanceAmount : 0); ?>" readonly/></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td colspan="3"><b><input type="text" class="f1 easyui-textbox" id="ReceiptDescs" name="ReceiptDescs" size="80" maxlength="150" value="<?php print($receipt->ReceiptDescs != null ? $receipt->ReceiptDescs : '-'); ?>" required/></b></td>
                <td>Status</td>
                <td><select class="easyui-combobox" id="ReceiptStatus" name="ReceiptStatus" style="width: 100px" readonly>
                        <option value="0" <?php print($receipt->ReceiptStatus == 0 ? 'selected="selected"' : '');?>>0 - Draft</option>
                        <option value="1" <?php print($receipt->ReceiptStatus == 1 ? 'selected="selected"' : '');?>>1 - Posted</option>
                        <option value="2" <?php print($receipt->ReceiptStatus == 2 ? 'selected="selected"' : '');?>>2 - Approved</option>
                        <option value="3" <?php print($receipt->ReceiptStatus == 3 ? 'selected="selected"' : '');?>>3 - Batal</option>
                    </select>
                </td>
                <td align="left" colspan="4">
                    <a id="btKembali" href="<?php print($helper->site_url("ar.receipt")); ?>" class="button">Kembali</a>
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
