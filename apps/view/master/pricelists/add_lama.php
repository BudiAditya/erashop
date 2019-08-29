<!DOCTYPE HTML>
<html>
<head>
	<title>ERASHOP - Entry Harga Barang</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/jquery-ui.css")); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php print($helper->path("public/css/common.css")); ?>"/>

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
            var elements = ["ItemCode","PriceDate","MaxDisc","HrgBeli","Markup1","HrgJual1","Markup2","HrgJual2","Markup3","HrgJual3","Markup4","HrgJual4","Markup5","HrgJual5","Markup6","HrgJual6","Submit"];
            BatchFocusRegister(elements);
            $("#PriceDate").customDatePicker({ showOn: "focus" });

            $('#ItemSearch').combogrid({
                panelWidth:500,
                url: "<?php print($helper->site_url("master.items/getjson_items"));?>",
                idField:'bid',
                textField:'bnama',
                mode:'remote',
                fitColumns:true,
                columns:[[
                    {field:'bkode',title:'Kode',width:50,sortable:true},
                    {field:'bnama',title:'Nama Barang',sortable:true,width:150},
                    {field:'bsatbesar',title:'Satuan',width:40}
                ]],
                onSelect: function(index,row){
                    var bid = row.bid;
                    console.log(bid);
                    var bkode = row.bkode;
                    console.log(bkode);
                    var bnama = row.bnama;
                    console.log(bnama);
                    var satuan = row.bsatbesar;
                    console.log(satuan);
                    $('#ItemId').val(bid);
                    $('#ItemCode').val(bkode);
                    $('#ItemDescs').val(bnama);
                    $('#Satuan').val(satuan);
                }
            });

            $("#ItemCode").change(function(e){
                //$ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$items->Bqtystock.'|'.$items->Bhargabeli.'|'.$items->Bhargajual;
                var itc = $("#ItemCode").val();
                var url = "<?php print($helper->site_url("master.items/getplain_items/"));?>"+itc;
                if (itc != ''){
                    $.get(url, function(data, status){
                        //alert("Data: " + data + "\nStatus: " + status);
                        if (status == 'success'){
                            var dtx = data.split('|');
                            if (dtx[0] == 'OK'){
                                $('#ItemId').val(dtx[1]);
                                $('#ItemDescs').val(dtx[2]);
                                $('#Satuan').val(dtx[3]);
                            }
                        }
                    });
                }
            });

            $("#HrgBeli").change(function(e){hitMarkup();});
            $("#Markup1").change(function(e){hitMarkup();});
            $("#Markup2").change(function(e){hitMarkup();});
            $("#Markup3").change(function(e){hitMarkup();});
            $("#Markup4").change(function(e){hitMarkup();});
            $("#Markup5").change(function(e){hitMarkup();});
            $("#Markup6").change(function(e){hitMarkup();});


        });

        function formatPrice(num,row){
            return Number(num).toLocaleString();
        }

        function hitMarkup(){
            var hBeli = Number($("#HrgBeli").val());
            var mUp1 = Number($("#Markup1").val());
            var mUp2 = Number($("#Markup2").val());
            var mUp3 = Number($("#Markup3").val());
            var mUp4 = Number($("#Markup4").val());
            var mUp5 = Number($("#Markup5").val());
            var mUp6 = Number($("#Markup6").val());
            if(mUp1 > 0){
                $("#HrgJual1").val(hBeli + Math.ceil(hBeli * (mUp1/100)));
            }else{
                $("#HrgJual1").val(hBeli);
            }
            if(mUp2 > 0){
                $("#HrgJual2").val(hBeli + Math.ceil(hBeli * (mUp2/100)));
            }else{
                $("#HrgJual2").val(hBeli);
            }
            if(mUp3 > 0){
                $("#HrgJual3").val(hBeli + Math.ceil(hBeli * (mUp3/100)));
            }else{
                $("#HrgJual3").val(hBeli);
            }
            if(mUp4 > 0){
                $("#HrgJual4").val(hBeli + Math.ceil(hBeli * (mUp4/100)));
            }else{
                $("#HrgJual4").val(hBeli);
            }
            if(mUp5 > 0){
                $("#HrgJual5").val(hBeli + Math.ceil(hBeli * (mUp5/100)));
            }else{
                $("#HrgJual5").val(hBeli);
            }
            if(mUp6 > 0){
                $("#HrgJual6").val(hBeli + Math.ceil(hBeli * (mUp6/100)));
            }else{
                $("#HrgJual6").val(hBeli);
            }
        }
    </script>
</head>

