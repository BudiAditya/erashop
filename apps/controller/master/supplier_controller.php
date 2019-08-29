<?php
class SupplierController extends AppController {
	private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $userAreaId;

	protected function Initialize() {
		require_once(MODEL . "master/contacts.php");
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
			$settings["title"] = "Master Data Supplier";

			if ($acl->CheckUserAccess("master.supplier", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.contacts/add/2", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.supplier", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.contacts/edit/2/%s", "Class" => "bt_edit", "ReqId" => 1,
						"Error" => "Maaf anda harus memilih data contact terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data contact",
						"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.supplier", "view")) {
				$settings["actions"][] = array("Text" => "View", "Url" => "master.contacts/view/2/%s", "Class" => "bt_view", "ReqId" => 1,
					"Error" => "Maaf anda harus memilih data contact terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data contact",
					"Confirm" => "");
			}
			$settings["actions"][] = array("Text" => "separator", "Url" => null);
			if ($acl->CheckUserAccess("master.supplier", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.contacts/delete/2/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Maaf anda harus memilih data contact terlebih dahulu sebelum proses hapus data.\nPERHATIAN: Pilih tepat 1 data contact",
					"Confirm" => "Apakah anda yakin mau menghapus data contact yang dipilih ? \n\n** Penghapusan Data akan mempengaruhi data transaksi yang berkaitan ** \n\nKlik 'OK' untuk melanjutkan prosedur");
			}
			$settings["def_order"] = 3;
			$settings["def_filter"] = 1;
			$settings["singleSelect"] = true;
		} else {
			$settings["from"] = "vw_m_contacts AS a ";
            $settings["where"] = "a.is_deleted = 0 and a.contacttype_id = 2 and a.entity_id = ".$this->userCompanyId;
		}
		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}
}
