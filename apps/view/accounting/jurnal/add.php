<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<?php
/** @var $jurnal Jurnal */
?>
<head>
	<title>Erasys - Entry Data Jurnal Akuntansi Manual</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var elements = ["KdVoucher","TglVoucher","Keterangan","DocAmount","btSubmit"];
            BatchFocusRegister(elements);
            $("#TglVoucher").customDatePicker({ showOn: "focus" });

            // autoNumeric
            $(".num").autoNumeric({mDec: '0'});
            $("#frm").submit(function(e) {
                $(".num").each(function(idx, ele){
                    this.value  = $(ele).autoNumericGet({mDec: '0'});
                });
            });
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
	<legend align="center"><strong>Entry Data Jurnal Akuntansi Manual</strong></legend>
    <form id="frm" action="<?php print($helper->site_url("accounting.jurnal/add")); ?>" method="post">
        <input type="hidden" id="DocStatus" name="DocStatus" value="<?php print($jurnal->DocStatus);?>"/>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="center">
            <tr>
                <td>Jenis Jurnal</td>
                <td><select class="text2" id="KdVoucher" name="KdVoucher" required style="width: 275px">
                        <option value="">-- pilih jenis jurnal --</option>
                        <?php
                        while ($row = $vouchertypes->FetchAssoc()) {
                            if($row["voucher_cd"] == $jurnal->KdVoucher){
                                printf('<option value="%s" selected="selected">%s - %s</option>',$row["voucher_cd"], $row["voucher_cd"],$row["voucher_desc"]);
                            }else{
                                printf('<option value="%s">%s - %s</option>',$row["voucher_cd"], $row["voucher_cd"],$row["voucher_desc"]);
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>Tanggal</td>
                <td><input type="text" class="text2" maxlength="10" size="10" id="TglVoucher" name="TglVoucher" value="<?php print($jurnal->FormatTglVoucher(JS_DATE));?>" required/></td>
                <td>No. Jurnal</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoVoucher" name="NoVoucher" value="<?php print($jurnal->NoVoucher == null ? 'Auto Number' : $jurnal->NoVoucher); ?>" disabled/></td>
                <td>Status</td>
                <td><select id="xDocStatus" name="xDocStatus" disabled>
                        <option value="0" <?php print($jurnal->DocStatus == 0 ? 'selected="selected"' : '');?>>Draft</option>
                        <option value="1" <?php print($jurnal->DocStatus == 1 ? 'selected="selected"' : '');?>>Approved</option>
                        <option value="2" <?php print($jurnal->DocStatus == 2 ? 'selected="selected"' : '');?>>Verified</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td><input type="text" class="text2" maxlength="200" size="50" id="Keterangan" name="Keterangan" value="<?php print($jurnal->Keterangan); ?>" required/></td>
                <td>Jumlah</td>
                <td><input type="text" class="text2" maxlength="15" size="15" id="DocAmount" name="DocAmount" value="<?php print($jurnal->DocAmount == null ? 0 : $jurnal->DocAmount); ?>" readonly style="text-align: right"/></td>
                <td>Refferensi</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="ReffNo" name="ReffNo" value="<?php print($jurnal->ReffNo); ?>"/></td>
                <td>Sumber Data</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="ReffSource" name="ReffSource" value="<?php print($jurnal->ReffSource); ?>"/></td>
            </tr>
            <tr>
                <td colspan="10" class="center">
                    <a href="<?php print($helper->site_url("accounting.jurnal")); ?>" class="button">Daftar Jurnal</a>
                    <button id="btSubmit" type="submit">Berikutnya &gt;</button>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</body>
</html>
