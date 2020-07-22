<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
/** @var $month int */ /** @var $year int */ /** @var $docTypes DocType[] */ /** @var $docIds int[] */ /** @var $vocTypes VoucherType[] */
/** @var $report ReaderBase */ /** @var $output string */ /** @var $company Company */ /** @var $orientation string */ /** @var $status int */ /** @var $monthNames array */
/** @var $cabangId int */ /** @var $cabangList Cabang[] */
?>
<head>
	<title>Erashop - Laporan Rekap Jurnal</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<style type="text/css">
		#list {
			margin: 0;
			padding: 0;
		}
		#list li {
			display: inline-block;
			padding: 0 2px;
		}
		#list li label {
			position: relative;
			top: 1px;
			display: inline-block;
			width: 150px;
			overflow: hidden;
		}
		.nowrap { white-space: nowrap; }
	</style>

	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		function CheckAll(type) {
			$("#list").find(":checkbox").each(function(idx, ele) {
				var voucher = $(ele).attr("voucher");
				if (voucher == type) {
					ele.checked = "checked";
				} else {
					ele.checked = "";
				}
			});
		}
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
	<legend><span class="bold">Laporan Rekap Jurnal</span></legend>

	<form action="<?php print($helper->site_url("accounting.report/recap")); ?>" method="GET">
		<table cellpadding="0" cellspacing="0" class="tablePadding" style="margin: 0 auto; width: 80%;">
			<tr>
				<td class="nowrap right"><label for="Month">Periode : </label></td>
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
				<td class="nowrap right" valign="top"><label style="margin-top: 2px; display: inline-block;">Jenis Dokumen :</label></td>
				<td>
					<ul id="list">
						<?php
						$buff = array();
						foreach ($docTypes as $docType) {
							if (in_array($docType->Id, $docIds)) {
								$checkbox = sprintf('<input type="checkbox" id="cb_%d" name="docType[]" value="%d" voucher="%s" checked="checked" />', $docType->Id, $docType->Id, strtoupper($docType->VoucherCd));
								$buff[] = strtoupper($docType->DocCode);
							} else {
								$checkbox = sprintf('<input type="checkbox" id="cb_%d" name="docType[]" value="%d" voucher="%s" />', $docType->Id, $docType->Id, strtoupper($docType->VoucherCd));
							}
							$label = sprintf('<label for="cb_%d" class="nowrap">%s - %s</label>', $docType->Id, $docType->DocCode, $docType->Description);
							printf("<li>%s %s</li>", $checkbox, $label);
						}
						?>
					</ul>
				</td>
			</tr>
			<tr>
				<td class="nowrap right">Centang Semua :</td>
				<td>
					<?php
					foreach ($vocTypes as $vocType) {
						printf('<button type="button" onclick="CheckAll(\'%s\')">%s</button>', strtoupper($vocType->VoucherCd), $vocType->VoucherCd);
					}
					?>
				</td>
			</tr>
            <tr>
                <td class="right"><label for="Cabang">Pilih Proyek : </label></td>
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
				<td class="nowrap right"><label for="Status">Status Voucher :</label></td>
				<td>
					<select id="Status" name="status">
						<option value="-1">SEMUA STATUS</option>
						<option value="0" <?php print($status == 0 ? 'selected="selected"' : ''); ?>>DRAFT</option>
						<option value="1" <?php print($status == 1 ? 'selected="selected"' : ''); ?>>APPROVED</option>
						<option value="2" <?php print($status == 2 ? 'selected="selected"' : ''); ?>>VERIFIED</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="nowrap right"><label for="Output">Output : </label></td>
				<td>
					<select id="Output" name="output">
						<option value="web" <?php print($output == "web" ? 'selected="selected"' : '') ?>>Web Browser</option>
						<option value="xls" <?php print($output == "xls" ? 'selected="selected"' : '') ?>>MS Excel</option>
					</select>
					<label for="Orientation"> posisi cetak : </label>
					<select id="Orientation" name="orientation">
						<option value="p">Portrait</option>
						<option value="l">Landscape</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<button type="submit">Generate</button>
				</td>
			</tr>
		</table>
	</form>
