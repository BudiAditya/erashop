<!DOCTYPE HTML>
<html>
<head>
	<title>ERASHOP - Entry Jenis Invoice (AR/Piutang)</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var $invoicetype InvoiceType */ /** @var $accounts CoaDetail[] */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
	<legend><span class="bold">Entry Jenis Invoice (AR/Piutang)</span></legend>
	<form action="<?php print($helper->site_url("ar.invoicetype/add")); ?>" method="post">
		<table cellspacing="0" cellpadding="0" class="tablePadding">
			<tr>
				<td class="bold right"><label for="IvcType">Jenis Invoice :</label></td>
                <td><input type="text" id="IvcType" name="IvcType" value="<?php print($invoicetype->IvcType); ?>" size="100" required/></td>
			</tr>
			<tr>
				<td class="bold right"><label for="RevAccId">Akun Pendapatan :</label></td>
				<td><select id="RevAccId" name="RevAccId" required>
					<option value="">-- PILIH AKUN --</option>
					<?php
					foreach ($accounts as $account) {
						if ($account->Id == $invoicetype->RevAccId) {
							printf('<option value="%d" selected="selected">%s - %s</option>', $account->Id, $account->Kode, $account->Perkiraan);
						} else {
							printf('<option value="%d">%s - %s</option>', $account->Id, $account->Kode, $account->Perkiraan);
						}
					}
					?>
				</select></td>
			</tr>
            <tr>
                <td class="bold right"><label for="ArAccId">Akun Piutang :</label></td>
                <td><select id="ArAccId" name="ArAccId" required>
                        <option value="">-- PILIH AKUN --</option>
                        <?php
                        foreach ($accounts as $account) {
                            if ($account->Id == $invoicetype->ArAccId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $account->Id, $account->Kode, $account->Perkiraan);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $account->Id, $account->Kode, $account->Perkiraan);
                            }
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td class="bold right"><label for="Description">Keterangan :</label></td>
                <td><input type="text" id="Description" name="Description" value="<?php print($invoicetype->Description); ?>" size="100"/></td>
            </tr>
			<tr>
				<td>&nbsp;</td>
                <td><button type="submit">Simpan</button>
                    &nbsp
                    <a href="<?php print($helper->site_url("ar.invoicetype")); ?>" class="button">Daftar Jenis Invoice</a>
                </td>
			</tr>
		</table>
	</form>
</fieldset>
</body>
</html>
