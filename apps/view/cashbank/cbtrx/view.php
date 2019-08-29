<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $cbtrx CbTrx */ /** @var $accounts CoaDetail[] */ /** @var $companies Company[] */ /** @var $trxtypes TrxType[] */ /** @var $cabangs Cabang[] */ /** @var $coabanks CoaDetail[] */
/** @var $contacts Contacts[] */
?>
<head>
    <title>ERASYS - View Data Transaksi Cash/Bank</title>
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
    <legend><b>View Transaksi Cash/Bank</b></legend>
    <table cellpadding="2" cellspacing="2">
        <tr>
            <td>Tanggal</td>
            <td><input type="text" class="text2" maxlength="10" size="10" id="TrxDate" name="TrxDate" value="<?php print($cbtrx->FormatTrxDate(JS_DATE)); ?>" disabled/></td>
            <td>No. Bukti</td>
            <td><input type="text" class="text2" maxlength="20" size="20" id="DocNo" name="DocNo" value="<?php print($cbtrx->DocNo); ?>" disabled/></td>
            <td>Status </td>
            <td><select id="TrxStatus" name="TrxStatus"  disabled>
                    <option value="0" <?php print($cbtrx->TrxStatus == 0 ? 'selected="selected"' : '');?>>Draft</option>
                    <option value="1" <?php print($cbtrx->TrxStatus == 1 ? 'selected="selected"' : '');?>>Posted</option>
                    <option value="2" <?php print($cbtrx->TrxStatus == 2 ? 'selected="selected"' : '');?>>Approved</option>
                    <option value="3" <?php print($cbtrx->TrxStatus == 3 ? 'selected="selected"' : '');?>>Void</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Nama Relasi</td>
            <td><select id="ContactId" name="ContactId" style="width: 250px;" disabled>
                    <option value="0">--Pilih Relasi--</option>
                    <?php
                    foreach ($contacts as $customer) {
                        if ($customer->Id == $cbtrx->ContactId) {
                            printf('<option value="%d" selected="selected">%s - %s</option>', $customer->Id,$customer->ContactCode,$customer->ContactName);
                        } else {
                            printf('<option value="%d">%s - %s</option>', $customer->Id,$customer->ContactCode,$customer->ContactName);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>No. Reff</td>
            <td colspan="4"><input type="text" class="text2" maxlength="150" size="70" id="ReffNo" name="ReffNo" value="<?php print($cbtrx->ReffNo == null ? '-' : $cbtrx->ReffNo); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Jenis Transaksi</td>
            <td><select id="xTrxTypeId" name="xTrxTypeId" style="width: 250px"  disabled>
                    <option value="">--Pilih Jenis Transaksi--</option>
                    <?php
                    foreach ($trxtypes as $trxtype) {
                        $txd = $trxtype->Id.'|'.$trxtype->TrxMode.'|'.$trxtype->DefAccNo.'|'.$trxtype->TrxAccNo.'|'.$trxtype->TrxDescs.'|'.$trxtype->RefftypeId;
                        if ($trxtype->Id == $cbtrx->TrxTypeId) {
                            printf('<option value="%s" selected="selected">%s</option>', $txd, $trxtype->TrxDescs);
                        } else {
                            printf('<option value="%s">%s</option>', $txd, $trxtype->TrxDescs);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>Keterangan</td>
            <td colspan="4"><input type="text" class="text2" maxlength="150" size="70" id="TrxDescs" name="TrxDescs" value="<?php print($cbtrx->TrxDescs); ?>" disabled/></td>
        </tr>
        <tr>
            <td>Debet Akun</td>
            <td><select id="DbAccNo" name="DbAccNo" style="width: 250px;" disabled>
                    <option value="">--Pilih Akun Debet--</option>
                    <?php
                    foreach ($accounts as $coadebet) {
                        if ($coadebet->Kode == $cbtrx->DbAccNo) {
                            printf('<option value="%s" selected="selected">%s</option>', $coadebet->Kode, $coadebet->Kode.' - '.$coadebet->Perkiraan);
                        } else {
                            printf('<option value="%s">%s</option>', $coadebet->Kode, $coadebet->Kode.' - '.$coadebet->Perkiraan);
                        }
                    }
                    ?>
                </select>
            </td>
            <td>Kredit Akun</td>
            <td colspan="3"><select id="CrAccNo" name="CrAccNo" style="width: 250px;" disabled>
                    <option value="">--Pilih Akun Kredit--</option>
                    <?php
                    foreach ($accounts as $coakredit) {
                        if ($coakredit->Kode == $cbtrx->CrAccNo) {
                            printf('<option value="%s" selected="selected">%s</option>', $coakredit->Kode, $coakredit->Kode.' - '.$coakredit->Perkiraan);
                        } else {
                            printf('<option value="%s">%s</option>', $coakredit->Kode, $coakredit->Kode.' - '.$coakredit->Perkiraan);
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Jumlah Uang</td>
            <td colspan="2"><b>Rp. <input type="text" class="text2" id="TrxAmount" name="TrxAmount" size="20" maxlength="20" value="<?php print($cbtrx->TrxAmount == null ? 0 : number_format($cbtrx->TrxAmount,0)); ?>" style="text-align: right" disabled/></b></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">
                <?php if ($cbtrx->TrxStatus < 2 && $cbtrx->CreateMode == 0){ ?>
                <a href="<?php print($helper->site_url("cashbank.cbtrx/edit/".$cbtrx->Id)); ?>" class="button">Ubah Transaksi</a>
                <?php } ?>
                <a href="<?php print($helper->site_url("cashbank.cbtrx")); ?>" class="button">Daftar Transaksi</a>
            </td>
        </tr>
    </table>
</fieldset>
</body>
</html>
