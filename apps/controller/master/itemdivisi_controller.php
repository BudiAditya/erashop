<?php
class ItemDivisiController extends AppController {
	private $userUid;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/itemdivisi.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.divisi", "display" => "Divisi", "width" => 100);
		$settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 300);

		$settings["filters"][] = array("name" => "a.divisi", "display" => "Divisi");
		$settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Divisi Barang";

			if ($acl->CheckUserAccess("master.itemdivisi", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.itemdivisi/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.itemdivisi", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.itemdivisi/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih itemdivisi terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu itemdivisi.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.itemdivisi", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.itemdivisi/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih itemdivisi terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu itemdivisi.",
					"Confirm" => "Apakah anda mau menghapus data itemdivisi yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 2;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "m_barang_divisi AS a";
            $settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(ItemDivisi $itemdivisi) {

		return true;
	}

	public function add() {
        $itemdivisi = new ItemDivisi();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $itemdivisi->Divisi = $this->GetPostValue("Divisi");
            $itemdivisi->Keterangan = $this->GetPostValue("Keterangan");
            if ($this->ValidateData($itemdivisi)) {
                $itemdivisi->CreatebyId = $this->userUid;
                $rs = $itemdivisi->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemdivisi','Add New Item Divisi -> Divisi: '.$itemdivisi->Divisi.' - '.$itemdivisi->Keterangan,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Data Divisi: %s (%s) sudah berhasil disimpan", $itemdivisi->Keterangan, $itemdivisi->Divisi));
                    redirect_url("master.itemdivisi");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemdivisi','Add New Item Divisi -> Divisi: '.$itemdivisi->Divisi.' - '.$itemdivisi->Keterangan,'-','Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $this->Set("itemdivisi", $itemdivisi);
	}

	public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.itemdivisi");
        }
        $log = new UserAdmin();
        $itemdivisi = new ItemDivisi();
        if (count($this->postData) > 0) {
            $itemdivisi->Id = $id;
            $itemdivisi->Divisi = $this->GetPostValue("Divisi");
            $itemdivisi->Keterangan = $this->GetPostValue("Keterangan");
            if ($this->ValidateData($itemdivisi)) {
                $itemdivisi->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $itemdivisi->Update($id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemdivisi','Update Item Divisi -> Divisi: '.$itemdivisi->Divisi.' - '.$itemdivisi->Keterangan,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data satuan: %s (%s) sudah berhasil disimpan", $itemdivisi->Keterangan, $itemdivisi->Divisi));
                    redirect_url("master.itemdivisi");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemdivisi','Update Item Divisi -> Divisi: '.$itemdivisi->Divisi.' - '.$itemdivisi->Keterangan,'-','Failed');
                    $this->Set("error", "Gagal pada saat merubah data satuan. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $itemdivisi = $itemdivisi->LoadById($id);
            if ($itemdivisi == null || $itemdivisi->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.itemdivisi");
            }
        }
        $this->Set("itemdivisi", $itemdivisi);
	}

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.itemdivisi");
        }
        $log = new UserAdmin();
        $itemdivisi = new ItemDivisi();
        $itemdivisi = $itemdivisi->LoadById($id);
        if ($itemdivisi == null || $itemdivisi->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.itemdivisi");
        }
        $rs = $itemdivisi->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemdivisi','Delete Item Divisi -> Divisi: '.$itemdivisi->Divisi.' - '.$itemdivisi->Keterangan,'-','Success');
            $this->persistence->SaveState("info", sprintf("Divisi Barang: %s (%s) sudah dihapus", $itemdivisi->Keterangan, $itemdivisi->Divisi));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemdivisi','Delete Item Divisi -> Divisi: '.$itemdivisi->Divisi.' - '.$itemdivisi->Keterangan,'-','Failed');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus jenis kontak: %s (%s). Error: %s", $itemdivisi->Keterangan, $itemdivisi->Divisi, $this->connector->GetErrorMessage()));
        }
		redirect_url("master.itemdivisi");
	}
}

// End of file: itemdivisi_controller.php
