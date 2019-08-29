<?php
class ArReturnController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "ar/arreturn.php");
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
        $settings["columns"][] = array("name" => "a.rj_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.rj_no", "display" => "No. Bukti", "width" => 80);
        $settings["columns"][] = array("name" => "a.customer_name", "display" => "Nama Customer", "width" => 150);
        $settings["columns"][] = array("name" => "a.rj_descs", "display" => "Keterangan", "width" => 160);
        $settings["columns"][] = array("name" => "format(a.rj_amount,0)", "display" => "Nilai Retur", "width" => 90, "align" => "right");
        //$settings["columns"][] = array("name" => "format(a.rj_allocate,0)", "display" => "Alokasi", "width" => 90, "align" => "right");
        //$settings["columns"][] = array("name" => "format(a.rj_amount - a.rj_allocate,0)", "display" => "Sisa", "width" => 90, "align" => "right");
        $settings["columns"][] = array("name" => "a.admin_name", "display" => "Admin", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.rj_status = 0,'Draft',if(a.rj_status = 3,'Void','Posted'))", "display" => "Status", "width" => 40);

        $settings["filters"][] = array("name" => "a.rj_no", "display" => "No. Bukti");
        $settings["filters"][] = array("name" => "a.rj_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.customer_name", "display" => "Nama Customer");
        $settings["filters"][] = array("name" => "if(a.rj_status = 0,'Draft',if(a.rj_status = 3,'Void','Posted'))", "display" => "Status");
        $settings["filters"][] = array("name" => "a.cabang_code", "display" => "Kode Cabang");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = true;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Retur Ex. Penjualan";

            if ($acl->CheckUserAccess("ar.arreturn", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "ar.arreturn/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("ar.arreturn", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "ar.arreturn/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data ArReturn terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data rekonsil",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("ar.arreturn", "delete")) {
                $settings["actions"][] = array("Text" => "Void", "Url" => "ar.arreturn/void/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("ar.arreturn", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "ar.arreturn/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data ArReturn terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data arreturn","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.arreturn", "print")) {
                $settings["actions"][] = array("Text" => "Print Bukti", "Url" => "ar.arreturn/print_pdf/%s", "Class" => "bt_pdf", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data ArReturn terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data arreturn","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.arreturn", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "ar.arreturn/report", "Class" => "bt_report", "ReqId" => 0);
            }
        } else {
            $settings["from"] = "vw_ar_return_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId ." And year(a.rj_date) = ".$this->trxYear." And month(a.rj_date) = ".$this->trxMonth;
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
		$arreturn = new ArReturn();
        $arreturn->CabangId = $this->userCabangId;
        if (count($this->postData) > 0) {
			$arreturn->CabangId = $this->GetPostValue("CabangId");
			$arreturn->RjDate = $this->GetPostValue("RjDate");
            $arreturn->RjNo = $this->GetPostValue("RjNo");
            $arreturn->RjDescs = $this->GetPostValue("RjDescs");
            $arreturn->CustomerId = $this->GetPostValue("CustomerId");
            if ($this->GetPostValue("RjStatus") == null || $this->GetPostValue("RjStatus") == 0){
                $arreturn->RjStatus = 1;
            }else{
                $arreturn->RjStatus = $this->GetPostValue("RjStatus");
            }
            $arreturn->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $arreturn->RjAmount = 0;
            $arreturn->RjAllocate = 0;
			if ($this->ValidateMaster($arreturn)) {
                if ($arreturn->RjNo == null || $arreturn->RjNo == "-" || $arreturn->RjNo == ""){
                    $arreturn->RjNo = $arreturn->GetArReturnDocNo();
                }
                $rs = $arreturn->Insert();
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->Set("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Add New Return',$arreturn->RjNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Add New Return',$arreturn->RjNo,'Success');
                    redirect_url("ar.arreturn/edit/".$arreturn->Id);
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
        $this->Set("arreturn", $arreturn);
	}

	private function ValidateMaster(ArReturn $arreturn) {
        // validation here
        if ($arreturn->CustomerId > 0){
            return true;
        }else{
            $this->Set("error", "Nama Customer masih kosong..");
            return false;
        }
	}

    public function edit($arreturnId = null) {
       require_once(MODEL . "master/cabang.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $arreturn = new ArReturn();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $arreturn->Id = $arreturnId;
            $arreturn->CabangId = $this->GetPostValue("CabangId");
            $arreturn->RjDate = $this->GetPostValue("RjDate");
            $arreturn->RjNo = $this->GetPostValue("RjNo");
            $arreturn->RjDescs = $this->GetPostValue("RjDescs");
            $arreturn->CustomerId = $this->GetPostValue("CustomerId");
            if ($this->GetPostValue("RjStatus") == null || $this->GetPostValue("RjStatus") == 0){
                $arreturn->RjStatus = 1;
            }else{
                $arreturn->RjStatus = $this->GetPostValue("RjStatus");
            }
            $arreturn->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateMaster($arreturn)) {
                $rs = $arreturn->Update($arreturn->Id);
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->persistence->SaveState("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Update Return',$arreturn->RjNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Update Return',$arreturn->RjNo,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Return/Nota No.: '%s' Tanggal: %s telah berhasil diubah..", $arreturn->RjNo, $arreturn->RjDate));
                    redirect_url("ar.arreturn/edit/".$arreturn->Id);
                }
            }
        }else{
            $arreturn = $arreturn->LoadById($arreturnId);
            if($arreturn == null){
               $this->persistence->SaveState("error", "Maaf Data Return dimaksud tidak ada pada database. Mungkin sudah dihapus!");
               redirect_url("ar.arreturn");
            }
            if ($arreturn->RjStatus == 3){
                $this->Set("error", "Maaf Data Return ini berstatus -VOID-!");
                redirect_url("ar.arreturn");
            }
            if ($arreturn->RjAllocate > 0){
                $this->Set("error", "Maaf Data Return sudah dialokasikan. Tidak boleh diubah!");
                redirect_url("ar.arreturn");
            }
        }
        // load details
        $arreturn->LoadDetails();
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
        $this->Set("arreturn", $arreturn);
        $this->Set("acl", $acl);
    }

	public function view($arreturnId = null) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $arreturn = new ArReturn();
        $arreturn = $arreturn->LoadById($arreturnId);
        if($arreturn == null){
            $this->persistence->SaveState("error", "Maaf Data ArReturn dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.arreturn");
        }
        // load details
        $arreturn->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("sales", $sales);
        $this->Set("cabangs", $cabang);
        $this->Set("arreturn", $arreturn);
        $this->Set("acl", $acl);
	}

    public function delete($arreturnId) {
        // Cek datanya
        $log = new UserAdmin();
        $arreturn = new ArReturn();
        $arreturn = $arreturn->FindById($arreturnId);
        if($arreturn == null){
            $this->Set("error", "Maaf Data Return dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.arreturn");
        }
        /** @var $arreturn ArReturn */
        if ($arreturn->RjAllocate > 0){
            $this->Set("error", "Maaf Data Return sudah dialokasikan. Tidak boleh dihapus!");
            redirect_url("ar.arreturn");
        }
        if ($arreturn->Delete($arreturnId) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Delete Return',$arreturn->RjNo,'Success');
            $this->persistence->SaveState("info", sprintf("Data Return No: %s sudah berhasil dihapus", $arreturn->RjNo));
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Delete Return',$arreturn->RjNo,'Failed');
            $this->persistence->SaveState("error", sprintf("Maaf, Data Return No: %s gagal dihapus", $arreturn->RjNo));
        }
        redirect_url("ar.arreturn");
    }

    public function void($arreturnId) {
        // Cek datanya
        $log = new UserAdmin();
        $arreturn = new ArReturn();
        $arreturn = $arreturn->FindById($arreturnId);
        if($arreturn == null){
            $this->Set("error", "Maaf Data Return dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.arreturn");
        }
        /** @var $arreturn ArReturn */
        if ($arreturn->RjAllocate > 0){
            $this->Set("error", "Maaf Data Return sudah dialokasikan. Tidak boleh dihapus!");
            redirect_url("ar.arreturn");
        }
        if ($arreturn->RjStatus == 3){
            $this->Set("error", "Maaf Data Return sudah berstatus -VOID-!");
            redirect_url("ar.arreturn");
        }
        if ($arreturn->Void($arreturnId) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Delete Return',$arreturn->RjNo,'Success');
            $this->persistence->SaveState("info", sprintf("Data Return No: %s sudah berhasil dibatalkan", $arreturn->RjNo));
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Delete Return',$arreturn->RjNo,'Failed');
            $this->persistence->SaveState("error", sprintf("Maaf, Data Return No: %s gagal dibatalkan", $arreturn->RjNo));
        }
        redirect_url("ar.arreturn");
    }

	public function add_detail($arreturnId = null) {
        $arreturn = new ArReturn($arreturnId);
        $retdetail = new ArReturnDetail();
        $log = new UserAdmin();
        $retdetail->RjId = $arreturnId;
        $retdetail->RjNo = $arreturn->RjNo;
        $retdetail->CabangId = $arreturn->CabangId;
        $items = null;
        if (count($this->postData) > 0) {
            $retdetail->ExInvoiceId = $this->GetPostValue("aExInvoiceId");
            $retdetail->ExInvoiceNo = $this->GetPostValue("aExInvoiceNo");
            $retdetail->ItemId = $this->GetPostValue("aItemId");
            $retdetail->ExInvDetailId = $this->GetPostValue("aExInvDetailId");
            $retdetail->ItemCode = $this->GetPostValue("aItemCode");
            $retdetail->ItemDescs = $this->GetPostValue("aItemDescs");
            $retdetail->QtyJual = $this->GetPostValue("aQtyJual");
            $retdetail->QtyRetur = $this->GetPostValue("aQtyRetur");
            $retdetail->Price = $this->GetPostValue("aPrice");
            $retdetail->SubTotal = $this->GetPostValue("aSubTotal");
            // insert ke table
            $rs = $retdetail->Insert()== 1;
            if ($rs > 0) {
                $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Add Return detail -> Ex.Inv No: '.$retdetail->ExInvoiceNo.' -> Item Code: '.$retdetail->ItemCode.' = '.$retdetail->QtyRetur,$arreturn->RjNo,'Success');
                echo json_encode(array());
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Add Return detail -> Ex.Inv No: '.$retdetail->ExInvoiceNo.' -> Item Code: '.$retdetail->ItemCode.' = '.$retdetail->QtyRetur,$arreturn->RjNo,'Failed');
                echo json_encode(array('errorMsg'=>'Some errors occured.'));
            }
        }
	}

    public function delete_detail($id) {
        // Cek datanya
        $log = new UserAdmin();
        $retdetail = new ArReturnDetail();
        $retdetail = $retdetail->FindById($id);
        if ($retdetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($retdetail->Delete($id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Delete Return detail -> Ex.Inv No: '.$retdetail->ExInvoiceNo.' -> Item Code: '.$retdetail->ItemCode.' = '.$retdetail->QtyRetur,$retdetail->RjNo,'Success');
            printf("Data Detail ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.arreturn','Delete Return detail -> Ex.Inv No: '.$retdetail->ExInvoiceNo.' -> Item Code: '.$retdetail->ItemCode.' = '.$retdetail->QtyRetur,$retdetail->RjNo,'Success');
            printf("Maaf, Data Detail ID: %d gagal dihapus!",$id);
        }
    }

    public function print_pdf($arreturnId = null) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        $loader = null;
        $arreturn = new ArReturn();
        $arreturn = $arreturn->LoadById($arreturnId);
        if($arreturn == null){
            $this->persistence->SaveState("error", "Maaf Data ArReturn dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.arreturn");
        }
        // load details
        $arreturn->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
        $userName = AclManager::GetInstance()->GetCurrentUser()->RealName;
        //kirim ke view
        $this->Set("sales", $sales);
        $this->Set("cabangs", $cabang);
        $this->Set("arreturn", $arreturn);
        $this->Set("userName", $userName);
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sContactsId = $this->GetPostValue("ContactsId");
            $sRjStatus = $this->GetPostValue("RjStatus");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $arreturn = new ArReturn();
            if ($sJnsLaporan == 1){
                $reports = $arreturn->Load4Reports($this->userCompanyId,$sCabangId,$sContactsId,$sRjStatus,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 2){
                $reports = $arreturn->Load4ReportsDetail($this->userCompanyId,$sCabangId,$sContactsId,$sRjStatus,$sStartDate,$sEndDate);
            }else{
                $reports = $arreturn->Load4ReportsRekapItem($this->userCompanyId,$sCabangId,$sContactsId,$sRjStatus,$sStartDate,$sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sContactsId = 0;
            $sRjStatus = -1;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sOutput = 0;
            $sJnsLaporan = 1;
            $reports = null;
        }
        $customer = new Contacts();
        $customer = $customer->LoadAll();
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
        $this->Set("customers",$customer);
        $this->Set("CabangId",$sCabangId);
        $this->Set("ContactsId",$sContactsId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Status",$sRjStatus);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userLevel",$this->userLevel);
        $this->Set("JnsLaporan",$sJnsLaporan);
    }

    public function getArReturnItemRows($id){
        $arreturn = new ArReturn();
        $rows = $arreturn->GetArReturnItemRow($id);
        print($rows);
    }

    public function createTextArReturn($id){
        $arreturn = new ArReturn($id);
        if ($arreturn <> null){
            $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            fwrite($myfile, $arreturn->CompanyName);
            fwrite($myfile, "\n".'FAKTUR PENJUALAN');

            fclose($myfile);
        }
    }

    public function getjson_returnlists($cabangId,$customerId){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $arreturn = new ArReturn();
        $retlists = $arreturn->GetJSonArReturns($cabangId,$customerId,$filter);
        echo json_encode($retlists);
    }
}


// End of File: estimasi_controller.php
