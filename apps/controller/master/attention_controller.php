<?php
class AttentionController extends AppController {
	private $userCabangId;
	protected function Initialize() {
		require_once(MODEL . "master/attention.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();
		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "if(a.att_jenis = 1,'Normal Text','Running Text')", "display" => "Type", "width" => 100);
		$settings["columns"][] = array("name" => "a.att_from", "display" => "Dari", "width" => 100);
        $settings["columns"][] = array("name" => "a.att_header", "display" => "Header", "width" => 150);
		$settings["columns"][] = array("name" => "a.att_content", "display" => "Isi Pegumuman", "width" => 450);
        $settings["columns"][] = array("name" => "if(a.is_aktif = 0,'Non Aktif','Aktif')", "display" => "Status", "width" => 80);

		$settings["filters"][] = array("name" => "a.att_from", "display" => "Dari");
		$settings["filters"][] = array("name" => "a.att_header", "display" => "Header");
        $settings["filters"][] = array("name" => "a.att_content", "display" => "Content");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Informasi Pengumuman";

			if ($acl->CheckUserAccess("attention", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.attention/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("attention", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.attention/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau merubah data pengumunan yang dipilih ?");
			}
			if ($acl->CheckUserAccess("attention", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.attention/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu data",
											   "Info" => "Apakah anda yakin mau menghapus data pengumunan yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "sys_attention AS a";
			$settings["where"] = "a.is_aktif = 1";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
        require_once(MODEL . "master/user_admin.php");
        $acl = AclManager::GetInstance(); //load class acl untuk session user id
        $uid = $acl->CurrentUser->Id;
		$loader = null;
		$log = new UserAdmin();
		$attention = new Attention();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$attention->IsAktif = 1;
			$attention->AttJenis = 1;
			$attention->AttFrom = $this->GetPostValue("AttFrom");
            $attention->AttHeader = $this->GetPostValue("AttHeader");
            $attention->AttContent = $this->GetPostValue("AttContent");

			if ($this->DoInsert($attention)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.attention','Add New Attention - From: '.$attention->AttFrom.' - '.$attention->AttHeader,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Pengumuman: '%s' Dengan Header: '%s' telah berhasil disimpan.", $attention->AttFrom, $attention->AttHeader));
				redirect_url("master.attention");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.attention','Add New Attention - From: '.$attention->AttFrom.' - '.$attention->AttHeader,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Data Pengumuman: '%s' telah ada pada database !", $attention->AttHeader));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		// load data company for combo box
		$loader = new UserAdmin();
		$userinfo = $loader->FindById($uid);
		// untuk kirim variable ke view
		$this->Set("attention", $attention);
        $this->Set("username", $userinfo->UserName);
	}

	private function DoInsert(Attention $attention) {
		if ($attention->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once(MODEL . "master/user_admin.php");
        $acl = AclManager::GetInstance(); //load class acl untuk session user id
        $uid = $acl->CurrentUser->Id;
        $loader = null;
		$log = new UserAdmin();
        $attention = new Attention();
        if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$attention->Id = $this->GetPostValue("Id");
            $attention->IsAktif = 1;
            $attention->AttJenis = 1;
            $attention->AttFrom = $this->GetPostValue("AttFrom");
            $attention->AttHeader = $this->GetPostValue("AttHeader");
            $attention->AttContent = $this->GetPostValue("AttContent");

			if ($this->DoUpdate($attention)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.attention','Update Attention - From: '.$attention->AttFrom.' - '.$attention->AttHeader,'-','Success');
				$this->persistence->SaveState("info", sprintf("Pengumuman Dari: '%s' Dengan Header: '%s' telah berhasil diupdate.", $attention->AttFrom, $attention->AttHeader));
				redirect_url("master.attention");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.attention','Update Attention - From: '.$attention->AttFrom.' - '.$attention->AttHeader,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Header: '%s' telah ada pada database !", $attention->AttHeader));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data pengumuman sebelum melakukan edit data !");
				redirect_url("master.attention");
			}
			$attention = $attention->FindById($id);
			if ($attention == null) {
				$this->persistence->SaveState("error", "Data Pengumuman yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.attention");
			}
		}
        // load data company for combo box
        $loader = new UserAdmin();
        $userinfo = $loader->FindById($uid);
        // untuk kirim variable ke view
        $this->Set("attention", $attention);
        $this->Set("username", $userinfo->UserName);
	}

	private function DoUpdate(Attention $attention) {

		if ($attention->Update($attention->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data attention sebelum melakukan hapus data !");
			redirect_url("master.attention");
		}
		$log = new UserAdmin();
		$attention = new Attention();
		$attention = $attention->FindById($id);
		if ($attention == null) {
			$this->persistence->SaveState("error", "Data Pengumuman yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.attention");
		}

		if ($attention->Delete($attention->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.attention','Delete Attention - From: '.$attention->AttFrom.' - '.$attention->AttHeader,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Pengumuman dari: '%s' Dengan Header: '%s' telah berhasil dihapus.", $attention->AttFrom, $attention->AttHeader));
			redirect_url("master.attention");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.attention','Delete Attention - From: '.$attention->AttFrom.' - '.$attention->AttHeader,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data attention: '%s'. Message: %s", $attention->AttFrom, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.attention");
	}
}
