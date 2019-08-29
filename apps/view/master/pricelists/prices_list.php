<?php
if ($output == "xls") {
    require_once(LIBRARY . "PHPExcel.php");
    include("prices_xls.php");
} else{
    require_once(LIBRARY . "tabular_pdf.php");
    include("prices_pdf.php");
}