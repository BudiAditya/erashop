<?php
class TrxTypeController extends AppController {
	private $userCompanyId;
	private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/trxtype.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.trx_descs", "display" => "Jenis Transaksi", "width" => 200);
        $settings["columns"][] = array("name" => "a.xmode", "display" => "Mode", "width" => 50);
		$settings["columns"][] = array("name" => "a.def_acc_no", "display" => "Default Akun", "width" => 70);
		$settings["columns"][] = array("name" => "a.def_acc_perkiraan", "display" => "Default Perkiraan", "width" => 150);
        $settings["columns"][] = array("name" => "a.trx_acc_no", "display" => "Kontra Akun", "width" => 70);
        $settings["columns"][] = array("name" => "a.trx_acc_perkiraan", "display" => "Kontra Perkiraan", "width" => 150);
        $settings["columns"][] = array("name" => "a.reff_type", "display" => "Refferensi", "width" => 100);

		$settings["filters"][] = array("name" => "a.xmode", "display" => "Mode");
		$settings["filters"][] = array("name" => "a.trx_descs", "display" => "Jenis Transaksi");
		$settings["filters"][] = array("name" => "a.kode", "display" => "Kontra Akun");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Jenis Transaksi";

			if ($acl->CheckUserAccess("master.trxtype", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.trxtype/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.trxtype", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.trxtype/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih trxtype terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu trxtype.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.trxtype", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.trxtype/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih trxtype terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu trxtype.",
					"Confirm" => "Apakah anda mau menghapus data trxtype yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 1;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "vw_trx_type AS a";
            $settings["where"] = "a.entity_id = ".$this->userCompanyId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(TrxType $trxtype) {
		return true;
	}

	public function add() {
		require_once(MODEL . "master/coadetail.php");
		$log = new UserAdmin();
		$trxtype = new TrxType();

		if (count($this->postData) > 0) {
			$trxtype->EntityId = $this->userCompanyId;
			$trxtype->CabangId = $this->userCabangId;
			$trxtype->TrxMode = $this->GetPostValue("TrxMode");
			$trxtype->TrxDescs = $this->GetPostValue("TrxDescs");
			$trxtype->TrxAccNo = $this->GetPostValue("TrxAccNo");
            $trxtype->DefAccNo = $this->GetPostValue("DefAccNo");
            $trxtype->RefftypeId = $this->GetPostValue("RefftypeId");
			if ($this->ValidateData($trxtype)) {
				$trxtype->CreateById = AclManager::GetInstance()->GetCurrentUser()->Id;
				$rs = $trxtype->Insert();
				if ($rs == 1) {
					$log = $log->UserActivityWriter($this->userCabangId,'master.trxtype','Add New Trx Type : '.$trxtype->TrxDescs.' ['.$trxtype->TrxMode.']','-','Success');
					$this->persistence->SaveState("info", sprintf("Data trxtype: %s (%s) sudah berhasil disimpan", $trxtype->TrxMode, $trxtype->TrxDescs));
					redirect_url("master.trxtype");
				} else {
					$log = $log->UserActivityWriter($this->userCabangId,'master.trxtype','Add New Trx Type : '.$trxtype->TrxDescs.' ['.$trxtype->TrxMode.']','-','Failed');
					$this->Set("error", "Gagal pada saat menyimpan data trxtype. Message: " . $this->connector->GetErrorMessage());
				}
			}
		}
        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId,$this->userCabangId);
		$this->Set("trxtype", $trxtype);
		$this->Set("accounts", $accounts);
	}

	public function edit($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih trxtype terlebih dahulu sebelum melakukan proses edit.");
			redirect_url("master.trxtype");
		}
		$log = new UserAdmin();
		require_once(MODEL . "master/coadetail.php");
		$trxtype = new TrxType();
		if (count($this->postData) > 0) {
			$trxtype->Id = $id;
			$trxtype->EntityId = $this->userCompanyId;
			$trxtype->CabangId = $this->userCabangId;
            $trxtype->TrxMode = $this->GetPostValue("TrxMode");
            $trxtype->TrxDescs = $this->GetPostValue("TrxDescs");
            $trxtype->TrxAccNo = $this->GetPostValue("TrxAccNo");
            $trxtype->DefAccNo = $this->GetPostValue("DefAccNo");
            $trxtype->RefftypeId = $this->GetPostValue("RefftypeId");
			if ($this->ValidateData($trxtype)) {
				$trxtype->UpdatedById = AclManager::GetInstance()->GetCurrentUser()->Id;
				$rs = $trxtype->Update($trxtype->Id);
				if ($rs == 1) {
					$log = $log->UserActivityWriter($this->userCabangId,'master.trxtype','Update Trx Type : '.$trxtype->TrxDescs.' ['.$trxtype->TrxMode.']','-','Success');
					$this->persistence->SaveState("info", sprintf("Perubahan data trxtype: %s (%s) sudah berhasil disimpan", $trxtype->TrxMode, $trxtype->TrxDescs));
					redirect_url("master.trxtype");
				} else {
					$log = $log->UserActivityWriter($this->userCabangId,'master.trxtype','Update Trx Type : '.$trxtype->TrxDescs.' ['.$trxtype->TrxMode.']','-','Failed');
					$this->Set("error", "Gagal pada saat merubah data trxtype. Message: " . $this->connector->GetErrorMessage());
				}
			}
		} else {
			$trxtype = $trxtype->LoadById($id);
			if ($trxtype == null || $trxtype->IsDeleted) {
				$this->persistence->SaveState("error", "Maaf trxtype yang diminta tidak dapat ditemukan atau sudah dihapus.");
				redirect_url("master.trxtype");
			}
		}

        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll($this->userCompanyId,$this->userCabangId);
        $this->Set("trxtype", $trxtype);
        $this->Set("accounts", $accounts);
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Marap memilih trxtype terlebih dahulu sebelum melakukan proses penghapusan data trxtype.");
			redirect_url("master.trxtype");
		}
		$log = new UserAdmin();
		$trxtype = new TrxType();
		$trxtype = $trxtype->LoadById($id);
		if ($trxtype == null || $trxtype->IsDeleted) {
			$this->persistence->SaveState("error", "Maaf trxtype yang diminta tidak dapat ditemukan atau sudah dihapus.");
			redirect_url("master.trxtype");
		}

		$rs = $trxtype->Delete($trxtype->Id);
		if ($rs == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.trxtype','Delete Trx Type : '.$trxtype->TrxDescs.' ['.$trxtype->TrxMode.']','-','Success');
			$this->persistence->SaveState("info", sprintf("Jenis Transaksi: %s (%s) sudah dihapus", $trxtype->TrxMode, $trxtype->TrxDescs));
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.trxtype','Delete Trx Type : '.$trxtype->TrxDescs.' ['.$trxtype->TrxMode.']','-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus trxtype: %s (%s). Error: %s", $trxtype->TrxMode, $trxtype->TrxDescs, $this->connector->GetErrorMessage()));
		}

		redirect_url("master.trxtype");
	}
}

// End of file: trxtype_controller.php
