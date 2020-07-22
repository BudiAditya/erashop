<!DOCTYPE HTML>
<html>
<?php /** @var $userAdmin UserAdmin */ /** @var $companies Company[] */ /** @var $cabangs Cabang[] */  /** @var $karyawans Karyawan[] */ ?>
<head>
	<title>ERASHOP - Tambah User</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/default/easyui.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/icon.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-themes/color.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/easyui-demo/demo.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.easyui.min.js")); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			var elements = ["UserId", "EmployeeId", "UserEmail", "UserPwd1", "UserPwd2", "xEntityId", "xCabangId", "UserLvl", "AllowMultipleLogin", "IsAktif"];
			BatchFocusRegister(elements);

			$('#xEntityId').combogrid({
				panelWidth:300,
				url: "<?php print($helper->site_url("master.company/getjson_companies"));?>",
				idField:'company_name',
				textField:'company_name',
				mode:'remote',
				fitColumns:true,
				columns:[[
					{field:'company_name',title:'Perusahaan',width:100},
					{field:'entity_cd',title:'Kode',width:50}
				]],
				onSelect: function(index,row){
					var eti = row.entity_id;
					console.log(eti);
					$("#CabangId").val(0);
					$("#EntityId").val(eti);
					var urz = "<?php print($helper->site_url("master.cabang/getjson_cabangs/"));?>"+eti;
					$('#xCabangId').combogrid('grid').datagrid('load',urz);
				}
			});

			$('#xCabangId').combogrid({
				panelWidth:300,
				url: "<?php print($helper->site_url("master.cabang/getjson_cabangs/0"));?>",
				idField:'kode',
				textField:'kode',
				mode:'remote',
				fitColumns:true,
				columns:[[
					{field:'kode',title:'Kode',width:50},
					{field:'cabang',title:'Nama Cabang',width:100}
				]],
				onSelect: function(index,row){
					var cbi = row.id;
					console.log(cbi);
					$("#CabangId").val(cbi);
				}
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
	<legend><b>Tambah Data User System</b></legend>
	<form id="frm" action="<?php print($helper->site_url("master.useradmin/add")); ?>" method="post">
		<input type="hidden" name="EntityId" id="EntityId" value="<?php print($userAdmin->EntityId ? null : 0);?>"/>
		<input type="hidden" name="CabangId" id="CabangId" value="<?php print($userAdmin->CabangId ? null : 0);?>"/>
		<table cellpadding="2" cellspacing="1">
			<tr>
				<td><label for="UserId">User ID</label></td>
				<td><input type="text" name="UserId" id="UserId" maxlength="10" size="10" value="<?php print($userAdmin->UserId);?>" autofocus/></td>
			</tr>
			<tr>
				<td><label for="EmployeeId">Nama Lengkap</label></td>
				<td colspan="3"><select class="easyui-combobox" name="EmployeeId" id="EmployeeId" style="width: 225px;">
                        <option value="0"></option>
                        <?php
                        foreach ($karyawans as $karyawan) {
                            if ($karyawan->Id == $userAdmin->EmployeeId) {
                                printf('<option value="%d" selected="selected">%s</option>', $karyawan->Id, $karyawan->Nama);
                            } else {
                                printf('<option value="%d">%s</option>', $karyawan->Id, $karyawan->Nama);
                            }
                        }
                        ?>
                    </select>
                </td>
			</tr>
			<tr>
				<td><label for="UserEmail">Alamat Email</label></td>
				<td colspan="2"><input type="email" name="UserEmail" id="UserEmail" maxlength="100" size="40" value="<?php print($userAdmin->UserEmail);?>"/></td>
			</tr>
			<tr>
				<td><label for="UserPwd1">Password</label></td>
				<td><input type="password" name="UserPwd1" id="UserPwd1" maxlength="50" size="15" value=""/></td>
			</tr>
			<tr>
				<td><label for="UserPwd2">Konf. Passwd</label></td>
				<td><input type="password" name="UserPwd2" id="UserPwd2" maxlength="50" size="15" value=""/></td>
			</tr>
			<tr>
				<td><label for="xEntityId">Perusahaan</label></td>
				<td><input name="xEntityId" id="xEntityId" style="width: 250px;" value="<?php print($userAdmin->CompanyName);?>"></td>
                <td><label for="xCabangId">Cabang</label></td>
                <td><input name="xCabangId" id="xCabangId" value="<?php print($userAdmin->CabangKode);?>"></td>
			</tr>
			<tr>
				<td><label for="UserLvl">User Level</label></td>
				<td><select class="easyui-combobox" name="UserLvl" id="UserLvl" style="width: 150px;">
					<option value="1" <?php print($userAdmin->UserLvl == 1 ? 'selected="selected"' : ''); ?>>Operator</option>
					<option value="2" <?php print($userAdmin->UserLvl == 2 ? 'selected="selected"' : ''); ?>>Supervisor</option>
					<option value="3" <?php print($userAdmin->UserLvl == 3 ? 'selected="selected"' : ''); ?>>Manager</option>
					<option value="4" <?php print($userAdmin->UserLvl == 4 ? 'selected="selected"' : ''); ?>>Owner</option>
					<option value="5" <?php print($userAdmin->UserLvl == 5 ? 'selected="selected"' : ''); ?>>Administrator</option>
				</select></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" id="AllowMultipleLogin" name="AllowMultipleLogin" <?php print($userAdmin->AllowMultipleLogin == 1 ? 'checked="checked"' : ''); ?> />
					<label for="AllowMultipleLogin">Boleh Multiple Login</label>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" id="IsAktif" name="IsAktif" <?php print($userAdmin->IsAktif == 1 ? 'checked="checked"' : ''); ?> />
					<label for="IsAktif">User Aktif</label>
					&nbsp;&nbsp;&nbsp;
					<input type="checkbox" id="IsForcePeriod" name="IsForcePeriod" <?php print($userAdmin->IsForceAccountingPeriod == 1 ? 'checked="checked"' : ''); ?> />
					<label for="IsForcePeriod">Force Select Accounting Period</label>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<button id="BtSimpan" type="submit">Submit</button>
                    <a href="<?php print($helper->site_url("master.useradmin")); ?>">Daftar User</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<!-- </body> -->
</html>
