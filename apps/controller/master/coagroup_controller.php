<?php
class CoaGroupController extends AppController {
	private $userCabangId;
	protected function Initialize() {
		require_once(MODEL . "master/coagroup.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.kd_induk", "display" => "Kode", "width" => 50);
		$settings["columns"][] = array("name" => "a.kategori", "display" => "Kategori Akun", "width" => 250);
        $settings["columns"][] = array("name" => "if(a.psaldo = 'DK','DEBET','KREDIT')", "display" => "Posisi Saldo", "width" => 100);
        $settings["columns"][] = array("name" => "a.nm_kelompok", "display" => "Kelompok", "width" => 150);
        $settings["columns"][] = array("name" => "a.nm_laporan", "display" => "Laporan", "width" => 100);

		$settings["filters"][] = array("name" => "a.kd_induk", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.kategori", "display" => "Kategori");
        $settings["filters"][] = array("name" => "a.nm_kelompok", "display" => "Kelompok");
        $settings["filters"][] = array("name" => "a.nm_laporan", "display" => "Laporan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Header/Kategori Akun";

			if ($acl->CheckUserAccess("coagroup", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.coagroup/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("coagroup", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.coagroup/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											 "Error" => "Maaf mohon memilih Kode Akun terlebih dahulu sebelum proses edit !\nPERHATIAN: Mohon memilih tepat 1 CoA.",
											 "Confirm" => "Apakah anda yakin mau mengedit kode akun yang dipilih ?");
			}
			if ($acl->CheckUserAccess("coagroup", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.coagroup/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											 "Error" => "Maaf mohon memilih kode akun terlebih dahulu sebelum proses hapus !\nPERHATIAN: Mohon memilih tepat 1 kode akun",
											 "Confirm" => "Apakah anda yakin mau menghapus kode akun yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "vw_coa_group AS a";
			//$settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
        require_once(MODEL . "master/coaheader.php");
		$coagroup = new CoaGroup();
		$log = new UserAdmin();
        $loader = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$coagroup->KdInduk = $this->GetPostValue("KdInduk");
			$coagroup->Kategori = $this->GetPostValue("Kategori");
			$coagroup->KdKelompok = $this->GetPostValue("KdKelompok");
            $coagroup->PSaldo = $this->GetPostValue("PSaldo");
            if ($coagroup->PSaldo == "DK"){
                $coagroup->Tk = 'T';
            }else{
                $coagroup->Tk = 'K';
            }
            $coagroup->MRekap = '3 Digit';
			$coagroup->UpdateById = AclManager::GetInstance()->GetCurrentUser()->Id;

			if ($this->DoInsert($coagroup)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.coagroup','Add New Header COA -> Kode: '.$coagroup->KdInduk.' - '.$coagroup->Kategori,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Header Akun: '%s' Dengan Kode: %s telah berhasil disimpan.", $coagroup->Kategori, $coagroup->KdInduk));
				redirect_url("master.coagroup");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.coagroup','Add New Header COA -> Kode: '.$coagroup->KdInduk.' - '.$coagroup->Kategori,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $coagroup->KdInduk));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
        $loader = new CoaHeader();
        $coaheader = $loader->LoadAll();
        $this->Set("coaheader", $coaheader);
		$this->Set("coagroup", $coagroup);
	}

	private function DoInsert(CoaGroup $coagroup) {
		if ($coagroup->KdInduk == "") {
			$this->Set("error", "Kode Induk masih kosong");
			return false;
		}
		if (strlen($coagroup->Kategori) == "") {
			$this->Set("error", "Nama Kategori");
			return false;
		}
		if ($coagroup->KdKelompok == "") {
			$this->Set("error", "Kode Kelompok masih kosong");
			return false;
		}
		if ($coagroup->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
        require_once(MODEL . "master/coaheader.php");
        $coagroup = new CoaGroup();
		$log = new UserAdmin();
        $loader = null;
        if (count($this->postData) > 0) {
            // OK user ada kirim data kita proses
            $coagroup->Id = $this->GetPostValue("Id");
            $coagroup->KdInduk = $this->GetPostValue("KdInduk");
            $coagroup->Kategori = $this->GetPostValue("Kategori");
            $coagroup->KdKelompok = $this->GetPostValue("KdKelompok");
            $coagroup->PSaldo = $this->GetPostValue("PSaldo");
            if ($coagroup->PSaldo == "DK"){
                $coagroup->Tk = 'T';
            }else{
                $coagroup->Tk = 'K';
            }
            $coagroup->MRekap = '3 Digit';
            $coagroup->UpdateById = AclManager::GetInstance()->GetCurrentUser()->Id;

            if ($this->DoUpdate($coagroup)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.coagroup','Update Header COA -> Kode: '.$coagroup->KdInduk.' - '.$coagroup->Kategori,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Kategori Akun: '%s' Dengan Kode: %s telah berhasil diubah.", $coagroup->Kategori, $coagroup->KdInduk));
                redirect_url("master.coagroup");
            } else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.coagroup','Update Header COA -> Kode: '.$coagroup->KdInduk.' - '.$coagroup->Kategori,'-','Failed');
                if ($this->connector->GetHasError()) {
                    $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                }
            }
        } else {
            if ($id == null) {
                $this->persistence->SaveState("error", "Anda harus memilih data ketgori akun sebelum melakukan edit data !");
                redirect_url("master.coagroup");
            }
            $coagroup = $coagroup->FindById($id);
            if ($coagroup == null) {
                $this->persistence->SaveState("error", "Kode Kategori yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                redirect_url("master.coagroup");
            }
        }
        $loader = new CoaHeader();
        $coaheader = $loader->LoadAll();
        $this->Set("coaheader", $coaheader);
        $this->Set("coagroup", $coagroup);
	}

	private function DoUpdate(CoaGroup $coagroup) {
        if ($coagroup->KdInduk == "") {
            $this->Set("error", "Kode Induk masih kosong");
            return false;
        }
        if (strlen($coagroup->Kategori) == "") {
            $this->Set("error", "Nama Kategori");
            return false;
        }
        if ($coagroup->KdKelompok == "") {
            $this->Set("error", "Kode Kelompok masih kosong");
            return false;
        }

        if ($coagroup->Update($coagroup->Id) == 1) {
            return true;
        } else {
            return false;
        }
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
			redirect_url("master.coagroup");
		}
		$log = new UserAdmin();
		$coagroup = new CoaGroup();
		$coagroup = $coagroup->FindById($id);
		if ($coagroup == null) {
			$this->persistence->SaveState("error", "Data Kategori Akun yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.coagroup");
		}
        // periksa dulu apakah kategori sudah terpakai?
        if ($coagroup->CheckKategoriUsed($coagroup->KdInduk) == 1){
            $this->persistence->SaveState("error", sprintf("Data Kategori Akun: '%s' Dengan Kode: %s sudah terpakai. Tidak boleh dihapus.", $coagroup->Kategori, $coagroup->KdInduk));
            redirect_url("master.coagroup");
        }
		if ($coagroup->Delete($coagroup->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.coagroup','Delete Header COA -> Kode: '.$coagroup->KdInduk.' - '.$coagroup->Kategori,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Kategori Akun: '%s' Dengan Kode: %s telah berhasil dihapus.", $coagroup->Kategori, $coagroup->KdInduk));
			redirect_url("master.coagroup");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.coagroup','Delete Header COA -> Kode: '.$coagroup->KdInduk.' - '.$coagroup->Kategori,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus Data Kategori Akun: '%s'. Message: %s", $coagroup->Kategori, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.coagroup");
	}
}
