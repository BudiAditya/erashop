<!DOCTYPE HTML>
<html>
<head>
	<title>ERASYS - Edit Data Informasi Perusahaan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var elements = ["EntityCd", "CompanyName", "Npwp", "Address", "City", "Province", "Telephone", "Facsimile", "PersonInCharge", "PicStatus", "StartDate"];
			BatchFocusRegister(elements);
            $("#StartDate").customDatePicker({ showOn: "focus" });
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
	<legend><b>Edit Data Perusahaan</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.company/edit")); ?>" method="post">
		<input type="hidden" id="EntityId" name="EntityId" value="<?php print($company->EntityId); ?>"/>
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td>Kode</td>
				<td><input type="text" name="EntityCd" size="4" maxlength="3" class="text2" id="EntityCd" value="<?php print($company->EntityCd); ?>" /></td>
			</tr>
			<tr>
				<td>Nama</td>
				<td><input type="text" name="CompanyName" size="50" maxlength="150" class="text2" id="CompanyName" value="<?php print($company->CompanyName); ?>" /></td>
			</tr>
			<tr>
				<td>N.P.W.P.</td>
				<td><input type="text" name="Npwp" size="20" maxlength="20" class="text2" id="Npwp" value="<?php print($company->Npwp); ?>" /></td>
			</tr>
			<tr>
				<td>Alamat Kantor</td>
				<td><input type="text" name="Address" size="122" maxlength="250" class="text2" id="Address" value="<?php print($company->Address); ?>" /></td>
			</tr>
			<tr>
				<td>Kota</td>
				<td><input type="text" name="City" size="25" maxlength="50" class="text2" id="City" value="<?php print($company->City); ?>" /></td>
			</tr>
			<tr>
				<td>Propinsi</td>
				<td><input type="text" name="Province" size="33" maxlength="50" class="text2" id="Province" value="<?php print($company->Province); ?>" /></td>
			</tr>
			<tr>
				<td>No.Telepon</td>
				<td><input type="text" name="Telephone" size="20" maxlength="50" class="text2" id="Telephone" value="<?php print($company->Telephone); ?>" /></td>
			</tr>
			<tr>
				<td>No.Facsimile</td>
				<td><input type="text" name="Facsimile" size="20" maxlength="50" class="text2" id="Facsimile" value="<?php print($company->Facsimile); ?>" /></td>
			</tr>
			<tr>
				<td>P.I.C.</td>
				<td><input type="text" name="PersonInCharge" size="25" maxlength="50" class="text2" id="PersonInCharge" value="<?php print($company->PersonInCharge); ?>" /></td>
			</tr>
			<tr>
				<td>Jabatan</td>
				<td><input type="text" name="PicStatus" size="33" maxlength="50" class="text2" id="PicStatus" value="<?php print($company->PicStatus); ?>" /></td>
			</tr>
            <tr>
                <td>Mulai Tanggal</td>
                <td><input type="text" name="StartDate" size="10" maxlength="10" class="text2" id="StartDate" value="<?php print($company->FormatStartDate(JS_DATE)); ?>" required /></td>
            </tr>
            <tr>
                <td><label for="PpnInAccNo">Akun PPN Masukan :</label></td>
                <td><select id="PpnInAccNo" name="PpnInAccNo">
                        <option value="">-- PILIH AKUN --</option>
                        <?php
                        foreach ($accounts as $account) {
                            if ($account->Kode == $company->PpnInAccNo) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $account->Id, $account->Kode, $account->Perkiraan);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $account->Id, $account->Kode, $account->Perkiraan);
                            }
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td><label for="PpnOutAccNo">Akun PPN Keluaran :</label></td>
                <td><select id="PpnOutAccNo" name="PpnOutAccNo">
                        <option value="">-- PILIH AKUN --</option>
                        <?php
                        foreach ($accounts as $account) {
                            if ($account->Kode == $company->PpnOutAccNo) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $account->Id, $account->Kode, $account->Perkiraan);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $account->Id, $account->Kode, $account->Perkiraan);
                            }
                        }
                        ?>
                    </select></td>
            </tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<button type="submit">Submit</button>
					<a href="<?php print($helper->site_url("master.company")); ?>">Daftar Perusahaan</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<!-- </body> -->
</html>
