<!DOCTYPE HTML>
<html>
<head>
    <title>ERASYS - Edit Jenis Barang</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var itemjenis ItemJenis */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
    <legend><span class="bold">Edit Data Jenis Barang</span></legend>
    <form action="<?php print($helper->site_url("master.itemjenis/edit/".$itemjenis->Id)); ?>" method="post">
        <table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0 auto;">
            <tr>
                <td class="bold right"><label for="JnsBarang">Jenis Barang :</label></td>
                <td><input type="text" id="JnsBarang" name="JnsBarang" value="<?php print($itemjenis->JnsBarang); ?>" size="50" required/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Keterangan">Keterangan :</label></td>
                <td><input type="text" id="Keterangan" name="Keterangan" value="<?php print($itemjenis->Keterangan); ?>" size="50" required/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="IvtAccNo">Akun Persediaan :</label></td>
                <td><select id="IvtAccNo" name="IvtAccNo" required>
                        <option value="">--Pilih Akun Persediaan--</option>
                        <?php
                        foreach ($ivtcoa as $coakredit) {
                            if ($coakredit->Kode == $itemjenis->IvtAccNo) {
                                printf('<option value="%s" selected="selected">%s</option>', $coakredit->Kode, $coakredit->Kode.' - '.$coakredit->Perkiraan);
                            } else {
                                printf('<option value="%s">%s</option>', $coakredit->Kode, $coakredit->Kode.' - '.$coakredit->Perkiraan);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><button type="submit" class="button">Update</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.itemjenis")); ?>" class="button">Datftar Jenis Barang</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
<!-- </body> -->
</html>
