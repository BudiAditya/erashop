<?php
class WarkatController extends AppController {
	private $userCompanyId;
    private $userCabangId;
    private $userLevel;

	protected function Initialize() {
		require_once(MODEL . "cashbank/warkat.php");
        require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		//$settings["columns"][] = array("name" => "a.kd_cabang", "display" => "Cabang", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.warkat_mode = 1,'Masuk','Keluar')", "display" => "Mode", "width" => 50);
        $settings["columns"][] = array("name" => "a.jns_warkat", "display" => "Jenis", "width" => 60);
        $settings["columns"][] = array("name" => "a.warkat_no", "display" => "No. Warkat", "width" => 100);
        $settings["columns"][] = array("name" => "a.warkat_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.contact_name", "display" => "Relasi", "width" => 100);
        $settings["columns"][] = array("name" => "a.reff_no", "display" => "Refferensi", "width" => 100);
        $settings["columns"][] = array("name" => "a.kontra_perkiraan", "display" => "Sumber", "width" => 100);
        $settings["columns"][] = array("name" => "format(a.warkat_amount,0)", "display" => "Nilai", "width" => 80, "align" => "right");
        $settings["columns"][] = array("name" => "if(a.warkat_status = 0,'Baru',if(a.warkat_status = 1,'Cair','Void'))", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "a.process_date", "display" => "Tgl. Cair", "width" => 60);
        $settings["columns"][] = array("name" => "a.bank_name", "display" => "Bank", "width" => 80);
        $settings["columns"][] = array("name" => "a.process_voucher_no", "display" => "No. Jurnal", "width" => 80);
        $settings["columns"][] = array("name" => "a.warkat_descs", "display" => "Keterangan", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.create_mode = 1,'Auto','Manual')", "display" => "Source", "width" => 50);

		$settings["filters"][] = array("name" => "a.kd_cabang", "display" => "Cabang");
		$settings["filters"][] = array("name" => "a.warkat_no", "display" => "No. Warkat");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Transaksi Warkat (B/G & Cheque)";
/*
			if ($acl->CheckUserAccess("cashbank.warkat", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "cashbank.warkat/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("cashbank.warkat", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "cashbank.warkat/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih Data Warkat terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu trxtype.",
					"Confirm" => "");
			}
*/
            if ($acl->CheckUserAccess("cashbank.warkat", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "cashbank.warkat/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Warkat terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data Warkat","Confirm" => "");
            }
/*
			if ($acl->CheckUserAccess("cashbank.warkat", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "cashbank.warkat/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih Data Warkat terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu data.",
					"Confirm" => "Apakah anda mau menghapus data Warkat yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}
*/
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbank.warkat", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "cashbank.warkat/report", "Class" => "bt_report", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("cashbank.warkat", "edit")) {
                $settings["actions"][] = array("Text" => "separator", "Url" => null);
                $settings["actions"][] = array("Text" => "Proses Warkat", "Url" => "cashbank.warkat/process/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Warkat terlebih dahulu sebelum proses data.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda mau memproses Data Warkat yang dipilih ?\nKlik OK untuk melanjutkan proses");
            }
/*
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbank.warkat", "approve")) {
                $settings["actions"][] = array("Text" => "Approval", "Url" => "cashbank.warkat/approve", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses approval.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda menyetujui data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            if ($acl->CheckUserAccess("cashbank.warkat", "approve")) {
                $settings["actions"][] = array("Text" => "Batal Approval", "Url" => "cashbank.warkat/unapprove", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses pembatalan.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda mau membatalkan approval data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
*/
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbank.warkat", "view")) {
                $settings["actions"][] = array("Text" => "Print Bukti", "Url" => "cashbank.warkat/cetakpdf/%s", "Class" => "bt_print", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Warkat terlebih dahulu sebelum proses cetak.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda akan mencetak Warkat yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
			$settings["def_order"] = 2;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "vw_cb_warkat_list AS a";
            //$settings["where"] = "a.is_deleted = 0";
            $settings["where"] = "a.cabang_id = ".$this->userCabangId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

    public function add() {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/coadetail.php");
        $loader = null;
        $log = new UserAdmin();
        $warkat = new Warkat();
        $warkat->CabangId = $this->userCabangId;
        if (count($this->postData) > 0) {
            $warkat->CabangId = $this->GetPostValue("CabangId");
            $warkat->WarkatMode = $this->GetPostValue("WarkatMode");
            $warkat->WarkatNo = $this->GetPostValue("WarkatNo");
            $warkat->WarkatDate = $this->GetPostValue("WarkatDate");
            $warkat->WarkatTypeId = $this->GetPostValue("WarkatTypeId");
            $warkat->WarkatDescs = $this->GetPostValue("WarkatDescs");
            $warkat->WarkatBankId = $this->GetPostValue("WarkatBankId");
            $warkat->WarkatAmount = $this->GetPostValue("WarkatAmount");
            $warkat->ContactId = $this->GetPostValue("ContactId");
            $warkat->ReffNo = $this->GetPostValue("ReffNo");
            $warkat->ReffAccNo = $this->GetPostValue("ReffAccNo");
            if ($this->GetPostValue("WarkatStatus") == null){
                $warkat->WarkatStatus = 0;
            }else{
                $warkat->WarkatStatus = $this->GetPostValue("WarkatStatus");
            }
            $warkat->CreateById = AclManager::GetInstance()->GetCurrentUser()->Id;
            $warkat->CreateMode = 0;
            if ($this->ValidateWarkat($warkat)) {
                $rs = $warkat->Insert();
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->Set("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.warkat','Add New Warkat No: '.$warkat->WarkatNo,$warkat->ReffNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.warkat','Add New Warkat No: '.$warkat->WarkatNo,$warkat->ReffNo,'Success');
                    redirect_url("cashbank.warkat");
                }
            }
        }
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
        $loader = new Bank();
        $banks = $loader->LoadAllNonCash($this->userCompanyId);
        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId);
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("warkat", $warkat);
        $this->Set("banks", $banks);
        $this->Set("accounts", $accounts);
    }

