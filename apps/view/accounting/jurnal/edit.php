<!DOCTYPE HTML>
<html>
<?php
/** @var $jurnal Jurnal */ /** $coas CoaDetail[] */
?>
<head>
	<title>Erasys - Ubah Data Jurnal Akuntansi Manual</title>
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

            $("#bAddDetail").click(function(e){
                $("#divadddetail").show();
            });

            $("#frmAddDetail").submit(function(e){
                var postData = $(this).serializeArray();
                var formURL = $(this).attr("action");
                $.ajax(
                    {
                        url : formURL,
                        type: "POST",
                        data : postData,
                        success:function(data, textStatus, jqXHR)
                        {
                            //data: return data from server
                            //alert(data);
                            location.reload();
                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            //if fails
                            alert('Maaf, gagal simpan data..')
                        }
                    });
                e.preventDefault(); //STOP default action
                e.unbind(); //unbind. to stop multiple form submit.
            });

            $("#bCancelAdd").click(function(e){
                $("#divadddetail").hide();
            });
        });

        function fdeldetail(dta){
            var dtx = dta.split('|');
            var id = dtx[0];
            var uraian = dtx[1];
            var norut = dtx[2];
            var urx = '<?php print($helper->site_url("accounting.jurnal/delete_detail/"));?>'+id;
            if (confirm('Hapus Detail Invoice No: '+norut+ '\nUraian: '+uraian+' ?')) {
                $.get(urx, function(data){
                    alert(data);
                    location.reload();
                });
            }
        }
    </script>
</head>
<body>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<?php
$badd = base_url('public/images/button/').'add.png';
$bsave = base_url('public/images/button/').'accept.png';
$bcancel = base_url('public/images/button/').'cancel.png';
$bview = base_url('public/images/button/').'view.png';
$bedit = base_url('public/images/button/').'edit.png';
$bdelete = base_url('public/images/button/').'delete.png';
$sts = null;
?>
<br />
<fieldset>
	<legend align="center"><strong>Ubah Data Jurnal Akuntansi Manual No. <?php print($jurnal->NoVoucher); ?></strong></legend>
    <form id="frm" action="<?php print($helper->site_url("accounting.jurnal/edit/".$jurnal->Id)); ?>" method="post">
        <input type="hidden" id="Id" name="Id" value="<?php print($jurnal->Id);?>"/>
        <input type="hidden" id="DocStatus" name="DocStatus" value="<?php print($jurnal->DocStatus);?>"/>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="center">
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
                <td><input type="text" class="text2" maxlength="20" size="20" id="NoVoucher" name="NoVoucher" value="<?php print($jurnal->NoVoucher); ?>" readonly/></td>
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
                    <button id="btSubmit" type="submit"><b>Update</b></button>
                </td>
            </tr>
        </table>
    </form>
    <div id="divadddetail" style="display: none">
        <br>
        <form id="frmAddDetail" action="<?php print($helper->site_url("accounting.jurnal/add_detail/".$jurnal->Id)); ?>" method="post">
            <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="center">
                <tr>
                    <th colspan="7"><strong>TAMBAH DETAIL JURNAL</strong></th>
                </tr>
                <tr>
                    <th>Akun Debet</th>
                    <th>Akun Kredit</th>
                    <th>Uraian</th>
                    <th>Jumlah</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <td><select id="AcDebetNo" name="AcDebetNo" required style="width: 300px;">
                            <option value="">-- Pilih Akun Debet --</option>
                            <?php
                            foreach($coas as $coa){
                                printf('<option value="%s">%s - %s</option>',$coa->Kode,$coa->Kode,$coa->Perkiraan);
                            }
                            ?>
                    </select>
                    <td><select id="AcKreditNo" name="AcKreditNo" required style="width: 300px;">
                            <option value="">-- Pilih Akun Kredit --</option>
                            <?php
                            foreach($coas as $coa){
                                printf('<option value="%s">%s - %s</option>',$coa->Kode,$coa->Kode,$coa->Perkiraan);
                            }
                            ?>
                        </select>
                    </td>
                    <td><input class="text2" type="text" id="Uraian" name="Uraian" size="50" maxlength="250" value="<?php print($jurnal->Keterangan);?>" required/></td>
                    <td><input class="right" type="text" id="Jumlah" name="Jumlah" size="15" maxlength="15" value="0" required style="text-align: right"/></td>
                    <td class="center">
                        <?php
                        printf('<input type="image" id="bSaveDetail" src="%s" style="cursor: pointer" value="Submit" name="submit"/>',$bsave);
                        printf('&nbsp<img id="bCancelAdd" src="%s" style="cursor: pointer"/>',$bdelete);
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <br>
    <div>
        <table cellpadding="0" cellspacing="0" class="tablePadding tableBorder" align="center">
            <tr>
                <th colspan="7"><strong>DETAIL JURNAL</strong></th>
            </tr>
            <tr>
                <th>No.</th>
                <th>Akun Debet</th>
                <th>Akun Kredit</th>
                <th>Uraian</th>
                <th>Jumlah</th>
                <th>Action</th>
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
                print("<td class='center'>");
                $dta = $detail->Id.'|'.$detail->Uraian.'|'.$counter;
                printf('&nbsp<img src="%s" style="cursor: pointer" onclick="return fdeldetail(%s);"/>',$bdelete,"'".$dta."'");
                print("</td>");
                print("</tr>");
                $total += $detail->Jumlah;
            }
            print("<tr>");
            print("<td colspan='4' class='right'>Sub-Total</td>");
            printf('<td class="right">%s</td>', number_format($total,0));
            print("<td class='center' colspan='2'>");
            printf('<img src="%s" id="bAddDetail" style="cursor: pointer;"/>',$badd);
            print("</td>");
            print("</tr>");
            ?>
        </table>
    </div>
</fieldset>
<!-- </body> -->
</html>
