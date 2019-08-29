<?php
if ($outPut == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("mutasi_xls.php");
} elseif ($outPut == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("mutasi.pdf.php");
} else {
    include("mutasi.web.php");
}