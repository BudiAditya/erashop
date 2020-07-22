<?php
/** @var $coagroup CoaGroup */ /** @var $coaheader CoaHeader */
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Erasys - Ubah Data Kategori Perkiraan</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var elements = ["KdKelompok", "KdInduk", "Kategori", "PSaldo"];
            BatchFocusRegister(elements);
        });
    </script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />

<fieldset>
    <legend><b>Ubah Data Kategori Perkiraan</b></legend>
    <form id="frm" action="<?php print($helper->site_url("master.coagroup/edit/".$coagroup->Id)); ?>" method="post">
        <input type="hidden" name="Id" id="Id" value="<?php print($coagroup->Id); ?>"/>
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td><label for="KdKelompok">Kelompok :</label></td>
                <td><select id="KdKelompok" name="KdKelompok" required>
                        <option value="">--pilih kelompok--</option>
                        <?php
                        foreach ($coaheader as $induk) {
                            if($coagroup->KdKelompok == $induk->KdKelompok){
                                printf("<option value='%s' selected='selected'>%s - %s</option>",$induk->KdKelompok,$induk->KdKelompok,$induk->NmKelompok);
                            }else{
                                printf("<option value='%s'>%s - %s</option>",$induk->KdKelompok,$induk->KdKelompok,$induk->NmKelompok);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="KdInduk">Kode:</label></td>
                <td><input type="text" id="KdInduk" name="KdInduk" size="10" maxlength="10" value="<?php print($coagroup->KdInduk); ?>" required/></td>
            </tr>
            <tr>
                <td><label for="Kategori">Nama Kategori:</label></td>
                <td><input type="text" id="Kategori" name="Kategori" size="50" value="<?php print($coagroup->Kategori); ?>" required/></td>
            </tr>
            <tr>
                <td><label for="PSaldo">Posisi Saldo:</label></td>
                <td><select name="PSaldo" id="PSaldo" required>
                        <option value="">--posisi saldo--</option>
                        <option value="DK" <?php $coagroup->PSaldo == 'DK' ? print('selected="selected"') : null;?>>DEBET</option>
                        <option value="KD" <?php $coagroup->PSaldo == 'KD' ? print('selected="selected"') : null;?>>KREDIT</option>
                    </select></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <button type="submit">Update</button>
                    <a href="<?php print($helper->site_url("master.coagroup")); ?>">Daftar Kategori Perkiraan</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
<!-- </body> -->
</html>
