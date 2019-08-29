<?php
if ($output == "xls") {
    require_once(LIBRARY . "PHPExcel.php");
    include("stock_xls.php");
} else{
    require_once(LIBRARY . "tabular_pdf.php");
    include("stock_pdf.php");
}