<?php
class NpkbController extends AppController {
	private $userCompanyId;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "cashbank/npkb.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.nm_cabang", "display" => "Cabang/Outlet", "width" => 80);
        $settings["columns"][] = array("name" => "a.npkb_no", "display" => "No. NPKB", "width" => 80);
        $settings["columns"][] = array("name" => "a.npkb_date", "display" => "Tanggal", "width" => 60);
		$settings["columns"][] = array("name" => "a.request_date", "display" => "Tgl. Diperlukan", "width" => 65);
		$settings["columns"][] = array("name" => "a.trx_descs", "display" => "Jenis Pengeluaran", "width" => 150);
        $settings["columns"][] = array("name" => "a.request_descs", "display" => "Keterangan rencana penggunaan dana", "width" => 300);
        $settings["columns"][] = array("name" => "a.request_by", "display" => "Diminta oleh", "width" => 80);
        $settings["columns"][] = array("name" => "format(a.request_amount,0)", "display" => "Jumlah Dana", "width" => 60, "align" => "right");
        $settings["columns"][] = array("name" => "a.reff_no", "display" => "Refferensi", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.npkb_status = 0,'Draft','Approved')", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "a.tgl_cair", "display" => "Tgl. Cair", "width" => 60);
        $settings["columns"][] = array("name" => "a.no_bkk", "display" => "No. BKK", "width" => 80);

		$settings["filters"][] = array("name" => "a.nm_cabang", "display" => "Cabang/Outlet");
		$settings["filters"][] = array("name" => "a.npkb_no", "display" => "No. NPKB");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Permintaan Pengeluaran Kas/Bank (NPKB)";

			if ($acl->CheckUserAccess("cashbank.npkb", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "cashbank.npkb/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("cashbank.npkb", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "cashbank.npkb/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih Data NPKB terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu trxtype.",
					"Confirm" => "");
			}
            if ($acl->CheckUserAccess("cashbank.npkb", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "cashbank.npkb/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data NPKB terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data npkb","Confirm" => "");
            }
			if ($acl->CheckUserAccess("cashbank.npkb", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "cashbank.npkb/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih Data NPKB terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu data.",
					"Confirm" => "Apakah anda mau menghapus data npkb yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("cashbank.npkb", "view")) {
                $settings["actions"][] = array("Text" => "Print NPKB", "Url" => "cashbank.npkb/cetakpdf/%s", "Class" => "bt_print", "ReqId" => 1,
                    "Error" => "Mohon memilih Data NPKB terlebih dahulu sebelum proses cetak.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda akan mencetak npkb yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
			$settings["def_order"] = 2;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "vw_cb_npkb AS a";
            $settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(Npkb $npkb) {
		return true;
	}

	public function add() {
        require_once(MODEL . "master/company.php");
		require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/trxtype.php");
        $loader = null;
		$npkb = new Npkb();
		if (count($this->postData) > 0) {
            $npkb->EntityId = $this->GetPostValue("EntityId");
            $npkb->CabangId = $this->GetPostValue("CabangId");
            $npkb->NpkbDate = $this->GetPostValue("NpkbDate");
            //$npkb->NpkbNo = $this->GetPostValue("NpkbNo");
            $npkb->TrxTypeId = $this->GetPostValue("TrxTypeId");
            $npkb->RequestDate = $this->GetPostValue("RequestDate");
            $npkb->RequestDescs = $this->GetPostValue("RequestDescs");
            $npkb->RequestAmount = $this->GetPostValue("RequestAmount");
            $npkb->RequestBy = $this->GetPostValue("RequestBy");
            $npkb->NpkbStatus = $this->GetPostValue("NpkbStatus");
            $npkb->TglCair = $this->GetPostValue("TglCair");
            $npkb->NoBkk = $this->GetPostValue("NoBkk");
            $npkb->ReffNo = $this->GetPostValue("ReffNo");

			if ($this->ValidateData($npkb)) {
                $npkb->CreateById = AclManager::GetInstance()->GetCurrentUser()->Id;
                $npkb->NpkbNo = $npkb->GetCbNpkbNo();
                $rs = $npkb->Insert();
				if ($rs == 1) {
					$this->persistence->SaveState("info", sprintf("Data NPKB: %s (%s) sudah berhasil disimpan", $npkb->NpkbNo, $npkb->RequestDescs));
					redirect_url("cashbank.npkb");
				} else {
					$this->Set("error", "Gagal pada saat menyimpan data npkb. Message: " . $this->connector->GetErrorMessage());
				}
			}
		}else{
            $npkb->EntityId = $this->userCompanyId;
            $npkb->CabangId = $this->userCabangId;
            $npkb->RequestBy = AclManager::GetInstance()->GetCurrentUser()->RealName;
        }
        // load data company for combo box
        $loader = new Company();
        if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
            $companies = $loader->LoadAll();
        } else {
            $companies = array();
            $companies[] = $loader->LoadById($this->userCompanyId);
        }
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $trxtypes = new TrxType();
        $trxtypes = $trxtypes->LoadByTrxMode(2);
        $this->Set("cabangs", $cabang);
        $this->Set("companies", $companies);
		$this->Set("npkb", $npkb);
		$this->Set("trxtypes", $trxtypes);
	}

	public function edit($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih Data NPKB terlebih dahulu sebelum melakukan proses edit.");
			redirect_url("cashbank.npkb");
		}
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/trxtype.php");
        $loader = null;
        $npkb = new Npkb();
        if (count($this->postData) > 0) {
            $npkb->Id = $this->GetPostValue("Id");
            $npkb->EntityId = $this->GetPostValue("EntityId");
            $npkb->CabangId = $this->GetPostValue("CabangId");
            $npkb->NpkbDate = $this->GetPostValue("NpkbDate");
            $npkb->NpkbNo = $this->GetPostValue("NpkbNo");
            $npkb->TrxTypeId = $this->GetPostValue("TrxTypeId");
            $npkb->RequestDate = $this->GetPostValue("RequestDate");
            $npkb->RequestDescs = $this->GetPostValue("RequestDescs");
            $npkb->RequestAmount = $this->GetPostValue("RequestAmount");
            $npkb->RequestBy = $this->GetPostValue("RequestBy");
            $npkb->NpkbStatus = $this->GetPostValue("NpkbStatus");
            $npkb->TglCair = $this->GetPostValue("TglCair");
            $npkb->NoBkk = $this->GetPostValue("NoBkk");
            $npkb->ReffNo = $this->GetPostValue("ReffNo");

            if ($this->ValidateData($npkb)) {
                $npkb->UpdateById = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $npkb->Update($npkb->Id);
                if ($rs == 1) {
                    $this->persistence->SaveState("info", sprintf("Data NPKB: %s (%s) sudah berhasil diupdate", $npkb->NpkbNo, $npkb->RequestDescs));
                    redirect_url("cashbank.npkb");
                } else {
                    $this->Set("error",sprintf("Gagal pada saat mengupdate Data NPKB No.%s . Message: %s",$npkb->NpkbNo, $this->connector->GetErrorMessage()));
                }
            }
        }else{
            $npkb = $npkb->LoadById($id);
            if ($npkb == null || $npkb->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf Data NPKB yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("cashbank.npkb");
            }
            if($npkb->NpkbStatus == 1){
                $this->persistence->SaveState("error", sprintf("Maaf Data NPKB No. %s tidak boleh diubah karena sudah berstatus -POSTED-!",$npkb->NpkbNo));
                redirect_url("cashbank.npkb");
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
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $trxtypes = new TrxType();
        $trxtypes = $trxtypes->LoadByTrxMode(2);
        $this->Set("cabangs", $cabang);
        $this->Set("companies", $companies);
        $this->Set("npkb", $npkb);
        $this->Set("trxtypes", $trxtypes);
	}

    public function view($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih Data NPKB terlebih dahulu sebelum melakukan proses view.");
            redirect_url("cashbank.npkb");
        }
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/trxtype.php");
        $loader = null;
        $npkb = new Npkb();
        $npkb = $npkb->LoadById($id);
        if ($npkb == null || $npkb->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf Data NPKB yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("cashbank.npkb");
        }
        if($npkb->NpkbStatus == 1){
            $this->persistence->SaveState("error", sprintf("Maaf Data NPKB No. %s tidak boleh diubah karena sudah berstatus -POSTED-!",$npkb->NpkbNo));
            redirect_url("cashbank.npkb");
        }
        // load data company for combo box
        $loader = new Company();
        if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
            $companies = $loader->LoadAll();
        } else {
            $companies = array();
            $companies[] = $loader->LoadById($this->userCompanyId);
        }
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $trxtypes = new TrxType();
        $trxtypes = $trxtypes->LoadByTrxMode(2);
        $this->Set("cabangs", $cabang);
        $this->Set("companies", $companies);
        $this->Set("npkb", $npkb);
        $this->Set("trxtypes", $trxtypes);
    }

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih Data NPKB terlebih dahulu sebelum melakukan proses penghapusan data.");
			redirect_url("cashbank.npkb");
		}
		$npkb = new Npkb();
        /** @var $npkb Npkb */
        $npkb = $npkb->LoadById($id);
		if ($npkb == null || $npkb->IsDeleted) {
			$this->persistence->SaveState("error", "Maaf Data NPKB yang diminta tidak dapat ditemukan atau sudah dihapus.");
			redirect_url("cashbank.npkb");
		}
        if($npkb->NpkbStatus == 0){
            $npkb->UpdateById = AclManager::GetInstance()->GetCurrentUser()->Id;
            $rs = $npkb->Delete($npkb->Id);
            if ($rs == 1) {
                $this->persistence->SaveState("info", sprintf("Data NPKB: %s (%s) berhasil dihapus", $npkb->NpkbNo, $npkb->RequestDescs));
            } else {
                $this->persistence->SaveState("error", sprintf("Gagal menghapus Data NPKB: %s (%s). Error: %s", $npkb->NpkbNo, $npkb->RequestDescs, $this->connector->GetErrorMessage()));
            }
        }else{
            $this->persistence->SaveState("error", sprintf("Maaf Data NPKB No. %s tidak boleh dihapus karena sudah berstatus -POSTED-!",$npkb->NpkbNo));
        }
		redirect_url("cashbank.npkb");
	}

    public function cetakpdf($id = null){
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih Data NPKB terlebih dahulu sebelum melakukan proses cetak.");
            redirect_url("cashbank.npkb");
        }
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/trxtype.php");
        $loader = null;
        $npkb = new Npkb();
        $npkb = $npkb->LoadById($id);
        if ($npkb == null || $npkb->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf Data NPKB yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("cashbank.npkb");
        }
        if($npkb->NpkbStatus == 1){
            $this->persistence->SaveState("error", sprintf("Maaf Data NPKB No. %s tidak boleh diubah karena sudah berstatus -POSTED-!",$npkb->NpkbNo));
            redirect_url("cashbank.npkb");
        }
        // load data company for combo box
        $loader = new Company();
        if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
            $companies = $loader->LoadAll();
        } else {
            $companies = array();
            $companies[] = $loader->LoadById($this->userCompanyId);
        }
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $trxtypes = new TrxType();
        $trxtypes = $trxtypes->LoadByTrxMode(2);
        $this->Set("cabangs", $cabang);
        $this->Set("companies", $companies);
        $this->Set("npkb", $npkb);
        $this->Set("trxtypes", $trxtypes);
    }
}

// End of file: npkb_controller.php
