<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php /** @var $company Company */ /** @var $monthNames string[] */ /** @var $parentAccounts Coa[] */ /** @var $parentId int */ /** @var $month int */ /** @var $year int */ /** @var $report null|ReaderBase */ ?>
<head>
	<title>Erashop - Worksheet Balance</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>" />
    <style type="text/css">
        .level0 {
            font-weight: bold;
            font-size: 1.2em;
            color:red;
        }
        .level1 {
            font-weight: bold;
            color: brown;
        }
        .level2 {
            font-weight: bold;
            color: darkblue;
        }
    </style>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>

<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />

<fieldset>
	<legend><span class="bold">Laporan Worksheet Balance</span></legend>

	<div class="center">
		<form action="<?php print($helper->site_url("accounting.worksheetbalance/recap")); ?>" method="GET">
            <label for="RekapMethod">Metode Rekap :</label>
            <select id="RekapMethod" name="rekapMethod">
                <option value="0" <?php if($rekapMethod == 0){print("selected='selected'");}?>>Sampai dengan</option>
                <option value="1" <?php if($rekapMethod == 1){print("selected='selected'");}?>>Khusus</option>
            </select>
			<label for="Month">Bulan : </label>
			<select id="Month" name="month">
				<?php
				foreach ($monthNames as $idx => $name) {
					if ($idx == $month) {
						printf('<option value="%d" selected="selected">%s</option>', $idx, $name);
					} else {
						printf('<option value="%d">%s</option>', $idx, $name);
					}
				}
				?>
			</select>
			<label for="Year">Tahun : </label>
			<select id="Year" name="year">
				<?php
				for ($i = date("Y"); $i >= 2010; $i--) {
					if ($i == $year) {
						printf('<option value="%d" selected="selected">%s</option>', $i, $i);
					} else {
						printf('<option value="%d">%s</option>', $i, $i);
					}
				}
				?>
			</select>
            <label for="IsIncludeOpening">Termasuk Saldo Awal : </label>
            <select id="IsIncludeOpening" name="isIncOb">
                <option value="1" <?php if($isIncOb == 1){print("selected='selected'");}?>>Ya</option>
                <option value="0" <?php if($isIncOb == 0){print("selected='selected'");}?>>Tidak</option>
            </select>
			<label for="Output">Output : </label>
			<select id="Output" name="output">
				<option value="web">Web Browser</option>
				<option value="xls">MS Excel</option>
			</select>
			<button type="submit">Generate</button>
		</form>
	</div>
</fieldset>


