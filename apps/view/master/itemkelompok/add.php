<!DOCTYPE HTML>
<html>
<head>
	<title>ERASHOP - Entry Kelompok Barang</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var $itemkelompok ItemKelompok */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
	<legend><span class="bold">Entry Data Kelompok Barang</span></legend>
	<form action="<?php print($helper->site_url("master.itemkelompok/add")); ?>" method="post">
		<table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0 auto;">
			<tr>
				<td class="bold right"><label for="Kelompok">Nama Kelompok :</label></td>
				<td><input type="text" id="Kelompok" name="Kelompok" value="<?php print($itemkelompok->Kelompok); ?>" size="50" required/></td>
			</tr>
			<tr>
				<td class="bold right"><label for="Keterangan">Kelompok Barang :</label></td>
				<td><input type="text" id="Keterangan" name="Keterangan" value="<?php print($itemkelompok->Keterangan); ?>" size="50" required/></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><button type="submit" class="button">Simpan</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.itemkelompok")); ?>" class="button">Batal</a>
                </td>
			</tr>
		</table>
	</form>
</fieldset>
<!-- </body> -->
</html>
