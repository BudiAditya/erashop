<?php

class InvoiceTypeController extends AppController {
	private $userCompanyId;

	protected function Initialize() {
		require_once(MODEL . "ar/invoicetype.php");
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		$settings["columns"][] = array("name" => "a.invoicetype", "display" => "Jenis Invoice", "width" => 200);
		$settings["columns"][] = array("name" => "concat(a.ar_acc_no,' - ',a.ar_acc_name)", "display" => "Akun Piutang", "width" => 250);
		$settings["columns"][] = array("name" => "concat(a.rev_acc_no,' - ',a.rev_acc_name)", "display" => "Akun Pendapatan", "width" => 250);
        $settings["columns"][] = array("name" => "a.description", "display" => "Keterangan", "width" => 300);

		$settings["filters"][] = array("name" => "a.invoicetype", "display" => "Jenis Invoice");
		$settings["filters"][] = array("name" => "a.description", "display" => "Keterangan");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Jenis Invoice (AR/Piutang)";

			if ($acl->CheckUserAccess("ar.invoicetype", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "ar.invoicetype/add", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("ar.invoicetype", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "ar.invoicetype/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih trxtype terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu trxtype.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("ar.invoicetype", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "ar.invoicetype/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih trxtype terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu trxtype.",
					"Confirm" => "Apakah anda mau menghapus data trxtype yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}

			$settings["def_order"] = 1;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "vw_ar_invoicetype AS a";
            $settings["where"] = "a.is_deleted = 0";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	private function ValidateData(InvoiceType $invoicetype) {
		return true;
	}

	public function add() {
		require_once(MODEL . "master/coadetail.php");

		$invoicetype = new InvoiceType();

		if (count($this->postData) > 0) {
			$invoicetype->IvcType = $this->GetPostValue("IvcType");
			$invoicetype->Description = $this->GetPostValue("Description");
			$invoicetype->ArAccId = $this->GetPostValue("ArAccId");
            $invoicetype->RevAccId = $this->GetPostValue("RevAccId");
			if ($this->ValidateData($invoicetype)) {
				$invoicetype->CreateById = AclManager::GetInstance()->GetCurrentUser()->Id;

				$rs = $invoicetype->Insert();
				if ($rs == 1) {
					$this->persistence->SaveState("info", sprintf("Data Invoice Type: %s (%s) sudah berhasil disimpan", $invoicetype->IvcType, $invoicetype->Description));
					redirect_url("ar.invoicetype");
				} else {
					$this->Set("error", "Gagal pada saat menyimpan data. Message: " . $this->connector->GetErrorMessage());
				}
			}
		}
        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll();
		$this->Set("invoicetype", $invoicetype);
		$this->Set("accounts", $accounts);
	}

	public function edit($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
			redirect_url("ar.invoicetype");
		}

		require_once(MODEL . "master/coadetail.php");
		$invoicetype = new InvoiceType();
		if (count($this->postData) > 0) {
			$invoicetype->Id = $id;
            $invoicetype->IvcType = $this->GetPostValue("IvcType");
            $invoicetype->Description = $this->GetPostValue("Description");
            $invoicetype->ArAccId = $this->GetPostValue("ArAccId");
            $invoicetype->RevAccId = $this->GetPostValue("RevAccId");
			if ($this->ValidateData($invoicetype)) {
				$invoicetype->UpdateById = AclManager::GetInstance()->GetCurrentUser()->Id;
				$rs = $invoicetype->Update($invoicetype->Id);
				if ($rs == 1) {
					$this->persistence->SaveState("info", sprintf("Perubahan data Invice Type: %s (%s) sudah berhasil disimpan", $invoicetype->IvcType, $invoicetype->Description));
					redirect_url("ar.invoicetype");
				} else {
					$this->Set("error", "Gagal pada saat merubah data. Message: " . $this->connector->GetErrorMessage());
				}
			}
		} else {
			$invoicetype = $invoicetype->LoadById($id);
			if ($invoicetype == null || $invoicetype->IsDeleted) {
				$this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
				redirect_url("ar.invoicetype");
			}
		}

        $accounts = new CoaDetail();
        $accounts = $accounts->LoadAll();
        $this->Set("invoicetype", $invoicetype);
        $this->Set("accounts", $accounts);
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan.");
			redirect_url("ar.invoicetype");
		}

		$invoicetype = new InvoiceType();
		$invoicetype = $invoicetype->LoadById($id);
		if ($invoicetype == null || $invoicetype->IsDeleted) {
			$this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
			redirect_url("ar.invoicetype");
		}

		$rs = $invoicetype->Delete($invoicetype->Id);
		if ($rs == 1) {
			$this->persistence->SaveState("info", sprintf("Jenis Invoice: -%s- (%s) sudah dihapus", $invoicetype->IvcType, $invoicetype->Description));
		} else {
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data: -%s= (%s). Error: %s", $invoicetype->IvcType, $invoicetype->Description, $this->connector->GetErrorMessage()));
		}
		redirect_url("ar.invoicetype");
	}
}

// End of file: trxtype_controller.php
