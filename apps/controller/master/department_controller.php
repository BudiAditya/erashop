<?php
class DepartmentController extends AppController {
	private $userCompanyId;
	private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/department.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "b.entity_cd", "display" => "Entity", "width" => 60);
		$settings["columns"][] = array("name" => "a.dept_cd", "display" => "Kode", "width" => 60);
		$settings["columns"][] = array("name" => "a.dept_name", "display" => "Nama Departemen", "width" => 250);

		$settings["filters"][] = array("name" => "a.dept_cd", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.dept_name", "display" => "Nama Departemen");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Informasi Departemen";

			if ($acl->CheckUserAccess("department", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.department/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("department", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.department/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data departemen terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu departemen",
											   "Info" => "Apakah anda yakin mau merubah data departemen yang dipilih ?");
			}
			if ($acl->CheckUserAccess("department", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.deparment/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data departemen terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu departemen",
											   "Info" => "Apakah anda yakin mau menghapus data departemen yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 2;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] =
"sys_dept AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id";
			if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
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

		$loader = null;

		$dept = new Department();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$dept->EntityId = $this->GetPostValue("EntityId");
			$dept->DeptCd = $this->GetPostValue("DeptCd");
			$dept->DeptName = $this->GetPostValue("DeptName");

			if ($this->DoInsert($dept)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.departemen','Add New Departement -> Kode: '.$dept->DeptCd.' - '.$dept->DeptName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Departemen: '%s' Dengan Kode: %s telah berhasil disimpan.", $dept->DeptName, $dept->DeptCd));
				redirect_url("master.department");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.departemen','Add New Departement -> Kode: '.$dept->DeptCd.' - '.$dept->DeptName,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $dept->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}

		// load data company for combo box
		$loader = new Company();
		if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
			$companies = $loader->LoadAll();
		} else {
			$companies = array();
			$companies[] = $loader->LoadById($this->userCompanyId);
		}

		// untuk kirim variable ke view
		$this->Set("dept", $dept);
		$this->Set("companies", $companies);
	}

	private function DoInsert(Department $dept) {

		if ($dept->EntityId == "") {
			$this->Set("error", "Kode perusahaan masih kosong");
			return false;
		}
		if ($dept->DeptCd == "") {
			$this->Set("error", "Kode departemen masih kosong");
			return false;
		}

		if ($dept->DeptName == "") {
			$this->Set("error", "Nama departemen masih kosong");
			return false;
		}

		if ($dept->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		require_once(MODEL . "master/company.php");

		$loader = null;

		$dept = new Department();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$dept->Id = $this->GetPostValue("Id");
			$dept->EntityId = $this->GetPostValue("EntityId");
			$dept->DeptCd = $this->GetPostValue("DeptCd");
			$dept->DeptName = $this->GetPostValue("DeptName");

			if ($this->DoUpdate($dept)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.departemen','Update Departement -> Kode: '.$dept->DeptCd.' - '.$dept->DeptName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Departemen: '%s' Dengan Kode: %s telah berhasil diupdate.", $dept->DeptName, $dept->DeptCd));
				redirect_url("master.department");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.departemen','Update Departement -> Kode: '.$dept->DeptCd.' - '.$dept->DeptName,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $dept->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data perusahaan sebelum melakukan edit data !");
				redirect_url("master.department");
			}
			$dept = $dept->FindById($id);
			if ($dept == null) {
				$this->persistence->SaveState("error", "Data Departemen yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.department");
			}
		}

		// load data company for combo box
		$loader = new Company();
		if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
			$companies = $loader->LoadAll();
		} else {
			$companies = array();
			$companies[] = $loader->LoadById($this->userCompanyId);
		}

		// untuk kirim variable ke view
		$this->Set("dept", $dept);
		$this->Set("companies", $companies);

	}

	private function DoUpdate(Department $dept) {
		if ($dept->EntityId == "") {
			$this->Set("error", "Kode departemen masih kosong");
			return false;
		}

		if ($dept->DeptName == "") {
			$this->Set("error", "Nama departemen masih kosong");
			return false;
		}

		if ($dept->Update($dept->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data departemen sebelum melakukan hapus data !");
			redirect_url("master.department");
		}
		$log = new UserAdmin();
		$dept = new Department();
		$dept = $dept->FindById($id);
		if ($dept == null) {
			$this->persistence->SaveState("error", "Data departemen yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.department");
		}

		if ($dept->Delete($dept->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.departemen','Delete Departement -> Kode: '.$dept->DeptCd.' - '.$dept->DeptName,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Departemen: '%s' Dengan Kode: %s telah berhasil dihapus.", $dept->DeptName, $dept->DeptCd));
			redirect_url("master.department");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.departemen','Delete Departement -> Kode: '.$dept->DeptCd.' - '.$dept->DeptName,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data departemen: '%s'. Message: %s", $dept->DeptName, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.department");
	}

	public function optlistbyentity($EntityId = null, $sDeptId = null) {
		$buff = '<option value="">-- PILIH DEPARTEMEN --</option>';
		if ($EntityId == null) {
			print($buff);
			return;
		}

		$department = new Department();
		$departments = $department->LoadByEntityId($EntityId);
		foreach ($departments as $department) {
			if ($department->Id == $sDeptId) {
				$buff .= sprintf('<option value="%d" selected="selected">%s - %s</option>', $department->Id, $department->DeptCd, $department->DeptName);
			} else {
				$buff .= sprintf('<option value="%d">%s - %s</option>', $department->Id, $department->DeptCd, $department->DeptName);
			}
		}

		print($buff);
	}
}
