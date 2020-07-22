<!DOCTYPE HTML>
<?php /** @var $year int */ /** @var $month int */ ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>ERASYS - User Login</title>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            //var elements = ["user_id", "user_pwd", "user_captcha", "user_trxmonth", "user_trxyear", "btn_login"];
			var elements = ["user_cabang_id","user_id", "user_pwd", "user_captcha", "btn_login"];
			BatchFocusRegister(elements);
		});
	</script>

	<style type="text/css"> /* css settings */

	.text1 {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
	}

	.text2 {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 10px;
		color: #0000FF;
	}

	.text4 {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 13px;
		color: #000066;
	}
	</style>
</head>

<body>
<div style="padding:50px;">
	<table align="center" width="100%" border="0" cellpadding="4" cellspacing="0">
		<td align="center">
			<img src="<?php print(base_url('public/images/company/pos-logo.jpg'));?>" width="500" height="150">
					</td>
	</table>
	<div align="center">
		<h1 style="color: #0000FF">ERASHOP - INTEGRATED SALES SYSTEM</h1>
	</div>
	<hr/>
	<form action="<?php echo site_url("home/login"); ?>" method="post" autocomplete="off">
		<table width="500" border="0" align="center" cellpadding="2" cellspacing="0">
			<tr>
				<td class="text4">Cabang/Outlet/Gudang</td>
				<td><select name="user_cabang_id" id="user_cabang_id" style="width:175px" required>
						<option value="0">-- PILIH CABANG --</option>
						<?php
								foreach($cablists as $cabang){
								printf("<option value='%d'>%s</option>",$cabang->Id,$cabang->Kode);
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="text4" width="128">Nama Pemakai</td>
				<td width="302"><input type="text" name="user_id" style="width:170px" value="" id="user_id" autocomplete="off" required/></td>
			</tr>
            <tr>
                <td class="text4">Kata Sandi</td>
                <td><input type="password" name="user_pwd" style="width:170px" value="" id="user_pwd" autocomplete="off" required/></td>
            </tr>
            <tr>
                <td class="text4">Nilai Captcha</td>
                <td><img src="<?php print($helper->site_url("home/capgambar")); ?>" alt="captcha" width="175" height="40" /></td>
            </tr>
            <tr>
                <td class="text4">Isikan Nilai Captcha</td>
                <td><input type="text" name="user_captcha" style="width:170px" value="" id="user_captcha" required/></td>
            </tr>
            <tr>
                <td class="text4">Periode Transaksi</td>
                <td><select name="user_trxmonth" id="user_trxmonth" style="width:115px" required>
                    <option value="1" <?php print($month == 1 ? 'selected="selected"' : ''); ?>>Januari</option>
                    <option value="2" <?php print($month == 2 ? 'selected="selected"' : ''); ?>>Februari</option>
                    <option value="3" <?php print($month == 3 ? 'selected="selected"' : ''); ?>>Maret</option>
                    <option value="4" <?php print($month == 4 ? 'selected="selected"' : ''); ?>>April</option>
                    <option value="5" <?php print($month == 5 ? 'selected="selected"' : ''); ?>>Mei</option>
                    <option value="6" <?php print($month == 6 ? 'selected="selected"' : ''); ?>>Juni</option>
                    <option value="7" <?php print($month == 7 ? 'selected="selected"' : ''); ?>>Juli</option>
                    <option value="8" <?php print($month == 8 ? 'selected="selected"' : ''); ?>>Agustus</option>
                    <option value="9" <?php print($month == 9 ? 'selected="selected"' : ''); ?>>September</option>
                    <option value="10" <?php print($month == 10 ? 'selected="selected"' : ''); ?>>Oktober</option>
                    <option value="11" <?php print($month == 11 ? 'selected="selected"' : ''); ?>>November</option>
                    <option value="12" <?php print($month == 12 ? 'selected="selected"' : ''); ?>>Desember</option>
                    </select>
                    <select name="user_trxyear" id="user_trxyear" required>
                        <?php
                        for ($i = date("Y"); $i >= 2016; $i--) {
                            if ($i == $year) {
                                printf('<option value="%d" selected="selected">%d</option>', $i, $i);
                            } else {
                                printf('<option value="%d">%d</option>', $i, $i);
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
			<tr>
				<td>&nbsp;</td>
				<td align="left"><input type="submit" name="btn_login" value="Masuk" id="btn_login" style="width:85px"/>
					<input type="reset" name="btn_reset" value="Reset" id="btn_reset" style="width:85px"/></td>
			</tr>
		</table>
	</form>
	<hr/>
	<div class="text1" align="center">IP Address Anda :
		<b><?php echo "<span class=\"text2\">" . getenv("REMOTE_ADDR") . "</span>"; ?></b></div>
	<div class="text1" align="center">** Untuk alasan keamanan, kami mencatat seluruh aktifitas Anda pada system **</div>
	<div class="text1" align="center">Permintaan bantuan hubungi : 0431 724 2544, 0812 4413 8229, 0811 431 9858 atau support@eraditya.com</div>
	<div class="text2" align="center">Copyright &copy; 2015 - 2017  CV. Erasystem Infotama</div>
	<?php if (isset($error)) { ?>
	<script type="text/javascript">
		alert("<?php print($error);?>");
	</script>
	<?php } ?>
</div>
<!-- </body> -->
</html>
