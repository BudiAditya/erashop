<!DOCTYPE HTML>
<html>
<head>
	<title>ERASHOP - Entry Divisi Barang</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var $itemdivisi ItemDept */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
	<legend><span class="bold">Entry Data Divisi Barang</span></legend>
	<form action="<?php print($helper->site_url("master.itemdivisi/add")); ?>" method="post">
		<table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0 auto;">
			<tr>
				<td class="bold right"><label for="Divisi">Nama Divisi :</label></td>
				<td><input type="text" id="Divisi" name="Divisi" value="<?php print($itemdivisi->Divisi); ?>" size="30" required/></td>
			</tr>
			<tr>
				<td class="bold right"><label for="Keterangan">Keterangan :</label></td>
				<td><input type="text" id="Keterangan" name="Keterangan" value="<?php print($itemdivisi->Keterangan); ?>" size="30" required/></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><button type="submit" class="button">Simpan</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.itemdivisi")); ?>" class="button">Batal</a>
                </td>
			</tr>
		</table>
	</form>
</fieldset>
<!-- </body> -->
</html>
