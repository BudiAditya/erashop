<?php
class CoaDetailController extends AppController {
	private $userCabangId;
	private $userEntityId;
	protected function Initialize() {
		require_once(MODEL . "master/coadetail.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userEntityId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		//$settings["columns"][] = array("name" => "a.cabang_kode", "display" => "Cabang", "width" => 70);
		$settings["columns"][] = array("name" => "a.kode", "display" => "Kode Akun", "width" => 50);
		$settings["columns"][] = array("name" => "a.perkiraan", "display" => "Nama Akun", "width" => 250);
        $settings["columns"][] = array("name" => "a.kd_induk", "display" => "Kode Induk", "width" => 50);
        $settings["columns"][] = array("name" => "a.kategori", "display" => "Kategori Akun", "width" => 150);
        $settings["columns"][] = array("name" => "if(a.psaldo = 'DK','DEBET','KREDIT')", "display" => "Posisi Saldo", "width" => 100);
		$settings["columns"][] = array("name" => "if(a.xmode = 0,'Semua (Pusat)','Cabang')", "display" => "Level", "width" => 100);

		$settings["filters"][] = array("name" => "a.kode", "display" => "No. Akun");
		$settings["filters"][] = array("name" => "a.perkiraan", "display" => "Nama Akun");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Kode Akun/Perkiraan (COA)";

			if ($acl->CheckUserAccess("coadetail", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.coadetail/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("coadetail", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.coadetail/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											 "Error" => "Maaf mohon memilih Kode Akun terlebih dahulu sebelum proses edit !\nPERHATIAN: Mohon memilih tepat 1 CoA.",
											 "Confirm" => "Apakah anda yakin mau mengedit kode akun yang dipilih ?");
			}
			if ($acl->CheckUserAccess("coadetail", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.coadetail/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											 "Error" => "Maaf mohon memilih kode akun terlebih dahulu sebelum proses hapus !\nPERHATIAN: Mohon memilih tepat 1 kode akun",
											 "Confirm" => "Apakah anda yakin mau menghapus kode akun yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "vw_coa_detail AS a";
			$settings["where"] = "a.entity_id = ".$this->userEntityId;
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
        require_once(MODEL . "master/coagroup.php");
		$coadetail = new CoaDetail();
		$log = new UserAdmin();
        $loader = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$coadetail->EntityId = $this->userEntityId;
			$coadetail->CabangId = $this->userCabangId;
			$coadetail->Kode = $this->GetPostValue("Kode");
			$coadetail->KdInduk = $this->GetPostValue("KdInduk");
			$coadetail->Perkiraan = $this->GetPostValue("Perkiraan");
			$coadetail->XMode = $this->GetPostValue("XMode");
			$coadetail->CreateById = AclManager::GetInstance()->GetCurrentUser()->Id;
			if ($this->DoInsert($coadetail)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.coadetail','Add New COA -> Kode: '.$coadetail->Kode.' - '.$coadetail->Perkiraan,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Akun: '%s' Dengan Kode: %s telah berhasil disimpan.", $coadetail->Perkiraan, $coadetail->Kode));
				redirect_url("master.coadetail");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.coadetail','Add New COA -> Kode: '.$coadetail->Kode.' - '.$coadetail->Perkiraan,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $coadetail->Kode));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
        $loader = new CoaGroup();
        $coagroup = $loader->LoadAll();
        $this->Set("coagroup",$coagroup);
		$this->Set("coadetail", $coadetail);
	}

	private function DoInsert(CoaDetail $coadetail) {
		if ($coadetail->Kode == "") {
			$this->Set("error", "Kode CoA masih kosong");
			return false;
		}
		if (strlen($coadetail->Perkiraan) == "") {
			$this->Set("error", "Nama Perkiraan");
			return false;
		}
		if ($coadetail->KdInduk == "") {
			$this->Set("error", "Kode Induk masih kosong");
			return false;
		}
		// Coba kita cari parentnya (Kita anggap pattern Kode sudah pasti xxx.xx.xx.xx)
		// Saat ini UI hanya mendukung entry dengan type 3 OK !
		$parentCoa = new CoaGroup();
		$parentCoa = $parentCoa->FindByKdInduk($coadetail->KdInduk);
		if ($parentCoa == null) {
			$this->Set("error", "Maaf Kode Induk salah atau tidak dapat ditemukan!");
			return false;
		}
		if ($coadetail->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once(MODEL . "master/coagroup.php");
        $coadetail = new CoaDetail();
		$log = new UserAdmin();
        $loader = null;
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $coadetail->Id = $this->GetPostValue("Id");
			$coadetail->EntityId = $this->userEntityId;
			$coadetail->CabangId = $this->userCabangId;
            $coadetail->Kode = $this->GetPostValue("Kode");
            $coadetail->KdInduk = $this->GetPostValue("KdInduk");
            $coadetail->Perkiraan = $this->GetPostValue("Perkiraan");
			$coadetail->XMode = $this->GetPostValue("XMode");
            $coadetail->UpdateById = AclManager::GetInstance()->GetCurrentUser()->Id;

            if ($this->DoUpdate($coadetail)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.coadetail','Update COA -> Kode: '.$coadetail->Kode.' - '.$coadetail->Perkiraan,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Akun: '%s' Dengan Kode: %s telah berhasil diubah.", $coadetail->Perkiraan, $coadetail->Kode));
                redirect_url("master.coadetail");
            } else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.coadetail','Update COA -> Kode: '.$coadetail->Kode.' - '.$coadetail->Perkiraan,'-','Failed');
                if ($this->connector->GetHasError()) {
                    $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                }
            }
        } else {
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data kode akun sebelum melakukan edit data !");
                redirect_url("master.coadetail");
            }
            $coadetail = $coadetail->FindById($id);
            if ($coadetail == null) {
                $this->persistence->SaveState("error", "Kode Akun yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("master.coadetail");
            }
        }
        $loader = new CoaGroup();
        $coagroup = $loader->LoadAll();
        $this->Set("coagroup",$coagroup);
        $this->Set("coadetail", $coadetail);
	}

	private function DoUpdate(CoaDetail $coadetail) {
        if ($coadetail->Kode == "") {
            $this->Set("error", "Kode CoA masih kosong");
            return false;
        }
        if (strlen($coadetail->Perkiraan) == "") {
            $this->Set("error", "Nama Perkiraan");
            return false;
        }
        if ($coadetail->KdInduk == "") {
            $this->Set("error", "Kode Induk masih kosong");
            return false;
        }
        // Coba kita cari parentnya (Kita anggap pattern Kode sudah pasti xxx.xx.xx.xx)
        // Saat ini UI hanya mendukung entry dengan type 3 OK !
        $parentCoa = new CoaGroup();
        $parentCoa = $parentCoa->FindByKdInduk($coadetail->KdInduk);
        if ($parentCoa == null) {
            $this->Set("error", "Maaf Kode Induk salah atau tidak dapat ditemukan!");
            return false;
        }
        if ($coadetail->Update($coadetail->Id) == 1) {
            return true;
        } else {
            return false;
        }
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih Akun sebelum melakukan hapus data !");
			redirect_url("master.coadetail");
		}
		$log = new UserAdmin();
        $coadetail = new CoaDetail();
        $coadetail = $coadetail->FindById($id);
		if ($coadetail == null) {
			$this->persistence->SaveState("error", "Kode Akun yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.coadetail");
		}

        // periksa apakah akun sudah terpakai belum, kalo sudah ya jangan dihapus
        if ($coadetail->CheckAkunUsed($id) == 1){
            $this->persistence->SaveState("error", sprintf(" Akun: '%s' Dengan Kode: %s sudah terpakai. Tidak boleh dihapus.", $coadetail->Perkiraan, $coadetail->Kode));
            redirect_url("master.coadetail");
        }

		if ($coadetail->Delete($coadetail->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.coadetail','Delete COA -> Kode: '.$coadetail->Kode.' - '.$coadetail->Perkiraan,'-','Success');
			$this->persistence->SaveState("info", sprintf("Akun: '%s' Dengan Kode: %s telah berhasil dihapus.", $coadetail->Perkiraan, $coadetail->Kode));
			redirect_url("master.coadetail");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.coadetail','Delete COA -> Kode: '.$coadetail->Kode.' - '.$coadetail->Perkiraan,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus Kode Akun: '%s'. Message: %s", $coadetail->Kode, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.coadetail");
	}
}
