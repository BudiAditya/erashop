<!DOCTYPE HTML>
<html>
<head>
    <title>ERASHOP - Ubah Data Pengumuman</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/tinymce/js/tinymce/tinymce.min.js")); ?>"></script>
    <script type="text/javascript">
        //$(document).ready(function() {
        //var elements = ["AttFrom", "Cabang","AttHeader", "AttContent"];
        //BatchFocusRegister(elements);
        //});
        tinymce.init({
            selector: "textarea",
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
        });
    </script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>

<br/>
<fieldset>
    <legend><b>Ubah Data Pengumuman</b></legend>
    <form id="frm" action="<?php print($helper->site_url("master.attention/edit/".$attention->Id)); ?>" method="post">
        <input type="hidden" name="Id" id="Id" value="<?php print($attention->Id); ?>"/>
        <table cellpadding="2" cellspacing="1">
            <tr>
                <td>Dari</td>
                <td><input type="text" class="text2" name="AttFrom" id="AttFrom" maxlength="50" size="20" value="<?php print($attention->AttFrom == null ? $username : $attention->AttFrom ); ?>" required/></td>
            </tr>
            <tr>
                <td>Judul</td>
                <td><input type="text" class="text2" name="AttHeader" id="AttHeader" maxlength="50" size="50" value="<?php print($attention->AttHeader); ?>" required/></td>
            </tr>
            <tr>
                <td>Isi Pengumuman</td>
                <td><textarea name="AttContent" id="AttContent"><?php print($attention->AttContent); ?></textarea>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <button type="submit">Update</button>
                    <a href="<?php print($helper->site_url("master.attention")); ?>" class="button">Daftar Pengumuman</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</body>
</html>
