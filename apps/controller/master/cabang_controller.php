<?php
class CabangController extends AppController {
	private $userCompanyId;
	private $userCabangId;
	private $userLevel;

	protected function Initialize() {
		require_once(MODEL . "master/cabang.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
		$settings["columns"][] = array("name" => "b.entity_cd", "display" => "Entity", "width" => 60);
		$settings["columns"][] = array("name" => "a.kode", "display" => "Kode", "width" => 100);
        $settings["columns"][] = array("name" => "c.area_name", "display" => "Area", "width" => 100);
		$settings["columns"][] = array("name" => "if(a.cab_type = 1,'Outlet Saja',if(a.cab_type = 2, 'Gudang Saja','Outlet + Gudang'))", "display" => "Jenis", "width" => 100);
		$settings["columns"][] = array("name" => "a.cabang", "display" => "Lokasi/Cabang", "width" => 200);
        $settings["columns"][] = array("name" => "a.nama_outlet", "display" => "Nama Outlet", "width" => 200);
        $settings["columns"][] = array("name" => "a.alamat", "display" => "Alamat", "width" => 350);
        $settings["columns"][] = array("name" => "a.pic", "display" => "P I C", "width" => 100);

		$settings["filters"][] = array("name" => "a.kode", "display" => "Kode");
		$settings["filters"][] = array("name" => "a.cabang", "display" => "Cabang");
		$settings["filters"][] = array("name" => "if(a.cab_type = 1,'Outlet Saja',if(a.cab_type = 2, 'Gudang Saja','Outlet + Gudang'))", "display" => "Jenis");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Informasi Cabang/Outlet";

			if ($acl->CheckUserAccess("cabang", "add", "master")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.cabang/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("cabang", "edit", "master")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.cabang/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
											   "Error" => "Mohon memilih data cabang terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu cabang",
											   "Info" => "Apakah anda yakin mau merubah data cabang yang dipilih ?");
			}
			if ($acl->CheckUserAccess("cabang", "delete", "master")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.cabang/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
											   "Error" => "Mohon memilih data cabang terlebih dahulu !\nPERHATIAN: Mohon memilih tepat satu cabang",
											   "Info" => "Apakah anda yakin mau menghapus data cabang yang dipilih ?");
			}

