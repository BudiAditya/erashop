<?php
class KaryawanController extends AppController {
	private $userCompanyId;
	private $userLevel;
	private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/karyawan.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "b.entity_cd", "display" => "Entity", "width" => 60);
		$settings["columns"][] = array("name" => "a.nik", "display" => "NIK", "width" => 60);
		$settings["columns"][] = array("name" => "a.nama", "display" => "Nama Karyawan", "width" => 150);
        $settings["columns"][] = array("name" => "a.nm_panggilan", "display" => "Nm. Panggilan", "width" => 100);
        $settings["columns"][] = array("name" => "a.alamat", "display" => "Alamat", "width" => 400);
        $settings["columns"][] = array("name" => "c.dept_cd", "display" => "Bagian", "width" => 50);
        $settings["columns"][] = array("name" => "a.jabatan", "display" => "Jabatan", "width" => 50);
        $settings["columns"][] = array("name" => "a.handphone", "display" => "Handphone", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.jkelamin = 'L','Laki-laki',if(a.jkelamin='P','Perempuan','-'))", "display" => "Gender", "width" => 100);

		$settings["filters"][] = array("name" => "a.nik", "display" => "Nik");
		$settings["filters"][] = array("name" => "a.nama", "display" => "Nama Karyawan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Data Karyawan";

			if ($acl->CheckUserAccess("karyawan", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.karyawan/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("karyawan", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.karyawan/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data karyawan terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu karyawan",
											   "Info" => "Apakah anda yakin mau merubah data karyawan yang dipilih ?");
			}
			if ($acl->CheckUserAccess("karyawan", "view", "master")) {
				$settings["actions"][] = array("Text" => "View", "Url" => "master.karyawan/view/%s", "Class" => "bt_view", "ReqId" => 1,
						"Error" => "Mohon memilih data karyawan terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu karyawan");
			}
			if ($acl->CheckUserAccess("karyawan", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.karyawan/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data karyawan terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu karyawan",
											   "Info" => "Apakah anda yakin mau menghapus data karyawan yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] =
    "m_karyawan AS a JOIN sys_company AS b ON a.entity_id = b.entity_id JOIN sys_dept As c on a.dept_id = c.id";
			if ($this->userLevel > 3) {
				$settings["where"] = "a.is_deleted = 0";
			} else {
				$settings["where"] = "a.is_deleted = 0 AND a.entity_id = " . $this->userCompanyId;
			}
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
		require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/department.php");

		$loader = null;
		$log = new UserAdmin();
		$karyawan = new Karyawan();
		$fpath = null;
		$ftmp = null;
		$fname = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$karyawan->EntityId = $this->GetPostValue("EntityId");
			$karyawan->Nik = strtoupper($this->GetPostValue("Nik"));
			$karyawan->Nama = $this->GetPostValue("Nama");
            $karyawan->NmPanggilan = $this->GetPostValue("NmPanggilan");
            $karyawan->DeptId = $this->GetPostValue("DeptId");
            $karyawan->Jabatan = $this->GetPostValue("Jabatan");
            $karyawan->MulaiKerja = strtotime($this->GetPostValue("MulaiKerja"));
            $karyawan->Agama = $this->GetPostValue("Agama");
            $karyawan->Status = $this->GetPostValue("Status");
            $karyawan->Jkelamin = $this->GetPostValue("Jkelamin");
            $karyawan->T4Lahir = $this->GetPostValue("T4Lahir");
            $karyawan->TglLahir = strtotime($this->GetPostValue("TglLahir"));
            $karyawan->Alamat = $this->GetPostValue("Alamat");
            $karyawan->Pendidikan = $this->GetPostValue("Pendidikan");
            $karyawan->FpNo = $this->GetPostValue("FpNo");
            $karyawan->TlpRumah = $this->GetPostValue("TlpRumah");
            $karyawan->Handphone = $this->GetPostValue("Handphone");
            $karyawan->Npwp = $this->GetPostValue("Npwp");
            $karyawan->BpjsNo = $this->GetPostValue("BpjsNo");
            $karyawan->BpjsDate = strtotime($this->GetPostValue("BpjsDate"));
            $karyawan->ResignDate = strtotime($this->GetPostValue("ResignDate"));
			$karyawan->Fphoto = null;
			if (!empty($_FILES['FileName']['tmp_name'])){
				$fpath = 'public/upload/images/';
				$ftmp = $_FILES['FileName']['tmp_name'];
				$fname = $_FILES['FileName']['name'];
				$fpath.= $fname;
				$karyawan->Fphoto = $fpath;
				if(!move_uploaded_file($ftmp,$fpath)){
					$this->Set("error", sprintf("Gagal Upload file photo..", $this->connector->GetErrorMessage()));
				}
			}
			if ($this->DoInsert($karyawan)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.karyawan','Add New Karyawan -> NIK: '.$karyawan->Nik.' - '.$karyawan->Nama,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Nama: '%s' Dengan Nik: %s telah berhasil disimpan.", $karyawan->Nama, $karyawan->Nik));
				redirect_url("master.karyawan");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.karyawan','Add New Karyawan -> NIK: '.$karyawan->Nik.' - '.$karyawan->Nama,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Nik: '%s' telah ada pada database !", $karyawan->Nik));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}

		// load data company for combo box
		$loader = new Company();
		if ($this->userLevel > 3) {
			$companies = $loader->LoadAll();
		} else {
			$companies = array();
			$companies[] = $loader->LoadById($this->userCompanyId);
		}
        $loader = new Department();
        $depts = $loader->LoadAll();

		// untuk kirim variable ke view
		$this->Set("karyawan", $karyawan);
		$this->Set("companies", $companies);
        $this->Set("depts", $depts);
	}

	private function DoInsert(Karyawan $karyawan) {

		if ($karyawan->EntityId == "") {
			$this->Set("error", "Nama perusahaan masih kosong");
			return false;
		}
		if ($karyawan->Nik == "") {
			$this->Set("error", "Nik karyawan masih kosong");
			return false;
		}

		if ($karyawan->Nama == "") {
			$this->Set("error", "Nama karyawan masih kosong");
			return false;
		}

		if ($karyawan->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/department.php");

		$loader = null;
		$log = new UserAdmin();
		$karyawan = new Karyawan();

		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$karyawan->Id = $this->GetPostValue("Id");
            $karyawan->EntityId = $this->GetPostValue("EntityId");
            $karyawan->Nik = strtoupper($this->GetPostValue("Nik"));
            $karyawan->Nama = $this->GetPostValue("Nama");
            $karyawan->NmPanggilan = $this->GetPostValue("NmPanggilan");
            $karyawan->DeptId = $this->GetPostValue("DeptId");
            $karyawan->Jabatan = $this->GetPostValue("Jabatan");
            $karyawan->MulaiKerja = strtotime($this->GetPostValue("MulaiKerja"));
            $karyawan->Agama = $this->GetPostValue("Agama");
            $karyawan->Status = $this->GetPostValue("Status");
            $karyawan->Jkelamin = $this->GetPostValue("Jkelamin");
            $karyawan->T4Lahir = $this->GetPostValue("T4Lahir");
            $karyawan->TglLahir = strtotime($this->GetPostValue("TglLahir"));
            $karyawan->Alamat = $this->GetPostValue("Alamat");
            $karyawan->Pendidikan = $this->GetPostValue("Pendidikan");
            $karyawan->FpNo = $this->GetPostValue("FpNo");
            $karyawan->TlpRumah = $this->GetPostValue("TlpRumah");
            $karyawan->Handphone = $this->GetPostValue("Handphone");
            $karyawan->Npwp = $this->GetPostValue("Npwp");
            $karyawan->BpjsNo = $this->GetPostValue("BpjsNo");
            $karyawan->BpjsDate = strtotime($this->GetPostValue("BpjsDate"));
            $karyawan->ResignDate = strtotime($this->GetPostValue("ResignDate"));
			$karyawan->Fphoto = $this->GetPostValue("Fphoto");
			if (!empty($_FILES['FileName']['tmp_name'])){
				$fpath = 'public/upload/images/';
				$ftmp = $_FILES['FileName']['tmp_name'];
				$fname = $_FILES['FileName']['name'];
				$fpath.= $fname;
				$karyawan->Fphoto = $fpath;
				if(!move_uploaded_file($ftmp,$fpath)){
					$this->Set("error", sprintf("Gagal Upload file photo..", $this->connector->GetErrorMessage()));
				}
			}
			if ($this->DoUpdate($karyawan)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.karyawan','Update Karyawan -> NIK: '.$karyawan->Nik.' - '.$karyawan->Nama,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Nama: '%s' Dengan Nik: %s telah berhasil diupdate.", $karyawan->Nama, $karyawan->Nik));
				redirect_url("master.karyawan");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.karyawan','Update Karyawan -> NIK: '.$karyawan->Nik.' - '.$karyawan->Nama,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Nik: '%s' telah ada pada database !", $karyawan->Nik));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data karyawan sebelum melakukan edit data !");
				redirect_url("master.karyawan");
			}
			$karyawan = $karyawan->FindById($id);
			if ($karyawan == null) {
				$this->persistence->SaveState("error", "Data Nama yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.karyawan");
			}
		}

		// load data company for combo box
		$loader = new Company();
		if ($this->userLevel > 3) {
			$companies = $loader->LoadAll();
		} else {
			$companies = array();
			$companies[] = $loader->LoadById($this->userCompanyId);
		}

        $loader = new Department();
        $depts = $loader->LoadAll();

        // untuk kirim variable ke view
        $this->Set("karyawan", $karyawan);
        $this->Set("companies", $companies);
        $this->Set("depts", $depts);

	}

	public function view($id = null) {
		require_once(MODEL . "master/company.php");
		require_once(MODEL . "master/department.php");

		$loader = null;
		$log = new UserAdmin();
		$karyawan = new Karyawan();

		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data karyawan untuk direview !");
			redirect_url("master.karyawan");
		}
		$karyawan = $karyawan->FindById($id);
		if ($karyawan == null) {
			$this->persistence->SaveState("error", "Data Nama yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.karyawan");
		}

		// load data company for combo box
		$loader = new Company();
		if ($this->userLevel > 3) {
			$companies = $loader->LoadAll();
		} else {
			$companies = array();
			$companies[] = $loader->LoadById($this->userCompanyId);
		}

		$loader = new Department();
		$depts = $loader->LoadAll();

		// untuk kirim variable ke view
		$this->Set("karyawan", $karyawan);
		$this->Set("companies", $companies);
		$this->Set("depts", $depts);

	}

	private function DoUpdate(Karyawan $karyawan) {
		if ($karyawan->Nik == "") {
			$this->Set("error", "Nik karyawan masih kosong");
			return false;
		}

		if ($karyawan->Nama == "") {
			$this->Set("error", "Nama karyawan masih kosong");
			return false;
		}

		if ($karyawan->Update($karyawan->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data karyawan sebelum melakukan hapus data !");
			redirect_url("master.karyawan");
		}
		$log = new UserAdmin();
		$karyawan = new Karyawan();
		$karyawan = $karyawan->FindById($id);
		if ($karyawan == null) {
			$this->persistence->SaveState("error", "Data karyawan yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.karyawan");
		}

		if ($karyawan->Delete($karyawan->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.karyawan','Delete Karyawan -> NIK: '.$karyawan->Nik.' - '.$karyawan->Nama,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Nama: '%s' Dengan Nik: %s telah berhasil dihapus.", $karyawan->Nama, $karyawan->Nik));
			redirect_url("master.karyawan");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.karyawan','Delete Karyawan -> NIK: '.$karyawan->Nik.' - '.$karyawan->Nama,'-','Success');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data karyawan: '%s'. Message: %s", $karyawan->Nama, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.karyawan");
	}

	public function autoNik($cabId) {
		$karyawan = new Karyawan();
		$nik = $karyawan->GetAutoNik($cabId);
		print($nik);
	}
}
