<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
/** @var $accountNo int */ /** @var $accounts array */ /** @var $start int */ /** @var $end int */ /** @var $openingBalance null|OpeningBalance */
/** @var int $status */ /** @var string $statusName */ /** @var $cabangId int */ /** @var $cabangList Cabang[] */
/** @var $transaction null|array */ /** @var $report null|ReaderBase */ /** @var $output string */ /** @var $company Company */
$haveData = $openingBalance != null;
?>
<head>
	<title>Erasys - Laporan Detail Buku Tambahan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#Start").customDatePicker().datepicker("show");
			$("#End").customDatePicker();
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
	<legend><span class="bold">Laporan Detail Buku Tambahan</span></legend>

	<form action="<?php print($helper->site_url("accounting.bukutambahan/detail")); ?>" method="GET">
		<table cellspacing="0" cellpadding="0" class="tablePadding" style="margin: 0 auto;">
			<tr>
				<td class="right"><label for="Account">Pilih Akun : </label></td>
				<td>
					<select id="Account" name="account">
						<option value="">-- PILIH AKUN --</option>
						<?php
						/** @var $selectedAccount CoaDetail */
						$selectedAccount = null;
						foreach ($accounts as $account) {
							if ($account->Kode == $accountNo) {
								$selectedAccount = $account;
								printf('<option value="%s" selected="selected">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
							} else {
								printf('<option value="%s">%s - %s</option>', $account->Kode, $account->Kode, $account->Perkiraan);
							}
						}
						?>
					</select>
				</td>
			</tr>
            <tr>
                <td class="right"><label for="Cabang">Pilih Cabang : </label></td>
                <td>
                    <select id="Cabang" name="idCabang">
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
				<td class="right"><label for="Start">Periode : </label></td>
				<td>
					<input type="text" id="Start" name="start" value="<?php print(date(JS_DATE, $start)) ?>" />
					<label for="End"> s.d. </label>
					<input type="text" id="End" name="end" value="<?php print(date(JS_DATE, $end)) ?>" />
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
						<option value="web" <?php print($output == "web" ? 'selected="selected"' : '') ?>>Web Browser</option>
						<option value="xls" <?php print($output == "xls" ? 'selected="selected"' : '') ?>>Excel</option>
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
		Detail Buku Tambahan<br />
		<?php printf("Periode: %s s.d. %s", date(HUMAN_DATE, $start), date(HUMAN_DATE, $end)); ?><br />
		<?php printf("Akun: %s - %s (Status: %s)", $selectedAccount->Kode, $selectedAccount->Perkiraan, $statusName); ?>
        <?php
        if($selectedCabang != null){
            print("<br/>");
            printf('Proyek : %s - %s', $selectedCabang->Kode, $selectedCabang->Cabang);
        }
        ?>
	</div><br /><br />

	<table cellpadding="0" cellspacing="0" class="tablePadding tableBorder">
		<tr class="bold center">
			<td>Tgl</td>
			<td>No. Voucher</td>
			<td>Uraian</td>
			<td>Cabang</td>
			<td>Debet</td>
			<td>Kredit</td>
		</tr>
		<tr>
			<td><?php print(date("d", $start)); ?></td>
			<td>&nbsp;</td>
			<td>Saldo Awal <?php print(date(HUMAN_DATE, $start)); ?></td>
			<td>&nbsp;</td>
			<td class="right"><?php print(number_format(($haveData && $openingBalance->GetCoa()->PosisiSaldo == "DK") ? $transaction["saldo"] : 0, 2)) ?></td>
			<td class="right"><?php print(number_format(($haveData && $openingBalance->GetCoa()->PosisiSaldo == "KD") ? $transaction["saldo"] : 0, 2)) ?></td>
		</tr>
		<?php
		$counter = 0;
		$prevDate = null;
		$prevVoucherNo = null;

		$flagDate = true;
		$flagVoucherNo = true;

		$totalDebit = ($haveData && $openingBalance->GetCoa()->PosisiSaldo == "DK") ? $transaction["saldo"] : 0;
		$totalCredit = ($haveData && $openingBalance->GetCoa()->PosisiSaldo == "KD") ? $transaction["saldo"] : 0;
		while ($row = $report->FetchAssoc()) {
			// Convert datetime jadi native format
			$row["tgl_voucher"] = strtotime($row["tgl_voucher"]);
			$counter++;
			$className = $counter % 2 == 0 ? "itemRow evenRow" : "itemRow oddRow";
			if ($prevDate != $row["tgl_voucher"]) {
				$prevDate = $row["tgl_voucher"];
				$flagDate = true;
			} else {
				$flagDate = false;
			}

			if ($prevVoucherNo != $row["no_voucher"]) {
				$prevVoucherNo = $row["no_voucher"];
				$flagVoucherNo = true;
				$link = $helper->site_url("accounting.jurnal/view/" . $row["id"]);
				$anchor = '<a href="' . $link . '" target="blank">' . $prevVoucherNo . '</a>';
			} else {
				$flagVoucherNo = false;
				$anchor = null;
			}

			$debit = $row["acdebet_no"] == $accountNo ? $row["jumlah"] : 0;
			$credit = $row["ackredit_no"] == $accountNo ? $row["jumlah"] : 0;
			$totalDebit += $debit;
			$totalCredit += $credit;
		?>
		<tr class="<?php print($className); ?>">
			<td><?php print($flagDate ? date("d", $prevDate) : "&nbsp;"); ?></td>
			<td><?php print($flagVoucherNo ? $anchor : "&nbsp;"); ?></td>
			<td><?php print($row["uraian"]); ?></td>
			<td><?php print($row["kd_cabang"].' - '.$row["cabang"]); ?></td>
			<td class="right"><?php print(number_format($debit, 2)); ?></td>
			<td class="right"><?php print(number_format($credit, 2)); ?></td>
		</tr>
		<?php }	?>
		<tr class="bold right">
			<td colspan="4">GRAND TOTAL :</td>
			<td><?php print(number_format($totalDebit, 2)); ?></td>
			<td><?php print(number_format($totalCredit, 2)); ?></td>
		</tr>
		<tr class="bold right">
			<td colspan="4">SALDO AKHIR :</td>
			<td><?php print($selectedAccount->PosisiSaldo == "DK" ? number_format($totalDebit - $totalCredit, 2) : "&nbsp;"); ?></td>
			<td><?php print($selectedAccount->PosisiSaldo == "KD" ? number_format($totalCredit - $totalDebit, 2) : "&nbsp;"); ?></td>
		</tr>
	</table>
</div>
<?php } ?>
<!-- END REGION: LAPORAN-->


</body>
</html>
