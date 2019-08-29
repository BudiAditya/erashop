<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>ERASHOP - System User Activity Log</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>
	<link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/jquery-ui.custom.min.js")); ?>"></script>
	<script type="text/javascript" src="<?php print($helper->path("public/js/common.js")); ?>"></script>
    <script type="text/javascript" src="<?php print($helper->path("public/js/auto-numeric.js")); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#StartDate").customDatePicker({ showOn: "focus" });
            $("#EndDate").customDatePicker({ showOn: "focus" });
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
<form id="frm" name="frmReport" method="post">
    <table cellpadding="2" cellspacing="1" class="tablePadding tableBorder">
        <tr class="center">
            <th colspan="10">Activity Log User:&nbsp;<b><?php print($userId.' - '.$userName);?></b></th>
        </tr>
        <tr class="center">
            <th>Dari Tanggal</th>
            <th>Sampai Tanggal</th>
            <th>Action</th>
        </tr>
        <tr>
           <td><input type="text" class="text2" maxlength="10" size="10" id="StartDate" name="StartDate" value="<?php printf(date(JS_DATE,$startDate));?>"/></td>
           <td><input type="text" class="text2" maxlength="10" size="10" id="EndDate" name="EndDate" value="<?php printf(date(JS_DATE,$endDate));?>"/></td>
            <td><button type="submit" formaction="<?php print($helper->site_url("master.useradmin/viewactivity/".$userUid)); ?>"><b>Proses</b></button>
                <a href="<?php print($helper->site_url("master.useradmin")); ?>">Daftar User</a>
            </td>
        </tr>
    </table>
</form>
<!-- start web report -->
<?php  if ($activities != null){ ?>
    <table cellpadding="1" cellspacing="1" class="tablePadding tableBorder">
        <tr>
            <th>Waktu</th>
            <th>Cabang</th>
            <th>Resource</th>
            <th>Modul</th>
            <th>Doc Number</th>
            <th>Process</th>
            <th>Status</th>
        </tr>
        <?php
        while ($row = $activities->FetchAssoc()) {
            print('<tr>');
            printf('<td>%s</td>',$row['log_time']);
            printf('<td>%s</td>',$row['cabang_code']);
            printf('<td>%s</td>',$row['resource']);
            printf('<td>%s</td>',$row['resource_name']);
            printf('<td>%s</td>',$row['doc_no']);
            printf('<td>%s</td>',$row['process']);
            printf('<td>%s</td>',$row['status']);
            print('</tr>');
        }
        ?>
    </table>
    <!-- end web report -->
<?php } ?>
</body>
</html>
