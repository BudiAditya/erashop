<?php
class ItemJenisController extends AppController {
	private $userUid;
    private $userCabangId;
    private $userCompanyId;

	protected function Initialize() {
		require_once(MODEL . "master/itemjenis.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.jenis", "display" => "Jenis Barang", "width" => 100);
		$settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 200);
        $settings["columns"][] = array("name" => "a.ivt_acc_no", "display" => "Kode Akun", "width" => 70);
        $settings["columns"][] = array("name" => "b.perkiraan", "display" => "Akun Persediaan", "width" => 200);

		$settings["filters"][] = array("name" => "a.jenis", "display" => "Jenis Barang");
		$settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Jenis Barang";

			if ($acl->CheckUserAccess("master.itemjenis", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.itemjenis/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.itemjenis", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.itemjenis/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih itemjenis terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu itemjenis.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.itemjenis", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.itemjenis/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih itemjenis terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu itemjenis.",
					"Confirm" => "Apakah anda mau menghapus data itemjenis yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 1;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "m_barang_jenis AS a Left Join m_account b On a.ivt_acc_no = b.kode And b.entity_id = ".$this->userCompanyId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(ItemJenis $itemjenis) {

		return true;
	}

	public function add() {
        require_once(MODEL . "master/coadetail.php");
        $itemjenis = new ItemJenis();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $itemjenis->JnsBarang = $this->GetPostValue("JnsBarang");
            $itemjenis->Keterangan = $this->GetPostValue("Keterangan");
            $itemjenis->IvtAccNo = $this->GetPostValue("IvtAccNo");
            if ($this->ValidateData($itemjenis)) {
                $itemjenis->CreatebyId = $this->userUid;
                $rs = $itemjenis->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemjenis','Add New Item Jenis -> Jenis: '.$itemjenis->JnsBarang.' - '.$itemjenis->Keterangan,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Data Jenis: %s (%s) sudah berhasil disimpan", $itemjenis->Keterangan, $itemjenis->JnsBarang));
                    redirect_url("master.itemjenis");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemjenis','Add New Item Jenis -> Jenis: '.$itemjenis->JnsBarang.' - '.$itemjenis->Keterangan,'-','Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $loader = new CoaDetail();
        $ivtCoa = $loader->LoadAll($this->userCompanyId);
        $this->Set("ivtcoa", $ivtCoa);
        $this->Set("itemjenis", $itemjenis);
	}

	public function edit($id = null) {
        require_once(MODEL . "master/coadetail.php");
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.itemjenis");
        }
        $log = new UserAdmin();
        $itemjenis = new ItemJenis();
        if (count($this->postData) > 0) {
            $itemjenis->Id = $id;
            $itemjenis->JnsBarang = $this->GetPostValue("JnsBarang");
            $itemjenis->Keterangan = $this->GetPostValue("Keterangan");
            $itemjenis->IvtAccNo = $this->GetPostValue("IvtAccNo");
            if ($this->ValidateData($itemjenis)) {
                $itemjenis->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $itemjenis->Update($id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemjenis','Update Item Jenis -> Jenis: '.$itemjenis->JnsBarang.' - '.$itemjenis->Keterangan,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data satuan: %s (%s) sudah berhasil disimpan", $itemjenis->Keterangan, $itemjenis->JnsBarang));
                    redirect_url("master.itemjenis");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemjenis','Update Item Jenis -> Jenis: '.$itemjenis->JnsBarang.' - '.$itemjenis->Keterangan,'-','Failed');
                    $this->Set("error", "Gagal pada saat merubah data satuan. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $itemjenis = $itemjenis->LoadById($id);
            if ($itemjenis == null) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.itemjenis");
            }
        }
        $loader = new CoaDetail();
        $ivtCoa = $loader->LoadAll($this->userCompanyId);
        $this->Set("ivtcoa", $ivtCoa);
        $this->Set("itemjenis", $itemjenis);
	}

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.itemjenis");
        }
        $log = new UserAdmin();
        $itemjenis = new ItemJenis();
        $itemjenis = $itemjenis->LoadById($id);
        if ($itemjenis == null) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.itemjenis");
        }
        $rs = $itemjenis->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemjenis','Delete Item Jenis -> Jenis: '.$itemjenis->JnsBarang.' - '.$itemjenis->Keterangan,'-','Success');
            $this->persistence->SaveState("info", sprintf("Jenis Barang: %s (%s) sudah dihapus", $itemjenis->Keterangan, $itemjenis->JnsBarang));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemjenis','Delete Item Jenis -> Jenis: '.$itemjenis->JnsBarang.' - '.$itemjenis->Keterangan,'-','Failed');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus jenis barang: %s (%s). Error: %s", $itemjenis->Keterangan, $itemjenis->JnsBarang, $this->connector->GetErrorMessage()));
        }
		redirect_url("master.itemjenis");
	}
}

// End of file: itemjenis_controller.php
