<!DOCTYPE HTML>
<html>
<head>
    <?php
    /** @var $contacts Contacts  */
    if ($ctype == 1){
        $jdl = "View Data Customer";
        $dft = "Daftar Customer";
        $burl = $helper->site_url("master.customer");
    }else{
        $jdl = "View Data Supplier";
        $dft = "Daftar Supplier";
        $burl = $helper->site_url("master.supplier");
    }
    $eurl = $helper->site_url("master.contacts/edit/".$ctype."/".$contacts->Id);
    ?>
    <title>ERASHOP - <?php print($jdl);?></title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>" />
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
    <div asuransi="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
    <div asuransi="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
    <legend class="bold"><?php print($jdl);?></legend>
    <table cellpadding="2" cellspacing="1" style="tablePadding">
        <tr>
            <td align="right">Entity :</td>
            <td><select id="EntityId" name="EntityId" autofocus disabled="">
                    <?php
                    foreach ($companies as $company) {
                        if ($company->EntityId == $contacts->EntityId) {
                            printf('<option value="%d" selected="selected">%s - %s</option>', $company->EntityId, $company->EntityCd, $company->CompanyName);
                        } else {
                            printf('<option value="%d">%s - %s</option>', $company->EntityId, $company->EntityCd, $company->CompanyName);
                        }
                    }
                    ?>
                </select></td>
        </tr>
        <tr>
            <td align="right">Atas Nama :</td>
            <td><input type="text" name="ContactName" id="ContactName" size="60" maxlength="100" value="<?php print($contacts->ContactName); ?>" readonly/></td>
            <td align="right">Kode :</td>
            <td>
                <input type="text" name="ContactCode" id="ContactCode" size="15" maxlength="20" value="<?php print($contacts->ContactCode); ?>" readonly/>
                Jenis Relasi :
                <select name="ContactTypeId" id="ContactTypeId" required>
                    <?php if ($ctype == 1){ ?>
                        <option value="1" <?php print($contacts->ContactTypeId == "1" ? 'selected="selected"' : ''); ?>>1 - CUSTOMER</option>
                    <?php }elseif ($ctype == 2){ ?>
                        <option value="2" <?php print($contacts->ContactTypeId == "2" ? 'selected="selected"' : ''); ?>>2 - SUPPLIER</option>
                    <?php }else{ ?>
                        <option value="1">1 - CUSTOMER</option>
                        <option value="2">2 - SUPPLIER</option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">Alamat Usaha :</td>
            <td>
                <input type="text" name="Address" id="Address" size="60" maxlength="250" value="<?php print($contacts->Address); ?>" readonly/>


            </td>
            <td align="right">Alamat Surat :</td>
            <td>
                <input type="text" name="MailAddr" id="MailAddr" size="60" maxlength="250" value="<?php print($contacts->MailAddr); ?>" readonly/>
            </td>
        </tr>
        <tr>
            <td align="right">Kota :</td>
            <td>
                <input type="text" name="City" id="City" size="30" maxlength="100" value="<?php print($contacts->City); ?>" readonly/>
                Kode Pos : <input name="PostCd" id="PostCd" size="7" maxlength="5" value="<?php print($contacts->PostCd); ?>" readonly/>
            </td>
            <td align="right">Kota :</td>
            <td>
                <input type="text" name="MailCity" id="MailCity" size="30" maxlength="100" value="<?php print($contacts->MailCity); ?>" readonly/>
                Kode Pos : <input name="MailPostCd" id="MailPostCd" size="7" maxlength="5" value="<?php print($contacts->MailPostCd); ?>" readonly/>
            </td>
        </tr>
    </table>

    <hr />

    <table cellpadding="2" cellspacing="1" align="center">
        <tr>
            <td align="right">No. Telephone :</td>
            <td><input type="tel" name="TelNo" id="TelNo" size="20" maxlength="50" value="<?php print($contacts->TelNo); ?>" /></td>
            <td align="right">No. Facsimile :</td>
            <td><input type="tel" name="FaxNo" id="FaxNo" size="20" maxlength="50" value="<?php print($contacts->FaxNo); ?>" readonly/></td>
            <td align="right">Contact Person :</td>
            <td><input type="text" name="ContactPerson" id="ContactPerson" size="37" maxlength="50" value="<?php print($contacts->ContactPerson); ?>" readonly/></td>
            <td align="right">Jabatan :</td>
            <td><input type="text" name="Position" id="Position" size="12" maxlength="50" value="<?php print($contacts->Position); ?>" readonly/></td>
        </tr>
        <tr>
            <td align="right">No. Handphone :</td>
            <td><input type="tel" name="HandPhone" id="HandPhone" size="20" maxlength="50" value="<?php print($contacts->HandPhone); ?>" readonly/></td>
            <td align="right">No. KTP/SIM :</td>
            <td><input type="text" name="IdCard" id="IdCard" size="20" maxlength="50" value="<?php print($contacts->IdCard); ?>" readonly/></td>
            <td align="right">Warga Negara :</td>
            <td>
                <input type="text" name="Nationality" id="Nationality" size="3" maxlength="3" value="<?php print($contacts->Nationality); ?>" readonly/>
                &nbsp;&nbsp;Tgl. Lahir : <input type="text" name="BirthDate" id="BirthDate" size="11" maxlength="10" value="<?php print($contacts->Birthday); ?>" readonly/>
            </td>
            <td align="right">Status Kawin:</td>
            <td><select name="MaritalStatus" id="MaritalStatus" disabled>
                    <option value="0" <?php print($contacts->MaritalStatus == "0" ? 'selected="selected"' : ''); ?>>N.A.</option>
                    <option value="1" <?php print($contacts->MaritalStatus == "1" ? 'selected="selected"' : ''); ?>>Single</option>
                    <option value="2" <?php print($contacts->MaritalStatus == "2" ? 'selected="selected"' : ''); ?>>Married</option>
                    <option value="3" <?php print($contacts->MaritalStatus == "3" ? 'selected="selected"' : ''); ?>>Widow</option>
                </select></td>
        </tr>
        <tr>
            <td align="right">N.P.W.P. :</td>
            <td><input type="text" name="Npwp" id="Npwp" size="20" maxlength="50" value="<?php print($contacts->Npwp); ?>" readonly/></td>
            <td align="right">Email Address :</td>
            <td><input type="email" name="EmailAdd" id="EmailAdd" size="20" maxlength="100" value="<?php print($contacts->EmailAdd); ?>" readonly/></td>
            <td align="right">Web Site :</td>
            <td><input type="url" name="WebSite" id="WebSite" size="37" maxlength="100" value="<?php print($contacts->WebSite); ?>" readonly/></td>
            <td align="right">Gender :</td>
            <td><select name="Gender" id="Gender" disabled>
                    <option value="" <?php print($contacts->Gender == "" ? 'selected="selected"' : ''); ?>></option>
                    <option value="m" <?php print($contacts->Gender == "m" ? 'selected="selected"' : ''); ?>>Male</option>
                    <option value="f" <?php print($contacts->Gender == "f" ? 'selected="selected"' : ''); ?>>Female</option>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">Keterangan :</td>
            <td colspan="5"><input name="Remark" id="Remark" size="100" maxlength="250" value="<?php print($contacts->Remark); ?>" readonly/></td>
        </tr>
    </table>
    <hr/>
    <table cellpadding="2" cellspacing="1" align="center">
        <tr>
            <td align="right">Status Relasi :</td>
            <td><select name="Status" id="Status" disabled>
                    <option value="1" <?php print($contacts->Status == "1" ? 'selected="selected"' : ''); ?>>Umum</option>
                    <option value="2" <?php print($contacts->Status == "2" ? 'selected="selected"' : ''); ?>>Member</option>
                    <option value="3" <?php print($contacts->Status == "3" ? 'selected="selected"' : ''); ?>>Prioritas</option>
                </select>
            </td>
            <td align="right">Level Diskon :</td>
            <td><select name="ContactLevel" id="ContactLevel" disabled>
                    <option value="0" <?php print($contacts->ContactLevel == "0" ? 'selected="selected"' : ''); ?>>0 - Umum</option>
                    <option value="1" <?php print($contacts->ContactLevel == "1" ? 'selected="selected"' : ''); ?>>1 - Silver</option>
                    <option value="2" <?php print($contacts->ContactLevel == "2" ? 'selected="selected"' : ''); ?>>2 - Gold</option>
                    <option value="3" <?php print($contacts->ContactLevel == "3" ? 'selected="selected"' : ''); ?>>3 - Platinum</option>
                    <option value="4" <?php print($contacts->ContactLevel == "4" ? 'selected="selected"' : ''); ?>>4 - Karyawan</option>
                    <option value="5" <?php print($contacts->ContactLevel == "5" ? 'selected="selected"' : ''); ?>>5 - Khusus</option>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">Lama Kredit :</td>
            <td><input type="text" name="CreditTerms" id="CreditTerms" size="3" maxlength="3" style="text-align:right" value="<?php print($contacts->CreditTerms); ?>" readonly/>&nbsp;hari</td>
            <td><input type="checkbox" name="Reminder" id="Reminder" value="1" <?php print($contacts->Reminder ? 'checked="checked"' : ''); ?> readonly/>&nbsp;Pengingat</td>
            <td><input type="checkbox" name="Interest" id="Interest" value="1" <?php print($contacts->Interest ? 'checked="checked"' : ''); ?> readonly/>&nbsp;Penalty</td>
        </tr>
        <tr>
            <td align="right">Kredit Limit :</td>
            <td><input type="text" name="CreditLimit" id="CreditLimit" size="10" maxlength="15" style="text-align:right" value="<?php print($contacts->CreditLimit); ?>" readonly/></td>
            <td align="right">Sisa Kredit :</td>
            <td><input type="text" name="CreditToDate" id="CreditToDate" size="10" maxlength="15" style="text-align:right" value="<?php print($contacts->CreditToDate); ?>" readonly/></td>
        </tr>
        <tr>
            <td align="right">Total Poin :</td>
            <td><input type="text" name="PointSum" id="PointSum" size="10" maxlength="15" style="text-align:right" value="<?php print($contacts->PointSum); ?>" readonly/></td>
            <td align="right">Poin Redeem :</td>
            <td><input type="text" name="PointRedem" id="PointRedem" size="10" maxlength="15" style="text-align:right" value="<?php print($contacts->PointRedem); ?>" readonly/></td>
        </tr>
    </table>
    <hr>
    <div align="center">
        <a href="<?php print($eurl); ?>" type="button">Ubah Data</a>
        &nbsp&nbsp
        <a href="<?php print($burl); ?>" type="button"><?php print($dft);?></a>
    </div>
</fieldset>
</body>
</html>
