<?php

class CbTrxController extends AppController {
	private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

	protected function Initialize() {
		require_once(MODEL . "cashbank/cbtrx.php");
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

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		//$settings["columns"][] = array("name" => "a.kode_cabang", "display" => "Cabang", "width" => 50);
        $settings["columns"][] = array("name" => "a.trx_date", "display" => "Tanggal", "width" => 70);
        $settings["columns"][] = array("name" => "a.doc_no", "display" => "No. Bukti", "width" => 85);
        $settings["columns"][] = array("name" => "a.xmode", "display" => "Mode", "width" => 50);
        $settings["columns"][] = array("name" => "a.bank_name", "display" => "Kas/Bank", "width" => 100);
		$settings["columns"][] = array("name" => "a.trx_descs", "display" => "Keterangan", "width" => 250);
        $settings["columns"][] = array("name" => "format(a.trx_amount,0)", "display" => "Jumlah", "width" => 90, "align" => "right");
        $settings["columns"][] = array("name" => "a.contact_name", "display" => "Relasi", "width" => 100);
        $settings["columns"][] = array("name" => "a.reff_no", "display" => "Refferensi", "width" => 100);
        $settings["columns"][] = array("name" => "a.xstatus", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "a.user_id", "display" => "Admin", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.create_mode = 1,'Auto','Manual')", "display" => "Source", "width" => 50);

        $settings["filters"][] = array("name" => "a.trx_descs", "display" => "Keterangan");
        $settings["filters"][] = array("name" => "a.kode_cabang", "display" => "Cabang");
		$settings["filters"][] = array("name" => "a.doc_no", "display" => "No. Bukti");
        $settings["filters"][] = array("name" => "a.reff_no", "display" => "Refferensi");
        $settings["filters"][] = array("name" => "a.xmode", "display" => "Mode");
        $settings["filters"][] = array("name" => "a.bank_name", "display" => "Kas/Bank");
        $settings["filters"][] = array("name" => "a.xstatus", "display" => "Status");
        $settings["filters"][] = array("name" => "a.user_id", "display" => "Admin");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Transaksi Kas & Bank";

			if ($acl->CheckUserAccess("cashbank.cbtrx", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "cashbank.cbtrx/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("cashbank.cbtrx", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "cashbank.cbtrx/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu trxtype.",
					"Confirm" => "");
			}
            if ($acl->CheckUserAccess("cashbank.cbtrx", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "cashbank.cbtrx/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Transaksi terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data transaksi","Confirm" => "");
            }
			if ($acl->CheckUserAccess("cashbank.cbtrx", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "cashbank.cbtrx/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu data.",
					"Confirm" => "Apakah anda mau menghapus data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbank.cbtrx", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "cashbank.cbtrx/report", "Class" => "bt_report", "ReqId" => 0);
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbank.cbtrx", "approve")) {
                $settings["actions"][] = array("Text" => "Approval", "Url" => "cashbank.cbtrx/approve", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses approval.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda menyetujui data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            if ($acl->CheckUserAccess("cashbank.cbtrx", "approve")) {
                $settings["actions"][] = array("Text" => "Batal Approval", "Url" => "cashbank.cbtrx/unapprove", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses pembatalan.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda mau membatalkan approval data transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbank.cbtrx", "view")) {
                $settings["actions"][] = array("Text" => "Print Bukti", "Url" => "cashbank.cbtrx/cetakpdf/%s", "Class" => "bt_print", "ReqId" => 1,
                    "Error" => "Mohon memilih Data Transaksi terlebih dahulu sebelum proses cetak.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda akan mencetak transaksi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
			$settings["def_order"] = 2;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = false;

		} else {
			$settings["from"] = "vw_cb_transaction AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                if ($this->userLevel > 2) {
                    $settings["where"] = "a.entity_id = ".$this->userCompanyId . " And year(a.trx_date) = " . $this->trxYear . " And month(a.trx_date) = " . $this->trxMonth;
                }else{
                    $settings["where"] = "a.cabang_id = " . $this->userCabangId . " And year(a.trx_date) = " . $this->trxYear . " And month(a.trx_date) = " . $this->trxMonth;
                }
            } else {
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = ".$this->userCabangId;
            }
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(CbTrx $cbtrx) {
		return true;
	}

	public function add() {
        require_once(MODEL . "master/coadetail.php");
        require_once(MODEL . "master/trxtype.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/contacts.php");
        $loader = null;
        $log = new UserAdmin();
		$cbtrx = new CbTrx();
		if (count($this->postData) > 0) {
            $cbtrx->EntityId = $this->userCompanyId;
            $cbtrx->CabangId = $this->userCabangId;
            $cbtrx->TrxDate = $this->GetPostValue("TrxDate");
            $cbtrx->TrxMode = $this->GetPostValue("TrxMode");
            $cbtrx->TrxTypeId = $this->GetPostValue("TrxTypeId");
            $cbtrx->TrxDescs = $this->GetPostValue("TrxDescs");
            $cbtrx->DbAccNo = $this->GetPostValue("DbAccNo");
            $cbtrx->CrAccNo = $this->GetPostValue("CrAccNo");
            //$cbtrx->CoaBankId = $this->GetPostValue("CoaBankId");
            //$cbtrx->DocNo = $this->GetPostValue("DocNo");
            if ($cbtrx->TrxMode == 1){
                $cbtrx->CoaBankId = $cbtrx->DbAccNo;
            }elseif ($cbtrx->TrxMode == 2){
                $cbtrx->CoaBankId = $cbtrx->CrAccNo;
            }else{
                $cbtrx->CoaBankId = 0;
            }
            $cbtrx->TrxAmount = $this->GetPostValue("TrxAmount");
            $cbtrx->ContactId = $this->GetPostValue("ContactId");
            $cbtrx->ReffNo = $this->GetPostValue("ReffNo");
            $cbtrx->TrxStatus = $this->GetPostValue("TrxStatus");
            $cbtrx->CreateMode = $this->GetPostValue("CreateMode");
            if ($cbtrx->CreateMode == null){
                $cbtrx->CreateMode = 0;
            }
			if ($this->ValidateData($cbtrx)) {
                $cbtrx->CreateById = AclManager::GetInstance()->GetCurrentUser()->Id;
                $cbtrx->DocNo = $cbtrx->GetCbDocNo();
                $rs = $cbtrx->Insert();
				if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Add New CashBook Trx',$cbtrx->DocNo,'Success');
					$this->persistence->SaveState("info", sprintf("Data Data Transaksi: %s (%s) sudah berhasil disimpan", $cbtrx->DocNo, $cbtrx->TrxDescs));
					redirect_url("cashbank.cbtrx");
				} else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Add New CashBook Trx',$cbtrx->DocNo,'Failed');
					$this->Set("error", "Gagal pada saat menyimpan data transaksi. Message: " . $this->connector->GetErrorMessage());
				}
			}
		}else{
            $cbtrx->EntityId = $this->userCompanyId;
            $cbtrx->CabangId = $this->userCabangId;
        }
        // load data for combo box
        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId);
        $trxtypes = new TrxType();
        $trxtypes = $trxtypes->LoadAll($this->userCompanyId);
        $coabanks = new CoaDetail();
        $coabanks = $coabanks->LoadCashBookAccount($this->userCompanyId);
        $contacts = new Contacts();
        $contacts = $contacts->LoadByCabang($this->userCabangId);
		$this->Set("cbtrx", $cbtrx);
		$this->Set("accounts", $accounts);
        $this->Set("trxtypes", $trxtypes);
        $this->Set("coabanks", $coabanks);
        $this->Set("contacts", $contacts);
	}

	public function edit($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih Data Transaksi terlebih dahulu sebelum melakukan proses edit.");
			redirect_url("cashbank.cbtrx");
		}
        require_once(MODEL . "master/coadetail.php");
        require_once(MODEL . "master/trxtype.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/contacts.php");
        $loader = null;
        $log = new UserAdmin();
        $cbtrx = new CbTrx();
        if (count($this->postData) > 0) {
            $cbtrx->Id = $this->GetPostValue("Id");
            $cbtrx->EntityId = $this->userCompanyId;
            $cbtrx->CabangId = $this->userCabangId;
            $cbtrx->TrxDate = $this->GetPostValue("TrxDate");
            $cbtrx->TrxMode = $this->GetPostValue("TrxMode");
            $cbtrx->TrxTypeId = $this->GetPostValue("TrxTypeId");
            $cbtrx->TrxDescs = $this->GetPostValue("TrxDescs");
            $cbtrx->DbAccNo = $this->GetPostValue("DbAccNo");
            $cbtrx->CrAccNo = $this->GetPostValue("CrAccNo");
            //$cbtrx->CoaBankId = $this->GetPostValue("CoaBankId");
            $cbtrx->DocNo = $this->GetPostValue("DocNo");
            if ($cbtrx->TrxMode == 1){
                $cbtrx->CoaBankId = $cbtrx->DbAccNo;
            }elseif ($cbtrx->TrxMode == 2){
                $cbtrx->CoaBankId = $cbtrx->CrAccNo;
            }else{
                $cbtrx->CoaBankId = 0;
            }
            $cbtrx->TrxAmount = $this->GetPostValue("TrxAmount");
            $cbtrx->ContactId = $this->GetPostValue("ContactId");
            $cbtrx->ReffNo = $this->GetPostValue("ReffNo");
            $cbtrx->TrxStatus = $this->GetPostValue("TrxStatus");
            $cbtrx->CreateMode = $this->GetPostValue("CreateMode");
            if ($cbtrx->CreateMode == null){
                $cbtrx->CreateMode = 0;
            }
            if ($this->ValidateData($cbtrx)) {
                $cbtrx->UpdatedById = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $cbtrx->Update($cbtrx->Id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Update CashBook Trx',$cbtrx->DocNo,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Transaksi: %s (%s) sudah berhasil diupdate..", $cbtrx->DocNo, $cbtrx->TrxDescs));
                    redirect_url("cashbank.cbtrx");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Update CashBook Trx',$cbtrx->DocNo,'Failed');
                    $this->Set("error", "Gagal pada saat mengupdate data transaksi. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $cbtrx = $cbtrx->LoadById($id);
            if ($cbtrx == null || $cbtrx->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("cashbank.cbtrx");
            }
            if($cbtrx->TrxStatus == 2){
               $this->persistence->SaveState("error", sprintf("Maaf Data Transaksi No. %s tidak boleh diubah karena sudah berstatus -APPROVED-!",$cbtrx->DocNo));
               redirect_url("cashbank.cbtrx");
            }
            if($cbtrx->CreateMode == 1){
                $this->persistence->SaveState("error", sprintf("Maaf Data Transaksi No. %s tidak boleh diubah secara manual!",$cbtrx->DocNo));
                redirect_url("cashbank.cbtrx");
            }
        }
        // load data for combo box
        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId);
        $trxtypes = new TrxType();
        $trxtypes = $trxtypes->LoadAll($this->userCompanyId);
        $coabanks = new CoaDetail();
        $coabanks = $coabanks->LoadCashBookAccount($this->userCompanyId);
        $contacts = new Contacts();
        $contacts = $contacts->LoadByCabang($this->userCabangId);
        $this->Set("cbtrx", $cbtrx);
        $this->Set("accounts", $accounts);
        $this->Set("trxtypes", $trxtypes);
        $this->Set("coabanks", $coabanks);
        $this->Set("contacts", $contacts);
	}

    public function view($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih Data Transaksi terlebih dahulu sebelum menampilkan data.");
            redirect_url("cashbank.cbtrx");
        }
        require_once(MODEL . "master/coadetail.php");
        require_once(MODEL . "master/trxtype.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/contacts.php");
        $loader = null;
        $cbtrx = new CbTrx();
        $cbtrx = $cbtrx->LoadById($id);
        if ($cbtrx == null || $cbtrx->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("cashbank.cbtrx");
        }
        // load data for combo box
        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId);
        $trxtypes = new TrxType();
        $trxtypes = $trxtypes->LoadAll($this->userCompanyId);
        $coabanks = new CoaDetail();
        $coabanks = $coabanks->LoadCashBookAccount($this->userCompanyId);
        $contacts = new Contacts();
        $contacts = $contacts->LoadByCabang($this->userCabangId);
        $this->Set("cbtrx", $cbtrx);
        $this->Set("accounts", $accounts);
        $this->Set("trxtypes", $trxtypes);
        $this->Set("coabanks", $coabanks);
        $this->Set("contacts", $contacts);
    }

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih Data Transaksi terlebih dahulu sebelum melakukan proses penghapusan data.");
			redirect_url("cashbank.cbtrx");
		}
		$cbtrx = new CbTrx();
        $log = new UserAdmin();
        /** @var $cbtrx CbTrx */
        $cbtrx = $cbtrx->LoadById($id);
		if ($cbtrx == null || $cbtrx->IsDeleted) {
			$this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta tidak dapat ditemukan atau sudah dihapus.");
			redirect_url("cashbank.cbtrx");
		}
        if($cbtrx->TrxStatus == 0){
            $rs = $cbtrx->Delete($cbtrx->Id);
            if ($rs == 1) {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Delete CashBook Trx',$cbtrx->DocNo,'Success');
                $this->persistence->SaveState("info", sprintf("Data Transaksi: %s (%s) sudah dihapus", $cbtrx->DocNo, $cbtrx->TrxDescs));
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Delete CashBook Trx',$cbtrx->DocNo,'Failed');
                $this->persistence->SaveState("error", sprintf("Gagal menghapus Data Transaksi: %s (%s). Error: %s", $cbtrx->DocNo, $cbtrx->TrxDescs, $this->connector->GetErrorMessage()));
            }
        }else{
            $this->persistence->SaveState("error", sprintf("Maaf Data Transaksi No. %s tidak boleh dihapus karena sudah berstatus -POSTED-!",$cbtrx->DocNo));
        }
		redirect_url("cashbank.cbtrx");
	}

    public function approve(){

        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di Approve !");
            redirect_url("cashbank.cbtrx");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $cbtrx = new CbTrx();
            $log = new UserAdmin();
            /** @var $cbtrx CbTrx */
            $cbtrx = $cbtrx->LoadById($id);
            // process jurnal
            if($cbtrx->TrxStatus < 2){
                $cbtrx->UpdatedById = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $cbtrx->Approve($cbtrx->Id);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Approve CashBook Trx',$cbtrx->DocNo,'Success');
                    $infos[] = sprintf("Data Transaksi: %s (%s) sudah di-Approved", $cbtrx->DocNo, $cbtrx->TrxDescs);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Approve CashBook Trx',$cbtrx->DocNo,'Failed');
                    $errors[] = sprintf("Gagal Approve Data Transaksi: %s (%s). Error: %s", $cbtrx->DocNo, $cbtrx->TrxDescs, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Maaf Data Transaksi No. %s tidak bisa diproses karena sudah berstatus -APPROVED-!",$cbtrx->DocNo);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("cashbank.cbtrx");
    }

    public function unapprove(){
        // proses batal approval dan unposting transaksi dari jurnal akuntansi
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di Batal Approve !");
            redirect_url("cashbank.cbtrx");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $log = new UserAdmin();
            $cbtrx = new CbTrx();
            /** @var $cbtrx CbTrx */
            $cbtrx = $cbtrx->LoadById($id);
            // process jurnal
            if($cbtrx->TrxStatus == 2 && $cbtrx->CreateMode == 0){
                $cbtrx->UpdatedById = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $cbtrx->Unapprove($cbtrx->Id);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Un-Approve CashBook Trx',$cbtrx->DocNo,'Success');
                    $infos[] = sprintf("Data Transaksi: %s (%s) sudah di-Unapproved", $cbtrx->DocNo, $cbtrx->TrxDescs);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Un-Approve CashBook Trx',$cbtrx->DocNo,'Failed');
                    $errors[] = sprintf("Gagal Unapprove Data Transaksi: %s (%s). Error: %s", $cbtrx->DocNo, $cbtrx->TrxDescs, $this->connector->GetErrorMessage());
                }
            }else{
                if ($cbtrx->CreateMode == 1){
                    $errors[] = sprintf("Maaf Data Transaksi No. %s tidak bisa diproses secara manual!", $cbtrx->DocNo);
                }else {
                    $errors[] = sprintf("Maaf Data Transaksi No. %s tidak boleh diproses karena sudah berstatus -POSTED-!", $cbtrx->DocNo);
                }
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("cashbank.cbtrx");
    }

    public function approve1($id = null){
        // proses batal approval dan unposting transaksi dari jurnal akuntansi
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $cbtrx = new CbTrx();
        $log = new UserAdmin();
        /** @var $cbtrx CbTrx */
        $cbtrx = $cbtrx->LoadById($id);
        // process jurnal
        if($cbtrx->TrxStatus == 0){
            $cbtrx->UpdatedById = AclManager::GetInstance()->GetCurrentUser()->Id;
            $rs = $cbtrx->Approve($cbtrx->Id);
            if ($rs) {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Approve CashBook Trx',$cbtrx->DocNo,'Success');
                $this->persistence->SaveState("info",sprintf("Data Transaksi: %s (%s) berhasil di-Approve", $cbtrx->DocNo, $cbtrx->TrxDescs));
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Approve CashBook Trx',$cbtrx->DocNo,'Failed');
                $this->persistence->SaveState("error",sprintf("Gagal Approval Data Transaksi: %s (%s). Error: %s", $cbtrx->DocNo, $cbtrx->TrxDescs, $this->connector->GetErrorMessage()));
            }
        }else{
            $this->persistence->SaveState("error",sprintf("Maaf Data Transaksi No. %s tidak boleh di-Approve karena sudah berstatus -POSTED-!",$cbtrx->DocNo));
        }
        redirect_url("cashbank.cbtrx/view/".$id);
    }

    public function unapprove1($id = null){
        // proses batal approval dan unposting transaksi dari jurnal akuntansi
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $cbtrx = new CbTrx();
        $log = new UserAdmin();
        /** @var $cbtrx CbTrx */
        $cbtrx = $cbtrx->LoadById($id);
        // process jurnal
        if($cbtrx->TrxStatus == 1){
            $cbtrx->UpdatedById = AclManager::GetInstance()->GetCurrentUser()->Id;
            $rs = $cbtrx->Unapprove($cbtrx->Id);
            if ($rs) {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Un-Approve CashBook Trx',$cbtrx->DocNo,'Success');
                $this->persistence->SaveState("info",sprintf("Data Transaksi: %s (%s) sudah di-Unapproved", $cbtrx->DocNo, $cbtrx->TrxDescs));
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'cashbank.cbtrx','Un-Approve CashBook Trx',$cbtrx->DocNo,'Failed');
                $this->persistence->SaveState("error",sprintf("Gagal Unapprove Data Transaksi: %s (%s). Error: %s", $cbtrx->DocNo, $cbtrx->TrxDescs, $this->connector->GetErrorMessage()));
            }
        }else{
            $this->persistence->SaveState("error",sprintf("Maaf Data Transaksi No. %s tidak boleh di-UnApprove karena sudah berstatus -DRAFT-!",$cbtrx->DocNo));
        }
        redirect_url("cashbank.cbtrx/view/".$id);
    }

    public function cetakpdf($id = null){
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih Data Transaksi terlebih dahulu sebelum mencetak bukti.");
            redirect_url("cashbank.cbtrx");
        }
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/coadetail.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/trxtype.php");
        require_once(MODEL . "master/bank.php");
        require_once(MODEL . "master/contacts.php");
        $loader = null;
        $cbtrx = new CbTrx();
        $cbtrx = $cbtrx->LoadById($id);
        if ($cbtrx == null || $cbtrx->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("cashbank.cbtrx");
        }
        if ($cbtrx->TrxStatus == 0) {
            $this->persistence->SaveState("error", sprintf("Maaf Data Transaksi No. %s masih berstatus -DRAFT- tidak boleh dicetak!",$cbtrx->DocNo));
            redirect_url("cashbank.cbtrx");
        }
        $loader = new Cabang($cbtrx->CabangId);
        $cabang = $loader->Cabang;
        $loader = new Company($cbtrx->EntityId);
        $company_name = $loader->CompanyName;
        $loader = new TrxType($cbtrx->TrxTypeId);
        $refftypeid = $loader->RefftypeId;
        $loader = new Customer($cbtrx->ContactId);
        $customer_name = $loader->CustomerName;
        $loader = new Supplier($cbtrx->CreditorId);
        $supplier_name = $loader->Nama;
        $this->Set("cbtrx", $cbtrx);
        $this->Set("refftypeid", $refftypeid);
        $this->Set("company_name", $company_name);
        $this->Set("cabang", $cabang);
        $this->Set("customer_name", $customer_name);
        $this->Set("supplier_name", $supplier_name);
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/trxtype.php");
        require_once(MODEL . "master/coadetail.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sTrxMode = $this->GetPostValue("TrxMode");
            $sTrxTypeId = $this->GetPostValue("TrxTypeId");
            $sCoaBankId= $this->GetPostValue("CoaBankId");
            $sTrxStatus = $this->GetPostValue("TrxStatus");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            // tahun transaksi harus sama
            if (date("Y",$sStartDate) == date("Y",$sEndDate)){
                // ambil data yang diperlukan
                $cbtrx = new cbtrx();
                $reports = $cbtrx->Load4Reports($this->userCompanyId,$sCabangId,$sTrxTypeId,$sTrxMode,$sCoaBankId,$sTrxStatus,$sStartDate,$sEndDate);
            }else{
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("cashbank.cbtrx/report");
            }
        }else{
            $sCabangId = 0;
            $sTrxMode = 0;
            $sCoaBankId = 0;
            $sTrxStatus = -1;
            $sTrxTypeId = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            $sEndDate = time();
            $sOutput = 0;
            $reports = null;
        }
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new TrxType();
        $trxTypes = $loader->LoadAll($this->userCompanyId);
        $loader = new CoaDetail();
        $coaBanks = $loader->LoadCashBookAccount($this->userCompanyId);
        //load data cabang
        $loader = new Cabang();
        $cabangs = $loader->LoadByEntityId($this->userCompanyId);
        // kirim ke view
        $this->Set("Cabangs",$cabangs);
        $this->Set("CoaBanks",$coaBanks);
        $this->Set("TrxTypes",$trxTypes);
        $this->Set("CabangId",$sCabangId);
        $this->Set("TrxTypeId",$sTrxTypeId);
        $this->Set("TrxMode",$sTrxMode);
        $this->Set("CoaBankId",$sCoaBankId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("TrxStatus",$sTrxStatus);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
    }

    public function rekoran(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/coadetail.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sCoaBankId= $this->GetPostValue("CoaBankId");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            // tahun transaksi harus sama
            if (date("Y",$sStartDate) == date("Y",$sEndDate)){
                // ambil data yang diperlukan
                $cbtrx = new cbtrx();
                $sSaldoAwal = $cbtrx->GetSaldoAwal($sCabangId,$sCoaBankId,$sStartDate);
                $reports = $cbtrx->LoadRekoran($sCabangId,$sCoaBankId,$sStartDate,$sEndDate);
            }else{
                $reports = null;
                $this->persistence->SaveState("error", "Maaf Data Transaksi yang diminta harus dari tahun yang sama.");
                redirect_url("cashbank.cbtrx/rekoran");
            }
        }else{
            $sCabangId = 0;
            $sCoaBankId = 1;
            $sSaldoAwal = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            $sEndDate = time();
            $sOutput = 0;
            $reports = null;
        }
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new CoaDetail();
        $coaBanks = $loader->LoadCashBookAccount();
        //load data cabang
        $loader = new Cabang();
        $cabangs = $loader->LoadByEntityId($this->userCompanyId);
        //load data kas/bank
        $loader = new CoaDetail($sCoaBankId);
        $sBankName = $loader->Perkiraan;
        $sBankKode = $loader->Kode;
        // kirim ke view
        $this->Set("Cabangs",$cabangs);
        $this->Set("CoaBanks",$coaBanks);
        $this->Set("CabangId",$sCabangId);
        $this->Set("CoaBankId",$sCoaBankId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Output",$sOutput);
        $this->Set("BankName",$sBankName);
        $this->Set("BankKode",$sBankKode);
        $this->Set("SaldoAwal",$sSaldoAwal);
        $this->Set("Reports",$reports);
    }


}

// End of file: cbtrx_controller.php