			$settings["def_filter"] = 0;
			$settings["def_order"] = 1;
			$settings["singleSelect"] = true;
		} else {
            $settings["def_order"] = 1;
			$settings["from"] = "m_cabang AS a JOIN sys_company AS b ON a.entity_id = b.entity_id Join m_area AS c on a.area_id = c.id";
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
        require_once(MODEL . "master/area.php");
		$loader = null;
		$cabang = new Cabang();
		$log = new UserAdmin();
        $fpath = null;
        $ftmp = null;
        $fname = null;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$cabang->EntityId = $this->GetPostValue("EntityId");
			$cabang->AreaId = $this->GetPostValue("AreaId");
			$cabang->Kode = $this->GetPostValue("Kode");
			$cabang->NamaCabang = $this->GetPostValue("NamaCabang");
			$cabang->Cabang = $this->GetPostValue("Cabang");
			$cabang->Alamat = $this->GetPostValue("Alamat");
			$cabang->Pic = $this->GetPostValue("Pic");
			$cabang->RawPrintMode = $this->GetPostValue("RawPrintMode");
			$cabang->RawPrinterName = $this->GetPostValue("RawPrinterName");
			$cabang->CabType = $this->GetPostValue("CabType");
			$cabang->AllowMinus = $this->GetPostValue("AllowMinus");
			$cabang->FLogo = null;
			if (!empty($_FILES['FileName']['tmp_name'])){
				$fpath = 'public/upload/images/';
				$ftmp = $_FILES['FileName']['tmp_name'];
				$fname = $_FILES['FileName']['name'];
				$fpath.= $fname;
				$cabang->FLogo = $fpath;
				if(!move_uploaded_file($ftmp,$fpath)){
					$this->Set("error", sprintf("Gagal Upload file logo..", $this->connector->GetErrorMessage()));
				}
			}
			$cabang->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
			if ($this->DoInsert($cabang)) {
				$log = $log->UserActivityWriter($this->userCabangId,'master.Cabang','Add New Cabang -> Kode: '.$cabang->Kode.' - '.$cabang->NamaCabang,'-','Success');
				$this->persistence->SaveState("info", sprintf("Data Cabang: '%s' Dengan Kode: %s telah berhasil disimpan..", $cabang->Cabang, $cabang->Kode));
				redirect_url("master.cabang");
			} else {
				$log = $log->UserActivityWriter($this->userCabangId,'master.Cabang','Add New Cabang -> Kode: '.$cabang->Kode.' - '.$cabang->NamaCabang,'-','Failed');
				if ($this->connector->GetHasError()) {
					if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
						$this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $cabang->Kode));
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
        $loader = new Area();
        $areas = $loader->LoadAll();
		// untuk kirim variable ke view
		$this->Set("cabang", $cabang);
        $this->Set("areas", $areas);
        $this->Set("userCompanyId", $this->userCompanyId);
		$this->Set("companies", $companies);
	}

	private function DoInsert(Cabang $cabang) {

		if ($cabang->Insert() == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function edit($id = null) {
		require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/area.php");
		$loader = null;
		$cabang = new Cabang();
		$log = new UserAdmin();
        $fpath = null;
        $ftmp = null;
        $fname = null;
        if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$cabang->Id = $this->GetPostValue("Id");
			$cabang->EntityId = $this->GetPostValue("EntityId");
            $cabang->AreaId = $this->GetPostValue("AreaId");
			$cabang->Kode = $this->GetPostValue("Kode");
            $cabang->NamaCabang = $this->GetPostValue("NamaCabang");
			$cabang->Cabang = $this->GetPostValue("Cabang");
            $cabang->Alamat = $this->GetPostValue("Alamat");
			$cabang->RawPrintMode = $this->GetPostValue("RawPrintMode");
			$cabang->RawPrinterName = $this->GetPostValue("RawPrinterName");
			$cabang->CabType = $this->GetPostValue("CabType");
			$cabang->AllowMinus = $this->GetPostValue("AllowMinus");
            $cabang->Pic = $this->GetPostValue("Pic");
			$cabang->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if (!empty($_FILES['FileName']['tmp_name'])){
                $fpath = 'public/upload/images/';
                $ftmp = $_FILES['FileName']['tmp_name'];
                $fname = $_FILES['FileName']['name'];
                $fpath.= $fname;
                $cabang->FLogo = $fpath;
                if(move_uploaded_file($ftmp,$fpath)){
                    if ($this->DoUpdate($cabang)) {
						$log = $log->UserActivityWriter($this->userCabangId,'master.Cabang','Update Cabang -> Kode: '.$cabang->Kode.' - '.$cabang->NamaCabang,'-','Success');
                        $this->persistence->SaveState("info", sprintf("Data Cabang: '%s' Dengan Kode: %s telah berhasil diupdate.", $cabang->Cabang, $cabang->Kode));
                        redirect_url("master.cabang");
                    } else {
						$log = $log->UserActivityWriter($this->userCabangId,'master.Cabang','Update Cabang -> Kode: '.$cabang->Kode.' - '.$cabang->NamaCabang,'-','Failed');
                        if ($this->connector->GetHasError()) {
                            if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                                $this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $cabang->Kode));
                            } else {
                                $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                            }
                        }
                    }
                }else{
                    $this->Set("error", sprintf("Gagal Upload file logo..", $this->connector->GetErrorMessage()));
                }
            }else{
                $cabang->FLogo = null;
                if ($this->DoUpdate($cabang)) {
                    $this->persistence->SaveState("info", sprintf("Data Cabang: '%s' Dengan Kode: %s telah berhasil diupdate.", $cabang->Cabang, $cabang->Kode));
                    redirect_url("master.cabang");
                } else {
                    if ($this->connector->GetHasError()) {
                        if ($this->connector->GetErrorCode() == $this->connector->GetDuplicateErrorCode()) {
                            $this->Set("error", sprintf("Kode: '%s' telah ada pada database !", $cabang->Kode));
                        } else {
                            $this->Set("error", sprintf("System Error: %s. Please Contact System Administrator.", $this->connector->GetErrorMessage()));
                        }
                    }
                }
            }
		} else {
			if ($id == null) {
				$this->persistence->SaveState("error", "Anda harus memilih data cabang sebelum melakukan edit data !");
				redirect_url("master.cabang");
			}
			$cabang = $cabang->FindById($id);
			if ($cabang == null) {
				$this->persistence->SaveState("error", "Data Cabang yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
				redirect_url("master.cabang");
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
		$loader = new Area();
		$areas = $loader->LoadAll();
        // untuk kirim variable ke view
        $this->Set("cabang", $cabang);
        $this->Set("areas", $areas);
		$this->Set("companies", $companies);

	}

	private function DoUpdate(Cabang $cabang) {
		if ($cabang->Update($cabang->Id) == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data cabang sebelum melakukan hapus data !");
			redirect_url("master.cabang");
		}
		$log = new UserAdmin();
		$cabang = new Cabang();
		$cabang = $cabang->FindById($id);
		if ($cabang == null) {
			$this->persistence->SaveState("error", "Data cabang yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.cabang");
		}

		if ($cabang->Delete($cabang->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.Cabang','Delete Cabang -> Kode: '.$cabang->Kode.' - '.$cabang->NamaCabang,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Cabang: '%s' Dengan Kode: %s telah berhasil dihapus.", $cabang->Cabang, $cabang->Kode));
			redirect_url("master.cabang");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.Cabang','Delete Cabang -> Kode: '.$cabang->Kode.' - '.$cabang->NamaCabang,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data cabang: '%s'. Message: %s", $cabang->Cabang, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.cabang");
	}

	public function getjson_cabangs($eti){
		$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
		$cabangs = new Cabang();
		$cablists = $cabangs->GetJSonCabangs($eti);
		echo json_encode($cablists);
	}

	public function getcombojson_cabangs($eti=0){
		$filter = isset($_POST['q']) ? strval($_POST['q']) : '';
		$cabangs = new Cabang();
		$cablists = $cabangs->GetComboJSonCabangs($eti);
		echo json_encode($cablists);
	}
}
