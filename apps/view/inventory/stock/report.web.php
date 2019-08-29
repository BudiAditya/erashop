<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>ERASHOP - Rekapitulasi Stock Barang</title>
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

            $('#SupplierCode').combogrid({
                panelWidth:600,
                url: "<?php print($helper->site_url("master.contacts/getjson_contacts/2"));?>",
                idField:'contact_code',
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
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="left">
            <th colspan="10"><b>REKAPITULASI STOCK BARANG: <?php print($company_name);?></b></th>
        </tr>
        <tr class="center">
            <th>Cabang/Gudang</th>
            <th>Jenis Barang</th>
            <th>Supplier</th>
            <th>Type Harga</th>
            <th>Output</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>
                <select name="CabangId" class="text2" id="CabangId" required>
                <?php
                    printf('<option value="%d">%s</option>', $userCabId, $userCabCode);
                ?>
                </select>
            </td>
            <td>
                <select name="JenisBarang" class="text2" id="JenisBarang" required>
                   <option value="-">- Semua Jenis Barang-</option>
                    <?php
                    foreach ($jenis as $jns) {
                        if ($jns->JnsBarang == $userJenisBarang) {
                            printf('<option value="%s" selected="selected">%s</option>', $jns->JnsBarang, $jns->JnsBarang);
                        } else {
                            printf('<option value="%s">%s</option>', $jns->JnsBarang, $jns->JnsBarang);
                        }
                    }
                    ?>
                </select>
            </td>
            <td><input class="easyui-combogrid" id="SupplierCode" name="SupplierCode" value="<?php print($userSupplierCode);?>" style="width: 250px"/></td>
            <td>
                <select id="TypeHarga" name="TypeHarga" required>
                    <option value="1" <?php print($userTypeHarga == 1 ? 'selected="selected"' : '');?>>Harga Beli/HPP</option>
                    <option value="2" <?php print($userTypeHarga == 2 ? 'selected="selected"' : '');?>>Harga Jual</option>
                </select>
            </td>
            <td>
                <select id="Output" name="Output" required>
                    <option value="0" <?php print($output == 0 ? 'selected="selected"' : '');?>>0 - Web Html</option>
                    <option value="1" <?php print($output == 1 ? 'selected="selected"' : '');?>>1 - Excel</option>
                </select>
            </td>
            <td><button type="submit" formaction="<?php print($helper->site_url("inventory.stock/report")); ?>"><b>Proses</b></button>
                <a href="<?php print($helper->site_url("inventory.stock")); ?>">Daftar Stock</a>
            </td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($reports != null){
    $ket = null;
    if ($scabangCode != null){
        $ket.= 'Cabang/Gudang: '.$scabangCode;
    }else{
        $ket.= 'Semua Cabang/Gudang';
    }
    if ($userJenisBarang != '-'){
        $ket.= ' - Jenis Barang : '.$userJenisBarang;
    }
    print('<h2>Rekapitulasi Stock Barang</h2>');
    if ($ket != null){
        printf('<h3>%s</h3>',$ket);
    }
?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>No.</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Qty Stock</th>
            <?php
            if($userTypeHarga == 1){
                print('<th>Harga Beli</th>');
            }else{
                print('<th>Harga Jual</th>');
            }
            ?>
            <th>Nilai Stock</th>
            <?php
            if ($userSupplierCode <> null){
                print('<th>Supplier</th>');
            }
            ?>
        </tr>
        <?php
            $nmr = 1;
            $tOtal = 0;
            while ($row = $reports->FetchAssoc()) {
                print("<tr valign='Top'>");
                printf("<td>%s</td>",$nmr);
                printf("<td nowrap='nowrap'>%s</td>",$row["item_code"]);
                printf("<td nowrap='nowrap'>%s</td>",$row["bnama"]);
                printf("<td nowrap='nowrap'>%s</td>",$row["bsatbesar"]);
                printf("<td align='right'>%s</td>",number_format($row["qty_stock"],0));
                if ($userTypeHarga == 1) {
                    printf("<td align='right'>%s</td>", number_format($row["hrg_beli"], 0));
                    printf("<td align='right'>%s</td>", number_format(round($row["qty_stock"] * $row["hrg_beli"], 0), 0));
                    $tOtal+= round($row["qty_stock"] * $row["hrg_beli"],0);
                }else{
                    printf("<td align='right'>%s</td>", number_format($row["hrg_jual"], 0));
                    printf("<td align='right'>%s</td>", number_format(round($row["qty_stock"] * $row["hrg_jual"], 0), 0));
                    $tOtal+= round($row["qty_stock"] * $row["hrg_jual"],0);
                }
                if ($userSupplierCode <> null){
                    printf("<td nowrap='nowrap'>%s</td>",$row["supplier_name"]);
                }
                print("</tr>");
                $nmr++;
            }
        print("<tr>");
        print("<td colspan='6' align='right'>Total Nilai Stock&nbsp;</td>");
        printf("<td align='right'>%s</td>",number_format($tOtal,0));
        if ($userSupplierCode <> null){
            print('<td>&nbsp</td>');
        }
        print("</tr>");
        ?>
    </table>
<!-- end web report -->
<?php } ?>
</body>
</html>