<body>
<?php /** @var $setprices Setprice */
$crDate = date(JS_DATE, strtotime(date('Y-m-d')));
?>
<?php include(VIEW . "main/menu.php"); ?>
<?php if (isset($error)) { ?>
<div class="ui-state-error subTitle center"><?php print($error); ?></div><?php } ?>
<?php if (isset($info)) { ?>
<div class="ui-state-highlight subTitle center"><?php print($info); ?></div><?php } ?>
<br />
<fieldset>
	<legend><span class="bold">Entry Data Harga Barang</span></legend>
	<form action="<?php print($helper->site_url("master.pricelists/add/".$setprices->Id)); ?>" method="post">
        <table cellpadding="0" cellspacing="0" class="tablePadding" style="font-size: 12px;font-family: tahoma">
            <tr>
                <td class="bold right">Cabang</td>
                <td colspan="3"><input type="text" class="f1 easyui-textbox" maxlength="20" style="width: 250px" id="CabangCode" name="CabangCode" value="<?php print($setprices->CabangCode != null ? $setprices->CabangCode : $userCabCode); ?>" disabled/>
                    <input type="hidden" id="CabangId" name="CabangId" value="<?php print($setprices->CabangId == null ? $userCabId : $setprices->CabangId);?>"/>
                </td>
            </tr>
            <tr>
                <td class="bold right">Cari Data</td>
                <td colspan="3"><input id="ItemSearch" name="ItemSearch" style="width: 500px"/></td>
            </tr>
            <tr>
                <td class="bold right">Kode Barang</td>
                <td>
                    <input type="text" class="bold" id="ItemCode" name="ItemCode" size="15" value="<?php print($setprices->ItemCode);?>" required/>
                    <input type="hidden" id="ItemId" name="ItemId" value="<?php print($setprices->ItemId);?>"/>
                    <input type="hidden" id="Id" name="Id" value="<?php print($setprices->Id);?>"/>
                </td>
                <td class="bold right">Tanggal</td>
                <td><input type="text" class="bold" size="10" id="PriceDate" name="PriceDate" value="<?php print($setprices->FormatPriceDate(JS_DATE));?>" required/></td>
            </tr>
            <tr>
                <td class="bold right">Nama Barang</td>
                <td colspan="3"><input type="text" class="bold" id="ItemDescs" name="ItemDescs" style="width:490px" value="<?php print(htmlspecialchars($setprices->ItemName));?>" disabled/></td>
                <td class="bold right">Satuan</td>
                <td><input type="text" class="bold" id="Satuan" name="Satuan" size="5" value="<?php print($setprices->Satuan);?>" readonly/></td>
            </tr>
            <tr>
                <td class="bold right">Max Discount</td>
                <td><input class="bold right" type="text" id="MaxDisc" name="MaxDisc" size="3" value="<?php print($setprices->MaxDisc == null ? 0 : $setprices->MaxDisc);?>"/>%</td>
                <td class="bold right">Harga Beli</td>
                <td><input class="bold right" type="text" id="HrgBeli" name="HrgBeli" size="10" value="<?php print($setprices->HrgBeli == null ? 0 : $setprices->HrgBeli);?>"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up1</td>
                <td><input class="bold right" type="text" id="Markup1" name="Markup1" size="3" value="<?php print($setprices->Markup1 == null ? 0 : $setprices->Markup1);?>"/>%</td>
                <td class="bold right">Harga Jual1</td>
                <td><input class="bold right" type="text" id="HrgJual1" name="HrgJual1" size="10" value="<?php print($setprices->HrgJual1 == null ? 0 : $setprices->HrgJual1);?>"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up2</td>
                <td><input class="bold right" type="text" id="Markup2" name="Markup2" size="3" value="<?php print($setprices->Markup2 == null ? 0 : $setprices->Markup2);?>"/>%</td>
                <td class="bold right">Harga Jual2</td>
                <td><input class="bold right" type="text" id="HrgJual2" name="HrgJual2" size="10" value="<?php print($setprices->HrgJual2 == null ? 0 : $setprices->HrgJual2);?>"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up3</td>
                <td><input class="bold right" type="text" id="Markup3" name="Markup3" size="3" value="<?php print($setprices->Markup3 == null ? 0 : $setprices->Markup3);?>"/>%</td>
                <td class="bold right">Harga Jual3</td>
                <td><input class="bold right" type="text" id="HrgJual3" name="HrgJual3" size="10" value="<?php print($setprices->HrgJual3 == null ? 0 : $setprices->HrgJual3);?>"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up4</td>
                <td><input class="bold right" type="text" id="Markup4" name="Markup4" size="3" value="<?php print($setprices->Markup4 == null ? 0 : $setprices->Markup4);?>"/>%</td>
                <td class="bold right">Harga Jual4</td>
                <td><input class="bold right" type="text" id="HrgJual4" name="HrgJual4" size="10" value="<?php print($setprices->HrgJual4 == null ? 0 : $setprices->HrgJual4);?>"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up5</td>
                <td><input class="bold right" type="text" id="Markup5" name="Markup5" size="3" value="<?php print($setprices->Markup5 == null ? 0 : $setprices->Markup5);?>"/>%</td>
                <td class="bold right">Harga Jual5</td>
                <td><input class="bold right" type="text" id="HrgJual5" name="HrgJual5" size="10" value="<?php print($setprices->HrgJual5 == null ? 0 : $setprices->HrgJual5);?>"/></td>
            </tr>
            <tr>
                <td class="bold right">Mark Up6</td>
                <td><input class="bold right" type="text" id="Markup6" name="Markup6" size="3" value="<?php print($setprices->Markup6 == null ? 0 : $setprices->Markup6);?>"/>%</td>
                <td class="bold right">Harga Jual6</td>
                <td><input class="bold right" type="text" id="HrgJual6" name="HrgJual6" size="10" value="<?php print($setprices->HrgJual6 == null ? 0 : $setprices->HrgJual6);?>"/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3"><button type="submit" id="Submit" class="button">Update Data</button>
                    &nbsp&nbsp
                    <a href="<?php print($helper->site_url("master.pricelists")); ?>">Daftar Harga Barang</a>
                </td>
            </tr>
        </table>
    </form>
</fieldset>
</body>
</html>
