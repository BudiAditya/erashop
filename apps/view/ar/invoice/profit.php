<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("profit_xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("profit.pdf.php");
} else {
    include("profit.web.php");
}