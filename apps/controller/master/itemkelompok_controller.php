<?php
class ItemKelompokController extends AppController {
	private $userUid;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/itemkelompok.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.kelompok", "display" => "Kelompok", "width" => 100);
		$settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 300);

		$settings["filters"][] = array("name" => "a.kelompok", "display" => "Kelompok");
		$settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Kelompok Barang";

			if ($acl->CheckUserAccess("master.itemkelompok", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.itemkelompok/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.itemkelompok", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.itemkelompok/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih itemkelompok terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu itemkelompok.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.itemkelompok", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.itemkelompok/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih itemkelompok terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu itemkelompok.",
					"Confirm" => "Apakah anda mau menghapus data itemkelompok yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 2;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "m_barang_kelompok AS a";
            $settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(ItemKelompok $itemgroup) {

		return true;
	}

	public function add() {
        $log = new UserAdmin();
        $itemgroup = new ItemKelompok();
        if (count($this->postData) > 0) {
            $itemgroup->Kelompok = $this->GetPostValue("Kelompok");
            $itemgroup->Keterangan = $this->GetPostValue("Keterangan");
            if ($this->ValidateData($itemgroup)) {
                $itemgroup->CreatebyId = $this->userUid;
                $rs = $itemgroup->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemkelompok','Add New Item Kelompok -> Jenis: '.$itemgroup->Kelompok.' - '.$itemgroup->Keterangan,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Data Kelompok: %s (%s) sudah berhasil disimpan", $itemgroup->Keterangan, $itemgroup->Kelompok));
                    redirect_url("master.itemkelompok");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemkelompok','Add New Item Kelompok -> Jenis: '.$itemgroup->Kelompok.' - '.$itemgroup->Keterangan,'-','Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $this->Set("itemkelompok", $itemgroup);
	}

	public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.itemkelompok");
        }
        $log = new UserAdmin();
        $itemgroup = new ItemKelompok();
        if (count($this->postData) > 0) {
            $itemgroup->Id = $id;
            $itemgroup->Kelompok = $this->GetPostValue("Kelompok");
            $itemgroup->Keterangan = $this->GetPostValue("Keterangan");
            if ($this->ValidateData($itemgroup)) {
                $itemgroup->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $itemgroup->Update($id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemkelompok','Update Item Kelompok -> Jenis: '.$itemgroup->Kelompok.' - '.$itemgroup->Keterangan,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data satuan: %s (%s) sudah berhasil disimpan", $itemgroup->Keterangan, $itemgroup->Kelompok));
                    redirect_url("master.itemkelompok");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemkelompok','Update Item Kelompok -> Jenis: '.$itemgroup->Kelompok.' - '.$itemgroup->Keterangan,'-','Failed');
                    $this->Set("error", "Gagal pada saat merubah data satuan. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $itemgroup = $itemgroup->LoadById($id);
            if ($itemgroup == null || $itemgroup->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.itemkelompok");
            }
        }
        $this->Set("itemkelompok", $itemgroup);
	}

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.itemkelompok");
        }
        $log = new UserAdmin();
        $itemgroup = new ItemKelompok();
        $itemgroup = $itemgroup->LoadById($id);
        if ($itemgroup == null || $itemgroup->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.itemkelompok");
        }
        $rs = $itemgroup->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemkelompok','Delete Item Kelompok -> Jenis: '.$itemgroup->Kelompok.' - '.$itemgroup->Keterangan,'-','Success');
            $this->persistence->SaveState("info", sprintf("Kelompok Barang: %s (%s) sudah dihapus", $itemgroup->Keterangan, $itemgroup->Kelompok));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemkelompok','Delete Item Kelompok -> Jenis: '.$itemgroup->Kelompok.' - '.$itemgroup->Keterangan,'-','Failed');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus jenis kontak: %s (%s). Error: %s", $itemgroup->Keterangan, $itemgroup->Kelompok, $this->connector->GetErrorMessage()));
        }
		redirect_url("master.itemkelompok");
	}
}

// End of file: itemkelompok_controller.php
