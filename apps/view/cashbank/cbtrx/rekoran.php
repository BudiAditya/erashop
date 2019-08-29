<?php
if ($Output == "1") {
    require_once(LIBRARY . "PHPExcel.php");
    include("rekoran_xls.php");
} elseif ($Output == 2){
    require_once(LIBRARY . "tabular_pdf.php");
    include("rekoran.pdf.php");
} else {
    include("rekoran.web.php");
}