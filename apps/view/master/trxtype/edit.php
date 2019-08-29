<!DOCTYPE HTML>
<html>
<head>
	<title>Erasys - Edit Jenis Transaksi</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php /** @var $trxtype TrxType */ /** @var $accounts CoaDetail[] */ ?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />

<fieldset>
	<legend><span class="bold">Edit Jenis Transaksi</span></legend>
	<form action="<?php print($helper->site_url("master.trxtype/edit/".$trxtype->Id)); ?>" method="post">
        <input type="hidden" id="Id" name="Id" value="<?php print($trxtype->Id);?>"/>
        <input type="hidden" name="RefftypeId" id="RefftypeId" value="0"/>
		<table cellspacing="0" cellpadding="0" class="tablePadding">
			<tr>
				<td class="bold right"><label for="TrxDescs">Jenis Transaksi :</label></td>
				<td><input type="text" id="TrxDescs" name="TrxDescs" value="<?php print($trxtype->TrxDescs); ?>" size="50" required/></td>
			</tr>
            <tr>
                <td class="bold right"><label for="TrxMode">Mode :</label></td>
                <td>
                    <select id="TrxMode" name="TrxMode" required>
                        <option value="">--pilih--</option>
                        <option value="1" <?php $trxtype->TrxMode == 1 ? print('selected="selected"') : null;?>>Debet</option>
                        <option value="2" <?php $trxtype->TrxMode == 2 ? print('selected="selected"') : null;?>>Kredit</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="bold right"><label for="DefAccNo">Default Akun :</label></td>
                <td><select id="DefAccNo" name="DefAccNo" required>
                        <option value="0">-- PILIH AKUN --</option>
                        <?php
                        foreach ($accounts as $account) {
                            if ($account->Kode == $trxtype->DefAccNo) {
                                printf('<option value="%s" selected="selected">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
                            } else {
                                printf('<option value="%s">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
                            }
                        }
                        ?>
                    </select></td>
            </tr>
			<tr>
				<td class="bold right"><label for="TrxAccNo">Kontra Akun :</label></td>
				<td><select id="TrxAccNo" name="TrxAccNo" required>
					<option value="0">-- PILIH AKUN --</option>
					<?php
					foreach ($accounts as $account) {
						if ($account->Id == $trxtype->TrxAccNo) {
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
				<td><button type="submit">Update</button>
                    &nbsp
                    <a href="<?php print($helper->site_url("master.trxtype")); ?>" class="button">Daftar Jenis Transaksi</a>
                </td>
			</tr>
		</table>
	</form>
</fieldset>

</body>
</html>
