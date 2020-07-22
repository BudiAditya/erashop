<?php
 /** @var $coadetail CoaDetail */ /** @var $coagroup CoaGroup */
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Erasys - Ubah Data Kode Perkiraan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["KdInduk", "Kode", "Perkiraan", "XMode"];
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
	<legend><b>Ubah Data Kode Perkiraan</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.coadetail/edit")); ?>" method="post">
		<table cellpadding="2" cellspacing="1">
            <input type="hidden" id="Id" name="Id" value="<?php print($coadetail->Id);?>"/>
            <tr>
                <td align="right"><label for="KdInduk">Kode Induk:</label></td>
                <td><select id="KdInduk" name="KdInduk" required>
                    <option value="">--pilih kode induk--</option>
            <?php
                    foreach ($coagroup as $induk) {
                      if($coadetail->KdInduk == $induk->KdInduk){
                         printf("<option value='%s' selected='selected'>%s - %s</option>",$induk->KdInduk,$induk->KdInduk,$induk->Kategori);
                      }else{
                         printf("<option value='%s'>%s - %s</option>",$induk->KdInduk,$induk->KdInduk,$induk->Kategori);
                      }
                    }
            ?>
                    </select>
                </td>
            </tr>
			<tr>
				<td align="right"><label for="Kode">Kode Akun:</label></td>
				<td><input type="text" id="Kode" name="Kode" size="15" maxlength="10" value="<?php print($coadetail->Kode); ?>" required/></td>
			</tr>
			<tr>
				<td align="right"><label for="Perkiraan">Nama Perkiraan:</label></td>
				<td><input type="text" id="Perkiraan" name="Perkiraan" size="50" value="<?php print($coadetail->Perkiraan); ?>" required/></td>
			</tr>
			<tr>
				<td align="right"><label for="XMode">Level Akun:</label></td>
				<td><select id="XMode" name="XMode" required>
						<option value="0" <?php $coadetail->XMode == 0 ? print('selected="selected"') : null;?>>Semua (Pusat)</option>
						<option value="1" <?php $coadetail->XMode == 1 ? print('selected="selected"') : null;?>>Khusus Cabang ini</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<button type="submit">Submit</button>
					<a href="<?php print($helper->site_url("master.coadetail")); ?>">Daftar Kode Perkiraan</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<!-- </body> -->
</html>
