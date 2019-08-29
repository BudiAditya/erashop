<?php
class ContactsController extends AppController {
	private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $userAreaId;

	protected function Initialize() {
		require_once(MODEL . "master/contacts.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
        $this->userAreaId = $this->persistence->LoadState("area_id");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "a.entity_cd", "display" => "Entity", "width" => 40);
		$settings["columns"][] = array("name" => "a.contact_code", "display" => "Kode", "width" => 50);
		$settings["columns"][] = array("name" => "a.contact_name", "display" => "Nama Contact/Relasi", "width" => 200);
        $settings["columns"][] = array("name" => "a.type_descs", "display" => "Kelompok", "width" => 70);
		$settings["columns"][] = array("name" => "a.address", "display" => "Alamat", "width" => 300);
        $settings["columns"][] = array("name" => "a.city", "display" => "Kota", "width" => 100);
        $settings["columns"][] = array("name" => "a.contact_person", "display" => "P I C", "width" => 100);
        $settings["columns"][] = array("name" => "a.position", "display" => "Jabatan", "width" => 100);
        $settings["columns"][] = array("name" => "a.hand_phone", "display" => "No. HP", "width" => 150);

		$settings["filters"][] = array("name" => "a.contact_code", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.contact_name", "display" => "Nama Contact");
		$settings["filters"][] = array("name" => "a.type_descs", "display" => "Kategori");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Master Data Relasi";

			if ($acl->CheckUserAccess("master.contacts", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.contacts/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.contacts", "view")) {
				$settings["actions"][] = array("Text" => "View", "Url" => "master.contacts/view/%s", "Class" => "bt_view", "ReqId" => 1,
					"Error" => "Maaf anda harus memilih data contact terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data contact",
					"Confirm" => "");
			}
			$settings["actions"][] = array("Text" => "separator", "Url" => null);
			if ($acl->CheckUserAccess("master.contacts", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.contacts/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Maaf anda harus memilih data contact terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data contact",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.contacts", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.contacts/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Maaf anda harus memilih data contact terlebih dahulu sebelum proses hapus data.\nPERHATIAN: Pilih tepat 1 data contact",
					"Confirm" => "Apakah anda yakin mau menghapus data contact yang dipilih ? \n\n** Penghapusan Data akan mempengaruhi data transaksi yang berkaitan ** \n\nKlik 'OK' untuk melanjutkan prosedur");
			}
			$settings["def_order"] = 3;
			$settings["def_filter"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "vw_m_contacts AS a ";
            $settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(Contacts $contacts) {
		if ($contacts->ContactName == "") {
			$this->Set("error", "Nama Contact masih kosong");
			return false;
		}
		if ($contacts->ContactCode == "") {
			$this->Set("error", "Kode contact masih kosong");
			return false;
		}
		return true;
	}

	public function add($ctype = 0) {
		require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/contacttype.php");
		$log = new UserAdmin();
		$contacts = new Contacts();
        $loader = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$contacts->EntityId = $this->userCompanyId;
            $contacts->CabangId = $this->userCabangId;
			$contacts->ContactTypeId = $this->GetPostValue("ContactTypeId");
			$contacts->ContactName = $this->GetPostValue("ContactName");
			$contacts->ContactCode = $this->GetPostValue("ContactCode");
			$contacts->Address = $this->GetPostValue("Address");
			$contacts->City = $this->GetPostValue("City");
			$contacts->PostCd = $this->GetPostValue("PostCd");
			$contacts->TelNo = $this->GetPostValue("TelNo");
			$contacts->HandPhone = $this->GetPostValue("HandPhone");
			$contacts->FaxNo = $this->GetPostValue("FaxNo");
			$contacts->Remark = $this->GetPostValue("Remark");
			$contacts->Gender = $this->GetPostValue("Gender");
			$contacts->Birthday = $this->GetPostValue("BirthDate");
			$contacts->Nationality = $this->GetPostValue("Nationality");
			$contacts->MaritalStatus = $this->GetPostValue("MaritalStatus");
			$contacts->Npwp = $this->GetPostValue("Npwp");
			$contacts->MailAddr = $this->GetPostValue("MailAddr");
			$contacts->MailCity = $this->GetPostValue("MailCity");
			$contacts->MailPostCd = $this->GetPostValue("MailPostCd");
			$contacts->ContactPerson = $this->GetPostValue("ContactPerson");
			$contacts->Position = $this->GetPostValue("Position");
			$contacts->IdCard = $this->GetPostValue("IdCard");
			$contacts->CreditTerms = $this->GetPostValue("CreditTerms");
			if (isset($this->postData["Reminder"])) {
				$contacts->Reminder = 1;
			} else {
				$contacts->Reminder = 0;
			}
			if (isset($this->postData["Interest"])) {
				$contacts->Interest = 1;
			} else {
				$contacts->Interest = 0;
			}
			$contacts->EmailAdd = $this->GetPostValue("EmailAdd");
			$contacts->WebSite = $this->GetPostValue("WebSite");
			$contacts->Status = $this->GetPostValue("Status");
            $contacts->ContactLevel = $this->GetPostValue("ContactLevel");
            $contacts->CreditLimit = $this->GetPostValue("CreditLimit");
            $contacts->CreditToDate = $this->GetPostValue("CreditToDate");
            $contacts->PointSum = $this->GetPostValue("PointSum");
            $contacts->PointRedem = $this->GetPostValue("PointRedem");
			if ($this->ValidateData($contacts)) {
				$contacts->CreatedById = AclManager::GetInstance()->GetCurrentUser()->Id;
				if ($contacts->Insert() == 1) {
					$this->persistence->SaveState("info", sprintf("Data Contact: '%s' telah berhasil disimpan.", $contacts->ContactName));
                    if ($ctype == 1) {
						$log = $log->UserActivityWriter($this->userCabangId,'master.contacts','Add New Customer -> Kode: '.$contacts->ContactCode.' - '.$contacts->ContactName,'-','Success');
                        redirect_url("master.customer");
                    }else{
						$log = $log->UserActivityWriter($this->userCabangId,'master.contacts','Add New Supplier -> Kode: '.$contacts->ContactCode.' - '.$contacts->ContactName,'-','Success');
                        redirect_url("master.supplier");
                    }
				} else {
					$log = $log->UserActivityWriter($this->userCabangId,'master.contacts','Add New Contact -> Kode: '.$contacts->ContactCode.' - '.$contacts->ContactName,'-','Failed');
					if ($this->connector->IsDuplicateError()) {
						$this->Set("error", sprintf("Data Relasi: '%s' telah ada pada database !", $contacts->ContactName));
					} else {
						$this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
					}
				}
			}
		}

		$company = new Company();
		if ($this->userLevel > 3) {
			$companies = $company->LoadAll();
		} else {
			$companies = array();
			$companies[] = $company->LoadById($this->userCompanyId);
		}
        $loader = new ContactType();
        $contacttype = $loader->FindById($ctype);
		$this->Set("companies", $companies);
		$this->Set("contacts", $contacts);
        $this->Set("contacttypes", $contacttype);
		$this->Set("userCompId", $this->userCompanyId);
        $this->Set("ctype", $ctype);
	}

	public function edit($ctype = 0,$id = null) {
		require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/contacttype.php");
		$contacts = new Contacts();
		$log = new UserAdmin();
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$contacts->Id = $id;
            $contacts->EntityId = $this->userCompanyId;
            $contacts->CabangId = $this->userCabangId;
            $contacts->ContactTypeId = $this->GetPostValue("ContactTypeId");
            $contacts->ContactName = $this->GetPostValue("ContactName");
            $contacts->ContactCode = $this->GetPostValue("ContactCode");
            $contacts->Address = $this->GetPostValue("Address");
            $contacts->City = $this->GetPostValue("City");
            $contacts->PostCd = $this->GetPostValue("PostCd");
            $contacts->TelNo = $this->GetPostValue("TelNo");
            $contacts->HandPhone = $this->GetPostValue("HandPhone");
            $contacts->FaxNo = $this->GetPostValue("FaxNo");
            $contacts->Remark = $this->GetPostValue("Remark");
            $contacts->Gender = $this->GetPostValue("Gender");
            $contacts->Birthday = $this->GetPostValue("BirthDate");
            $contacts->Nationality = $this->GetPostValue("Nationality");
            $contacts->MaritalStatus = $this->GetPostValue("MaritalStatus");
            $contacts->Npwp = $this->GetPostValue("Npwp");
            $contacts->MailAddr = $this->GetPostValue("MailAddr");
            $contacts->MailCity = $this->GetPostValue("MailCity");
            $contacts->MailPostCd = $this->GetPostValue("MailPostCd");
            $contacts->ContactPerson = $this->GetPostValue("ContactPerson");
            $contacts->Position = $this->GetPostValue("Position");
            $contacts->IdCard = $this->GetPostValue("IdCard");
            $contacts->CreditTerms = $this->GetPostValue("CreditTerms");
            if (isset($this->postData["Reminder"])) {
                $contacts->Reminder = 1;
            } else {
                $contacts->Reminder = 0;
            }
            if (isset($this->postData["Interest"])) {
                $contacts->Interest = 1;
            } else {
                $contacts->Interest = 0;
            }
            $contacts->EmailAdd = $this->GetPostValue("EmailAdd");
            $contacts->WebSite = $this->GetPostValue("WebSite");
            $contacts->Status = $this->GetPostValue("Status");
            $contacts->ContactLevel = $this->GetPostValue("ContactLevel");
            $contacts->CreditLimit = $this->GetPostValue("CreditLimit");
            $contacts->CreditToDate = $this->GetPostValue("CreditToDate");
            $contacts->PointSum = $this->GetPostValue("PointSum");
            $contacts->PointRedem = $this->GetPostValue("PointRedem");

			if ($this->ValidateData($contacts)) {
				$contacts->UpdatedById = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $contacts->Update($id);
                if ($rs == 1) {
                    $this->persistence->SaveState("info", sprintf("Data Contact: '%s' telah berhasil diubah.", $contacts->ContactName));
                    if ($ctype == 1){
						$log = $log->UserActivityWriter($this->userCabangId,'master.contacts','Update Customer -> Kode: '.$contacts->ContactCode.' - '.$contacts->ContactName,'-','Success');
                        redirect_url("master.customer");
                    }else{
						$log = $log->UserActivityWriter($this->userCabangId,'master.contacts','Update Supplier -> Kode: '.$contacts->ContactCode.' - '.$contacts->ContactName,'-','Success');
                        redirect_url("master.supplier");
                    }
                } else {
					$log = $log->UserActivityWriter($this->userCabangId,'master.contacts','Update Contact -> Kode: '.$contacts->ContactCode.' - '.$contacts->ContactName,'-','Failed');
                    $this->Set("error", "Gagal pada saat merubah data kontak. Message: " . $this->connector->GetErrorMessage());
                }
			}
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih salah satu data sebelum melakukan edit data !");
                if ($ctype == 1){
                    redirect_url("master.customer");
                }else{
                    redirect_url("master.supplier");
                }
			}
			$contacts = $contacts->FindById($id);
			if ($contacts == null) {
				$this->persistence->SaveState("error", "Data Contact yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
                if ($ctype == 1){
                    redirect_url("master.customer");
                }else{
                    redirect_url("master.supplier");
                }
			}
			if ($this->userLevel < 4) {
				if ($contacts->EntityId != $this->userCompanyId) {
					// AKSES DATA BEDA Entity KICK!!!!
					$this->persistence->SaveState("error", "Maaf, Data contact yang dipilih tidak boleh diedit oleh anda..");
                    if ($ctype == 1){
                        redirect_url("master.customer");
                    }else{
                        redirect_url("master.supplier");
                    }
				}
			}
		}

		$company = new Company();
		if ($this->userLevel >3) {
			$companies = $company->LoadAll();
		} else {
			$companies = array();
			$companies[] = $company->LoadById($this->userCompanyId);
		}
        $loader = new ContactType();
        $contacttype = $loader->FindById($ctype);
        $this->Set("companies", $companies);
        $this->Set("contacts", $contacts);
        $this->Set("contacttypes", $contacttype);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("ctype", $ctype);
	}

	public function view($ctype = 0,$id = null) {
		require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/contacttype.php");
		$contacts = new Contacts();
        if ($id == null) {
            $this->persistence->SaveState("error", "Anda harus memilih salah satu data sebelum melakukan view data !");
            if ($ctype == 1){
                redirect_url("master.customer");
            }else{
                redirect_url("master.supplier");
            }
        }
        $contacts = $contacts->FindById($id);
        if ($contacts == null) {
            $this->persistence->SaveState("error", "Data Contact yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            if ($ctype == 1){
                redirect_url("master.customer");
            }else{
                redirect_url("master.supplier");
            }
        }
        $company = new Company();
        if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
        $companies = $company->LoadAll();
        } else {
            $companies = array();
            $companies[] = $company->LoadById($this->userCompanyId);
        }
        $loader = new ContactType();
        $contacttype = $loader->FindById($ctype);
        $this->Set("companies", $companies);
        $this->Set("contacts", $contacts);
        $this->Set("contacttypes", $contacttype);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("ctype", $ctype);
	}


	public function delete($ctype,$id = null) {
        if ($this->userLevel < 4){
            $this->persistence->SaveState("error", "Anda tidak diperbolehkan melakukan penghapusan data ini !");
            if ($ctype == 1){
                redirect_url("master.customer");
            }else{
                redirect_url("master.supplier");
            }
        }
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data sebelum melakukan hapus data !");
            if ($ctype == 1){
                redirect_url("master.customer");
            }else{
                redirect_url("master.supplier");
            }
		}
		$log = new UserAdmin();
		$contacts = new Contacts();
		$contacts = $contacts->FindById($id);
		if ($contacts == null) {
			$this->persistence->SaveState("error", "Data yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
            if ($ctype == 1){
                redirect_url("master.customer");
            }else{
                redirect_url("master.supplier");
            }
		}
		if ($this->userCompanyId != 1 && $this->userCompanyId != null) {
			if ($contacts->EntityId != $this->userCompanyId) {
				// AKSES DATA BEDA Entity KICK!!!!
				$this->persistence->SaveState("error", "Maaf, Data ini yang dipilih tidak boleh dihapus oleh anda..");
                if ($ctype == 1){
                    redirect_url("master.customer");
                }else{
                    redirect_url("master.supplier");
                }
				return;
			}
		}

		if ($contacts->Delete($contacts->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.contacts','Delete Contact -> Kode: '.$contacts->ContactCode.' - '.$contacts->ContactName,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Contact: '%s' Dengan Kode: %s telah berhasil dihapus.", $contacts->ContactName, $contacts->ContactCode));
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.contacts','Delete Contact -> Kode: '.$contacts->ContactCode.' - '.$contacts->ContactName,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data contact: '%s'. Message: %s", $contacts->ContactName, $this->connector->GetErrorMessage()));
		}
        if ($ctype == 1){
            redirect_url("master.customer");
        }else{
            redirect_url("master.supplier");
        }
	}

	public function autocustcd($ctype,$custname) {
		$contacts = new Contacts();
		$custcd = $contacts->GetAutoCode($ctype,$custname);
		print($custcd);
	}

    public function getjson_contacts($ctype = 0,$eti = 0){
       $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
       $contacts = new Contacts();
       $contlists = $contacts->GetJSonContacts($ctype,$eti,$filter);
       echo json_encode($contlists);
    }

	public function get_credittodate($id,$entity_id = 0,$cabang_id = 0){
		$creditodate = 0;
		$loader = new Contacts();
		$creditodate = $loader->GetCreditToDate($id,$entity_id,$cabang_id);
		print($creditodate);
	}

	public function get_warkattodate($id,$entity_id = 0,$cabang_id = 0){
		require_once(MODEL . "cashbank/warkat.php");
		$warkatodate = 0;
		$loader = new Warkat();
		$warkatodate = $loader->GetWarkatToDate($id,$entity_id,$cabang_id);
		print($warkatodate);
	}
}