</fieldset>


<!-- REGION: LAPORAN -->
<?php
if ($report != null) {
	switch ($status) {
		case 0:
			$subTitle = "REKAP JURNAL: " . implode(", ", $buff) . " status: DRAFT";
			break;
		case 1:
			$subTitle = "REKAP JURNAL: " . implode(", ", $buff) . " status: APPROVED";
			break;
		case 2:
			$subTitle = "REKAP JURNAL: " . implode(", ", $buff) . " status: VERIFIED";
			break;
		default:
			$subTitle = "REKAP JURNAL: " . implode(", ", $buff) . " status: SEMUA";
			break;
	}
?>
<br />
<div class="container">
	<div class="title bold">
		<?php printf("%s - %s", $company->EntityCd, $company->CompanyName); ?><br />
	</div>
	<div class="subTitle">
		<?php print($subTitle); ?><br />
		Periode: <?php printf("%s %s", $monthNames[$month], $year);
        if($selectedCabang != null){
            print("<br/>");
            printf('Cabang : %s - %s', $selectedCabang->Kode, $selectedCabang->Cabang);
        }
        ?>
	</div><br /><br />

	<table cellpadding="0" cellspacing="0" class="tablePadding">
		<tr class="bold center">
			<td rowspan="2" class="bN bE bS bW">No. Akun</td>
			<td rowspan="2" class="bN bE bS">Nama Akun</td>
			<td colspan="2" class="bN bE bS">Mutasi <?php printf("%s %s", $monthNames[$month], $year); ?></td>
			<td colspan="2" class="bN bE bS">Mutasi s.d. <?php printf("%s %s", $monthNames[$month], $year); ?></td>
		</tr>
		<tr class="bold center">
			<td class="bE bS">Debet</td>
			<td class="bE bS">Kredit</td>
			<td class="bE bS">Debet</td>
			<td class="bE bS">Kredit</td>
		</tr>
		<?php
		$sumDebit = 0;
		$sumCredit = 0;
		$sumAllDebit = 0;
		$sumAllCredit = 0;
		while($row = $report->FetchAssoc()) {
			$sumDebit += $row["total_debit"];
			$sumCredit += $row["total_credit"];
			$sumAllDebit += $row["total_debit"] + $row["total_debit_prev"];
			$sumAllCredit += $row["total_credit"] + $row["total_credit_prev"];
			?>
			<tr>
				<td class="bE bW"><?php print($row["kode"]); ?></td>
				<td class="bE"><?php print($row["perkiraan"]); ?></td>
				<td class="bE right"><?php print(number_format($row["total_debit"], 2)); ?></td>
				<td class="bE right"><?php print(number_format($row["total_credit"], 2)); ?></td>
				<td class="bE right"><?php print(number_format($row["total_debit"] + $row["total_debit_prev"], 2)); ?></td>
				<td class="bE right"><?php print(number_format($row["total_credit"] + $row["total_credit_prev"], 2)); ?></td>
			</tr>
		<?php } ?>
		<tr class="bold">
			<td colspan="2" class="bN bE bS bW right">TOTAL :</td>
			<td class="bN bE bS right"><?php print(number_format($sumDebit, 2)); ?></td>
			<td class="bN bE bS right"><?php print(number_format($sumCredit, 2)); ?></td>
			<td class="bN bE bS right"><?php print(number_format($sumAllDebit, 2)); ?></td>
			<td class="bN bE bS right"><?php print(number_format($sumAllCredit, 2)); ?></td>
		</tr>
	</table>
</div>
<?php } ?>
<!-- END REGION: LAPORAN-->


<!-- </body> -->
</html>
