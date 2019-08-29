<!DOCTYPE HTML>
<html>
<head>
	<title>ERASYS - Entry Master Kas/Bank</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var $bank Bank */ /** @var $accounts CoaDetail[] */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />

<fieldset>
	<legend><span class="bold">Entry Data Kas/Bank</span></legend>
	<form action="<?php print($helper->site_url("master.bank/add")); ?>" method="post">
		<table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0 auto;">
			<tr>
				<td class="right bold">Cabang :</td>
				<td><?php printf('%s', $cabCode) ?></td>
			</tr>
			<tr>
				<td class="bold right"><label for="Name">Nama Kas/Bank :</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php print($bank->Name); ?>" size="30" required/></td>
			</tr>
			<tr>
				<td class="bold right"><label for="Branch">Cabang Bank :</label></td>
				<td><input type="text" id="Branch" name="Branch" value="<?php print($bank->Branch); ?>" size="15" required/></td>
			</tr>
			<tr>
				<td class="bold right"><label for="Address">Alamat :</label></td>
				<td><input type="text" id="Address" name="Address" value="<?php print($bank->Address); ?>" size="50" /></td>
			</tr>
			<tr>
				<td class="bold right"><label for="NoRek">Nomor Rekening :</label></td>
				<td><input type="text" id="NoRek" name="NoRek" value="<?php print($bank->NoRekening); ?>" size="30" required/></td>
			</tr>
			<tr>
				<td class="bold right"><label for="CurrencyCode">Mata Uang :</label></td>
				<td><input type="text" id="CurrencyCode" name="CurrencyCode" value="<?php print($bank->CurrencyCode); ?>" size="5" required/></td>
			</tr>
			<tr>
				<td class="bold right"><label for="AccNo">Kode Akun :</label></td>
				<td><select id="AccNo" name="AccNo" required>
					<option value="">-- PILIH AKUN --</option>
					<?php
					foreach ($accounts as $account) {
						if ($account->Kode == $bank->AccNo) {
							printf('<option value="%s" selected="selected">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
						} else {
							printf('<option value="%s">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
						}
					}
					?>
				</select></td>
			</tr>
			<tr>
				<td class="bold right"><label for="CostAccNo">Kode Akun Biaya :</label></td>
				<td><select id="CostAccNo" name="CostAccNo">
					<option value="">-- PILIH AKUN --</option>
					<?php
					foreach ($accounts as $account) {
						if ($account->Kode == $bank->CostAccNo) {
							printf('<option value="%s" selected="selected">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
						} else {
							printf('<option value="%s">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
						}
					}
					?>
				</select></td>
			</tr>
			<tr>
				<td class="bold right"><label for="RevAccNo">Kode Akun Pendapatan :</label></td>
				<td><select id="RevAccNo" name="RevAccNo">
					<option value="">-- PILIH AKUN --</option>
					<?php
					foreach ($accounts as $account) {
						if ($account->Kode == $bank->RevAccNo) {
							printf('<option value="%s" selected="selected">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
						} else {
							printf('<option value="%s">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
						}
					}
					?>
				</select></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><button type="submit">Submit</button>
					<a href="<?php print($helper->site_url("master.bank")); ?>" class="button">Daftar Kas/Bank</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

</body>
</html>
