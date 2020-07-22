<!DOCTYPE HTML>
<html>
<head>
	<title>ERASHOP - Tambah Data Informasi Departemen</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["DeptCd", "DeptName"];
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

<br/>
<fieldset>
	<legend><b>Tambah Data Departemen</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.department/add")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Entity</td>
				<td><select name="EntityId" class="text2" id="EntityId">
					<option value=""></option>
					<?php
					foreach ($companies as $sbu) {
						if ($sbu->EntityId == $dept->EntityId) {
							printf('<option value="%d" selected="selected">%s - %s</option>', $sbu->EntityId, $sbu->EntityCd, $sbu->CompanyName);
						} else {
							printf('<option value="%d">%s - %s</option>', $sbu->EntityId, $sbu->EntityCd, $sbu->CompanyName);
						}
					}
					?>
				</select></td>
			</tr>
			<tr>
				<td>Kode</td>
				<td><input type="text" class="text2" name="DeptCd" id="DeptCd" maxlength="5" size="5" value="<?php print($dept->DeptCd); ?>" /></td>
			</tr>
			<tr>
				<td>Nama Departemen</td>
				<td><input type="text" class="text2" name="DeptName" id="DeptName" maxlength="50" size="50" value="<?php print($dept->DeptName); ?>" /></td>
			</tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button type="submit">Submit</button>
					<a href="<?php print($helper->site_url("master.department")); ?>" class="button">Daftar Departemen</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<!-- </body> -->
</html>
