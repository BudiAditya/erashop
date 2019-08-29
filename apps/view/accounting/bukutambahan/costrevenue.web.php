<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
/** @var $company Company */ /** @var $monthNames string[] */ /** @var $parentAccounts CoaGroup[] */ /** @var $kodeInduk int */
/** @var $month int */ /** @var $year int */ /** @var int $status */ /** @var string $statusName */
/** @var $report null|ReaderBase */
/** @var $cabangId int */ /** @var $cabangList Cabang[] */
?>
<head>
	<title>Erasys - Cost & Revenue</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>" />

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
	<legend><span class="bold">Laporan Pendapatan dan Biaya</span></legend>

	<form action="<?php print($helper->site_url("accounting.bukutambahan/costrevenue")); ?>" method="GET">
		<table cellpadding="0" cellspacing="0" class="tablePadding" style="margin: 0 auto;">
			<tr>
				<td class="right"><label for="kodeInduk">Jenis Laporan : </label></td>
				<td>
					<select id="kodeInduk" name="kodeInduk">
                    <option value="4"<?php print($kodeInduk == "4" ? "selected='selected'" : "");?>>Pendapatan  (400)</option>
                    <option value="5"<?php print($kodeInduk == "5" ? "selected='selected'" : "");?>>Biaya-biaya (500)</option>
					</select>
				</td>
			</tr>
            <tr>
                <td class="right"><label for="idCabang">Cabang : </label></td>
                <td>
                    <select id="idCabang" name="idCabang">
                        <option value="0">-- Not Filtered --</option>
                        <?php
                        $selectedCabang = null;
                        foreach ($cabangList as $cabang) {
                            if($cabang->Id == $idCabang){
                                $selectedCabang = $cabang;
                                printf('<option value="%d" selected="selected">%s - %s</option>', $cabang->Id, $cabang->Kode, $cabang->Cabang);
                            }else{
                                printf('<option value="%d">%s - %s</option>', $cabang->Id, $cabang->Kode, $cabang->Cabang);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
			<tr>
				<td class="right"><label for="Month">Periode : </label></td>
				<td>
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
				</td>
			</tr>
			<tr>
				<td class="right"><label for="DocStatus">Status Dokumen :</label></td>
				<td>
					<select id="DocStatus" name="status">
						<option value="-1" <?php print($status == -1 ? 'selected="selected"' : ''); ?>>SEMUA DOKUMEN</option>
						<option value="0" <?php print($status == 0 ? 'selected="selected"' : ''); ?>>DRAFT</option>
						<option value="1" <?php print($status == 1 ? 'selected="selected"' : ''); ?>>APPROVED</option>
						<option value="2" <?php print($status == 2 ? 'selected="selected"' : ''); ?>>VERIFIED</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="right"><label for="Output">Output : </label></td>
				<td>
					<select id="Output" name="output">
						<option value="web">Web Browser</option>
                        <option value="xls">Excel Format</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><button type="submit">Generate</button></td>
			</tr>
		</table>
	</form>
</fieldset>


<!-- REGION: LAPORAN -->
<?php if ($report != null) { ?>
<br />
<div class="container">
	<div class="title bold">
		<?php printf("%s - %s", $company->EntityCd, $company->CompanyName); ?><br />
	</div>
	<div class="subTitle">
        <?php
        if($kodeInduk == "4"){
            print("LAPORAN PENDAPATAN-PENDAPATAN");
        }else{
            print("LAPORAN BIAYA-BIAYA");
        }
        print("<br />");
		printf("Periode : %s %s", $monthNames[$month], $year);
        print("<br />");
        if($selectedCabang != null){printf('Cabang : %s - %s', $selectedCabang->Kode, $selectedCabang->Cabang);}
        ?>
	</div><br /><br />

	<table cellpadding="0" cellspacing="0" class="tablePadding">
		<tr class="bold center">
			<td rowspan="2" class="bN bE bS bW">No. Akun</td>
			<td rowspan="2" class="bN bE bS">Nama Akun</td>
			<td rowspan="2" class="bN bE bS">s.d. Bulan Lalu</td>
			<td colspan="2" class="bN bE bS">Mutasi <?php printf("%s %s", $monthNames[$month], $year); ?></td>
			<td rowspan="2" class="bN bE bS">s.d. Bulan Ini</td>
		</tr>
		<tr class="bold center">
			<td class="bE bS">Debet</td>
			<td class="bE bS">Kredit</td>
		</tr>
		<?php
		$sumDebit = 0;
		$sumCredit = 0;
		$sumPrevSaldo = 0;
		$sumSaldo = 0;
		$startDate = mktime(0, 0, 0, $month, 1, $year);
		$endDate = mktime(0, 0, 0, $month + 1, 0, $year);
		while($row = $report->FetchAssoc()) {
			$posisiSaldo = $row["psaldo"];
			$sumDebit += $row["total_debit"];
			$sumCredit += $row["total_credit"];

			if ($posisiSaldo == "DK") {
				$prevSaldo = ($row["bal_debit_amt"] - $row["bal_credit_amt"]) + ($row["total_debit_prev"] - $row["total_credit_prev"]);
				$saldo = $row["total_debit"] - $row["total_credit"];
			} else  if($posisiSaldo == "KD") {
				$prevSaldo = ($row["bal_credit_amt"] - $row["bal_debit_amt"]) + ($row["total_credit_prev"] - $row["total_debit_prev"]);
				$saldo = $row["total_credit"] - $row["total_debit"];
			} else {
				throw new Exception("Invalid posisi_saldo! CODE: " . $posisiSaldo);
			}

			$sumPrevSaldo += $prevSaldo;
			$sumSaldo += $prevSaldo + $saldo;
        if ($prevSaldo + $saldo <> 0){
		?>
		<tr>
			<td class="bE bW"><?php print($row["acc_no"]); ?></td>
			<td class="bE bW"><?php print($row["acc_name"]); ?></td>
			<td class="bE right"><?php print(number_format($prevSaldo, 2)); ?></td>
			<td class="bE right"><?php print(number_format($row["total_debit"], 2)); ?></td>
			<td class="bE right"><?php print(number_format($row["total_credit"], 2)); ?></td>
			<td class="bE right"><?php print(number_format($prevSaldo + $saldo, 2)); ?></td>
		</tr>
		<?php }} ?>
		<tr class="bold">
			<td colspan="2" class="bN bE bS bW right">TOTAL :</td>
			<td class="bN bE bS right"><?php print(number_format($sumPrevSaldo, 2)); ?></td>
			<td class="bN bE bS right"><?php print(number_format($sumDebit, 2)); ?></td>
			<td class="bN bE bS right"><?php print(number_format($sumCredit, 2)); ?></td>
			<td class="bN bE bS right"><?php print(number_format($sumSaldo, 2)); ?></td>
		</tr>
	</table>
</div>
<?php } ?>
<!-- END REGION: LAPORAN-->

</body>
</html>
