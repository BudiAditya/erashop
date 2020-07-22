<!DOCTYPE HTML>
<html>
<head>
	<title>ERASHOP - Tambah Data Informasi Cabang</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["Kode", "Cabang","Alamat", "Pic"];
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
	<legend><b>Tambah Data Cabang</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.cabang/add")); ?>" method="post" enctype="multipart/form-data">
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Entity</td>
				<td><select name="EntityId" class="text2" id="EntityId" autofocus required>
					<option value=""></option>
					<?php
					foreach ($companies as $sbu) {
						if ($sbu->EntityId == $userCompanyId) {
							printf('<option value="%d" selected="selected">%s - %s</option>', $sbu->EntityId, $sbu->EntityCd, $sbu->CompanyName);
						} else {
							printf('<option value="%d">%s - %s</option>', $sbu->EntityId, $sbu->EntityCd, $sbu->CompanyName);
						}
					}
					?>
				</select></td>
			</tr>
            <tr>
                <td>Area</td>
                <td><select name="AreaId" class="text2" id="AreaId" required>
                        <option value=""></option>
                        <?php
                        foreach ($areas as $area) {
                            if ($area->Id == $cabang->AreaId) {
                                printf('<option value="%d" selected="selected">%s</option>', $area->Id, $area->AreaName);
                            } else {
                                printf('<option value="%d">%s</option>', $area->Id, $area->AreaName);
                            }
                        }
                        ?>
                    </select></td>
            </tr>
			<tr>
				<td>Jenis Cabang</td>
				<td><select id="CabType" name="CabType" >
						<option value="0" <?php print($cabang->CabType == 0 ? 'selected="selected"' : '');?>>Outlet + Gudang</option>
						<option value="1" <?php print($cabang->CabType == 1 ? 'selected="selected"' : '');?>>Outlet Saja</option>
						<option value="2" <?php print($cabang->CabType == 2 ? 'selected="selected"' : '');?>>Gudang Saja</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Kode Cabang</td>
				<td><input type="text" class="text2" name="Kode" id="Kode" maxlength="50" size="50" value="<?php print($cabang->Kode); ?>" required/></td>
			</tr>
			<tr>
				<td>Lokasi/Cabang</td>
				<td><input type="text" class="text2" name="Cabang" id="Cabang" maxlength="50" size="50" value="<?php print($cabang->Cabang); ?>" required/></td>
			</tr>
            <tr>
                <td>Nama Outlet</td>
                <td><input type="text" class="text2" name="NamaCabang" id="NamaCabang" maxlength="50" size="50" value="<?php print($cabang->NamaCabang); ?>"/></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td><input type="text" class="text2" name="Alamat" id="Alamat" maxlength="250" size="50" value="<?php print($cabang->Alamat); ?>" /></td>
            </tr>
            <tr>
                <td>P I C</td>
                <td><input type="text" class="text2" name="Pic" id="Pic" maxlength="50" size="50" value="<?php print($cabang->Pic); ?>" /></td>
            </tr>
			<tr>
				<td>Aturan Stock</td>
				<td><select id="AllowMinus" name="AllowMinus" >
						<option value="0" <?php print($cabang->AllowMinus == 0 ? 'selected="selected"' : '');?>>0 - Tidak Boleh Minus</option>
						<option value="1" <?php print($cabang->AllowMinus == 1 ? 'selected="selected"' : '');?>>1 - Boleh Minus</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Raw Printing Mode</td>
				<td><select id="RawPrintMode" name="RawPrintMode" >
						<option value="0">--Pilih Print Mode--</option>
						<option value="1" <?php print($cabang->RawPrintMode == 1 ? 'selected="selected"' : '');?>>1 - Plain Paper</option>
						<option value="2" <?php print($cabang->RawPrintMode == 2 ? 'selected="selected"' : '');?>>2 - Form Paper</option>
						<option value="3" <?php print($cabang->RawPrintMode == 3 ? 'selected="selected"' : '');?>>3 - P D F</option>
						<option value="4" <?php print($cabang->RawPrintMode == 4 ? 'selected="selected"' : '');?>>4 - POS Struk</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Raw Printer Name</td>
				<td><input type="text" class="text2" name="RawPrinterName" id="RawPrinterName" maxlength="50" size="50" value="<?php print($cabang->RawPrinterName); ?>" /></td>
			</tr>
            <tr>
                <td>File Logo</td>
                <td><input type="file" class="text2" name="FileName" id="FileName" accept="image/*" /></td>
            </tr>
			<tr>
                <td>&nbsp;</td>
				<td>
					<button type="submit">Submit</button>
					<a href="<?php print($helper->site_url("master.cabang")); ?>" class="button">Daftar Cabang</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<!-- </body> -->
</html>
