<?php
class ReceiptController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "ar/receipt.php");
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
        $settings["columns"][] = array("name" => "a.receipt_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.receipt_no", "display" => "No. Receipt", "width" => 80);
        $settings["columns"][] = array("name" => "a.debtor_name", "display" => "Nama Customer", "width" => 150);
        $settings["columns"][] = array("name" => "a.receipt_descs", "display" => "Keterangan", "width" => 160);
        $settings["columns"][] = array("name" => "a.cara_bayar", "display" => "Cara Bayar", "width" => 80);
        $settings["columns"][] = array("name" => "a.bank_name", "display" => "Bank", "width" => 80);
        $settings["columns"][] = array("name" => "format(a.receipt_amount,0)", "display" => "Penerimaan", "width" => 90, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.allocate_amount,0)", "display" => "Alokasi", "width" => 90, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.receipt_amount - a.allocate_amount,0)", "display" => "Sisa", "width" => 90, "align" => "right");
        $settings["columns"][] = array("name" => "a.admin_name", "display" => "Admin", "width" => 80);
        $settings["columns"][] = array("name" => "a.status_desc", "display" => "Status", "width" => 50);

        $settings["filters"][] = array("name" => "a.cabang_code", "display" => "Kode Cabang");
        $settings["filters"][] = array("name" => "a.receipt_no", "display" => "No. Receipt");
        $settings["filters"][] = array("name" => "a.receipt_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.debtor_name", "display" => "Nama Customer");
        $settings["filters"][] = array("name" => "a.status_desc", "display" => "Status");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = false;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Penerimaan Piutang";

            if ($acl->CheckUserAccess("ar.receipt", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "ar.receipt/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("ar.receipt", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "ar.receipt/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Receipt terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data rekonsil",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("ar.receipt", "delete")) {
                $settings["actions"][] = array("Text" => "Void", "Url" => "ar.receipt/void/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("ar.receipt", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "ar.receipt/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Receipt terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data receipt","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.receipt", "print")) {
                $settings["actions"][] = array("Text" => "Print Receipt", "Url" => "ar.receipt/print_pdf/%s", "Class" => "bt_pdf", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Receipt terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data receipt","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.receipt", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "ar.receipt/report", "Class" => "bt_report", "ReqId" => 0);
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.receipt", "approve")) {
                $settings["actions"][] = array("Text" => "Approve Receipt", "Url" => "ar.receipt/approve", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Receipt terlebih dahulu sebelum proses approval.",
                    "Confirm" => "Apakah anda menyetujui data invoice yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            if ($acl->CheckUserAccess("ar.receipt", "approve")) {
                $settings["actions"][] = array("Text" => "Batal Approve", "Url" => "ar.receipt/unapprove", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Receipt terlebih dahulu sebelum proses pembatalan.",
                    "Confirm" => "Apakah anda mau membatalkan approval data invoice yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
        } else {
            $settings["from"] = "vw_ar_receipt_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId ." And year(a.receipt_date) = ".$this->trxYear." And month(a.receipt_date) = ".$this->trxMonth;
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
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/warkattype.php");
        require_once(MODEL . "cashbank/warkat.php");
        $loader = null;
        $log = new UserAdmin();
		$receipt = new Receipt();
        $receipt->CabangId = $this->userCabangId;
        if (count($this->postData) > 0) {
			$receipt->CabangId = $this->GetPostValue("CabangId");
			$receipt->ReceiptDate = $this->GetPostValue("ReceiptDate");
            $receipt->ReceiptNo = $this->GetPostValue("ReceiptNo");
            $receipt->ReceiptDescs = $this->GetPostValue("ReceiptDescs");
            $receipt->DebtorId = $this->GetPostValue("DebtorId");
            $receipt->WarkatTypeId = $this->GetPostValue("WarkatTypeId");
            $receipt->WarkatBankId = $this->GetPostValue("WarkatBankId");
            $receipt->WarkatNo = $this->GetPostValue("WarkatNo");
            $receipt->WarkatDate = $this->GetPostValue("WarkatDate");
            $receipt->ReceiptAmount = $this->GetPostValue("ReceiptAmount");
            $receipt->AllocateAmount = $this->GetPostValue("AllocateAmount");
            $receipt->ReturnNo = $this->GetPostValue("ReturnNo");
            if ($this->GetPostValue("ReceiptStatus") == null || $this->GetPostValue("ReceiptStatus") == 0){
                $receipt->ReceiptStatus = 1;
            }else{
                $receipt->ReceiptStatus = $this->GetPostValue("ReceiptStatus");
            }
            $receipt->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateMaster($receipt)) {
                if ($receipt->ReceiptNo == null || $receipt->ReceiptNo == "-" || $receipt->ReceiptNo == ""){
                    $receipt->ReceiptNo = $receipt->GetReceiptDocNo();
                }
                $rs = $receipt->Insert();
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->Set("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Add New Receipt',$receipt->ReceiptNo,'Failed');
                }else{
                    // simpan data warkat
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Add New Receipt',$receipt->ReceiptNo,'Success');
                    $warkat = null;
                    if ($receipt->WarkatTypeId > 1 && $receipt->WarkatTypeId < 5){
                        $warkat = new Warkat();
                        /** @var $warkat Warkat */
                        $warkat->CabangId = $receipt->CabangId;
                        $warkat->ContactId = $receipt->DebtorId;
                        $warkat->WarkatMode = 1;
                        $warkat->WarkatNo = $receipt->WarkatNo;
                        $warkat->WarkatDate = $receipt->WarkatDate;
                        $warkat->WarkatTypeId = $receipt->WarkatTypeId;
                        $warkat->WarkatBankId = $receipt->WarkatBankId;
                        $warkat->WarkatAmount = $receipt->ReceiptAmount;
                        $warkat->WarkatStatus = 0;
                        $warkat->ReffNo = $receipt->ReceiptNo;
                        $warkat->CreateById = AclManager::GetInstance()->GetCurrentUser()->Id;
                        $rs = $warkat->Insert();
                        if ($rs != 1) {
                            if ($this->connector->IsDuplicateError()) {
                                $this->Set("error", "Nomor Warkat sudah ada pada database.");
                            } else {
                                $this->Set("error", "Error saat simpan data warkat. Message: " . $this->connector->GetErrorMessage());
                            }
                        }
                    }
                    redirect_url("ar.receipt/edit/".$receipt->Id);
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
        $loader = new Bank();
        $banks = $loader->LoadByCabangId($this->userCabangId);
        $loader = new WarkatType();
        $warkattypes = $loader->LoadAll();
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("receipt", $receipt);
        $this->Set("banks", $banks);
        $this->Set("warkattypes", $warkattypes);
	}

	private function ValidateMaster(Receipt $receipt) {
        if ($receipt->DebtorId == null || $receipt->DebtorId == 0 || $receipt->DebtorId == ''){
            $this->Set("error", "Data Customer belum diisi!");
            return false;
        }
        if ($receipt->WarkatTypeId > 1 && $receipt->WarkatTypeId < 6){
            if ($receipt->WarkatBankId < 2){
                $this->Set("error", "Data Bank belum diisi!");
                return false;
            }
            if ($receipt->WarkatNo == null || $receipt->WarkatNo == ''){
                $this->Set("error", "No. Warkat belum diisi!");
                return false;
            }
            if ($receipt->WarkatDate == null || $receipt->WarkatDate == ''){
                $this->Set("error", "Tanggal Warkat belum diisi!");
                return false;
            }
        }elseif ($receipt->WarkatTypeId == 1){
            $receipt->WarkatBankId = 1;
            $receipt->ReturnNo = '';
            $receipt->WarkatNo = '';
            $receipt->WarkatDate = null;
        }elseif ($receipt->WarkatTypeId == 7){
            $receipt->WarkatBankId = 0;
            $receipt->WarkatNo = '';
            $receipt->WarkatDate = null;
            if ($receipt->ReturnNo == '-' || $receipt->ReturnNo == '' || $receipt->ReturnNo == null){
                $this->Set("error", "Data No Retur belum diisi!");
                return false;
            }
        }
        if ($receipt->ReceiptAmount == 0 || $receipt->ReceiptAmount == '' || $receipt->ReceiptAmount == null){
            $this->Set("error", "Jumlah Penerimaan belum diisi!");
            return false;
        }
		return true;
	}

    public function edit($receiptId = null) {
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/warkattype.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $receipt = new Receipt();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $receipt->Id = $receiptId;
            $receipt->CabangId = $this->GetPostValue("CabangId");
            $receipt->ReceiptDate = $this->GetPostValue("ReceiptDate");
            $receipt->ReceiptNo = $this->GetPostValue("ReceiptNo");
            $receipt->ReceiptDescs = $this->GetPostValue("ReceiptDescs");
            $receipt->DebtorId = $this->GetPostValue("DebtorId");
            $receipt->WarkatTypeId = $this->GetPostValue("WarkatTypeId");
            $receipt->WarkatBankId = $this->GetPostValue("WarkatBankId");
            $receipt->WarkatNo = $this->GetPostValue("WarkatNo");
            $receipt->WarkatDate = $this->GetPostValue("WarkatDate");
            $receipt->ReceiptAmount = $this->GetPostValue("ReceiptAmount");
            $receipt->AllocateAmount = $this->GetPostValue("AllocateAmount");
            $receipt->ReturnNo = $this->GetPostValue("ReturnNo");
            if ($this->GetPostValue("ReceiptStatus") == null || $this->GetPostValue("ReceiptStatus") == 0){
                $receipt->ReceiptStatus = 1;
            }else{
                $receipt->ReceiptStatus = $this->GetPostValue("ReceiptStatus");
            }
            $receipt->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateMaster($receipt)) {
                $rs = $receipt->Update($receipt->Id);
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->persistence->SaveState("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Update Receipt',$receipt->ReceiptNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Update Receipt',$receipt->ReceiptNo,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Receipt No.: '%s' Tanggal: %s telah berhasil diubah..", $receipt->ReceiptNo, $receipt->ReceiptDate));
                    redirect_url("ar.receipt/edit/".$receipt->Id);
                }
            }
        }else{
            $receipt = $receipt->LoadById($receiptId);
            if($receipt == null){
               $this->persistence->SaveState("error", "Maaf Data Receipt dimaksud tidak ada pada database. Mungkin sudah dihapus!");
               redirect_url("ar.receipt");
            }
            if($receipt->ReceiptStatus == 2){
                $this->persistence->SaveState("error", sprintf("Maaf Data Receipt No. %s sudah berstatus -APPROVED-",$receipt->ReceiptNo));
                redirect_url("ar.receipt");
            }
            if($receipt->ReceiptStatus == 3){
                $this->persistence->SaveState("error", sprintf("Maaf Data Receipt No. %s sudah berstatus -VOID-",$receipt->ReceiptNo));
                redirect_url("ar.receipt");
            }
        }

        // load details
        $receipt->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $loader = new Bank();
        $banks = $loader->LoadByCabangId($this->userCabangId);
        $loader = new WarkatType();
        $warkattypes = $loader->LoadAll();
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("receipt", $receipt);
        $this->Set("banks", $banks);
        $this->Set("warkattypes", $warkattypes);
        $this->Set("acl", $acl);
    }

	public function view($receiptId = null) {
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/warkattype.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $receipt = new Receipt();
        $receipt = $receipt->LoadById($receiptId);
        if($receipt == null){
            $this->persistence->SaveState("error", "Maaf Data Receipt dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.receipt");
        }
        // load details
        $receipt->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $loader = new Bank();
        $banks = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new WarkatType();
        $warkattypes = $loader->LoadAll();
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
        $this->Set("receipt", $receipt);
        $this->Set("banks", $banks);
        $this->Set("warkattypes", $warkattypes);
        $this->Set("acl", $acl);
	}

    public function delete($receiptId) {
        // Cek datanya
        $receipt = new Receipt();
        $log = new UserAdmin();
        $receipt = $receipt->FindById($receiptId);
        if($receipt == null){
            $this->Set("error", "Maaf Data Receipt dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.receipt");
        }
        if($receipt->ReceiptStatus == 2){
            $this->persistence->SaveState("error", sprintf("Maaf Data Receipt No. %s sudah berstatus -APPROVED-",$receipt->ReceiptNo));
            redirect_url("ar.receipt");
        }
        /** @var $receipt Receipt */
        if ($receipt->Delete($receiptId) > 0) {
            $this->persistence->SaveState("info", sprintf("Data Receipt No: %s sudah berhasil dihapus", $receipt->ReceiptNo));
            $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Delete Receipt',$receipt->ReceiptNo,'Success');
        }else{
            $this->persistence->SaveState("error", sprintf("Maaf, Data Receipt No: %s gagal dihapus", $receipt->ReceiptNo));
            $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Delete Receipt',$receipt->ReceiptNo,'Failed');
        }
        redirect_url("ar.receipt");
    }

    public function void($receiptId) {
        // Cek datanya
        $receipt = new Receipt();
        $log = new UserAdmin();
        $receipt = $receipt->FindById($receiptId);
        if($receipt == null){
            $this->Set("error", "Maaf Data Receipt dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.receipt");
        }
        if($receipt->ReceiptStatus == 2){
            $this->persistence->SaveState("error", sprintf("Maaf Data Receipt No. %s sudah berstatus -APPROVED-",$receipt->ReceiptNo));
            redirect_url("ar.receipt");
        }
        if($receipt->ReceiptStatus == 3){
            $this->persistence->SaveState("error", sprintf("Maaf Data Receipt No. %s sudah berstatus -VOID-",$receipt->ReceiptNo));
            redirect_url("ar.receipt");
        }
        /** @var $receipt Receipt */
        if ($receipt->Void($receiptId) > 0) {
            $this->persistence->SaveState("info", sprintf("Data Receipt No: %s sudah berhasil dibatalkan", $receipt->ReceiptNo));
            $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Delete Receipt',$receipt->ReceiptNo,'Success');
        }else{
            $this->persistence->SaveState("error", sprintf("Maaf, Data Receipt No: %s gagal dibatalkan", $receipt->ReceiptNo));
            $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Delete Receipt',$receipt->ReceiptNo,'Failed');
        }
        redirect_url("ar.receipt");
    }

	public function add_detail($receiptId = null) {
        $receipt = new Receipt($receiptId);
        $log = new UserAdmin();
        $recdetail = new ReceiptDetail();
        $recdetail->ReceiptId = $receiptId;
        $recdetail->ReceiptNo = $receipt->ReceiptNo;
        $recdetail->CabangId = $receipt->CabangId;
        if (count($this->postData) > 0) {
            $recdetail->InvoiceId = $this->GetPostValue("aInvoiceId");
            $recdetail->InvoiceNo = $this->GetPostValue("aInvoiceNo");
            $recdetail->InvoiceOutstanding = $this->GetPostValue("aInvoiceOutStanding");
            $recdetail->AllocateAmount = $this->GetPostValue("aAllocateAmount");
            $recdetail->InvoiceAmount = $this->GetPostValue("aAllocateAmount");
            $recdetail->PotPph = 0;
            $recdetail->PotLain = 0;
            $rs = $recdetail->Insert()== 1;
            if ($rs > 0) {
                $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Add Receipt detail -> Inv. '.$recdetail->InvoiceNo.' = '.$recdetail->AllocateAmount,$receipt->ReceiptNo,'Success');
                echo json_encode(array());
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Add Receipt detail -> Inv. '.$recdetail->InvoiceNo.' = '.$recdetail->AllocateAmount,$receipt->ReceiptNo,'Failed');
                echo json_encode(array('errorMsg'=>'Some database errors occured.'));
            }
        }
	}    

    public function delete_detail($id) {
        // Cek datanya
        $log = new UserAdmin();
        $recdetail = new ReceiptDetail();
        $recdetail = $recdetail->FindById($id);
        if ($recdetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($recdetail->Delete($id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Delete Receipt detail -> Inv. '.$recdetail->InvoiceNo.' = '.$recdetail->AllocateAmount,$recdetail->ReceiptNo,'Success');
            printf("Data Detail Receipt ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Delete Receipt detail -> Inv. '.$recdetail->InvoiceNo.' = '.$recdetail->AllocateAmount,$recdetail->ReceiptNo,'Failed');
            printf("Maaf, Data Detail Receipt ID: %d gagal dihapus!",$id);
        }
    }

    public function print_pdf($receiptId = null) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        $loader = null;
        $receipt = new Receipt();
        $receipt = $receipt->LoadById($receiptId);
        if($receipt == null){
            $this->persistence->SaveState("error", "Maaf Data Receipt dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.receipt");
        }
        // load details
        $receipt->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Karyawan();
        $banks = $loader->LoadAll();
        $userName = AclManager::GetInstance()->GetCurrentUser()->RealName;
        //kirim ke view
        $this->Set("sales", $banks);
        $this->Set("cabangs", $cabang);
        $this->Set("receipt", $receipt);
        $this->Set("userName", $userName);
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/warkattype.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sContactsId = $this->GetPostValue("ContactsId");
            $sWarkatBankId = $this->GetPostValue("WarkatBankId");
            $sReceiptStatus = $this->GetPostValue("ReceiptStatus");
            $sWarkatTypeId = $this->GetPostValue("WarkatTypeId");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $receipt = new Receipt();
            $reports = $receipt->Load4Reports($this->userCompanyId,$sCabangId,$sWarkatBankId,$sContactsId,$sWarkatTypeId,$sReceiptStatus,$sStartDate,$sEndDate);
        }else{
            $sCabangId = 0;
            $sContactsId = 0;
            $sWarkatBankId = 0;
            $sReceiptStatus = -1;
            $sWarkatTypeId = -1;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sOutput = 0;
            $reports = null;
        }
        $customer = new Contacts();
        $customer = $customer->LoadAll();
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new Bank();
        $banks = $loader->LoadAll();
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
        $loader = new WarkatType();
        $warkattypes = $loader->LoadAll();
        // kirim ke view
        $this->Set("cabangs", $cabang);
        $this->Set("customers",$customer);
        $this->Set("banks",$banks);
        $this->Set("CabangId",$sCabangId);
        $this->Set("ContactsId",$sContactsId);
        $this->Set("WarkatBankId",$sWarkatBankId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("ReceiptStatus",$sReceiptStatus);
        $this->Set("WarkatTypeId",$sWarkatTypeId);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userLevel",$this->userLevel);
        $this->Set("warkattypes",$warkattypes);
    }

    public function getReceiptItemRows($id){
        $receipt = new Receipt();
        $rows = $receipt->GetReceiptItemRow($id);
        print($rows);
    }

    public function createTextReceipt($id){
        $receipt = new Receipt($id);
        if ($receipt <> null){
            $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            fwrite($myfile, $receipt->CompanyName);
            fwrite($myfile, "\n".'FAKTUR PENJUALAN');

            fclose($myfile);
        }
    }

    public function getoutstandinginvoices_plain($cabangId = 0,$customerId = 0 ,$receiptNo = null){
        require_once(MODEL . "ar/invoice.php");
        $ret = 'ER|0';
        if($receiptNo != null || $receiptNo != ''){
            /** @var $receipt Invoice[] */
            $receipt = new Invoice();
            $receipt = $receipt->GetUnpaidInvoices($cabangId,$customerId,$receiptNo);
            if ($receipt != null){
                $ret = 'OK|'.$receipt->Id.'|'.date(JS_DATE,$receipt->InvoiceDate).'|'.date(JS_DATE,$receipt->DueDate).'|'.$receipt->BalanceAmount;
            }
        }
        print $ret;
    }

    public function getoutstandinginvoices_json($cabangId,$customerId){
        //$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $receipt = new Receipt();
        $itemlists = $receipt->GetJSonUnpaidInvoices($cabangId,$customerId);
        echo json_encode($itemlists);
    }

    public function approve() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ar.receipt");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $log = new UserAdmin();
            $receipt = new Receipt();
            $receipt = $receipt->FindById($id);
            /** @var $receipt Receipt */
            // process receipt
            if($receipt->ReceiptStatus == 1){
                $rs = $receipt->Approve($receipt->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Approve Receipt',$receipt->ReceiptNo,'Success');
                    $infos[] = sprintf("Data Receipt No.: '%s' (%s) telah berhasil di-approve.", $receipt->ReceiptNo, $receipt->ReceiptDescs);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Approve Receipt',$receipt->ReceiptNo,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses approve Data Invoice: '%s'. Message: %s", $receipt->ReceiptNo, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Receipt No.%s sudah berstatus -Approved- !",$receipt->ReceiptNo);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ar.receipt");
    }

    public function unapprove() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ar.receipt");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $log = new UserAdmin();
            $receipt = new Receipt();
            $receipt = $receipt->FindById($id);
            /** @var $receipt Receipt */
            // process invoice
            if($receipt->ReceiptStatus == 2){
                $rs = $receipt->Unapprove($receipt->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Un-Approve Receipt',$receipt->ReceiptNo,'Success');
                    $infos[] = sprintf("Data Receipt No.: '%s' (%s) telah berhasil di-batalkan.", $receipt->ReceiptNo, $receipt->ReceiptDescs);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.receipt','Un-Approve Receipt',$receipt->ReceiptNo,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses pembatalan Data Invoice: '%s'. Message: %s", $receipt->ReceiptNo, $this->connector->GetErrorMessage());
                }
            }else{
                if ($receipt->ReceiptStatus == 1){
                    $errors[] = sprintf("Data Receipt No.%s masih berstatus -POSTED- !",$receipt->ReceiptNo);
                }else{
                    $errors[] = sprintf("Data Receipt No.%s masih berstatus -DRAFT- !",$receipt->ReceiptNo);
                }
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ar.receipt");
    }
}


// End of File: estimasi_controller.php
