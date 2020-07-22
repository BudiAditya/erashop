<!DOCTYPE HTML>
<html>
<?php
/** @var $npkb Npkb */ /** @var $companies Company[] */ /** @var $trxtypes TrxType[] */ /** @var $cabangs Cabang[] */ 
?>
<head>
	<title>ERASHOP - View Data Permintaan Kas (NPBK)</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>

<br />
<fieldset>
	<legend align="center"><b>View Permintaan Kas NPKB No. <?php print($npkb->NpkbNo); ?></b></legend>
	    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder" align="center">
			<tr>
				<td>Cabang/Outlet</td>
                <td><select name="CabangId" class="text2" id="CabangId" readonly>
                        <option value=""></option>
                        <?php
                        foreach ($cabangs as $cab) {
                            if ($cab->Id == $npkb->CabangId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $cab->Id, $cab->Kode, $cab->Cabang);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Tanggal Pengajuan</td>
                <td><input type="text" class="text2" maxlength="10" size="10" id="NpkbDate" name="NpkbDate" value="<?php print($npkb->FormatNpkbDate(JS_DATE)); ?>" readonly/></td>
                <td>No. NPKB</td>
                <td><input type="text" class="text2" maxlength="50" size="18 id="NpkbNo" name="NpkbNo" value="<?php print($npkb->NpkbNo); ?>" readonly/></td>
                <td>No. Reff</td>
                <td><input type="text" class="text2" maxlength="50" size="22" id="ReffNo" name="ReffNo" value="<?php print($npkb->ReffNo); ?>" readonly/></td>
                <td>Status</td>
                <td><select id="NpkbStatus" name="NpkbStatus" readonly>
                        <option value="0" <?php print($npkb->NpkbStatus == 0 ? 'selected="selected"' : '');?>>Draft</option>
                        <option value="1" <?php print($npkb->NpkbStatus == 1 ? 'selected="selected"' : '');?>>Posted</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Jenis Biaya</td>
                <td><select id="xTrxTypeId" name="xTrxTypeId" readonly>
                        <option value="">--Pilih Jenis Transaksi--</option>
                        <?php
                        foreach ($trxtypes as $trxtype) {
                            $txd = $trxtype->Id.'|'.$trxtype->TrxDescs;
                            if ($trxtype->Id == $npkb->TrxTypeId) {
                                printf('<option value="%s" selected="selected">%s</option>', $txd, $trxtype->TrxDescs);
                            } else {
                                printf('<option value="%s">%s</option>', $txd, $trxtype->TrxDescs);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Ket. Penggunaan Dana</td>
                <td colspan="5"><input type="text" class="text2" maxlength="100" size="92" id="RequestDescs" name="RequestDescs" value="<?php print($npkb->RequestDescs); ?>" readonly/></td>
                <td>Jumlah Dana</td>
                <td><b>Rp. <input type="text" class="text2" id="RequestAmount" name="RequestAmount" size="18" maxlength="20" value="<?php print($npkb->RequestAmount == null ? 0 : $npkb->RequestAmount); ?>" style="text-align: right" readonly/></b></td>
            </tr>
            <tr>
                <td>Diminta Oleh</td>
                <td><input type="text" class="text2" maxlength="50" size="40" id="RequestBy" name="RequestBy" value="<?php print($npkb->RequestBy); ?>" readonly/></td>
                <td>Tanggal Diperlukan</td>
                <td><input type="text" class="text2" maxlength="10" size="10" id="RequestDate" name="RequestDate" value="<?php print($npkb->FormatRequestDate(JS_DATE)); ?>" readonly/></td>
                <td>Tgl. Cair</td>
                <td><input type="text" class="text2" maxlength="10" size="10" id="TglCair" name="TglCair" value="<?php print($npkb->FormatTglCair(JS_DATE)); ?>" readonly/></td>
                <td>No. BKK</td>
                <td><input type="text" class="text2" maxlength="50" size="20" id="NoBkk" name="NoBkk" value="<?php print($npkb->NoBkk); ?>" readonly/></td>
				<td colspan="2" align="center">
					<a href="<?php print($helper->site_url("cashbank.npkb/edit/".$npkb->Id)); ?>" class="button"><b>Edit Data</b></a>
                    <a href="<?php print($helper->site_url("cashbank.npkb")); ?>" class="button"><b>Daftar NPKB</b></a>
				</td>
			</tr>
		</table>

</fieldset>
<!-- </body> -->
</html>
