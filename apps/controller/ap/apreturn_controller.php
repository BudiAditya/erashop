<?php
class ApReturnController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "ap/apreturn.php");
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
        $settings["columns"][] = array("name" => "a.cabang_code", "display" => "Cabang", "width" => 80);
        $settings["columns"][] = array("name" => "a.rb_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.rb_no", "display" => "No. Bukti", "width" => 80);
        $settings["columns"][] = array("name" => "a.supplier_name", "display" => "Nama Supplier", "width" => 150);
        $settings["columns"][] = array("name" => "a.rb_descs", "display" => "Keterangan", "width" => 160);
        $settings["columns"][] = array("name" => "format(a.rb_amount,0)", "display" => "Nilai Retur", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "a.admin_name", "display" => "Admin", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.rb_status = 0,'Draft',if(a.rb_status = 3,'Void','Posted'))", "display" => "Status", "width" => 40);

        $settings["filters"][] = array("name" => "a.cabang_code", "display" => "Kode Cabang");
        $settings["filters"][] = array("name" => "a.rb_no", "display" => "No. Bukti");
        $settings["filters"][] = array("name" => "a.rb_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.supplier_name", "display" => "Nama Supplier");
        $settings["filters"][] = array("name" => "if(a.rb_status = 0,'Draft',if(a.rb_status = 3,'Void','Posted'))", "display" => "Status");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = true;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Retur Ex. Pembelian";

            if ($acl->CheckUserAccess("ap.apreturn", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "ap.apreturn/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("ap.apreturn", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "ap.apreturn/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Retur terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data rekonsil",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("ap.apreturn", "delete")) {
                $settings["actions"][] = array("Text" => "Void", "Url" => "ap.apreturn/void/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("ap.apreturn", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "ap.apreturn/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Retur terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data apreturn","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ap.apreturn", "print")) {
                $settings["actions"][] = array("Text" => "Print Bukti", "Url" => "ap.apreturn/print_pdf/%s", "Class" => "bt_pdf", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data ApReturn terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data apreturn","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ap.apreturn", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "ap.apreturn/report", "Class" => "bt_report", "ReqId" => 0);
            }
        } else {
            $settings["from"] = "vw_ap_return_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId ." And year(a.rb_date) = ".$this->trxYear." And month(a.rb_date) = ".$this->trxMonth;
            } else {
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

	/* Untuk entry data estimasi perbaikan dan penggantian spare part */
	public function add() {
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/contacts.php");
        $loader = null;
        $log = new UserAdmin();
		$apreturn = new ApReturn();
        $apreturn->CabangId = $this->userCabangId;
        if (count($this->postData) > 0) {
			$apreturn->CabangId = $this->GetPostValue("CabangId");
			$apreturn->RbDate = $this->GetPostValue("RbDate");
            $apreturn->RbNo = $this->GetPostValue("RbNo");
            $apreturn->RbDescs = $this->GetPostValue("RbDescs");
            $apreturn->SupplierId = $this->GetPostValue("SupplierId");
            if ($this->GetPostValue("RbStatus") == null || $this->GetPostValue("RbStatus") == 0){
                $apreturn->RbStatus = 1;
            }else{
                $apreturn->RbStatus = $this->GetPostValue("RbStatus");
            }
            $apreturn->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $apreturn->RbAmount = 0;
			if ($this->ValidateMaster($apreturn)) {
                if ($apreturn->RbNo == null || $apreturn->RbNo == "-" || $apreturn->RbNo == ""){
                    $apreturn->RbNo = $apreturn->GetApReturnDocNo();
                }
                $rs = $apreturn->Insert();
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->Set("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Add New Return',$apreturn->RbNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Add New Return',$apreturn->RbNo,'Success');
                    redirect_url("ap.apreturn/edit/".$apreturn->Id);
                }
			}
		}
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("apreturn", $apreturn);
	}

	private function ValidateMaster(ApReturn $apreturn) {
        // validation here
        if ($apreturn->SupplierId > 0){
            return true;
        }else{
            $this->Set("error", "Nama Supplier masih kosong..");
            return false;
        }
	}

    public function edit($apreturnId = null) {
       require_once(MODEL . "master/cabang.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $log = new UserAdmin();
        $apreturn = new ApReturn();
        if (count($this->postData) > 0) {
            $apreturn->Id = $apreturnId;
            $apreturn->CabangId = $this->GetPostValue("CabangId");
            $apreturn->RbDate = $this->GetPostValue("RbDate");
            $apreturn->RbNo = $this->GetPostValue("RbNo");
            $apreturn->RbDescs = $this->GetPostValue("RbDescs");
            $apreturn->SupplierId = $this->GetPostValue("SupplierId");
            if ($this->GetPostValue("RbStatus") == null || $this->GetPostValue("RbStatus") == 0){
                $apreturn->RbStatus = 1;
            }else{
                $apreturn->RbStatus = $this->GetPostValue("RbStatus");
            }
            $apreturn->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateMaster($apreturn)) {
                $rs = $apreturn->Update($apreturn->Id);
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->persistence->SaveState("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Update Return',$apreturn->RbNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Update Return',$apreturn->RbNo,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Return/Nota No.: '%s' Tanggal: %s telah berhasil diubah..", $apreturn->RbNo, $apreturn->RbDate));
                    redirect_url("ap.apreturn/edit/".$apreturn->Id);
                }
            }
        }else{
            $apreturn = $apreturn->LoadById($apreturnId);
            if($apreturn == null){
               $this->persistence->SaveState("error", "Maaf Data Return dimaksud tidak ada pada database. Mungkin sudah dihapus!");
               redirect_url("ap.apreturn");
            }
            if($apreturn->RbStatus == 3){
                $this->persistence->SaveState("error", "Maaf Data Return sudah berstatus -VOID-!");
                redirect_url("ap.apreturn/view/".$apreturnId);
            }
        }
        // load details
        $apreturn->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("apreturn", $apreturn);
        $this->Set("acl", $acl);
    }

	public function view($apreturnId = null) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $apreturn = new ApReturn();
        $apreturn = $apreturn->LoadById($apreturnId);
        if($apreturn == null){
            $this->persistence->SaveState("error", "Maaf Data ApReturn dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ap.apreturn");
        }
        // load details
        $apreturn->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("apreturn", $apreturn);
        $this->Set("acl", $acl);
	}

    public function delete($apreturnId) {
        // Cek datanya
        $log = new UserAdmin();
        $apreturn = new ApReturn();
        $apreturn = $apreturn->FindById($apreturnId);
        if($apreturn == null){
            $this->Set("error", "Maaf Data Return dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ap.apreturn");
        }
        /** @var $apreturn ApReturn */
        if ($apreturn->Delete($apreturnId) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Delete Return',$apreturn->RbNo,'Success');
            $this->persistence->SaveState("info", sprintf("Data Return No: %s sudah berhasil dihapus", $apreturn->RbNo));
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Delete Return',$apreturn->RbNo,'Failed');
            $this->persistence->SaveState("error", sprintf("Maaf, Data Return No: %s gagal dihapus", $apreturn->RbNo));
        }
        redirect_url("ap.apreturn");
    }

    public function void($apreturnId) {
        // Cek datanya
        $log = new UserAdmin();
        $apreturn = new ApReturn();
        $apreturn = $apreturn->FindById($apreturnId);
        if($apreturn == null){
            $this->Set("error", "Maaf Data Return dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ap.apreturn");
        }
        if($apreturn->RbStatus == 3){
            $this->persistence->SaveState("error", "Maaf Data Return sudah berstatus -VOID-!");
            redirect_url("ap.apreturn/view/".$apreturnId);
        }
        /** @var $apreturn ApReturn */
        if ($apreturn->Void($apreturnId) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Delete Return',$apreturn->RbNo,'Success');
            $this->persistence->SaveState("info", sprintf("Data Return No: %s sudah berhasil dibatalkan", $apreturn->RbNo));
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Delete Return',$apreturn->RbNo,'Failed');
            $this->persistence->SaveState("error", sprintf("Maaf, Data Return No: %s gagal dibatalkan", $apreturn->RbNo));
        }
        redirect_url("ap.apreturn");
    }

	public function add_detail($apreturnId = null) {
        $log = new UserAdmin();
        $apreturn = new ApReturn($apreturnId);
        $retdetail = new ApReturnDetail();
        $retdetail->RbId = $apreturnId;
        $retdetail->RbNo = $apreturn->RbNo;
        $retdetail->CabangId = $apreturn->CabangId;
        $items = null;
        if (count($this->postData) > 0) {
            $retdetail->ExGrnId = $this->GetPostValue("aExGrnId");
            $retdetail->ExGrnNo = $this->GetPostValue("aExGrnNo");
            $retdetail->ItemId = $this->GetPostValue("aItemId");
            $retdetail->ExGrnDetailId = $this->GetPostValue("aExGrnDetailId");
            $retdetail->ItemCode = $this->GetPostValue("aItemCode");
            $retdetail->ItemDescs = $this->GetPostValue("aItemDescs");
            $retdetail->QtyBeli = $this->GetPostValue("aQtyBeli");
            $retdetail->QtyRetur = $this->GetPostValue("aQtyRetur");
            $retdetail->Price = $this->GetPostValue("aPrice");
            $retdetail->SubTotal = $this->GetPostValue("aSubTotal");
            // insert ke table
            $rs = $retdetail->Insert()== 1;
            if ($rs > 0) {
                $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Add Return detail -> Ex. Purchase No: '.$retdetail->ExGrnNo.' -> Item Code: '.$retdetail->ItemCode.' = '.$retdetail->QtyRetur,$apreturn->RbNo,'Success');
                echo json_encode(array());
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Add Return detail -> Ex. Purchase No: '.$retdetail->ExGrnNo.' -> Item Code: '.$retdetail->ItemCode.' = '.$retdetail->QtyRetur,$apreturn->RbNo,'Failed');
                echo json_encode(array('errorMsg'=>'Some errors occured.'));
            }
        }
	}

    public function delete_detail($id) {
        // Cek datanya
        $log = new UserAdmin();
        $retdetail = new ApReturnDetail();
        $retdetail = $retdetail->FindById($id);
        if ($retdetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($retdetail->Delete($id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Delete Return detail -> Ex. Purchase No: '.$retdetail->ExGrnNo.' -> Item Code: '.$retdetail->ItemCode.' = '.$retdetail->QtyRetur,$retdetail->RbNo,'Success');
            printf("Data Detail ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ap.apreturn','Delete Return detail -> Ex. Purchase No: '.$retdetail->ExGrnNo.' -> Item Code: '.$retdetail->ItemCode.' = '.$retdetail->QtyRetur,$retdetail->RbNo,'Failed');
            printf("Maaf, Data Detail ID: %d gagal dihapus!",$id);
        }
    }

    public function print_pdf($apreturnId = null) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        $loader = null;
        $apreturn = new ApReturn();
        $apreturn = $apreturn->LoadById($apreturnId);
        if($apreturn == null){
            $this->persistence->SaveState("error", "Maaf Data ApReturn dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ap.apreturn");
        }
        // load details
        $apreturn->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
        $userName = AclManager::GetInstance()->GetCurrentUser()->RealName;
        //kirim ke view
        $this->Set("sales", $sales);
        $this->Set("cabangs", $cabang);
        $this->Set("apreturn", $apreturn);
        $this->Set("userName", $userName);
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/cabang.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sContactsId = $this->GetPostValue("ContactsId");
            $sStatus = $this->GetPostValue("Status");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            // ambil data yang diperlukan
            $apreturn = new ApReturn();
            if ($sJnsLaporan == 1){
                $reports = $apreturn->Load4Reports($this->userCompanyId,$sCabangId,$sContactsId,$sStatus,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 2){
                $reports = $apreturn->Load4ReportsDetail($this->userCompanyId,$sCabangId,$sContactsId,$sStatus,$sStartDate,$sEndDate);
            }else{
                $reports = $apreturn->Load4ReportsRekapItem($this->userCompanyId,$sCabangId,$sContactsId,$sStatus,$sStartDate,$sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sContactsId = 0;
            $sStatus = -1;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sOutput = 0;
            $sJnsLaporan = 1;
            $reports = null;
        }
        $supplier = new Contacts();
        $supplier = $supplier->LoadAll();
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
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
        // kirim ke view
        $this->Set("cabangs", $cabang);
        $this->Set("suppliers",$supplier);
        $this->Set("CabangId",$sCabangId);
        $this->Set("ContactsId",$sContactsId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Status",$sStatus);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userLevel",$this->userLevel);
        $this->Set("JnsLaporan",$sJnsLaporan);
    }

    public function createTextApReturn($id){
        $apreturn = new ApReturn($id);
        if ($apreturn <> null){
            $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            fwrite($myfile, $apreturn->CompanyName);
            fwrite($myfile, "\n".'FAKTUR PENJUALAN');

            fclose($myfile);
        }
    }
}


// End of File: estimasi_controller.php