<!-- REGION: LAPORAN -->
<?php if ($report != null) { ?>
<br />
<div class="container">
	<div class="title bold">
		<?php print($company->CompanyName); ?><br />
	</div>
	<div class="subTitle">
		<?php printf("<u>%s : %s %s</u>",$rekapMethod ? "Bulan" : "S/d. Bulan ",$monthNames[$month], $year); ?>
	</div><br /><br />

	<table cellpadding="0" cellspacing="0" class="tablePadding">
		<tr class="bold center">
			<td rowspan="2" class="bN bE bS bW">No.</td>
            <td rowspan="2" class="bN bE bS bW">No. Akun</td>
			<td rowspan="2" class="bN bE bS">Nama Akun</td>
			<td colspan="2" class="bN bE bS">Saldo Awal</td>
			<td colspan="2" class="bN bE bS">Mutasi Kas (BKK/BKM)</td>
			<td colspan="2" class="bN bE bS">Accrual (BJK/BPK)</td>
            <td colspan="2" class="bN bE bS">Adjustment (BMM/BPB)</td>
            <td colspan="2" class="bN bE bS">After Adjustment</td>
            <td colspan="2" class="bN bE bS">Trial Balance</td>
            <td colspan="2" class="bN bE bS">Profit/Loss</td>
            <td colspan="2" class="bN bE bS">Balance</td>
		</tr>
		<tr class="bold center">
			<td class="bE bS">Debet</td>
			<td class="bE bS">Kredit</td>
            <td class="bE bS">Debet</td>
            <td class="bE bS">Kredit</td>
            <td class="bE bS">Debet</td>
            <td class="bE bS">Kredit</td>
            <td class="bE bS">Debet</td>
            <td class="bE bS">Kredit</td>
            <td class="bE bS">Debet</td>
            <td class="bE bS">Kredit</td>
            <td class="bE bS">Debet</td>
            <td class="bE bS">Kredit</td>
            <td class="bE bS">Debet</td>
            <td class="bE bS">Kredit</td>
            <td class="bE bS">Debet</td>
            <td class="bE bS">Kredit</td>
		</tr>

		<?php
        $oblDebitSum = 0;
        $oblCreditSum = 0;
        $mtcDebitSum = 0;
        $mtcCreditSum = 0;
        $acrDebitSum = 0;
        $acrCreditSum = 0;
        $trbDebitSum = 0;
        $trbCreditSum = 0;
        $adjDebitSum = 0;
        $adjCreditSum = 0;
        $aadjDebitSum = 0;
        $aadjCreditSum = 0;
        $plDebitSum = 0;
        $plCreditSum = 0;
        $blDebitSum = 0;
        $blCreditSum = 0;
        $nurut = 0;
		while ($row = $report->FetchAssoc()) {
            $oblDebit = 0;
            $oblCredit = 0;
            $mtcDebit = 0;
            $mtcCredit = 0;
            $acrDebit = 0;
            $acrCredit = 0;
            $adjDebit = 0;
            $adjCredit = 0;
            $trbDebit = 0;
            $trbCredit = 0;
            $aadjDebit = 0;
            $aadjCredit = 0;
            $plDebit = 0;
            $plCredit = 0;
            $blDebit = 0;
            $blCredit = 0;
            if($isIncOb == 1){
                $oblDebit = ($row["total_debit_prev"] + $row["bal_debit_amt"]);
                $oblCredit = ($row["total_credit_prev"] + $row["bal_credit_amt"]);
            }
            $mtcDebit = $row["mtc_debit"];
            $acrDebit = $row["acr_debit"];
            $adjDebit = $row["adj_debit"];
            $mtcCredit = $row["mtc_credit"];
            $acrCredit = $row["acr_credit"];
            $adjCredit = $row["adj_credit"];

            $aadjDebit = $oblDebit + $mtcDebit + $acrDebit + $adjDebit;
            $aadjCredit = $oblCredit + $mtcCredit + $acrCredit + $adjCredit;
            if($row["posisi_saldo"] == "DK"){
                $trbDebit = $aadjDebit - $aadjCredit;
            }elseif($row["posisi_saldo"] == "KD"){
                $trbCredit = $aadjCredit - $aadjDebit;
            }
            // hitung profit-loss
            if(left($row["acc_no"],1) > 3){
               if($row["posisi_saldo"] == "DK"){
                  $plDebit = $trbDebit;
               }elseif($row["posisi_saldo"] == "KD"){
                  $plCredit = $trbCredit;
               }
            }else{
                if($row["posisi_saldo"] == "DK"){
                    $blDebit = $trbDebit;
                }elseif($row["posisi_saldo"] == "KD"){
                    $blCredit = $trbCredit;
                }
            }
            $oblDebitSum += $oblDebit;
            $oblCreditSum += $oblCredit;
            $mtcDebitSum += $mtcDebit;
            $mtcCreditSum += $mtcCredit;
            $acrDebitSum += $acrDebit;
            $acrCreditSum += $acrCredit;
            $trbDebitSum += $trbDebit;
            $trbCreditSum += $trbCredit;
            $adjDebitSum += $adjDebit;
            $adjCreditSum += $adjCredit;
            $aadjDebitSum += $aadjDebit;
            $aadjCreditSum += $aadjCredit;
            $plDebitSum += $plDebit;
            $plCreditSum += $plCredit;
            $blDebitSum += $blDebit;
            $blCreditSum += $blCredit;
        // jangan tampilkan yang kosong nilainya
        //if($aadjDebit+$aadjCredit <> 0){
        if($oblDebit+$oblCredit+$mtcDebit+$mtcCredit+$acrDebit+$acrCredit+$adjDebit+$adjCredit <> 0){
           $nurut++;
        ?>
		<tr>
			<td class="bE bW"><?php print($nurut); ?></td>
            <td class="bE bW"><?php print($row["acc_no"]); ?></td>
			<td class="bE" nowrap="nowrap"><?php print($row["acc_name"]); ?></td>
			<td class="bE right"><?php print(number_format($oblDebit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($oblCredit, 2)); ?></td>
			<td class="bE right"><?php print(number_format($mtcDebit, 2)); ?></td>
			<td class="bE right"><?php print(number_format($mtcCredit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($acrDebit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($acrCredit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($adjDebit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($adjCredit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($aadjDebit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($aadjCredit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($trbDebit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($trbCredit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($plDebit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($plCredit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($blDebit, 2)); ?></td>
            <td class="bE right"><?php print(number_format($blCredit, 2)); ?></td>
		</tr>
		<?php }} ?>
        <tr class="bold center">
            <td colspan="3" class="bN bE bS bW">&nbsp;</td>
            <td colspan="2" class="bN bE bS">Saldo Awal</td>
            <td colspan="2" class="bN bE bS" nowrap="nowrap">Mutasi Kas (BKK/BKM)</td>
            <td colspan="2" class="bN bE bS" nowrap="nowrap">Accrual (BJK/BPK)</td>
            <td colspan="2" class="bN bE bS" nowrap="nowrap">Adjustment (BMM/BPB)</td>
            <td colspan="2" class="bN bE bS" nowrap="nowrap">After Adjustment</td>
            <td colspan="2" class="bN bE bS" nowrap="nowrap">Trial Balance</td>
            <td colspan="2" class="bN bE bS" nowrap="nowrap">Profit/Loss</td>
            <td colspan="2" class="bN bE bS">Balance</td>
        </tr>
        <tr class="bold">
            <td colspan="3" class="bN bE bS bW right">TOTAL :</td>
            <td class="bN bE bS right"><?php print(number_format($oblDebitSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($oblCreditSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($mtcDebitSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($mtcCreditSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($acrDebitSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($acrCreditSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($adjDebitSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($adjCreditSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($aadjDebitSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($aadjCreditSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($trbDebitSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($trbCreditSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($plDebitSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($plCreditSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($blDebitSum, 2)); ?></td>
            <td class="bN bE bS right"><?php print(number_format($blCreditSum, 2)); ?></td>
        </tr>
        <tr class="bold">
            <td colspan="3" class="bN bE bS bW right">SELISIH :</td>
            <td class="bN bE bS center" colspan="2"><?php print(number_format($oblDebitSum - $oblCreditSum, 2)); ?></td>
            <td class="bN bE bS center" colspan="2"><?php print(number_format($mtcDebitSum - $mtcCreditSum, 2)); ?></td>
            <td class="bN bE bS center" colspan="2"><?php print(number_format($acrDebitSum - $acrCreditSum, 2)); ?></td>
            <td class="bN bE bS center" colspan="2"><?php print(number_format($adjDebitSum - $adjCreditSum, 2)); ?></td>
            <td class="bN bE bS center" colspan="2"><?php print(number_format($aadjDebitSum - $aadjCreditSum, 2)); ?></td>
            <td class="bN bE bS center" colspan="2"><?php print(number_format($trbDebitSum - $trbCreditSum, 2)); ?></td>
            <td class="bN bE bS right" style="color: green"><?php print(number_format($plDebitSum > $plCreditSum ? 0 : $plCreditSum - $plDebitSum, 2)); ?></td>
            <td class="bN bE bS right" style="color: red"><?php print(number_format($plDebitSum > $plCreditSum ? $plDebitSum - $plCreditSum : 0, 2)); ?></td>
            <td class="bN bE bS right" style="color: red"><?php print(number_format($blDebitSum > $blCreditSum ? 0 : $blCreditSum - $blDebitSum, 2)); ?></td>
            <td class="bN bE bS right" style="color: green"><?php print(number_format($blDebitSum > $blCreditSum ? $blDebitSum - $blCreditSum : 0, 2)); ?></td>
        </tr>
	</table>
</div>
<?php } ?>
<!-- END REGION: LAPORAN-->

</body>
</html>