    private function ValidateWarkat(Warkat $warkat) {
        if ($warkat->ContactId == 0 || $warkat->ContactId == null || $warkat->ContactId == ''){
            $this->Set("error", "Relasi tidak boleh kosong!");
            return false;
        }
        if ($warkat->WarkatBankId == 0 || $warkat->WarkatBankId == null || $warkat->WarkatBankId == ''){
            $this->Set("error", "Bank Tujuan tidak boleh kosong!");
            return false;
        }
        if ($warkat->WarkatAmount == null || $warkat->WarkatAmount == 0){
            $this->Set("error", "Nilai Uang belum diisi!");
            return false;
        }
        return true;
    }

	public function edit($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih Data Warkat terlebih dahulu sebelum melakukan proses edit.");
			redirect_url("cashbank.warkat");
		}
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/coadetail.php");
        $loader = null;
        $log = new UserAdmin();
        $warkat = new Warkat();
        if (count($this->postData) > 0) {
            $warkat->Id = $id;
            $warkat->CabangId = $this->GetPostValue("CabangId");
            $warkat->WarkatMode = $this->GetPostValue("WarkatMode");
            $warkat->WarkatNo = $this->GetPostValue("WarkatNo");
            $warkat->WarkatDate = $this->GetPostValue("WarkatDate");
            $warkat->WarkatTypeId = $this->GetPostValue("WarkatTypeId");
            $warkat->WarkatDescs = $this->GetPostValue("WarkatDescs");
            $warkat->WarkatBankId = $this->GetPostValue("WarkatBankId");
            $warkat->WarkatAmount = $this->GetPostValue("WarkatAmount");
            $warkat->ContactId = $this->GetPostValue("ContactId");
            $warkat->ReffNo = $this->GetPostValue("ReffNo");
            $warkat->ReffAccNo = $this->GetPostValue("ReffAccNo");
            if ($this->GetPostValue("WarkatStatus") == null){
                $warkat->WarkatStatus = 0;
            }else{
                $warkat->WarkatStatus = $this->GetPostValue("WarkatStatus");
            }
            $warkat->CreateMode = 0;
            if ($this->ValidateWarkat($warkat)) {
                $warkat->UpdateById = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $warkat->Update($id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.warkat','Add New Warkat No: '.$warkat->WarkatNo,$warkat->ReffNo,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Warkat: %s (%s) sudah berhasil diupdate", $warkat->WarkatNo, $warkat->WarkatDescs));
                    redirect_url("cashbank.warkat");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.warkat','Add New Warkat No: '.$warkat->WarkatNo,$warkat->ReffNo,'Failed');
                    $this->Set("error",sprintf("Gagal pada saat mengupdate Data Warkat No.%s . Message: %s",$warkat->WarkatNo, $this->connector->GetErrorMessage()));
                }
            }
        }else{
            $warkat = $warkat->LoadById($id);
            if($warkat->WarkatStatus == 1){
                $this->persistence->SaveState("error", sprintf("Maaf Data Warkat No. %s tidak boleh diubah karena sudah berstatus -CAIR-!",$warkat->WarkatNo));
                redirect_url("cashbank.warkat");
            }
            if($warkat->WarkatStatus == 2){
                $this->persistence->SaveState("error", sprintf("Maaf Data Warkat No. %s tidak boleh diubah karena sudah berstatus -VOID-!",$warkat->WarkatNo));
                redirect_url("cashbank.warkat");
            }
            if($warkat->WarkatStatus == 0 && $warkat->CreateMode == 1){
                $this->persistence->SaveState("error", sprintf("Maaf Data Warkat No. %s tidak boleh diubah secara manual!",$warkat->WarkatNo));
                redirect_url("cashbank.warkat");
            }
        }
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
        $loader = new Bank();
        $banks = $loader->LoadAllNonCash($this->userCabangId);
        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId);
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("warkat", $warkat);
        $this->Set("banks", $banks);
        $this->Set("accounts", $accounts);
	}

    public function process($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih Data Warkat terlebih dahulu sebelum melakukan proses warkat.");
            redirect_url("cashbank.warkat");
        }
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/coadetail.php");
        $loader = null;
        $log = new UserAdmin();
        $warkat = new Warkat();
        if (count($this->postData) > 0) {
            $warkat->Id = $id;
            $warkat->WarkatStatus = $this->GetPostValue("WarkatStatus");
            $warkat->WarkatBankId = $this->GetPostValue("WarkatBankId");
            $warkat->ProcessDate = $this->GetPostValue("ProcessDate");
            $warkat->WarkatDescs = $this->GetPostValue("WarkatDescs");
            $warkat->UpdateById = AclManager::GetInstance()->GetCurrentUser()->Id;
            $rs = $warkat->Process($id);
            if ($rs) {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.warkat','Process Warkat No: '.$warkat->WarkatNo,$warkat->ReffNo,'Success');
                $this->persistence->SaveState("info", sprintf("Data Warkat: %s (%s) sudah berhasil diproses", $warkat->WarkatNo, $warkat->WarkatDescs));
                redirect_url("cashbank.warkat");
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.warkat','Process Warkat No: '.$warkat->WarkatNo,$warkat->ReffNo,'Failed');
                $this->Set("error",sprintf("Gagal proses Data Warkat No.%s . Message: %s",$warkat->WarkatNo, $this->connector->GetErrorMessage()));
            }
        }else{
            $warkat = $warkat->LoadById($id);
            if($warkat->WarkatStatus == 1){
                $this->persistence->SaveState("error", sprintf("Maaf Data Warkat No. %s tidak boleh diproses karena sudah berstatus -CAIR-!",$warkat->WarkatNo));
                redirect_url("cashbank.warkat");
            }
            if($warkat->WarkatStatus == 2){
                $this->persistence->SaveState("error", sprintf("Maaf Data Warkat No. %s tidak boleh diproses karena sudah berstatus -VOID-!",$warkat->WarkatNo));
                redirect_url("cashbank.warkat");
            }
        }
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
        $loader = new Bank();
        $banks = $loader->LoadAllNonCash($this->userCabangId);
        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId);
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("warkat", $warkat);
        $this->Set("banks", $banks);
        $this->Set("accounts", $accounts);
    }

    public function view($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih Data Warkat terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("cashbank.warkat");
        }
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/coadetail.php");
        $loader = null;
        $warkat = new Warkat();
        $warkat = $warkat->LoadById($id);
       if($warkat == null){
            $this->persistence->SaveState("error", sprintf("Maaf Data Warkat No. %s tidak ditemukan atau sudah dihapus!",$warkat->WarkatNo));
            redirect_url("cashbank.warkat");
        }
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
        $loader = new Bank();
        $banks = $loader->LoadAllNonCash($this->userCabangId);
        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId);
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("warkat", $warkat);
        $this->Set("banks", $banks);
        $this->Set("accounts", $accounts);
    }

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih Data Warkat terlebih dahulu sebelum melakukan proses penghapusan data.");
			redirect_url("cashbank.warkat");
		}
		$warkat = new Warkat();
        $log = new UserAdmin();
        /** @var $warkat Warkat */
        $warkat = $warkat->LoadById($id);
        if($warkat->WarkatStatus == 0 && $warkat->CreateMode == 0){
            $rs = $warkat->Delete($warkat->Id);
            if ($rs == 1) {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.warkat','Delete Warkat No: '.$warkat->WarkatNo,$warkat->ReffNo,'Success');
                $this->persistence->SaveState("info", sprintf("Data Warkat: %s (%s) berhasil dihapus", $warkat->WarkatNo, $warkat->WarkatDescs));
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.warkat','Delete Warkat No: '.$warkat->WarkatNo,$warkat->ReffNo,'Failed');
                $this->persistence->SaveState("error", sprintf("Gagal menghapus Data Warkat: %s (%s). Error: %s", $warkat->WarkatNo, $warkat->WarkatDescs, $this->connector->GetErrorMessage()));
            }
        }
        if($warkat->WarkatStatus == 1){
            $this->persistence->SaveState("error", sprintf("Maaf Data Warkat No. %s tidak boleh dihapus karena sudah berstatus -CAIR-!",$warkat->WarkatNo));
        }
        if($warkat->WarkatStatus == 2){
            $this->persistence->SaveState("error", sprintf("Maaf Data Warkat No. %s tidak boleh dihapus karena sudah berstatus -VOID-!",$warkat->WarkatNo));
        }
        if($warkat->WarkatStatus == 0 && $warkat->CreateMode == 1){
            $this->persistence->SaveState("error", sprintf("Maaf Data Warkat No. %s tidak boleh dihapus secara manual!",$warkat->WarkatNo));
        }
		redirect_url("cashbank.warkat");
	}

   public function checkIfExistWarkat($warkatNo){
       $warkat = new Warkat();
       $warkat = $warkat->LoadByWarkatNo($warkatNo);
       if ($warkat == null){
           print ("OK");
       }else{
           printf("EX|%s|%s|%s",$warkat->FormatWarkatDate(JS_DATE),$warkat->ContactName,$warkat->WarkatAmount);
       }
   }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/trxtype.php");
        require_once(MODEL . "master/bank.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sTrxMode = $this->GetPostValue("TrxMode");
            $sBankId= $this->GetPostValue("BankId");
            $sTrxStatus = $this->GetPostValue("TrxStatus");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            // tahun transaksi harus sama
            if (date("Y",$sStartDate) == date("Y",$sEndDate)){
                // ambil data yang diperlukan
                $warkat = new Warkat();
                $reports = $warkat->Load4Reports($this->userCompanyId,$sCabangId,$sTrxMode,$sBankId,$sTrxStatus,$sStartDate,$sEndDate);
            }else{
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("cashbank.warkat/report");
            }
        }else{
            $sCabangId = 0;
            $sTrxMode = 0;
            $sBankId = 0;
            $sTrxStatus = -1;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            $sEndDate = time();
            $sOutput = 0;
            $reports = null;
        }
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new TrxType();
        $trxTypes = $loader->LoadAll($this->userCompanyId);
        $loader = new Bank();
        $Banks = $loader->LoadAllNonCash($this->userCompanyId);
        //load data cabang
        $loader = new Cabang();
        $cabangs = $loader->LoadByEntityId($this->userCompanyId);
        // kirim ke view
        $this->Set("Cabangs",$cabangs);
        $this->Set("Banks",$Banks);
        $this->Set("TrxTypes",$trxTypes);
        $this->Set("CabangId",$sCabangId);
        $this->Set("TrxMode",$sTrxMode);
        $this->Set("BankId",$sBankId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("TrxStatus",$sTrxStatus);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
    }

}

// End of file: warkat_controller.php
