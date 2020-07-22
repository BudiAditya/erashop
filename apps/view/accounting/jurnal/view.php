<!DOCTYPE HTML>
<html>
<?php
/** @var $jurnal Jurnal */ /** $coas CoaDetail[] */
?>
<head>
	<title>Erasys - View Data Jurnal Akuntansi Manual</title>
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
	<legend align="center"><strong>View Data Jurnal Akuntansi Manual No. <?php print($jurnal->NoVoucher); ?></strong></legend>

        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="center">
            <tr>
                <td>Jenis Jurnal</td>
                <td><select class="text2" id="KdVoucher" name="KdVoucher" disabled style="width:275px">
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
                <td><input type="text" class="text2" maxlength="10" size="10" id="TglVoucher" name="TglVoucher" value="<?php print($jurnal->FormatTglVoucher(JS_DATE));?>" disabled/></td>
                <td>No. Jurnal</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoVoucher" name="NoVoucher" value="<?php print($jurnal->NoVoucher); ?>" disabled/></td>
                <td>Status</td>
                <td><select id="DocStatus" name="DocStatus" disabled>
                        <option value="0" <?php print($jurnal->DocStatus == 0 ? 'selected="selected"' : '');?>>Draft</option>
                        <option value="1" <?php print($jurnal->DocStatus == 1 ? 'selected="selected"' : '');?>>Approved</option>
                        <option value="2" <?php print($jurnal->DocStatus == 2 ? 'selected="selected"' : '');?>>Verified</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td><input type="text" class="text2" maxlength="200" size="50" id="Keterangan" name="Keterangan" value="<?php print($jurnal->Keterangan); ?>" disabled/></td>
                <td>Jumlah</td>
                <td><input type="text" class="text2" maxlength="15" size="15" id="DocAmount" name="DocAmount" value="<?php print(number_format($jurnal->DocAmount,0)); ?>" disabled style="text-align: right"/></td>
                <td>Refferensi</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="ReffNo" name="ReffNo" value="<?php print($jurnal->ReffNo); ?>" disabled/></td>
                <td>Sumber Data</td>
                <td><input type="text" class="text2" maxlength="20" size="20" id="ReffSource" name="ReffSource" value="<?php print($jurnal->ReffSource); ?>" disabled/></td>
            </tr>
            <tr>
                <td colspan="10" class="center">
                    <a href="<?php print($helper->site_url("accounting.jurnal")); ?>" class="button">Daftar Jurnal</a>
                    <?php
                    if($jurnal->DocStatus == 0){
                       printf('<a href="%s" class="button">Ubah Data</a>',$helper->site_url("accounting.jurnal/edit/".$jurnal->Id));
                    }
                    ?>
                </td>
            </tr>
        </table>
    <br>
    <div>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="center">
            <tr>
                <th colspan="6"><strong>DETAIL JURNAL</strong></th>
            </tr>
            <tr>
                <th>No.</th>
                <th>Akun Debet</th>
                <th>Akun Kredit</th>
                <th>Uraian</th>
                <th>Jumlah</th>
            </tr>
            <?php
            $counter = 0;
            $total = 0;
            foreach($jurnal->Details as $idx => $detail) {
                $counter++;
                print("<tr>");
                printf('<td class="right">%s.</td>', $counter);
                printf('<td>%s</td>', $detail->AcDebetNo);
                printf('<td>%s</td>', $detail->AcKreditNo);
                printf('<td>%s</td>', $detail->Uraian);
                printf('<td class="right">%s</td>', number_format($detail->Jumlah,0));
                print("</tr>");
                $total += $detail->Jumlah;
            }
            print("<tr>");
            print("<td colspan='4' class='right'>Sub-Total</td>");
            printf('<td class="right">%s</td>', number_format($total,0));
            print("</tr>");
            ?>
        </table>
    </div>
</fieldset>
<!-- </body> -->
</html>
