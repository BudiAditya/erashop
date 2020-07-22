<?php

class PriceListsController extends AppController {
	private $userUid;
	private $userCompanyId;
	private $userCabangId;
	private $userLevel;

	protected function Initialize() {
		require_once(MODEL . "master/setprice.php");
		require_once(MODEL . "master/user_admin.php");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
		$this->userCompanyId = $this->persistence->LoadState("entity_id");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
		$router = Router::GetInstance();
		$settings = array();

		$settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 0);
		//$settings["columns"][] = array("name" => "a.cabang_code", "display" => "Cabang", "width" => 80);
        $settings["columns"][] = array("name" => "a.item_code", "display" => "Kode Barang", "width" => 100);
        $settings["columns"][] = array("name" => "a.item_name", "display" => "Nama Barang", "width" => 350);
        $settings["columns"][] = array("name" => "a.satuan", "display" => "Satuan", "width" => 40);
		if ($this->userLevel > 1) {
			$settings["columns"][] = array("name" => "format(a.hrg_beli,0)", "display" => "Harga Beli", "width" => 60, "align" => "right");
			$settings["columns"][] = array("name" => "format(a.hrg_jual1,0)", "display" => "Harga Jual1", "width" => 60, "align" => "right");
			$settings["columns"][] = array("name" => "format(a.hrg_jual2,0)", "display" => "Harga Jual2", "width" => 60, "align" => "right");
			$settings["columns"][] = array("name" => "format(a.hrg_jual3,0)", "display" => "Harga Jual3", "width" => 60, "align" => "right");
			//$settings["columns"][] = array("name" => "format(a.hrg_jual4,0)", "display" => "Harga Jual4", "width" => 60, "align" => "right");
			//$settings["columns"][] = array("name" => "format(a.hrg_jual5,0)", "display" => "Harga Jual5", "width" => 60, "align" => "right");
			//$settings["columns"][] = array("name" => "format(a.hrg_jual6,0)", "display" => "Harga Jual6", "width" => 60, "align" => "right");
		}else{
			$settings["columns"][] = array("name" => "format(a.hrg_jual1,0)", "display" => "Harga Jual", "width" => 60, "align" => "right");
		}
		$settings["columns"][] = array("name" => "a.price_date", "display" => "Last Update", "width" => 60);
		$settings["columns"][] = array("name" => "a.supplier_name", "display" => "Supplier", "width" => 200);

		$settings["filters"][] = array("name" => "a.item_code", "display" => "Kode Barang");
		$settings["filters"][] = array("name" => "a.item_name", "display" => "Nama Barang");
        $settings["filters"][] = array("name" => "a.supplier_name", "display" => "Supplier");
        //$settings["filters"][] = array("name" => "a.cabang_code", "display" => "Sumber/Cabang");

		if (!$router->IsAjaxRequest) {
			$acl = AclManager::GetInstance();
			$settings["title"] = "Daftar Harga Barang";

			if ($acl->CheckUserAccess("master.pricelists", "add")) {
				$settings["actions"][] = array("Text" => "Add", "Url" => "master.pricelists/add/0", "Class" => "bt_add", "ReqId" => 0);
			}
			if ($acl->CheckUserAccess("master.pricelists", "edit")) {
				$settings["actions"][] = array("Text" => "Edit", "Url" => "master.pricelists/add/%s", "Class" => "bt_edit", "ReqId" => 1,
					"Error" => "Mohon memilih items terlebih dahulu sebelum proses edit.\nPERHATIAN: Mohon memilih tepat satu items.",
					"Confirm" => "");
			}
			if ($acl->CheckUserAccess("master.pricelists", "delete")) {
				$settings["actions"][] = array("Text" => "Delete", "Url" => "master.pricelists/delete/%s", "Class" => "bt_delete", "ReqId" => 1,
					"Error" => "Mohon memilih items terlebih dahulu sebelum proses penghapusan.\nPERHATIAN: Mohon memilih tepat satu items.",
					"Confirm" => "Apakah anda mau menghapus data items yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
			}
			$settings["actions"][] = array("Text" => "separator", "Url" => null);
			if ($acl->CheckUserAccess("master.pricelists", "edit")) {
				$settings["actions"][] = array("Text" => "Upload Daftar Harga", "Url" => "master.pricelists/upload", "Class" => "bt_excel", "ReqId" => 0);
			}
			$settings["actions"][] = array("Text" => "separator", "Url" => null);
			if ($acl->CheckUserAccess("master.pricelists", "view")) {
				$settings["actions"][] = array("Text" => "Daftar Harga Barang", "Url" => "master.pricelists/prices_list/xls", "Class" => "bt_excel", "ReqId" => 0);
			}

			$settings["def_order"] = 2;
			$settings["def_filter"] = 0;
			$settings["singleSelect"] = true;

		} else {
			$settings["from"] = "vw_m_itemprice AS a";
			$settings["where"] = "a.cabang_id = ".$this->userCabangId;
            $settings["order by"] = "a.bkode";
		}

		$dispatcher = Dispatcher::CreateInstance();
		$dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
	}

