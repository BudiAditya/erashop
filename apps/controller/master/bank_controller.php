<?php

class BankController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userLevel;

	protected function Initialize() {
		require_once(MODEL . "master/bank.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		//$settings["columns"][] = array("name" => "a.kode", "display" => "Cabang", "width" => 100);
		$settings["columns"][] = array("name" => "a.bank_name", "display" => "Nama Kas/Bank", "width" => 150);
		$settings["columns"][] = array("name" => "a.branch", "display" => "Bank Cabang", "width" => 100);
		$settings["columns"][] = array("name" => "a.rek_no", "display" => "No. Rekening", "width" => 100);
		$settings["columns"][] = array("name" => "a.currency_cd", "display" => "Mata Uang", "width" => 60);
		$settings["columns"][] = array("name" => "a.kode_akun_control", "display" => "No. Akun Kontrol", "width" => 100);
		$settings["columns"][] = array("name" => "a.kode_akun_biaya", "display" => "No. Akun Biaya", "width" => 100);
		$settings["columns"][] = array("name" => "a.kode_akun_pendapatan", "display" => "No. Akun Pendapatan", "width" => 100);

		$settings["filters"][] = array("name" => "a.bank_name", "display" => "Bank");
		$settings["filters"][] = array("name" => "a.branch", "display" => "Cabang");
		$settings["filters"][] = array("name" => "a.rek_no", "display" => "No. Rekening");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Bank";

			if ($acl->CheckUserAccess("master.bank", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.bank/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.bank", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.bank/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih kas/bank terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu kas/bank.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.bank", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.bank/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih kas/bank terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu kas/bank.",
					"Confirm" => "Apakah anda mau menghapus data kas/bank yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 2;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "vw_bank_account AS a";
			$settings["where"] = "a.is_deleted = 0 AND a.cabang_id = " . $this->userCabangId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(Bank $bank) {
		if ($bank->Name == null) {
			$this->Set("error", "Mohon memasukkan nama kas/bank terlebih dahulu.");
			return false;
		}
		if ($bank->CurrencyCode == null) {
			$this->Set("error", "Mohon memasukkan mata uang rekening kas/bank terlebih dahulu.");
			return false;
		}
		if ($bank->AccNo == null) {
			$this->Set("error", "Mohon memilih akun kontrol terlebih dahulu.");
			return false;
		}

		if ($bank->CostAccNo == "") {
			$bank->CostAccNo = null;
		}
		if ($bank->RevAccNo == "") {
			$bank->RevAccNo = null;
		}

		return true;
	}

	public function add() {
		require_once(MODEL . "master/company.php");
		require_once(MODEL . "master/coadetail.php");
		require_once(MODEL . "master/cabang.php");
		$bank = new Bank();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			$bank->Name = $this->GetPostValue("Name");
			$bank->Branch = $this->GetPostValue("Branch");
			$bank->Address = $this->GetPostValue("Address");
			$bank->NoRekening = $this->GetPostValue("NoRek");
			$bank->CurrencyCode = $this->GetPostValue("CurrencyCode");
			$bank->AccNo = $this->GetPostValue("AccNo");
			$bank->CostAccNo = $this->GetPostValue("CostAccNo");
			$bank->RevAccNo = $this->GetPostValue("RevAccNo");
			if ($this->ValidateData($bank)) {
				$bank->EntityId = $this->userCompanyId;
				$bank->CabangId = $this->userCabangId;
				$bank->CreateById = AclManager::GetInstance()->GetCurrentUser()->Id;
				$rs = $bank->Insert();
				if ($rs == 1) {
					$log = $log->UserActivityWriter($this->userCabangId,'master.bank','Add New Kas/Bank - Name: '.$bank->Name,'-','Success');
					$this->persistence->SaveState("info", sprintf("Data kas/bank: %s (%s) sudah berhasil disimpan", $bank->Name, $bank->Branch));
					redirect_url("master.bank");
				} else {
					$log = $log->UserActivityWriter($this->userCabangId,'master.bank','Add New Kas/Bank - Name: '.$bank->Name,'-','Failed');
					$this->Set("error", "Gagal pada saat menyimpan data kas/bank. Message: " . $this->connector->GetErrorMessage());
				}
			}
		}
		$cabang = new Cabang();
		$cabang = $cabang->LoadById($this->userCabangId);
		$cabCode = $cabang->Kode;
		$accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId);
		$this->Set("cabCode", $cabCode);
		$this->Set("bank", $bank);
		$this->Set("accounts", $accounts);
	}

	public function edit($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih kas/bank terlebih dahulu sebelum melakukan proses edit.");
			redirect_url("master.bank");
		}
		require_once(MODEL . "master/company.php");
		require_once(MODEL . "master/coadetail.php");
		require_once(MODEL . "master/cabang.php");
		$bank = new Bank();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			$bank->Id = $id;
			$bank->Name = $this->GetPostValue("Name");
			$bank->Branch = $this->GetPostValue("Branch");
			$bank->Address = $this->GetPostValue("Address");
			$bank->NoRekening = $this->GetPostValue("NoRek");
			$bank->CurrencyCode = $this->GetPostValue("CurrencyCode");
			$bank->AccNo = $this->GetPostValue("AccNo");
			$bank->CostAccNo = $this->GetPostValue("CostAccNo");
			$bank->RevAccNo = $this->GetPostValue("RevAccNo");
			if ($this->ValidateData($bank)) {
				$bank->EntityId = $this->userCompanyId;
				$bank->CabangId = $this->userCabangId;
				$bank->UpdateById = AclManager::GetInstance()->GetCurrentUser()->Id;
				$rs = $bank->Update($bank->Id);
				if ($rs == 1) {
					$log = $log->UserActivityWriter($this->userCabangId,'master.bank','Update Kas/Bank - Name: '.$bank->Name,'-','Success');
					$this->persistence->SaveState("info", sprintf("Perubahan data kas/bank: %s (%s) sudah berhasil disimpan", $bank->Name, $bank->Branch));
					redirect_url("master.bank");
				} else {
					$log = $log->UserActivityWriter($this->userCabangId,'master.bank','Update Kas/Bank - Name: '.$bank->Name,'-','Failed');
					$this->Set("error", "Gagal pada saat merubah data kas/bank. Message: " . $this->connector->GetErrorMessage());
				}
			}
		} else {
			$bank = $bank->LoadById($id);
			if ($bank == null || $bank->IsDeleted) {
				$this->persistence->SaveState("error", "Maaf kas/bank yang diminta tidak dapat ditemukan atau sudah dihapus.");
				redirect_url("master.bank");
			}
		}
		$cabang = new Cabang();
		$cabang = $cabang->LoadById($this->userCabangId);
		$cabCode = $cabang->Kode;
		$accounts = new CoaDetail();
		$accounts = $accounts->LoadAll($this->userCompanyId);
		$this->Set("cabCode", $cabCode);
		$this->Set("bank", $bank);
		$this->Set("accounts", $accounts);
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Marap memilih kas/bank terlebih dahulu sebelum melakukan proses penghapusan data.");
			redirect_url("master.bank");
		}
		$log = new UserAdmin();
		$bank = new Bank();
		$bank = $bank->LoadById($id);
		if ($bank == null || $bank->IsDeleted) {
			$this->persistence->SaveState("error", "Maaf kas/bank yang diminta tidak dapat ditemukan atau sudah dihapus.");
			redirect_url("master.bank");
		}
		$rs = $bank->Delete($bank->Id);
		if ($rs == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.bank','Delete Kas/Bank - Name: '.$bank->Name,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Kas/Bank: %s (%s) sudah berhasil dihapus", $bank->Name, $bank->Branch));
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.bank','Delete Kas/Bank - Name: '.$bank->Name,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data kas/bank: %s (%s). Error: %s", $bank->Name, $bank->Branch, $this->connector->GetErrorMessage()));
		}

		redirect_url("master.bank");
	}
}

// End of file: bank_controller.php
