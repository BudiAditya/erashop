<!DOCTYPE HTML>
<html>
<?php
/** @var $assembly Assembly */
?>
<head>
    <title>ERASYS - View Proses Produksi</title>
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
            //var addetail = ["aItemSearch", "aItemCode", "aQty","aPrice", "aDiscFormula", "aDiscAmount", "aSubTotal", "bSaveDetail"];
            //BatchFocusRegister(addetail);
            //var addmaster = ["CabangId", "AssemblyDate","CustomerId", "SalesId", "AssemblyDescs", "aItemMasterId","aItemMasterCode","aItemMasterQty","Disc1Pct","Disc1Amount","TaxPct","TaxAmount","OtherCosts","OtherCostsAmount","TotalAmount","bUpdate","bKembali"];
            //BatchFocusRegister(addmaster);
            $("#AssemblyDate").customDatePicker({ showOn: "focus" });

            $("#bEdit").click(function(){
                if (confirm('Ubah Data Proses Produksi?')){
                    location.href="<?php print($helper->site_url("inventory.assembly/add/".$assembly->Id)); ?>";
                }
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
                if (confirm('Cetak PDF Bukti Produksi ini?')){
                    window.open("<?php print($helper->site_url("inventory.assembly/assembly_print/?&id[]=").$assembly->Id); ?>");
                }
            });

            $("#bKembali").click(function(){
                location.href="<?php print($helper->site_url("inventory.assembly")); ?>";
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
<div id="p" class="easyui-panel" title="View Proses Produksi" style="width:100%;height:100%;padding:10px;" data-options="footer:'#ft'">
    <table cellpadding="0" cellspacing="0" class="tablePadding" align="left" style="font-size: 13px;font-family: tahoma">
        <tr>
            <td>Cabang</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($assembly->CabangCode != null ? $assembly->CabangCode : $userCabCode); ?>" disabled/></td>
            <td>Tanggal</td>
            <td><input type="text" size="12" id="AssemblyDate" name="AssemblyDate" value="<?php print($assembly->FormatAssemblyDate(JS_DATE));?>"  <?php print($assembly->AssemblyStatus > 0 ? 'disabled' : 'required');?>/></td>
            <td>No. Produksi</td>
            <td><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 150px" id="AssemblyNo" name="AssemblyNo" value="<?php print($assembly->AssemblyNo != null ? $assembly->AssemblyNo : '-'); ?>" readonly/></td>
            <td>Status</td>
            <td><select id="AssemblyStatus" name="AssemblyStatus" disabled>
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
            </td>
            <td colspan="9"><input class="bold" type="text" id="aItemMasterCode" name="aItemMasterCode" size="15" value="<?php print($assembly->ItemCode);?>" readonly/>
                &nbsp;
                Nama Barang
                <input class="bold" type="text" id="aItemMasterName" name="aItemMasterName" size="40" value="<?php print($assembly->ItemName);?>" disabled/>
                &nbsp;
                QTY :
                <input class="bold right" type="text" id="aItemMasterQty" name="aItemMasterQty" size="5" value="<?php print($assembly->Qty);?>" readonly/>
                <input class="bold" type="text" id="aItemMasterSatuan" name="aItemMasterSatuan" size="3" value="<?php print($assembly->ItemSatuan);?>" disabled/>
                HPP :
                <input class="bold right" type="text" id="aItemMasterPrice" name="aItemMasterPrice" size="8" value="<?php print($assembly->Price == '' ? 0 : $assembly->Price);?>" readonly/>
                Total :
                <input class="bold right" type="text" id="aItemMasterTotalPrice" name="aItemMasterTotalPrice" size="10" value="<?php print($assembly->Price == '' ? 0 : number_format(round($assembly->Price * $assembly->Qty,0),0));?>" readonly/>
            </td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="left" style="font-size: 12px;font-family: tahoma">
        <tr>
            <th colspan="7">DETAIL BARANG YANG DIPAKAI</th>
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
            print("</tr>");
            $total += round($detail->Qty * $detail->Price,0);
        }
        ?>
        <tr>
            <td colspan="6" class="bold right">Nilai Produksi</td>
            <td class="bold right"><?php print(number_format($total,0));?></td>
        </tr>
        <tr>
            <td colspan="7" class="right">
                <?php
                if ($acl->CheckUserAccess("inventory.assembly", "add")) {
                    printf('<img src="%s" alt="Data Baru" title="Buat Data Baru" id="bTambah" style="cursor: pointer;"/> &nbsp',$baddnew);
                }
                if ($acl->CheckUserAccess("inventory.assembly", "edit")) {
                    printf('<img src="%s" alt="Edit Data" title="Ubah Data Produksi" id="bEdit" style="cursor: pointer;"/> &nbsp',$bedit);
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
    Copyright &copy; 2016 - 2020 <a href='http://rekasys.com'>Rekasys Inc</a>
</div>
<!-- </body> -->
</html>