	public function add($pId = 0) {
		require_once(MODEL . "master/itemjenis.php");
		require_once(MODEL . "master/cabang.php");
		$setprices = new SetPrice();
		$log = new UserAdmin();
		$loader = null;
		if (count($this->postData) > 0) {
			$setprices->Id = $this->GetPostValue("Id");
			$setprices->CabangId = $this->GetPostValue("CabangId");
			$setprices->PriceDate = $this->GetPostValue("PriceDate");
			$setprices->ItemId = $this->GetPostValue("ItemId");
			$setprices->ItemCode = $this->GetPostValue("ItemCode");
			$setprices->MaxDisc = $this->GetPostValue("MaxDisc");
			$setprices->HrgBeli = $this->GetPostValue("HrgBeli");
			$setprices->HrgJual1 = $this->GetPostValue("HrgJual1");
			$setprices->HrgJual2 = $this->GetPostValue("HrgJual2");
			$setprices->HrgJual3 = $this->GetPostValue("HrgJual3");
            $setprices->HrgJual1k = $this->GetPostValue("HrgJual1k");
            $setprices->HrgJual2k = $this->GetPostValue("HrgJual2k");
            $setprices->HrgJual3k = $this->GetPostValue("HrgJual3k");
			//$setprices->HrgJual4 = $this->GetPostValue("HrgJual4");
			//$setprices->HrgJual5 = $this->GetPostValue("HrgJual5");
			//$setprices->HrgJual6 = $this->GetPostValue("HrgJual6");
			$setprices->Markup1 = $this->GetPostValue("Markup1");
			$setprices->Markup2 = $this->GetPostValue("Markup2");
			$setprices->Markup3 = $this->GetPostValue("Markup3");
            $setprices->nMarkup1 = $this->GetPostValue("nMarkup1");
            $setprices->nMarkup2 = $this->GetPostValue("nMarkup2");
            $setprices->nMarkup3 = $this->GetPostValue("nMarkup3");
			//$setprices->Markup4 = $this->GetPostValue("Markup4");
			//$setprices->Markup5 = $this->GetPostValue("Markup5");
			//$setprices->Markup6 = $this->GetPostValue("Markup6");
			$setprices->Satuan = $this->GetPostValue("SatBesar");
			if ($this->ValidateData($setprices)) {
				$setprices->CreatebyId = $this->userUid;
				$setprices->UpdatebyId = $this->userUid;
				// cek kalo sudah ada data harganya diupdate saja
				$priceId = 0;
				if ($setprices->Id == 0){
					$pricelist = new SetPrice();
					$priceId = $pricelist->FindPriceByKode($setprices->CabangId,$setprices->ItemCode);
				}else{
					$priceId = $setprices->Id;
				}
				if ($priceId > 0){
					$rs = $setprices->Update($priceId);
					if ($rs <> 0) {
						$log = $log->UserActivityWriter($this->userCabangId,'master.pricelists','Update Price -> Item Code: '.$setprices->ItemCode.' - Beli: '.$priceId->HrgBeli.' -> '.$setprices->HrgBeli.' - Jual: '.$priceId->HrgJual1.' -> '.$setprices->HrgJual1,'-','Success');
						$this->persistence->SaveState("info", sprintf("Data Harga: %s (%s) sudah berhasil diupdate", $setprices->ItemName, $setprices->ItemCode));
						redirect_url("master.pricelists");
					} else {
						$log = $log->UserActivityWriter($this->userCabangId,'master.pricelists','Update Price -> Item Code: '.$setprices->ItemCode.' - Beli: '.$priceId->HrgBeli.' -> '.$setprices->HrgBeli.' - Jual: '.$priceId->HrgJual1.' -> '.$setprices->HrgJual1,'-','Failed');
						$this->Set("error", "Gagal pada saat update data.. Message: " . $this->connector->GetErrorMessage(). ' ['.$rs.']');
					}
				}else {
					$rs = $setprices->Insert();
					if ($rs <> 0) {
						$log = $log->UserActivityWriter($this->userCabangId,'master.pricelists','Add Price -> Item Code: '.$setprices->ItemCode.' - Beli: '.$setprices->HrgBeli.' - Jual: '.$setprices->HrgJual1,'-','Success');
						$this->persistence->SaveState("info", sprintf("Data Barang: %s (%s) sudah berhasil disimpan", $setprices->ItemName, $setprices->ItemCode));
						redirect_url("master.pricelists");
					} else {
						$log = $log->UserActivityWriter($this->userCabangId,'master.pricelists','Add Price -> Item Code: '.$setprices->ItemCode.' - Beli: '.$setprices->HrgBeli.' - Jual: '.$setprices->HrgJual1,'-','Failed');
						$this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage(). ' ['.$rs.']');
					}
				}
			}
		}else{
			if ($pId > 0) {
				$setprices = $setprices->LoadById($pId);
			}else{
				$setprices->Id = 0;
			}
		}
		$loader = new Cabang();
		$cabang = $loader->LoadById($this->userCabangId);
		$cabCode = $cabang->Kode;
		$cabName = $cabang->Cabang;
		//send to form
		$this->Set("userLevel", $this->userLevel);
		$this->Set("userCompId", $this->userCompanyId);
		$this->Set("userCabId", $this->userCabangId);
		$this->Set("userCabCode", $cabCode);
		$this->Set("userCabName", $cabName);
		$this->Set("cabangs", $cabang);
		$this->Set("setprices", $setprices);
	}
	private function ValidateData(SetPrice $setprices) {
		return true;
	}

	public function delete($id = null) {
		if ($id == null) {
			$this->persistence->SaveState("error", "Anda harus memilih data harga sebelum melakukan hapus data !");
			redirect_url("master.pricelists");
		}
		$log = new UserAdmin();
		$prices = new SetPrice();
		$prices = $prices->FindById($id);
		if ($prices == null) {
			$this->persistence->SaveState("error", "Data harga yang dipilih tidak ditemukan ! Mungkin data sudah dihapus.");
			redirect_url("master.pricelists");
		}

		if ($prices->Delete($prices->Id) == 1) {
			$log = $log->UserActivityWriter($this->userCabangId,'master.pricelists','Delete Price -> Item Code: '.$prices->ItemCode.' - Beli: '.$prices->HrgBeli.' - Jual: '.$prices->HrgJual1,'-','Success');
			$this->persistence->SaveState("info", sprintf("Data Harga Barang: '%s' Dengan Kode: %s telah berhasil dihapus.", $prices->ItemName, $prices->ItemCode));
			redirect_url("master.pricelists");
		} else {
			$log = $log->UserActivityWriter($this->userCabangId,'master.pricelists','Delete Price -> Item Code: '.$prices->ItemCode.' - Beli: '.$prices->HrgBeli.' - Jual: '.$prices->HrgJual1,'-','Failed');
			$this->persistence->SaveState("error", sprintf("Gagal menghapus data harga: '%s'. Message: %s", $prices->ItemName, $this->connector->GetErrorMessage()));
		}
		redirect_url("master.pricelists");
	}

	public function prices_list($output){
		require_once(MODEL . "master/company.php");
		$company = new Company();
		$company = $company->LoadById($this->userCompanyId);
		$compname = $company->CompanyName;
		$items = new SetPrice();
		$items = $items->LoadAll($this->userCabangId);
		$this->Set("items", $items);
		$this->Set("output", $output);
        $this->Set("ulevel", $this->userLevel);
		$this->Set("company_name", $compname);
	}

	public function upload(){
		// untuk melakukan upload dan update data sparepart
		if (count($this->postData) > 0) {
			// Ada data yang di upload...
			$this->doUpload();
			redirect_url("master.pricelists");
		}
	}

	public function doUpload(){
		$prices = new SetPrice();
		$log = new UserAdmin();
		$uploadedFile = $this->GetPostValue("fileUpload");
		$processedData = 0;
		$infoMessages = array();	// Menyimpan info message yang akan di print
		$errorMessages = array();	// Menyimpan error message yang akan di print

		if ($uploadedFile["error"] !== 0) {
			$this->persistence->SaveState("error", "Gagal Upload file ke server !");
			return;
		}

		$tokens = explode(".", $uploadedFile["name"]);
		$ext = end($tokens);

		if ($ext != "xls" && $ext != "xlsx") {
			$this->persistence->SaveState("error", "File yang diupload bukan berupa file excel !");
			return;
		}

		// Load libs Excel
		require_once(LIBRARY . "PHPExcel.php");
		if ($ext == "xls") {
			$reader = new PHPExcel_Reader_Excel5();
		} else {
			$reader = new PHPExcel_Reader_Excel2007();
		}
		$phpExcel = $reader->load($uploadedFile["tmp_name"]);

		// OK baca file excelnya sekarang....

		// Step #01: Baca mapping kode shift
		$sheet = $phpExcel->getSheetByName("Data Harga");
		$maxRow = $sheet->getHighestRow();
		$startFrom = 4;
		$xprice = null;
		$nmr = 0;
		for ($i = $startFrom; $i <= $maxRow; $i++) {
			$nmr++;
			$pItemId = $sheet->getCellByColumnAndRow(1, $i)->getCalculatedValue();
			$pPriceDate = trim($sheet->getCellByColumnAndRow(2, $i)->getCalculatedValue());
			$pItemCode = trim($sheet->getCellByColumnAndRow(3, $i)->getCalculatedValue());
			$pItemName = trim($sheet->getCellByColumnAndRow(4, $i)->getCalculatedValue());
			$pSatuan = trim($sheet->getCellByColumnAndRow(5, $i)->getCalculatedValue());
			$pHrgBeli = $sheet->getCellByColumnAndRow(6, $i)->getCalculatedValue();
			$pMaxDisc = $sheet->getCellByColumnAndRow(7, $i)->getCalculatedValue();
			$pHrgJual1 = $sheet->getCellByColumnAndRow(8, $i)->getCalculatedValue();
			$pHrgJual2 = $sheet->getCellByColumnAndRow(9, $i)->getCalculatedValue();
			$pHrgJual3 = $sheet->getCellByColumnAndRow(10, $i)->getCalculatedValue();
			$pHrgJual4 = $sheet->getCellByColumnAndRow(11, $i)->getCalculatedValue();
			$pHrgJual5 = $sheet->getCellByColumnAndRow(12, $i)->getCalculatedValue();
			$pHrgJual6 = $sheet->getCellByColumnAndRow(13, $i)->getCalculatedValue();
			if ($pItemCode == "" || $pItemCode == null || $pItemCode == '-'){
				$infoMessages[] = sprintf("[%d] Kode Barang: -%s- tidak valid! Pastikan Kode Barang pada template sudah benar!",$nmr,$pItemCode);
				continue;
			}
			$prices->CabangId = $this->userCabangId;
			$prices->PriceDate = date('Y-m-d',time());
			$prices->ItemId = $pItemId;
			$prices->ItemCode = $pItemCode;
			$prices->Satuan = $pSatuan;
			$prices->HrgBeli = $pHrgBeli == null || $pHrgBeli == '' ? 0 : $pHrgBeli;
			$prices->MaxDisc = $pMaxDisc == null || $pMaxDisc == '' ? 0 : $pMaxDisc;
			$prices->HrgJual1 = $pHrgJual1 == null || $pHrgJual1 == '' ? 0 : $pHrgJual1;
			$prices->HrgJual2 = $pHrgJual2 == null || $pHrgJual2 == '' ? 0 : $pHrgJual2;
			$prices->HrgJual3 = $pHrgJual3 == null || $pHrgJual3 == '' ? 0 : $pHrgJual3;
			$prices->HrgJual4 = $pHrgJual4 == null || $pHrgJual4 == '' ? 0 : $pHrgJual4;
			$prices->HrgJual5 = $pHrgJual5 == null || $pHrgJual5 == '' ? 0 : $pHrgJual5;
			$prices->HrgJual6 = $pHrgJual6 == null || $pHrgJual6 == '' ? 0 : $pHrgJual6;
			$prices->CreatebyId = $this->userUid;
			$prices->UpdatebyId = $this->userUid;

			//cek apa ada perubahan data
			$xprice = new SetPrice();
			$xprice = $xprice->FindByKode($this->userCabangId,$pItemCode);
			$isupdate = true;
			if ($xprice != null){
				/** @var $xprice SetPrice */
				if (floatval($xprice->HrgBeli) == floatval($pHrgBeli) && floatval($xprice->MaxDisc) == floatval($pMaxDisc) && floatval($xprice->HrgJual1) == floatval($pHrgJual1) && floatval($xprice->HrgJual2) == floatval($pHrgJual2) && floatval($xprice->HrgJual3) == floatval($pHrgJual3) && floatval($xprice->HrgJual4) == floatval($pHrgJual4) && floatval($xprice->HrgJual5) == floatval($pHrgJual5) && floatval($xprice->HrgJual6) == floatval($pHrgJual6)){
					$isupdate = false;
				}
			}
			// mulai proses update
			if ($isupdate) {
				$this->connector->BeginTransaction();
				$hasError = false;
				$rs = $prices->DeleteByKode($this->userCabangId,$pItemCode);
				$rs = $prices->Insert();
				if ($rs != 1) {
					// Hmm error apa lagi ini ?? DBase related harusnya
					$errorMessages[] = sprintf("[%d] Gagal simpan Data Harga Barang-> Kode: %s - Nama: %s Message: %s", $nmr, $pItemCode, $pItemName, $this->connector->GetErrorMessage());
					$hasError = true;
					break;
				}
				// Step #06: Commit/Rollback transcation per karyawan...
				if ($hasError) {
					$this->connector->RollbackTransaction();
				} else {
					$this->connector->CommitTransaction();
					$processedData++;
				}
			}
		}

		// Step #07: Sudah selesai.... semua karyawan sudah diproses
		if (count($errorMessages) > 0) {
			$this->persistence->SaveState("error", sprintf('<ol style="margin: 0;"><li>%s</li></ol>', implode("</li><li>", $errorMessages)));
			$infoMessages[] = "Data Harga Barang yang ERROR tidak di-entry ke system sedangkan yang lainnya tetap dimasukkan.";
		}
		if ($processedData > 0){
			$log = $log->UserActivityWriter($this->userCabangId,'master.pricelists','Upload Price from excel file -> '.$processedData.' item(s) updated','-','Failed');
		}

		$infoMessages[] = "Proses Upload Data Barang selesai. Jumlah data yang diproses: " . $processedData;
		$this->persistence->SaveState("info", sprintf('<ol style="margin: 0;"><li>%s</li></ol>', implode("</li><li>", $infoMessages)));

		// Completed...
	}

	public function template(){
		// untuk melakukan download template
		$prices = new SetPrice();
		$prices = $prices->LoadAll($this->userCabangId,'a.item_name,a.item_code');
		$this->Set("prices",$prices);
	}
}

// End of file: items_controller.php
