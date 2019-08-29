<?php
class AssemblyController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "inventory/assembly.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
        $this->trxMonth = $this->persistence->LoadState("acc_month");
        $this->trxYear = $this->persistence->LoadState("acc_year");
    }

    public function index() {
        $router = Router::GetInstance();
        $settings = array();

        $settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
        //$settings["columns"][] = array("name" => "a.entity_cd", "display" => "Entity", "width" => 30);
        $settings["columns"][] = array("name" => "a.cabang_code", "display" => "Cabang", "width" => 100);
        $settings["columns"][] = array("name" => "a.assembly_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.assembly_no", "display" => "No. Produksi", "width" => 100);
        $settings["columns"][] = array("name" => "a.item_code", "display" => "Kode Barang", "width" => 100);
        $settings["columns"][] = array("name" => "a.item_name", "display" => "Nama Barang", "width" => 250);
        $settings["columns"][] = array("name" => "format(a.qty,0)", "display" => "QTY", "width" => 50, "align" => "right");
        $settings["columns"][] = array("name" => "a.bsatbesar", "display" => "Satuan", "width" => 50);
        $settings["columns"][] = array("name" => "format(a.price,0)", "display" => "HPP", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.price * a.qty,0)", "display" => "Jumlah", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "a.admin_name", "display" => "Admin", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.assembly_status = 0,'Draft','Posted')", "display" => "Status", "width" => 40);

        $settings["filters"][] = array("name" => "a.assembly_no", "display" => "No. Bukti");
        $settings["filters"][] = array("name" => "a.item_code", "display" => "Kode Barang");
        $settings["filters"][] = array("name" => "a.item_anem", "display" => "Nama Barang");
        $settings["filters"][] = array("name" => "a.assembly_descs", "display" => "Keterangan");
        $settings["filters"][] = array("name" => "a.assembly_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "if(a.assembly_status = 0,'Draft','Posted')", "display" => "Status");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = true;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Produksi";

            if ($acl->CheckUserAccess("inventory.assembly", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "inventory.assembly/add/0", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("inventory.assembly", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "inventory.assembly/add/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Assembly terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("inventory.assembly", "delete")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "inventory.assembly/delete/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("inventory.assembly", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "inventory.assembly/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Assembly terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data assembly","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("inventory.assembly", "print")) {
                $settings["actions"][] = array("Text" => "Print Bukti", "Url" => "inventory.assembly/assembly_print","Class" => "bt_pdf", "ReqId" => 2, "Confirm" => "Cetak Bukti Produksi yang dipilih?");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("inventory.assembly", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "inventory.assembly/report", "Class" => "bt_report", "ReqId" => 0);
            }

        } else {
            $settings["from"] = "vw_ic_assembly_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId ." And year(a.assembly_date) = ".$this->trxYear." And month(a.assembly_date) = ".$this->trxMonth;
            } else {
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

    /* entry data penjualan*/
    public function add($assemblyId = 0) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $assembly = new Assembly();
        if ($assemblyId > 0 ) {
            $assembly = $assembly->LoadById($assemblyId);
            if ($assembly == null) {
                $this->persistence->SaveState("error", "Maaf Data Assembly dimaksud tidak ada pada database. Mungkin sudah dihapus!");
                redirect_url("inventory.assembly");
            }
            if ($assembly->AssemblyStatus == 2) {
                $this->persistence->SaveState("error", sprintf("Maaf Assembly No. %s sudah di-Approve- Tidak boleh diubah lagi..", $assembly->AssemblyNo));
                redirect_url("inventory.assembly");
            }
        }
        // load details
        $assembly->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("assembly", $assembly);
        $this->Set("acl", $acl);
    }

    public function proses_master($assemblyId = 0) {
        $assembly = new Assembly();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $assembly->Id = $assemblyId;
            $assembly->CabangId = $this->GetPostValue("CabangId");
            $assembly->AssemblyDate = date('Y-m-d',strtotime($this->GetPostValue("AssemblyDate")));
            $assembly->AssemblyNo = $this->GetPostValue("AssemblyNo");
            $assembly->ItemId = $this->GetPostValue("aItemMasterId");
            $assembly->ItemCode = $this->GetPostValue("aItemMasterCode");
            $assembly->Qty = $this->GetPostValue("aItemMasterQty");
            $assembly->AssemblyStatus = $this->GetPostValue("AssemblyStatus");
            if ($assembly->AssemblyStatus == null || $assembly->AssemblyStatus == ''){
                $assembly->AssemblyStatus == 0;
            }
            $assembly->AssemblyDescs = 'Produksi';
            $assembly->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $assembly->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;;
            if ($assembly->Id == 0) {
                $assembly->Price = 0;
                $assembly->AssemblyNo = $assembly->GetAssemblyDocNo();
                $rs = $assembly->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Add New Assembly/Produksi',$assembly->AssemblyNo,'Success');
                    printf("OK|A|%d",$assembly->Id);
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Add New Assembly/Produksi',$assembly->AssemblyNo,'Failed');
                    printf("ER|A|%d",$assembly->Id);
                }
            }else{
                //$rs = $assembly->Update($assembly->Id);
                //if ($rs == 1) {
                    printf("OK|U|%d",$assembly->Id);
                //}else{
                //    printf("ER|U|%d",$assembly->Id);
                //}
            }
        }else{
            printf("ER|X|%d",$assemblyId);
        }
    }

    private function ValidateMaster(Assembly $assembly) {
        return true;
    }

    public function view($assemblyId = null) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $assembly = new Assembly();
        $assembly = $assembly->LoadById($assemblyId);
        if($assembly == null){
            $this->persistence->SaveState("error", "Maaf Data Assembly dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("inventory.assembly");
        }
        // load details
        $assembly->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        if ($this->userLevel > 3){
            $cabang = $loader->LoadByEntityId($this->userCompanyId);
        }else{
            $cabang = $loader->LoadById($this->userCabangId);
            $cabCode = $cabang->Kode;
            $cabName = $cabang->Cabang;
        }
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("sales", $sales);
        $this->Set("cabangs", $cabang);
        $this->Set("assembly", $assembly);
        $this->Set("acl", $acl);
    }

    public function delete($assemblyId) {
        // Cek datanya
        $log = new UserAdmin();
        $assembly = new Assembly();
        $assembly = $assembly->FindById($assemblyId);
        if($assembly == null){
            $this->Set("error", "Maaf Data Assembly dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("inventory.assembly");
        }
        /** @var $assembly Assembly */
        if($assembly->AssemblyStatus == 2){
            $this->persistence->SaveState("error", sprintf("Maaf Assembly No. %s sudah di-Approve- Tidak boleh dihapus..",$assembly->AssemblyNo));
            redirect_url("inventory.assembly");
        }
        if ($assembly->Delete($assemblyId) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Delete Assembly/Produksi',$assembly->AssemblyNo,'Success');
            $this->persistence->SaveState("info", sprintf("Data Assembly No: %s sudah berhasil dihapus", $assembly->AssemblyNo));
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Delete Assembly/Produksi',$assembly->AssemblyNo,'Failed');
            $this->persistence->SaveState("error", sprintf("Maaf, Data Assembly No: %s gagal dihapus", $assembly->AssemblyNo));
        }
        redirect_url("inventory.assembly");
    }


    public function add_detail($assemblyId = null) {
        $assembly = new Assembly($assemblyId);
        $assdetail = new AssemblyDetail();
        $assdetail->AssemblyId = $assemblyId;
        $assdetail->AssemblyNo = $assembly->AssemblyNo;
        $assdetail->CabangId = $assembly->CabangId;
        $items = null;
        $log = new UserAdmin();
        $is_item_exist = false;
        if (count($this->postData) > 0) {
            $assdetail->ItemId = $this->GetPostValue("aItemId");
            $assdetail->ItemCode = $this->GetPostValue("aItemCode");
            $assdetail->Qty = $this->GetPostValue("aQty");
            $assdetail->Price = $this->GetPostValue("aPrice");
            $assdetail_exists = new AssemblyDetail();
            $assdetail_exists = $assdetail_exists->FindDuplicate($assdetail->CabangId,$assdetail->AssemblyId,$assdetail->ItemCode,$assdetail->Price);
            if ($assdetail_exists != null){
                // proses penggabungan disini
                /** @var $assdetail_exists AssemblyDetail */
                $is_item_exist = true;
                $assdetail->Qty+= $assdetail_exists->Qty;
            }
            // insert ke table
            if ($is_item_exist){
                // sudah ada item yg sama gabungkan..
                $rs = $assdetail->Update($assdetail_exists->Id);
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Merge Assembly/Produksi detail -> Item Code: '.$assdetail->ItemCode.' = '.$assdetail->Qty,$assembly->AssemblyNo,'Success');
                    print('OK|Proses simpan update berhasil!');
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Merge Assembly/Produksi detail -> Item Code: '.$assdetail->ItemCode.' = '.$assdetail->Qty,$assembly->AssemblyNo,'Failed');
                    print('ER|Gagal proses update data!');
                }
            }else {
                // item baru simpan
                $rs = $assdetail->Insert() == 1;
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Add Assembly/Produksi detail -> Item Code: '.$assdetail->ItemCode.' = '.$assdetail->Qty,$assembly->AssemblyNo,'Success');
                    print('OK|Proses simpan data berhasil!');
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Add Assembly/Produksi detail -> Item Code: '.$assdetail->ItemCode.' = '.$assdetail->Qty,$assembly->AssemblyNo,'Failed');
                    print('ER|Gagal proses simpan data!');
                }
            }
        }
    }

    public function delete_detail($id) {
        // Cek datanya
        $log = new UserAdmin();
        $assdetail = new AssemblyDetail();
        $assdetail = $assdetail->FindById($id);
        if ($assdetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($assdetail->Delete($id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Delete Assembly/Produksi detail -> Item Code: '.$assdetail->ItemCode.' = '.$assdetail->Qty,$assdetail->AssemblyNo,'Success');
            printf("Data Detail Assembly ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.assembly','Delete Assembly/Produksi detail -> Item Code: '.$assdetail->ItemCode.' = '.$assdetail->Qty,$assdetail->AssemblyNo,'Failed');
            printf("Maaf, Data Detail Assembly ID: %d gagal dihapus!",$id);
        }
    }

    public function getitemprices_json($order="a.bnama"){
        require_once(MODEL . "master/setprice.php");
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $setprice = new SetPrice();
        $itemlists = $setprice->GetJSonItemPrice($this->userCompanyId,$this->userCabangId,$filter,$order);
        echo json_encode($itemlists);
    }

    public function getitemprices_plain($cabangId,$bkode){
        require_once(MODEL . "master/setprice.php");
        require_once(MODEL . "master/items.php");
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $setprice SetPrice */
            /** @var $items Items  */
            $items = new Items();
            $items = $items->LoadByKode($bkode);
            $hrg_beli = 0;
            $hrg_jual = 0;
            $setprice = null;
            if ($items != null){
                $setprice = new SetPrice();
                $setprice = $setprice->FindByKode($cabangId,$bkode);
                if ($setprice != null){
                    $hrg_beli = $setprice->HrgBeli;
                    $hrg_jual = $setprice->HrgJual1;
                }
                if($hrg_beli == null){
                    $hrg_beli = 0;
                }
                if($hrg_jual == null){
                    $hrg_jual = 0;
                }
                $ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$hrg_beli.'|'.$hrg_jual;
            }
        }
        print $ret;
    }

    public function getitempricestock_json($level,$cabangId){
        require_once(MODEL . "master/setprice.php");
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $setprice = new SetPrice();
        $itemlists = $setprice->GetJSonItemPriceStock($level,$cabangId,$filter);
        echo json_encode($itemlists);
    }

    public function getitempricestock_plain($cabangId,$bkode,$level){
        require_once(MODEL . "master/setprice.php");
        require_once(MODEL . "master/items.php");
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $setprice SetPrice */
            /** @var $items Items  */
            $setprice = new SetPrice();
            $setprice = $setprice->FindByKode($cabangId,$bkode);
            $items = null;
            if ($setprice != null){
                $ret = "OK|".$setprice->ItemId.'|'.$setprice->ItemName.'|'.$setprice->Satuan.'|'.$setprice->QtyStock.'|'.$setprice->HrgBeli;
                if ($level == -1 && $setprice->HrgBeli > 0){
                    $ret.= '|'.$setprice->HrgBeli;
                }elseif($level == 1 && $setprice->HrgJual2 > 0){
                    $ret.= '|'.$setprice->HrgJual3;
                }elseif($level == 2 && $setprice->HrgJual3 > 0){
                    $ret.= '|'.$setprice->HrgJual3;
                }elseif($level == 3 && $setprice->HrgJual4 > 0){
                    $ret.= '|'.$setprice->HrgJual4;
                }elseif($level == 4 && $setprice->HrgJual5 > 0){
                    $ret.= '|'.$setprice->HrgJual5;
                }elseif($level == 5 && $setprice->HrgJual6 > 0){
                    $ret.= '|'.$setprice->HrgJual6;
                }else{
                    $ret.= '|'.$setprice->HrgJual1;
                }
            }
        }
        print $ret;
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $assembly = new Assembly();
            if ($sJnsLaporan == 1) {
                $reports = $assembly->LoadProduksi4Reports($this->userCompanyId, $sCabangId, $sStartDate, $sEndDate);
            }elseif ($sJnsLaporan == 2){
                $reports = $assembly->LoadRekapProduksi4Reports($this->userCompanyId, $sCabangId, $sStartDate, $sEndDate);
            }elseif ($sJnsLaporan == 3){
                $reports = $assembly->LoadMaterial4Reports($this->userCompanyId, $sCabangId, $sStartDate, $sEndDate);
            }else{
                $reports = $assembly->LoadRekapMaterial4Reports($this->userCompanyId, $sCabangId, $sStartDate, $sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sJnsLaporan = 1;
            $sOutput = 0;
            $reports = null;
        }
        $company = new Company($this->userCompanyId);
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        if ($this->userLevel > 3){
            $cabang = $loader->LoadByEntityId($this->userCompanyId);
        }else{
            $cabang = $loader->LoadById($this->userCabangId);
            $cabCode = $cabang->Kode;
            $cabName = $cabang->Cabang;
        }
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("CabangId",$sCabangId);
        $this->Set("cabangs", $cabang);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
        $this->Set("company_name", $company->CompanyName);
    }

    //proses cetak bukti
    public function assembly_print() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Harap pilih data yang akan dicetak !");
            redirect_url("inventory.transfer");
            return;
        }
        $report = array();
        foreach ($ids as $id) {
            $assembly = new Assembly();
            $assembly = $assembly->LoadById($id);
            $assembly->LoadDetails();
            $report[] = $assembly;
        }
        $this->Set("report", $report);
    }

}


// End of File: estimasi_controller.php
