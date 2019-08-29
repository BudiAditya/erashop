<?php
class UserAdminController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userLevel;

	protected function Initialize() {
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.user_uid", "display" => "ID", "width" => 50);
		$settings["columns"][] = array("name" => "a.user_id", "display" => "User ID", "width" => 50);
		$settings["columns"][] = array("name" => "a.user_name", "display" => "User Name", "width" => 150);
		$settings["columns"][] = array("name" => "a.user_email", "display" => "Email", "width" => 150);
		$settings["columns"][] = array("name" => "b.entity_cd", "display" => "Entity", "width" => 50);
        $settings["columns"][] = array("name" => "d.kode", "display" => "Cabang", "width" => 70);
        $settings["columns"][] = array("name" => "f.dept_cd", "display" => "Bagian", "width" => 50);
		$settings["columns"][] = array("name" => "CASE a.is_aktif WHEN 1 THEN 'Aktif' ELSE 'Non-Aktif' END", "display" => "Status", "width" => 50);
		$settings["columns"][] = array("name" => "g.short_desc", "display" => "Level", "width" => 60);
		$settings["columns"][] = array("name" => "c.short_desc", "display" => "Status Login", "width" => 70);
		$settings["columns"][] = array("name" => "a.login_time", "display" => "Waktu Login", "width" => 100);
		$settings["columns"][] = array("name" => "a.login_from", "display" => "Login Dari", "width" => 80);

		$settings["filters"][] = array("name" => "a.user_id", "display" => "User ID");
		$settings["filters"][] = array("name" => "a.user_name", "display" => "User Name");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();

			$settings["title"] = "User Management";
			if ($acl->CheckUserAccess("master.useradmin", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.useradmin/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.useradmin", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.useradmin/edit/%s", "Class" => "bt_edit", "ReqId" => 1);
			}
			if ($acl->CheckUserAccess("master.useradmin", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.useradmin/delete/%s", "Class" => "bt_delete", "ReqId" => 1);
			}
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
			if ($acl->CheckUserAccess("master.useracl", "edit")) {
				$settings["actions"][] = array("Text" => "Setting Hak Akses", "Url" => "master.useracl/add/%s/0", "Class" => "bt_lock", "ReqId" => 1);
			}
			$settings["actions"][] = array("Text" => "separator", "Url" => null);
			if ($acl->CheckUserAccess("master.useradmin", "view")) {
				$settings["actions"][] = array("Text" => "Daftar Hak Akses", "Url" => "master.useracl/view/%s", "Class" => "bt_report", "ReqId" => 1);
			}
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("master.userprivileges", "edit")) {
                $settings["actions"][] = array("Text" => "User Privileges", "Url" => "master.userprivileges/edit/%s", "Class" => "bt_edit", "ReqId" => 1);
            }
			if ($this->userLevel > 3) {
				$settings["actions"][] = array("Text" => "separator", "Url" => null);
				$settings["actions"][] = array("Text" => "User Activity Log", "Url" => "master.useradmin/viewactivity/%s","Class" => "bt_report", "ReqId" => 1);
			}
		} else {
			$settings["from"] =
				"sys_users AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN sys_status_code AS c ON a.status = c.code AND c.key = 'login_audit'
	JOIN sys_status_code AS g ON a.user_lvl = g.code AND g.key = 'user_level'
	JOIN m_cabang As d ON a.entity_id = d.entity_id And a.cabang_id = d.id
	LEFT JOIN m_karyawan As e ON a.employee_id = e.id
	JOIN sys_dept As f ON e.dept_id = f.id";

			if ($this->userLevel < 4) {
				$settings["where"] = "a.is_aktif = 1 AND a.entity_id = " . $this->userCompanyId . " And a.user_lvl < " . $this->userLevel;
			}
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
		require_once(MODEL . "master/company.php");
		require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
		$log = new UserAdmin();
		$loader = null;
		$userAdmin = new UserAdmin();
		$akses = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$akses = $this->GetPostValue("aCabangId");
			$akses = implode(",",$akses);
			$userAdmin->ACabangId = $akses;
			$userAdmin->UserId = $this->GetPostValue("UserId");
			$userAdmin->EmployeeId = $this->GetPostValue("EmployeeId");
			$userAdmin->UserEmail = $this->GetPostValue("UserEmail");
			$userAdmin->UserPwd1 = $this->GetPostValue("UserPwd1");
			$userAdmin->UserPwd2 = $this->GetPostValue("UserPwd2");
			$userAdmin->EntityId = $this->GetPostValue("EntityId");
			$userAdmin->CabangId = $this->GetPostValue("CabangId");
			$userAdmin->UserLvl = $this->GetPostValue("UserLvl");
            $userAdmin->EmployeeId = $this->GetPostValue("EmployeeId");
			if ($this->GetPostValue("AllowMultipleLogin") != null) {
				$userAdmin->AllowMultipleLogin = 1;
			} else {
				$userAdmin->AllowMultipleLogin = 0;
			}
			if ($this->GetPostValue("IsAktif") != null) {
				$userAdmin->IsAktif = 1;
			} else {
				$userAdmin->IsAktif = 0;
			}
			if ($this->GetPostValue("IsForcePeriod") != null) {
				$userAdmin->IsForceAccountingPeriod = 1;
			} else {
				$userAdmin->IsForceAccountingPeriod = 0;
			}
            if ($userAdmin->EmployeeId > 0){
                $loader = new Karyawan($userAdmin->EmployeeId);
                $userAdmin->UserName = $loader->Nama;
            }
			if ($this->DoInsert($userAdmin)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.useradmin','Add New System User -> User: '.$userAdmin->UserId.' - '.$userAdmin->UserName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data User: '%s' Dengan ID: %s telah berhasil disimpan.", $userAdmin->EmployeeId, $userAdmin->UserId));
				redirect_url("master.useradmin");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.useradmin','Add New System User -> User: '.$userAdmin->UserId.' - '.$userAdmin->UserName,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("ID: '%s' telah ada pada database !", $userAdmin->UserId));
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
		$loader = new Cabang();
		$cabangs = $loader->LoadAll();
        $loader = new Karyawan();
        $karyawans = $loader->LoadAll();
		$loader = new UserAdmin();
		$userLvl = $loader->GetUserLevel($this->userLevel);
		$this->Set("companies", $companies);
		$this->Set("cabangs", $cabangs);
        $this->Set("karyawans", $karyawans);
		$this->Set("userAdmin", $userAdmin);
		$this->Set("userLvl", $userLvl);
	}

	private function DoInsert(UserAdmin $userAdmin) {
		if ($userAdmin->UserId == "") {
			$this->Set("error", "ID User masih kosong");
			return false;
		}

		if ($userAdmin->EmployeeId == "") {
			$this->Set("error", "Nama User masih kosong");
			return false;
		}

		if ($userAdmin->EntityId == "") {
			$this->Set("error", "Nama Entity masih kosong");
			return false;
		}

		if (strlen($userAdmin->UserPwd1) == 0 || strlen($userAdmin->UserPwd2) == 0) {
			$this->Set("error", "Password belum diisi");
			return false;
		}
		if ($userAdmin->UserPwd1 <> $userAdmin->UserPwd2) {
			$this->Set("error", "Password & Password Konfirmasi tidak sama");
			return false;
		}

		if ($userAdmin->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		require_once(MODEL . "master/company.php");
		require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
		$log = new UserAdmin();
		$loader = null;
		$userAdmin = new UserAdmin();
		$akses = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$akses = $this->GetPostValue("aCabangId");
			$akses = implode(",",$akses);
			$userAdmin->ACabangId = $akses;
			$userAdmin->UserUid = $this->GetPostValue("UserUid");
			$userAdmin->UserId = $this->GetPostValue("UserId");
			$userAdmin->EmployeeId = $this->GetPostValue("EmployeeId");
			$userAdmin->UserEmail = $this->GetPostValue("UserEmail");
			$userAdmin->UserPwd1 = $this->GetPostValue("UserPwd1");
			$userAdmin->UserPwd2 = $this->GetPostValue("UserPwd2");
			$userAdmin->EntityId = $this->GetPostValue("EntityId");
			$userAdmin->CabangId = $this->GetPostValue("CabangId");
			$userAdmin->UserLvl = $this->GetPostValue("UserLvl");
            $userAdmin->EmployeeId = $this->GetPostValue("EmployeeId");
			if (isset($this->postData["AllowMultipleLogin"])) {
				$userAdmin->AllowMultipleLogin = 1;
			} else {
				$userAdmin->AllowMultipleLogin = 0;
			}
			if (isset($this->postData["IsAktif"])) {
				$userAdmin->IsAktif = 1;
			} else {
				$userAdmin->IsAktif = 0;
			}
			if (isset($this->postData["IsForcePeriod"])) {
				$userAdmin->IsForceAccountingPeriod = 1;
			} else {
				$userAdmin->IsForceAccountingPeriod = 0;
			}
            if ($userAdmin->EmployeeId > 0){
                $loader = new Karyawan($userAdmin->EmployeeId);
                $userAdmin->UserName = $loader->Nama;
            }
			if ($this->DoUpdate($userAdmin)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.useradmin','Update System User -> User: '.$userAdmin->UserId.' - '.$userAdmin->UserName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data User: '%s' Dengan ID: %s telah berhasil diupdate.", $userAdmin->EmployeeId, $userAdmin->UserId));
				redirect_url("master.useradmin");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.useradmin','Update System User -> User: '.$userAdmin->UserId.' - '.$userAdmin->UserName,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("ID: '%s' telah ada pada database !", $userAdmin->UserId));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
				redirect_url("master.useradmin");
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data User sebelum melakukan edit data !");
				redirect_url("master.useradmin");
			}
			$userAdmin = $userAdmin->FindById($id);
			if ($userAdmin == null) {
				$this->persistence->SaveState("error", "Data User yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.useradmin");
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
        $loader = new Karyawan();
        $karyawans = $loader->LoadAll();
		$cabang = new Cabang();
		$loader = new UserAdmin();
		$userLvl = $loader->GetUserLevel($this->userLevel);
		$this->Set("companies", $companies);
		$this->Set("cabangs", $cabang->LoadAll());
		$this->Set("userAdmin", $userAdmin);
        $this->Set("karyawans", $karyawans);
		$this->Set("userLvl", $userLvl);
	}

	private function DoUpdate(UserAdmin $userAdmin) {
		if ($userAdmin->UserId == "") {
			$this->Set("error", "ID User masih kosong");
			return false;
		}

		if ($userAdmin->EmployeeId == "") {
			$this->Set("error", "Nama User masih kosong");
			return false;
		}

		if ($userAdmin->EntityId == "") {
			$this->Set("error", "Nama Entity masih kosong");
			return false;
		}

		if ($userAdmin->UserPwd1 <> $userAdmin->UserPwd2) {
			$this->Set("error", "Password & Password Konfirmasi tidak sama");
			return false;
		}

		if ($userAdmin->Update($userAdmin->UserUid) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data User sebelum melakukan hapus data !");
			redirect_url("master.useradmin");
		}
		$log = new UserAdmin();
		$userAdmin = new UserAdmin();
		$userAdmin = $userAdmin->FindById($id);
		if ($userAdmin == null) {
			$this->persistence->SaveState("error", "Data User yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.useradmin");
		}else {
			if ($userAdmin->Delete($userAdmin->UserUid) <> 0) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.useradmin','Delete System User -> User: '.$userAdmin->UserId.' - '.$userAdmin->UserName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data User: '%s' Dengan ID: %s telah berhasil dihapus.", $userAdmin->EmployeeId, $userAdmin->UserId));
				redirect_url("master.useradmin");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.useradmin','Delete System User -> User: '.$userAdmin->UserId.' - '.$userAdmin->UserName,'-','Failed');
				$this->persistence->SaveState("error", sprintf("Gagal menghapus data User: '%s'. Message: %s", $userAdmin->EmployeeId, $this->connector->GetErrorMessage()));
			}
		}
		redirect_url("master.useradmin");
	}

	public function CheckCabangAkses($userUid,$cabangId){
		$userakses = new UserAdmin();
		$userakses = $userakses->FindById($userUid);
		$aksescabang = null;
		if ($userakses == null){
			return false;
		}else{
			/** @var  $userakses UserAdmin */
			$aksescabang = $userakses->ACabangId;
			$aksescabang = explode(",",$aksescabang);
			if (in_array($cabangId,$aksescabang)){
				return true;
			}else{
				return false;
			}
		}
	}

	public function viewactivity($uid){
		$users = new UserAdmin();
		$users = $users->FindById($uid);
		$userId = null;
		$userName = null;
		$activity = null;
		// Intelligent time detection...
		$month = (int)date("n");
		$year = (int)date("Y");
		if (count($this->postData) > 0) {
			$sStartDate = strtotime($this->GetPostValue("StartDate"));
			$sEndDate = strtotime($this->GetPostValue("EndDate"));
		}else{
			//$sStartDate = mktime(0, 0, 0, $month, 1, $year);
			$sStartDate = time();
			$sEndDate = time();
		}
		if ($users != null){
			$userId = $users->UserId;
			$userName = $users->UserName;
			$activity = new UserAdmin();
			$activity = $activity->GetSysUserActivity($uid,$sStartDate,$sEndDate);
			$this->Set("userId", $userId);
			$this->Set("userUid", $uid);
			$this->Set("userName", $userName);
			$this->Set("startDate", $sStartDate);
			$this->Set("endDate", $sEndDate);
			$this->Set("activities", $activity);
		}else{
			redirect_url("master.useradmin");
		}
	}
}
