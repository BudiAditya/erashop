<!DOCTYPE HTML>
<html>
<head>
	<title>ERASHOP - Edit Data Karyawan</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            var elements = ["EntityId","Nik","Nama","NmPanggilan","DeptId","Jabatan","Alamat","Handphone","T4Lahir","TglLahir","Jkelamin","Agama","Pendidikan","Status","BpjsNo","BpjsDate","MulaiKerja","ResignDate","btSubmit"];
            BatchFocusRegister(elements);
            $("#TglLahir").customDatePicker({ showOn: "focus" });
            $("#MulaiKerja").customDatePicker({ showOn: "focus" });
            $("#BpjsDate").customDatePicker({ showOn: "focus" });
            $("#ResignDate").customDatePicker({ showOn: "focus" });
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
	<legend><b>Ubah Data Karyawan</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.karyawan/edit/".$karyawan->Id)); ?>" method="post" enctype="multipart/form-data">
		<input type="hidden" id="Id" name="Id" value="<?php print($karyawan->Id); ?>"/>
        <input type="hidden" id="Fphoto" name="Fphoto" value="<?php print($karyawan->Fphoto); ?>"/>
		<table cellpadding="2" cellspacing="1">
            <tr>
                <td>Perusahaan</td>
                <td colspan="2"><select name="EntityId" class="text2" id="EntityId" autofocus required>
                        <option value=""></option>
                        <?php
                        foreach ($companies as $sbu) {
                            if ($sbu->EntityId == $karyawan->EntityId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $sbu->EntityId, $sbu->EntityCd, $sbu->CompanyName);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $sbu->EntityId, $sbu->EntityCd, $sbu->CompanyName);
                            }
                        }
                        ?>
                    </select></td>
                <td>N I K</td>
                <td><input type="text" class="text2" name="Nik" id="Nik" maxlength="10" size="20" value="<?php print($karyawan->Nik); ?>" pattern="^\s*([1-9][0-9]{3})\s*$" required /></td>
                <td rowspan="7" colspan="2" class="center">
                    <?php
                    printf('<img src="%s" width="200" height="200"/>',$helper->site_url($karyawan->Fphoto));
                    ?>
                </td>
            </tr>
            <tr>
                <td>Nama Lengkap</td>
                <td colspan="2"><input type="text" class="text2" name="Nama" id="Nama" maxlength="50" size="50" value="<?php print($karyawan->Nama); ?>" required /></td>
                <td>Panggilan</td>
                <td><input type="text" class="text2" name="NmPanggilan" id="NmPanggilan" maxlength="50" size="20" value="<?php print($karyawan->NmPanggilan); ?>" required /></td>
            </tr>
            <tr>
                <td>Bagian</td>
                <td colspan="2"><select name="DeptId" class="text2" id="DeptId" required>
                        <option value=""></option>
                        <?php
                        foreach ($depts as $dept) {
                            if ($dept->Id == $karyawan->DeptId) {
                                printf('<option value="%d" selected="selected">%s - %s</option>', $dept->Id, $dept->DeptCd, $dept->DeptName);
                            } else {
                                printf('<option value="%d">%s - %s</option>', $dept->Id, $dept->DeptCd, $dept->DeptName);
                            }
                        }
                        ?>
                    </select></td>
                <td>Jabatan</td>
                <td><select name="Jabatan" class="text2" id="Jabatan">
                        <option value=""></option>
                        <option value="STF" <?php ($karyawan->Jabatan == "STF" ? print('selected = "selected"'):'');?>>Staf</option>
                        <option value="SPV" <?php ($karyawan->Jabatan == "SPV" ? print('selected = "selected"'):'');?>>Supervisor</option>
                        <option value="MGR" <?php ($karyawan->Jabatan == "MGR" ? print('selected = "selected"'):'');?>>Manager</option>
                        <option value="DIR" <?php ($karyawan->Jabatan == "DIR" ? print('selected = "selected"'):'');?>>Direktur</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Alamat</td>
                <td colspan="4"><input type="text" class="text2" name="Alamat" id="Alamat" maxlength="250" size="90" value="<?php print($karyawan->Alamat); ?>" /></td>
            </tr>
            <tr>
                <td>HandPhone</td>
                <td colspan="3"><input type="tel" class="text2" name="Handphone" id="Handphone" maxlength="50" size="50" value="<?php print($karyawan->Handphone); ?>" /></td>
            </tr>
            <tr>
                <td>Lahir di</td>
                <td><input type="text" class="text2" name="T4Lahir" id="T4Lahir" maxlength="50" size="20" value="<?php print($karyawan->T4Lahir); ?>" /></td>
                <td>Tgl.Lahir</td>
                <td><input type="text" class="text2" name="TglLahir" id="TglLahir" maxlength="10" size="15" value="<?php print($karyawan->FormatTglLahir(JS_DATE)); ?>" /></td>
                <td>Gender
                    &nbsp;&nbsp;
                    <select name="Jkelamin" class="text2" id="Jkelamin" required style="width: 130px;">
                        <option value=""></option>
                        <option value="L" <?php ($karyawan->Jkelamin == "L" ? print('selected = "selected"'):'');?>>Laki-laki</option>
                        <option value="P" <?php ($karyawan->Jkelamin == "P" ? print('selected = "selected"'):'');?>>Perempuan</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Agama</td>
                <td><select name="Agama" class="text2" id="Agama" style="width: 125px">
                        <option value=""></option>
                        <option value="Budha" <?php ($karyawan->Agama == "Budha" ? print('selected = "selected"'):'');?>>Budha</option>
                        <option value="Hindu" <?php ($karyawan->Agama == "Hindu" ? print('selected = "selected"'):'');?>>Hindu</option>
                        <option value="Islam" <?php ($karyawan->Agama == "Islam" ? print('selected = "selected"'):'');?>>Islam</option>
                        <option value="Katolik" <?php ($karyawan->Agama == "Katolik" ? print('selected = "selected"'):'');?>>Katolik</option>
                        <option value="Kristen" <?php ($karyawan->Agama == "Kristen" ? print('selected = "selected"'):'');?>>Kristen</option>
                    </select>
                </td>
                <td>Pendidikan</td>
                <td><select name="Pendidikan" class="text2" id="Pendidikan" style="width: 100px;">
                        <option value=""></option>
                        <option value="SD" <?php ($karyawan->Pendidikan == "SD" ? print('selected = "selected"'):'');?>>SD</option>
                        <option value="SMP" <?php ($karyawan->Pendidikan == "SMP" ? print('selected = "selected"'):'');?>>SMP</option>
                        <option value="SMA" <?php ($karyawan->Pendidikan == "SMA" ? print('selected = "selected"'):'');?>>SMA</option>
                        <option value="Diploma" <?php ($karyawan->Pendidikan == "Diploma" ? print('selected = "selected"'):'');?>>Diploma</option>
                        <option value="Sarjana" <?php ($karyawan->Pendidikan == "Sarjana" ? print('selected = "selected"'):'');?>>Sarjana</option>
                    </select>
                </td>
                <td>Status
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <select name="Status" class="text2" id="Status" style="width: 130px;">
                        <option value=""></option>
                        <option value="TK" <?php ($karyawan->Status == "TK" ? print('selected = "selected"'):'');?>>TK - Tidak Kawin</option>
                        <option value="K0" <?php ($karyawan->Status == "K0" ? print('selected = "selected"'):'');?>>K/0 - Kawin 0 Anak</option>
                        <option value="K1" <?php ($karyawan->Status == "K1" ? print('selected = "selected"'):'');?>>K/1 - Kawin 1 Anak</option>
                        <option value="K2" <?php ($karyawan->Status == "K2" ? print('selected = "selected"'):'');?>>K/2 - Kawin 2 Anak</option>
                        <option value="K3" <?php ($karyawan->Status == "K3" ? print('selected = "selected"'):'');?>>K/3 - Kawin 3 Anak</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>No. BPJS</td>
                <td><input type="text" class="text2" name="BpjsNo" id="BpjsNo" maxlength="15" size="20" value="<?php print($karyawan->BpjsNo); ?>" /></td>
                <td>Tgl. BPJS</td>
                <td><input type="text" class="text2" name="BpjsDate" id="BpjsDate" maxlength="15" size="15" value="<?php print($karyawan->FormatBpjsDate(JS_DATE)); ?>" /></td>
                <td>&nbsp;</td>
                <td>File Photo</td>
                <td><input type="file" class="text2" name="FileName" id="FileName" accept="image/*" /></td>
            </tr>
            <tr>
                <td>Tgl. Mulai Kerja</td>
                <td><input type="text" class="text2" name="MulaiKerja" id="MulaiKerja" maxlength="20" size="20" value="<?php print($karyawan->FormatMulaiKerja(JS_DATE)); ?>" /></td>
                <td>Tgl. Keluar</td>
                <td><input type="text" class="text2" name="ResignDate" id="ResignDate" maxlength="15" size="15" value="<?php print($karyawan->FormatResignDate(JS_DATE)); ?>" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">
                    <button id="btSubmit" type="submit">Update</button>
                    <a href="<?php print($helper->site_url("master.karyawan")); ?>" class="button">Daftar Karyawan</a>
                </td>
            </tr>
		</table>
	</form>
</fieldset>
</body>
</html>
