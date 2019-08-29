<?php
class CompanyController extends AppController {
	private $userCompanyId;
	private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/company.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		require_once(MODEL . "master/user_admin.php");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");

		//TO-DO: Apakah controller ini hanya boleh diakses oleh Corporate Level ? Bila Diakses non-CORP datanya cuma ada 1 LOLZ
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.entity_id", "display" => "ID", "width" => 50);
		$settings["columns"][] = array("name" => "a.entity_cd", "display" => "Kode", "width" => 50);
		$settings["columns"][] = array("name" => "a.company_name", "display" => "Nama Perusahaan", "width" => 150);
		$settings["columns"][] = array("name" => "a.address", "display" => "Alamat", "width" => 250);
		$settings["columns"][] = array("name" => "a.telephone", "display" => "Telepon", "width" => 80);
		$settings["columns"][] = array("name" => "a.facsimile", "display" => "Faximile", "width" => 80);
		$settings["columns"][] = array("name" => "a.npwp", "display" => "NPWP", "width" => 80);
		$settings["columns"][] = array("name" => "a.personincharge", "display" => "PIC", "width" => 150);
		$settings["columns"][] = array("name" => "a.pic_status", "display" => "Jabatan", "width" => 100);

		$settings["filters"][] = array("name" => "a.entity_cd", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.company_name", "display" => "Nama Perushaaan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Informasi Perusahaan";

			if ($acl->CheckUserAccess("master.company", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.company/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.company", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.company/edit/%s", "Class" => "bt_edit", "ReqId" => 1, "Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.company", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.company/delete/%s", "Class" => "bt_delete", "ReqId" => 1);
			}

		} else {
			$settings["from"] = "sys_company AS a";
			//$settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add() {
		require_once(MODEL . "master/coadetail.php");
		$company = new Company();
		$log = new UserAdmin();
		$loader = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$company->EntityCd = $this->GetPostValue("EntityCd");
			$company->CompanyName = $this->GetPostValue("CompanyName");
			$company->Address = $this->GetPostValue("Address");
			$company->City = $this->GetPostValue("City");
			$company->Province = $this->GetPostValue("Province");
			$company->Npwp = trim($this->GetPostValue("Npwp"));
			$company->Telephone = trim($this->GetPostValue("Telephone"));
			$company->Facsimile = trim($this->GetPostValue("Facsimile"));
			$company->PersonInCharge = trim($this->GetPostValue("PersonInCharge"));
			$company->PicStatus = trim($this->GetPostValue("PicStatus"));
            $company->StartDate = $this->GetPostValue("StartDate");
			$company->PpnInAccNo = $this->GetPostValue("PpnInAccNo");
			$company->PpnOutAccNo = $this->GetPostValue("PpnOutAccNo");
			if ($this->DoInsert($company)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.company','Add New Company -> Kode: '.$company->EntityCd.' - '.$company->CompanyName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Perusahaan: '%s' Dengan Kode: %s telah berhasil disimpan.", $company->CompanyName, $company->EntityCd));
				redirect_url("master.company");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.company','Add New Company -> Kode: '.$company->EntityCd.' - '.$company->CompanyName,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $company->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}
		$loader = new CoaDetail();
		$coalist = $loader->LoadAll($this->userCompanyId);
		$this->Set("company", $company);
		$this->Set("accounts", $coalist);
	}

	private function DoInsert(Company $company) {
		if ($company->EntityCd == "") {
			$this->Set("error", "Kode perusahaan masih kosong");
			return false;
		}

		if ($company->CompanyName == "") {
			$this->Set("error", "Nama perusahaan masih kosong");
			return false;
		}

		if ($company->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		require_once(MODEL . "master/coadetail.php");
		$company = new Company();
		$log = new UserAdmin();
		$loader = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$company->EntityId = $this->GetPostValue("EntityId");
			$company->EntityCd = $this->GetPostValue("EntityCd");
			$company->CompanyName = $this->GetPostValue("CompanyName");
			$company->Address = $this->GetPostValue("Address");
			$company->City = $this->GetPostValue("City");
			$company->Province = $this->GetPostValue("Province");
			$company->Npwp = trim($this->GetPostValue("Npwp"));
			$company->Telephone = trim($this->GetPostValue("Telephone"));
			$company->Facsimile = trim($this->GetPostValue("Facsimile"));
			$company->PersonInCharge = trim($this->GetPostValue("PersonInCharge"));
			$company->PicStatus = trim($this->GetPostValue("PicStatus"));
            $company->StartDate = $this->GetPostValue("StartDate");
			$company->PpnInAccNo = $this->GetPostValue("PpnInAccNo");
			$company->PpnOutAccNo = $this->GetPostValue("PpnOutAccNo");
			if ($this->DoUpdate($company)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.company','Update Company -> Kode: '.$company->EntityCd.' - '.$company->CompanyName,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Perusahaan: '%s' Dengan Kode: %s telah berhasil diupdate.", $company->CompanyName, $company->EntityCd));
				redirect_url("master.company");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.company','Update Company -> Kode: '.$company->EntityCd.' - '.$company->CompanyName,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $company->EntityCd));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data perusahaan sebelum melakukan edit data !");
				redirect_url("master.company");
			}
			$company = $company->FindById($id);
			if ($company == null) {
				$this->persistence->SaveState("error", "Data Perusahaan yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.company");
			}
		}

		$loader = new CoaDetail();
		$coalist = $loader->LoadAll($this->userCompanyId);
		$this->Set("company", $company);
		$this->Set("accounts", $coalist);
	}

	private function DoUpdate(Company $company) {
		if ($company->EntityCd == "") {
			$this->Set("error", "Kode perusahaan masih kosong");
			return false;
		}

		if ($company->CompanyName == "") {
			$this->Set("error", "Nama perusahaan masih kosong");
			return false;
		}

		if ($company->Update($company->EntityId) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data perusahaan sebelum melakukan hapus data !");
			redirect_url("master.company");
		}
		$log = new UserAdmin();
		$company = new company();
		$company = $company->FindById($id);
		if ($company == null) {
			$this->persistence->SaveState("error", "Data perusahaan yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.company");
		}

		if ($company->Delete($company->EntityId) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.company','Delete Company -> Kode: '.$company->EntityCd.' - '.$company->CompanyName,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Perusahaan: '%s' Dengan Kode: %s telah berhasil dihapus.", $company->CompanyName, $company->EntityCd));
			redirect_url("master.company");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.company','Delete Company -> Kode: '.$company->EntityCd.' - '.$company->CompanyName,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data perusahaan: '%s'. Message: %s", $company->CompanyName, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.company");
	}

	public function getjson_companies(){
		$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
		$companies = new Company();
		$complists = $companies->GetJSonCompanies();
		echo json_encode($complists);
	}

	public function getcombojson_companies(){
		$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
		$companies = new Company();
		$complists = $companies->GetComboJSonCompanies();
		echo json_encode($complists);
	}
}
