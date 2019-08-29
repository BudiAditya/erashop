<!DOCTYPE HTML>
<html>
<head>
    <title>ERASHOP - Edit Jenis Kontak</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var contacttype ContactType */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
    <legend><span class="bold">Edit Data Jenis Kontak</span></legend>
    <form action="<?php print($helper->site_url("master.contacttype/edit/".$contacttype->Id)); ?>" method="post">
        <table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0 auto;">
            <tr>
                <td class="bold right"><label for="TypeCode">Kode :</label></td>
                <td><input type="text" id="TypeCode" name="TypeCode" value="<?php print($contacttype->TypeCode); ?>" size="30" required/></td>
            </tr>
            <tr>
                <td class="bold right"><label for="TypeDescs">Jenis Kontak :</label></td>
                <td><input type="text" id="TypeDescs" name="TypeDescs" value="<?php print($contacttype->TypeDescs); ?>" size="30" required/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><button type="submit" class="button">Update</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.contacttype")); ?>" class="button">Batal</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</body>
</html>
