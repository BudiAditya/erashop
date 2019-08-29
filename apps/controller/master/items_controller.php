<?php

class ItemsController extends AppController {
    private $userUid;
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;

    protected function Initialize() {
        require_once(MODEL . "master/items.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
    }

    public function index() {
        $router = Router::GetInstance();
        $settings = array();
        $settings["columns"][] = array("name" => "a.bid", "display" => "ID", "width" => 0);
        $settings["columns"][] = array("name" => "a.bkode", "display" => "Kode Barang", "width" => 100);
        $settings["columns"][] = array("name" => "a.bnama", "display" => "Nama Barang", "width" => 350);
        $settings["columns"][] = array("name" => "a.bjenis", "display" => "Jenis", "width" => 60);
        $settings["columns"][] = array("name" => "a.bdivisi", "display" => "Divisi", "width" => 60);
        $settings["columns"][] = array("name" => "a.bkelompok", "display" => "Kelompok", "width" => 60);
        $settings["columns"][] = array("name" => "a.bsatbesar", "display" => "Kemasan", "width" => 40);
        $settings["columns"][] = array("name" => "format(a.bisisatkecil,0)", "display" => "Isi", "width" => 30,"align" => "right");
        $settings["columns"][] = array("name" => "a.bsatkecil", "display" => "Satuan", "width" => 40);
        //$settings["columns"][] = array("name" => "a.bbarcode", "display" => "Bar Code", "width" => 100);
        $settings["columns"][] = array("name" => "a.bsnama", "display" => "Supplier", "width" => 150);
        //$settings["columns"][] = array("name" => "a.bminstock", "display" => "Minimum", "width" => 40,"align" => "right");
        //$settings["columns"][] = array("name" => "if(a.bisallowmin = 1,'Boleh','Tidak')", "display" => "Minus", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.bisaktif = 1,'Aktif','Tidak')", "display" => "Is Aktif", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.item_level = 0,'Global',if(a.item_level = 1,'Company','Private'))", "display" => "Level", "width" => 40);
        $settings["columns"][] = array("name" => "a.def_cabang_code", "display" => "Def. Cabang", "width" => 80);
        $settings["columns"][] = array("name" => "a.bketerangan", "display" => "Keterangan", "width" =>250);

        $settings["filters"][] = array("name" => "a.bnama", "display" => "Nama Barang");
        $settings["filters"][] = array("name" => "a.bkode", "display" => "Kode Barang");
        $settings["filters"][] = array("name" => "a.bjenis", "display" => "Jenis");
        $settings["filters"][] = array("name" => "a.bdivisi", "display" => "Divisi");
        $settings["filters"][] = array("name" => "a.bkelompok", "display" => "Kelompok");
        $settings["filters"][] = array("name" => "a.bsnama", "display" => "Supplier");
        $settings["filters"][] = array("name" => "if(a.bisaktif = 1,'Aktif','Tidak')", "display" => "Status Aktif");
        $settings["filters"][] = array("name" => "if(a.item_level = 0,'Global',if(a.item_level = 1,'Company','Private'))", "display" => "Level");

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Barang";

            if ($acl->CheckUserAccess("master.items", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "master.items/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("master.items", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "master.items/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Mohon memilih items terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu items.",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("master.items", "delete")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "master.items/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
                    "Error" => "Mohon memilih items terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu items.",
                    "Confirm" => "Apakah anda mau menghapus data items yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("master.items", "add")) {
                $settings["actions"][] = array("Text" => "Upload Daftar Barang", "Url" => "master.items/upload", "Class" => "bt_excel", "ReqId" => 0);
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("master.items", "view")) {
                $settings["actions"][] = array("Text" => "Daftar Barang (Aktif)", "Url" => "master.items/items_list/xls/1", "Class" => "bt_excel", "ReqId" => 0);
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Daftar Barang (Non-Aktif)", "Url" => "master.items/items_list/xls/0", "Class" => "bt_excel", "ReqId" => 0);
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Daftar Barang (All)", "Url" => "master.items/items_list/xls/-1", "Class" => "bt_excel", "ReqId" => 0);
            }

            $settings["def_order"] = 4;
            $settings["def_filter"] = 0;
            $settings["singleSelect"] = true;

        } else {
            $settings["from"] = "vw_m_barang AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.is_deleted = 0 AND a.bisaktif = 1 And Not (a.item_level = 1 And a.entity_id <>".$this->userCompanyId.") And Not (a.item_level = 2 And a.def_cabang_id <>".$this->userCabangId.")";
            } else {
                //$settings["where"] = "a.is_deleted = 0 And Not (a.item_level = 1 And a.entity_id <>".$this->userCompanyId.") And Not (a.item_level = 2 And a.def_cabang_id <>".$this->userCabangId.")";
                $settings["where"] = "a.is_deleted = 0";
            }
            $settings["order by"] = "a.bkode";
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

    private function ValidateData(Items $items) {
        return true;
    }

    public function add() {
        require_once(MODEL . "master/itemdivisi.php");
        require_once(MODEL . "master/itemkelompok.php");
        require_once(MODEL . "master/itemuom.php");
        require_once(MODEL . "master/itemjenis.php");
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/cabang.php");
        $log = new UserAdmin();
        $items = new Items();
        $loader = null;
        if (count($this->postData) > 0) {
            $items->Bkode = $this->GetPostValue("Bkode");
            $items->Bnama = $this->GetPostValue("Bnama");
            $items->Bketerangan = $this->GetPostValue("Bketerangan");
            $items->Bjenis = $this->GetPostValue("Bjenis");
            $items->Bdivisi = $this->GetPostValue("Bdivisi");
            $items->Bkelompok = $this->GetPostValue("Bkelompok");
            $items->Bsupplier = $this->GetPostValue("Bsupplier");
            $items->Bbarcode = $this->GetPostValue("Bbarcode");
            $items->Bsatbesar = $this->GetPostValue("Bsatbesar");
            $items->Bsatkecil = $this->GetPostValue("Bsatkecil");
            $items->Bisisatkecil = $this->GetPostValue("Bisisatkecil");
            $items->Bisaktif = $this->GetPostValue("Bisaktif");
            $items->Bminstock = $this->GetPostValue("Bminstock");
            $items->DefCabangId = $this->GetPostValue("DefCabangId");
            $items->ItemLevel = $this->GetPostValue("ItemLevel");
            if (isset($this->postData["Bisallowmin"])) {
                $items->Bisallowmin = 1;
            } else {
                $items->Bisallowmin = 0;
            }
            if ($this->ValidateData($items)) {
                $items->CreatebyId = $this->userUid;
                $rs = $items->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.items','Add New Item -> Kode: '.$items->Bkode.' - '.$items->Bnama,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Data Barang: %s (%s) sudah berhasil disimpan", $items->Bnama, $items->Bkode));
                    redirect_url("master.items");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.items','Add New Item -> Kode: '.$items->Bkode.' - '.$items->Bnama,'-','Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $loader = new Cabang();
        $cabangs = $loader->LoadByType($this->userCompanyId,-1,">");
        $loader = new ItemJenis();
        $itemjenis = $loader->LoadAll();
        $loader = new ItemDivisi();
        $itemdepts = $loader->LoadAll("a.divisi");
        $loader = new ItemKelompok();
        $itemgroups = $loader->LoadAll("a.kelompok");
        $loader = new ItemUom();
        $itemuoms = $loader->LoadAll();
        $loader = new Contacts();
        $suppliers = $loader->LoadByType(2);
        //send to form
        $this->Set("itemjenis", $itemjenis);
        $this->Set("itemdepts", $itemdepts);
        $this->Set("itemgroups", $itemgroups);
        $this->Set("itemuoms", $itemuoms);
        $this->Set("suppliers", $suppliers);
        $this->Set("items", $items);
        $this->Set("cabId", $this->userCabangId);
        $this->Set("cabCode", $cabCode);
        $this->Set("cabName", $cabName);
        $this->Set("cabangs", $cabangs);
    }

    public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data barang terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.items");
        }
        require_once(MODEL . "master/itemjenis.php");
        require_once(MODEL . "master/itemdivisi.php");
        require_once(MODEL . "master/itemkelompok.php");
        require_once(MODEL . "master/itemuom.php");
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/cabang.php");
        $items = new Items();
        $log = new UserAdmin();
        $loader = null;
        if (count($this->postData) > 0) {
            $items->Bid = $id;
            $items->Bkode = $this->GetPostValue("Bkode");
            $items->Bnama = $this->GetPostValue("Bnama");
            $items->Bketerangan = $this->GetPostValue("Bketerangan");
            $items->Bjenis = $this->GetPostValue("Bjenis");
            $items->Bdivisi = $this->GetPostValue("Bdivisi");
            $items->Bkelompok = $this->GetPostValue("Bkelompok");
            $items->Bsupplier = $this->GetPostValue("Bsupplier");
            $items->Bbarcode = $this->GetPostValue("Bbarcode");
            $items->Bsatbesar = $this->GetPostValue("Bsatbesar");
            $items->Bsatkecil = $this->GetPostValue("Bsatkecil");
            $items->Bisisatkecil = $this->GetPostValue("Bisisatkecil");
            $items->Bminstock = $this->GetPostValue("Bminstock");
            $items->DefCabangId = $this->GetPostValue("DefCabangId");
            $items->ItemLevel = $this->GetPostValue("ItemLevel");
            if (isset($this->postData["Bisallowmin"])) {
                $items->Bisallowmin = 1;
            } else {
                $items->Bisallowmin = 0;
            }
            $items->Bisaktif = $this->GetPostValue("Bisaktif");
            if ($this->ValidateData($items)) {
                $items->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $items->Update($id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.items','Update Item -> Kode: '.$items->Bkode.' - '.$items->Bnama,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data barang: %s (%s) sudah berhasil disimpan", $items->Bnama, $items->Bkode));
                    redirect_url("master.items");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.items','Update Item -> Kode: '.$items->Bkode.' - '.$items->Bnama,'-','Failed');
                    $this->Set("error", "Gagal pada saat merubah data barang. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $items = $items->LoadById($id);
            if ($items == null || $items->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.items");
            }
        }
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $loader = new Cabang();
        $cabangs = $loader->LoadByType($this->userCompanyId,-1,">");
        $loader = new ItemJenis();
        $itemjenis = $loader->LoadAll();
        $loader = new ItemDivisi();
        $itemdepts = $loader->LoadAll("a.divisi");
        $loader = new ItemKelompok();
        $itemgroups = $loader->LoadAll("a.kelompok");
        $loader = new ItemUom();
        $itemuoms = $loader->LoadAll();
        $loader = new Contacts();
        $suppliers = $loader->LoadByType(2);
        //send to form
        $this->Set("itemjenis", $itemjenis);
        $this->Set("itemdepts", $itemdepts);
        $this->Set("itemgroups", $itemgroups);
        $this->Set("itemuoms", $itemuoms);
        $this->Set("suppliers", $suppliers);
        $this->Set("items", $items);
        $this->Set("cabId", $this->userCabangId);
        $this->Set("cabCode", $cabCode);
        $this->Set("cabName", $cabName);
        $this->Set("cabangs", $cabangs);
    }

    public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.items");
        }
        $log = new UserAdmin();
        $items = new Items();
        $items = $items->LoadById($id);
        if ($items == null || $items->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.items");
        }
        $rs = $items->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.items','Delete Item -> Kode: '.$items->Bkode.' - '.$items->Bnama,'-','Success');
            $this->persistence->SaveState("info", sprintf("Barang Barang: %s (%s) sudah dihapus", $items->Bnama, $items->Bkode));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.items','Delete Item -> Kode: '.$items->Bkode.' - '.$items->Bnama,'-','Success');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus jenis kontak: %s (%s). Error: %s", $items->Bnama, $items->Bkode, $this->connector->GetErrorMessage()));
        }
        redirect_url("master.items");
    }

    public function checkcode($bkode = null){
        $items = new Items();
        $items = $items->FindByKode($bkode);
        $ret = 0;
        if ($items != null){
            $ret = $items->Bnama;
        }
        print $ret;
    }

    public function getjson_items(){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $items = new Items();
        $itemlists = $items->GetJSonItems($this->userCompanyId,$this->userCabangId,$filter);
        echo json_encode($itemlists);
    }

    public function getplain_items($bkode){
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $items Items */
            $items = new Items();
            $items = $items->FindByKode($bkode);
            if ($items != null){
                $ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$items->Bqtystock.'|'.$items->Bhargabeli.'|'.$items->Bhargajual;
            }
        }
        print $ret;
    }

    public function items_list($output,$status){
        require_once(MODEL . "master/company.php");
        $company = new Company();
        $company = $company->LoadById($this->userCompanyId);
        $compname = $company->CompanyName;
        $items = new Items();
        $items = $items->LoadItemList($this->userCompanyId,$this->userCabangId,$status);
        $this->Set("items", $items);
        $this->Set("output", $output);
        $this->Set("company_name", $compname);
    }

    public function upload(){
        // untuk melakukan upload dan update data sparepart
        if (count($this->postData) > 0) {
            // Ada data yang di upload...
            $this->doUpload();
            redirect_url("master.items");
        }
    }

    public function doUpload(){
        $log = new UserAdmin();
        $items = new Items();
        $uploadedFile = $this->GetPostValue("fileUpload");
        $processedData = 0;
        $infoMessages = array();	// Menyimpan info message yang akan di print
        $errorMessages = array();	// Menyimpan error message yang akan di print

        if ($uploadedFile["error"] !== 0) {
            $this->persistence->SaveState("error", "Gagal Upload file ke server !");
            return;
        }

        $tokens = explode(".", $uploadedFile["name"]);
        $ext = end($tokens);

        if ($ext != "xls" && $ext != "xlsx") {
            $this->persistence->SaveState("error", "File yang diupload bukan berupa file excel !");
            return;
        }

        // Load libs Excel
        require_once(LIBRARY . "PHPExcel.php");
        if ($ext == "xls") {
            $reader = new PHPExcel_Reader_Excel5();
        } else {
            $reader = new PHPExcel_Reader_Excel2007();
        }
        $phpExcel = $reader->load($uploadedFile["tmp_name"]);

        // OK baca file excelnya sekarang....
        require_once(MODEL . "master/itemjenis.php");
        require_once(MODEL . "master/itemdivisi.php");
        require_once(MODEL . "master/itemkelompok.php");
        require_once(MODEL . "master/itemuom.php");
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/setprice.php");
        require_once(MODEL . "inventory/stock.php");

        // Step #01: Baca mapping kode shift
        $sheet = $phpExcel->getSheetByName("Data Barang");
        $maxRow = $sheet->getHighestRow();
        $startFrom = 4;
        $sql = null;
        $nmr = 0;
        for ($i = $startFrom; $i <= $maxRow; $i++) {
            $nmr++;
            // OK kita lihat apakah User berbaik hati menggunakan ID atau tidak
            $iJenis = trim($sheet->getCellByColumnAndRow(1, $i)->getCalculatedValue());
            $iDivisi = trim($sheet->getCellByColumnAndRow(2, $i)->getCalculatedValue());
            $iKelompok = trim($sheet->getCellByColumnAndRow(3, $i)->getCalculatedValue());
            $iKdBarang = trim($sheet->getCellByColumnAndRow(4, $i)->getCalculatedValue());
            $iNmBarang = trim($sheet->getCellByColumnAndRow(5, $i)->getCalculatedValue());
            $iSatBesar = trim($sheet->getCellByColumnAndRow(6, $i)->getCalculatedValue());
            $iIsiSatKecil = $sheet->getCellByColumnAndRow(7, $i)->getCalculatedValue();
            $iSatKecil = trim($sheet->getCellByColumnAndRow(8, $i)->getCalculatedValue());
            $iKdSupplier = trim($sheet->getCellByColumnAndRow(9, $i)->getCalculatedValue());
            $iKeterangan = trim($sheet->getCellByColumnAndRow(10, $i)->getCalculatedValue());
            $iHrgBeli = $sheet->getCellByColumnAndRow(11, $i)->getCalculatedValue();
            $iHrgJual = $sheet->getCellByColumnAndRow(12, $i)->getCalculatedValue();

            if ($iJenis == "" || $iJenis == null || $iJenis == '-'){
                $infoMessages[] = sprintf("[%d] Jenis Barang: -%s- tidak valid! Pastikan Jenis Barang pada template sudah benar!",$nmr,$iJenis);
                continue;
            }
            if ($iDivisi == "" || $iDivisi == null || $iDivisi == '-'){
                $infoMessages[] = sprintf("[%d] Divisi Barang: -%s- tidak valid! Pastikan Divisi Barang pada template sudah benar!",$nmr,$iDivisi);
                continue;
            }
            if ($iKelompok == "" || $iKelompok == null || $iKelompok == '-'){
                $infoMessages[] = sprintf("[%d] Kelompok Barang: -%s- tidak valid! Pastikan Kelompok Barang pada template sudah benar!",$nmr,$iKelompok);
                continue;
            }
            if ($iKdBarang == "" || $iKdBarang == null || $iKdBarang == '-'){
                $infoMessages[] = sprintf("[%d] Kode Barang: -%s- tidak valid! Pastikan Kode Barang pada template sudah benar ",$nmr,$iKdBarang);
                continue;
            }
            if ($iNmBarang == "" || $iNmBarang == null || $iNmBarang == '-'){
                $infoMessages[] = sprintf("[%d] Nama Barang: -%s- tidak valid! Pastikan Nama Barang pada template sudah benar!",$nmr,$iNmBarang);
                continue;
            }
            if ($iSatBesar == "" || $iSatBesar == null || $iSatBesar == '-'){
                $infoMessages[] = sprintf("[%d] Satuan Barang: -%s- tidak valid! Pastikan Satuan Barang pada template sudah benar!",$nmr,$iNmBarang);
                continue;
            }
            //periksa kode supplier
            //if (($iKdSupplier != "" && $iKdSupplier != null && $iKdSupplier != '-') || (strlen(trim($iKdSupplier))>3)){
            if ((strlen(trim($iKdSupplier))>3)){
                $bsupplier = new Contacts();
                $bsupplier = $bsupplier->FindBySupplierCode($iKdSupplier);
                if ($bsupplier == null) {
                    $infoMessages[] = sprintf("[%d] Kode Supplier: -%s- tidak valid! Pastikan Kode Supplier pada template sudah benar!", $nmr, $iKdSupplier);
                    continue;
                }
            }
            //periksa jenis barang jika tidak ada tambahkan
            $bjenis = new ItemJenis();
            $bjenis = $bjenis->FindByJenis($iJenis);
            if($bjenis == null){
                $bjenis = new ItemJenis();
                $bjenis->JnsBarang = $iJenis;
                $bjenis->Keterangan = $iJenis;
                $rs = $bjenis->Insert();
                if ($rs == 0){
                    $infoMessages[] = sprintf("[%d] Jenis Barang: -%s- tidak valid! Pastikan Jenis Barang pada template sudah benar!",$nmr,$iJenis);
                    continue;
                }
            }
            //periksa divisi barang jika tidak ada tambahkan
            $bdivisi = new ItemDivisi();
            $bdivisi = $bdivisi->FindByDivisi($iDivisi);
            if($bdivisi == null){
                $bdivisi = new ItemDivisi();
                $bdivisi->Divisi = $iDivisi;
                $bdivisi->Keterangan = $iDivisi;
                $rs = $bdivisi->Insert();
                if ($rs == 0){
                    $infoMessages[] = sprintf("[%d] Divisi Barang: -%s- tidak valid! Pastikan Divisi Barang pada template sudah benar!",$nmr,$iDivisi);
                    continue;
                }
            }
            //periksa kelompok barang jika tidak ada tambahkan
            $bkelompok = new ItemKelompok();
            $bkelompok = $bkelompok->FindByKelompok($iKelompok);
            if($bkelompok == null){
                $bkelompok = new ItemKelompok();
                $bkelompok->Kelompok = $iKelompok;
                $bkelompok->Keterangan = $iKelompok;
                $rs = $bkelompok->Insert();
                if ($rs == 0){
                    $infoMessages[] = sprintf("[%d] Kelompok Barang: -%s- tidak valid! Pastikan Kelompok Barang pada template sudah benar!",$nmr,$iKelompok);
                    continue;
                }
            }
            $xitems = null;
            $isnew = true;
            $isoke = true;
            $iBid = 0;
            $items->Bjenis = $iJenis;
            $items->Bdivisi = $iDivisi;
            $items->Bkelompok = $iKelompok;
            $items->Bkode = $iKdBarang;
            $items->Bnama = $iNmBarang;
            $items->Bsatbesar = $iSatBesar;
            $items->Bsatkecil = $iSatKecil;
            $items->Bisisatkecil = $iIsiSatKecil == null ? 1 : $iIsiSatKecil;
            $items->Bsupplier = $iKdSupplier;
            $items->Bhargabeli = $iHrgBeli == null ? 0 : $iHrgBeli;
            $items->Bhargajual = $iHrgJual == null ? 0 : $iHrgJual;
            $items->Bbarcode = $iKdBarang;
            $items->Bisaktif = 1;
            $items->Bketerangan = $iKeterangan;
            $xitems = new Items();
            $xitems = $xitems->LoadByKode($iKdBarang);
            if ($xitems != null){
                $isnew = false;
            }
            // mulai proses update
            $this->connector->BeginTransaction();
            $hasError = false;
            //$rs = $items->DeleteProcess();
            if ($isnew) {
                $items->CreatebyId = $this->userUid;
                $items->ItemLevel = 2;
                $items->DefCabangId = $this->userCabangId;
                $rs = $items->Insert();
                if ($rs != 1) {
                    // Hmm error apa lagi ini ?? DBase related harusnya
                    $errorMessages[] = sprintf("[%d] Gagal simpan Data Barang-> Kode: %s - Nama: %s Message: %s",$nmr,$iKdBarang,$iNmBarang,$this->connector->GetErrorMessage());
                    $hasError = true;
                    $isoke = false;
                    break;
                }else{
                    $iBid = $items->Bid;
                }
            }else{
                $items->ItemLevel = 2;
                $items->DefCabangId = $this->userCabangId;
                $items->UpdatebyId = $this->userUid;
                $rs = $items->Update($xitems->Bid);
                if ($rs != 1) {
                    // Hmm error apa lagi ini ?? DBase related harusnya
                    $errorMessages[] = sprintf("[%d] Gagal Update Data Barang-> Kode: %s - Nama: %s Message: %s",$nmr,$iKdBarang,$iNmBarang,$this->connector->GetErrorMessage());
                    $hasError = true;
                    $isoke = false;
                    break;
                }else{
                    $iBid = $xitems->Bid;
                }
            }
            //update daftar harga sekalian
            if ($isoke){
                $bprice = new SetPrice();
                $bprice = $bprice->FindByKode($this->userCabangId,$iKdBarang);
                if ($bprice == null){
                    //harga barang baru
                    $bprice = new SetPrice();
                    $bprice->CabangId = $this->userCabangId;
                    $bprice->ItemId = $iBid;
                    $bprice->ItemCode = $iKdBarang;
                    $bprice->Satuan = $iSatKecil;
                    $bprice->HrgBeli = $iHrgBeli;
                    $bprice->HrgJual1 = $iHrgJual;
                    $bprice->PriceDate = date('Y-m-d',time());
                    $bprice->HrgJual2 = $iHrgJual;
                    $bprice->HrgJual3 = $iHrgJual;
                    $bprice->HrgJual4 = $iHrgJual;
                    $bprice->HrgJual5 = $iHrgJual;
                    $bprice->HrgJual6 = $iHrgJual;
                    $bprice->CreatebyId = $this->userUid;
                    $bprice->UpdatebyId = $this->userUid;
                    $bprice->Insert();
                }else{
                    //harga barang baru
                    $bprice = new SetPrice();
                    $bprice->CabangId = $this->userCabangId;
                    $bprice->ItemId = $iBid;
                    $bprice->ItemCode = $iKdBarang;
                    $bprice->Satuan = $iSatKecil;
                    $bprice->HrgBeli = $iHrgBeli;
                    $bprice->HrgJual1 = $iHrgJual;
                    $bprice->PriceDate = date('Y-m-d',time());
                    $bprice->HrgJual2 = $iHrgJual;
                    $bprice->HrgJual3 = $iHrgJual;
                    $bprice->HrgJual4 = $iHrgJual;
                    $bprice->HrgJual5 = $iHrgJual;
                    $bprice->HrgJual6 = $iHrgJual;
                    $bprice->CreatebyId = $this->userUid;
                    $bprice->UpdatebyId = $this->userUid;
                    $bprice->Update($iBid);
                }
                // revised 20170614
                // isi stockcenter dengan 0
                $bstock = new Stock();
                $bstock = $bstock->FindByKode($this->userCabangId,$iKdBarang);
                if ($bstock == null){
                    $bstock = new Stock();
                    $bstock->CabangId = $this->userCabangId;
                    $bstock->ItemId = $iBid;
                    $bstock->ItemCode = $iKdBarang;
                    $bstock->QtyStock = 0;
                    $bstock->CreatebyId = $this->userUid;
                    $rs = $bstock->Insert();
                }
            }
            // Step #06: Commit/Rollback transcation per karyawan...
            if ($hasError) {
                $this->connector->RollbackTransaction();
            } else {
                $this->connector->CommitTransaction();
                $processedData++;
            }
        }

        // Step #07: Sudah selesai.... semua karyawan sudah diproses
        if (count($errorMessages) > 0) {
            $this->persistence->SaveState("error", sprintf('<ol style="margin: 0;"><li>%s</li></ol>', implode("</li><li>", $errorMessages)));
            $infoMessages[] = "Data Barang yang ERROR tidak di-entry ke system sedangkan yang lainnya tetap dimasukkan.";
        }
        if ($processedData > 0) {
            $log = $log->UserActivityWriter($this->userCabangId, 'master.items', 'Upload Data Items from excel file = '.$processedData.' item(s)', '-', 'Success');
        }
        $infoMessages[] = "Proses Upload Data Barang selesai. Jumlah data yang diproses: " . $processedData;
        $this->persistence->SaveState("info", sprintf('<ol style="margin: 0;"><li>%s</li></ol>', implode("</li><li>", $infoMessages)));

        // Completed...
    }

    public function template(){
        // untuk melakukan download template
        require_once(MODEL . "master/itemjenis.php");
        require_once(MODEL . "master/itemdivisi.php");
        require_once(MODEL . "master/itemkelompok.php");
        require_once(MODEL . "master/itemuom.php");
        require_once(MODEL . "master/contacts.php");
        $ijenis = new ItemJenis();
        $ijenis = $ijenis->LoadAll();
        $this->Set("ijenis",$ijenis);
        $idivisi = new ItemDivisi();
        $idivisi = $idivisi->LoadAll();
        $this->Set("idivisi",$idivisi);
        $ikelompok = new ItemKelompok();
        $ikelompok = $ikelompok->LoadAll();
        $this->Set("ikelompok",$ikelompok);
        $isatuan = new ItemUom();
        $isatuan = $isatuan->LoadAll();
        $this->Set("isatuan",$isatuan);
        $isupplier = new Contacts();
        $isupplier = $isupplier->LoadByType(2);
        $this->Set("isupplier",$isupplier);
    }
}

// End of file: items_controller.php