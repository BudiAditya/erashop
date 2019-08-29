<?php
class TransferController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "inventory/transfer.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
        $this->trxMonth = $this->persistence->LoadState("acc_month");
        $this->trxYear = $this->persistence->LoadState("acc_year");
    }

    public function index() {
        $router = Router::GetInstance();
        $settings = array();

        $settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
        //$settings["columns"][] = array("name" => "a.entity_cd", "display" => "Entity", "width" => 30);
        $settings["columns"][] = array("name" => "a.cabang_code", "display" => "Dari Cabang", "width" => 100);
        $settings["columns"][] = array("name" => "a.npb_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.npb_no", "display" => "No. NPB", "width" => 100);
        $settings["columns"][] = array("name" => "a.to_cabang_code", "display" => "Ke Cabang", "width" => 100);
        $settings["columns"][] = array("name" => "a.npb_descs", "display" => "Keterangan", "width" => 300);
        $settings["columns"][] = array("name" => "a.admin_name", "display" => "Admin", "width" => 100);
        $settings["columns"][] = array("name" => "if(a.npb_status = 0,'Draft','Posted')", "display" => "Status", "width" => 40);

        $settings["filters"][] = array("name" => "a.cabang_code", "display" => "Dari Cabang");
        $settings["filters"][] = array("name" => "a.to_cabang_code", "display" => "Ke Cabang");
        $settings["filters"][] = array("name" => "a.npb_no", "display" => "No. NPB");
        $settings["filters"][] = array("name" => "a.npb_descs", "display" => "Keterangan");
        $settings["filters"][] = array("name" => "a.npb_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "if(a.npb_status = 0,'Draft','Posted')", "display" => "Status");

        $settings["def_filter"] = 0;
        $settings["def_transfer"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = true;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Pengiriman Barang Antar Cabang";

            if ($acl->CheckUserAccess("inventory.transfer", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "inventory.transfer/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("inventory.transfer", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "inventory.transfer/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Transfer terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data rekonsil",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("inventory.transfer", "delete")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "inventory.transfer/delete/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("inventory.transfer", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "inventory.transfer/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Transfer terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data rekonsil","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("inventory.transfer", "print")) {
                $settings["actions"][] = array("Text" => "Print Bukti", "Url" => "inventory.transfer/transfer_print","Class" => "bt_pdf", "ReqId" => 2, "Confirm" => "Cetak Bukti Transfer Stok yang dipilih?");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("inventory.transfer", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "inventory.transfer/report", "Class" => "bt_report", "ReqId" => 0);
            }
        } else {
            $settings["from"] = "vw_ic_transfer_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId ." And year(a.npb_date) = ".$this->trxYear." And month(a.npb_date) = ".$this->trxMonth;
            } else {
                $settings["where"] = "a.is_deleted = 0 And a.cabang_id = " . $this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

	/* Untuk entry data estimasi perbaikan dan penggantian spare part */
	public function add() {
        require_once(MODEL . "master/cabang.php");
        $loader = null;
        $log = new UserAdmin();
		$transfer = new Transfer();
        $transfer->CabangId = $this->userCabangId;
        if (count($this->postData) > 0) {
			$transfer->CabangId = $this->GetPostValue("CabangId");
			$transfer->NpbDate = $this->GetPostValue("NpbDate");
            $transfer->NpbNo = $this->GetPostValue("NpbNo");
            $transfer->NpbDescs = $this->GetPostValue("NpbDescs");
            $transfer->ToCabangId = $this->GetPostValue("ToCabangId");
            if ($this->GetPostValue("NpbStatus") == null || $this->GetPostValue("NpbStatus") == 0){
                $transfer->NpbStatus = 1;
            }else{
                $transfer->NpbStatus = $this->GetPostValue("NpbStatus");
            }
            $transfer->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateMaster($transfer)) {
                if ($transfer->NpbNo == null || $transfer->NpbNo == "-" || $transfer->NpbNo == ""){
                    $transfer->NpbNo = $transfer->GetNpbDocNo();
                }
                $rs = $transfer->Insert();
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->Set("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Add New Stock Transfer',$transfer->NpbNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Add New Stock Transfer',$transfer->NpbNo,'Success');
                    redirect_url("inventory.transfer/edit/".$transfer->Id);
                }
			}
		}
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        //kirim ke view
        $this->Set("cabangs", $cabang);
        $this->Set("transfer", $transfer);
	}

	private function ValidateMaster(Transfer $transfer) {
        if ($transfer->CabangId == 0 || $transfer->CabangId == null || $transfer->CabangId == ''){
            $this->Set("error", "Cabang/Gudang asal barang belum dipilih..");
            return false;
        }
        if ($transfer->ToCabangId == 0 || $transfer->ToCabangId == null || $transfer->ToCabangId == ''){
            $this->Set("error", "Cabang/Gudang tujuan barang belum dipilih..");
            return false;
        }
        if ($transfer->ToCabangId == $transfer->CabangId){
            $this->Set("error", "Cabang/Gudang asal dan tujuan barang tidak boleh sama..");
            return false;
        }
		return true;
	}

    public function edit($transferId = null) {
        require_once(MODEL . "master/cabang.php");
        $loader = null;
        $log = new UserAdmin();
        $transfer = new Transfer();
        if (count($this->postData) > 0) {
            $transfer->Id = $transferId;
            $transfer->CabangId = $this->GetPostValue("CabangId");
            $transfer->NpbDate = $this->GetPostValue("NpbDate");
            $transfer->NpbNo = $this->GetPostValue("NpbNo");
            $transfer->NpbDescs = $this->GetPostValue("NpbDescs");
            $transfer->ToCabangId = $this->GetPostValue("ToCabangId");
            if ($this->GetPostValue("NpbStatus") == null || $this->GetPostValue("NpbStatus") == 0){
                $transfer->NpbStatus = 1;
            }else{
                $transfer->NpbStatus = $this->GetPostValue("NpbStatus");
            }
            $transfer->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateMaster($transfer)) {
                $rs = $transfer->Update($transfer->Id);
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->persistence->SaveState("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Update Stock Transfer',$transfer->NpbNo,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Update Stock Transfer',$transfer->NpbNo,'Success');
                    $this->persistence->SaveState("info", sprintf("Data Transfer/Nota No.: '%s' Tanggal: %s telah berhasil diubah..", $transfer->NpbNo, $transfer->NpbDate));
                    redirect_url("inventory.transfer/edit/".$transfer->Id);
                }
            }
        }else{
            $transfer = $transfer->LoadById($transferId);
            if($transfer == null){
               $this->persistence->SaveState("error", "Maaf Data Transfer dimaksud tidak ada pada database. Mungkin sudah dihapus!");
               redirect_url("inventory.transfer");
            }
            if($transfer->NpbStatus == 2){
                $this->persistence->SaveState("error", sprintf("Maaf Data Transfer No. %s sudah berstatus -CLOSED-",$transfer->NpbNo));
                redirect_url("inventory.transfer");
            }
        }
        // load details
        $transfer->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        //kirim ke view
        $this->Set("cabangs", $cabang);
        $this->Set("transfer", $transfer);
    }

	public function view($transferId = null) {
        require_once(MODEL . "master/cabang.php");
        $loader = null;
        $transfer = new Transfer();
        $transfer = $transfer->LoadById($transferId);
        if($transfer == null){
            $this->persistence->SaveState("error", "Maaf Data Transfer dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("inventory.transfer");
        }
        // load details
        $transfer->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        //kirim ke view
        $this->Set("cabangs", $cabang);
        $this->Set("transfer", $transfer);
	}

    public function delete($transferId) {
        // Cek datanya
        $log = new UserAdmin();
        $transfer = new Transfer();
        $transfer = $transfer->FindById($transferId);
        if($transfer == null){
            $this->Set("error", "Maaf Data Transfer dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("inventory.transfer");
        }
        // periksa status po
        if($transfer->NpbStatus < 2){
            $transfer->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($transfer->Delete($transferId) == 1) {
                $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Delete Stock Transfer',$transfer->NpbNo,'Success');
                $this->persistence->SaveState("info", sprintf("Data Transfer No: %s sudah berhasil dihapus", $transfer->NpbNo));
            }else{
                $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Delete Stock Transfer',$transfer->NpbNo,'Failed');
                $this->persistence->SaveState("error", sprintf("Maaf, Data Transfer No: %s gagal dihapus", $transfer->NpbNo));
            }
        }else{
            $this->persistence->SaveState("error", sprintf("Maaf, Data Transfer No: %s sudah berstatus -CLOSED-", $transfer->NpbNo));
        }
        redirect_url("inventory.transfer");
    }

	public function add_detail($transferId = null) {
        require_once(MODEL . "master/items.php");
        $log = new UserAdmin();
        $transfer = new Transfer($transferId);
        $transferdetail = new TransferDetail();
        $transferdetail->NpbId = $transferId;
        $transferdetail->NpbNo = $transfer->NpbNo;
        $transferdetail->CabangId = $transfer->CabangId;
        $items = null;
        if (count($this->postData) > 0) {
            $transferdetail->ItemId = $this->GetPostValue("aItemId");
            $transferdetail->Qty = $this->GetPostValue("aQty");
            $items = new Items($transferdetail->ItemId);
            if ($items != null){
                $transferdetail->ItemCode = $items->Bkode;
                $transferdetail->ItemDescs = $items->Bnama;
                $transferdetail->Lqty = 0;
                $transferdetail->Sqty = 0;
                // insert ke table
                $rs = $transferdetail->Insert()== 1;
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Add Stock Transfer detail -> Item Code: '.$transferdetail->ItemCode.' = '.$transferdetail->Qty,$transfer->NpbNo,'Success');
                    echo json_encode(array());
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Add Stock Transfer detail -> Item Code: '.$transferdetail->ItemCode.' = '.$transferdetail->Qty,$transfer->NpbNo,'Failed');
                    echo json_encode(array('errorMsg'=>'Some errors occured.'));
                }
            }else{
                echo json_encode(array('errorMsg'=>'Data barang tidak ditemukan!'));
            }
        }
	}

    public function edit_detail($transferId = null) {
        require_once(MODEL . "master/items.php");
        $log = new UserAdmin();
        $transfer = new Transfer($transferId);
        $transferdetail = new TransferDetail();
        $transferdetail->NpbId = $transferId;
        $transferdetail->NpbNo = $transfer->NpbNo;
        $transferdetail->CabangId = $transfer->CabangId;
        $items = null;
        if (count($this->postData) > 0) {
            $transferdetail->Id = $this->GetPostValue("aId");
            $transferdetail->ItemId = $this->GetPostValue("aItemId");
            $transferdetail->Qty = $this->GetPostValue("aQty");
            $items = new Items($transferdetail->ItemId);
            if ($items != null){
                $transferdetail->ItemCode = $items->Bkode;
                $transferdetail->ItemDescs = $items->Bnama;
                // insert ke table
                $rs = $transferdetail->Update($transferdetail->Id);
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Update Stock Transfer detail -> Item Code: '.$transferdetail->ItemCode.' = '.$transferdetail->Qty,$transfer->NpbNo,'Success');
                    echo json_encode(array());
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Update Stock Transfer detail -> Item Code: '.$transferdetail->ItemCode.' = '.$transferdetail->Qty,$transfer->NpbNo,'Failed');
                    echo json_encode(array('errorMsg'=>'Some errors occured.'));
                }
            }else{
                echo json_encode(array('errorMsg'=>'Data barang tidak ditemukan!'));
            }
        }
    }

    public function delete_detail($id) {
        // Cek datanya
        $log = new UserAdmin();
        $transferdetail = new TransferDetail();
        $transferdetail = $transferdetail->FindById($id);
        if ($transferdetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($transferdetail->Delete($id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Delete Stock Transfer detail -> Item Code: '.$transferdetail->ItemCode.' = '.$transferdetail->Qty,$transferdetail->NpbNo,'Success');
            printf("Data Detail Transfer ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.transfer','Delete Stock Transfer detail -> Item Code: '.$transferdetail->ItemCode.' = '.$transferdetail->Qty,$transferdetail->NpbNo,'Failed');
            printf("Maaf, Data Detail Transfer ID: %d gagal dihapus!",$id);
        }
    }

    //proses cetak bukti stock transfer
    public function transfer_print() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Harap pilih data yang akan dicetak !");
            redirect_url("inventory.transfer");
            return;
        }
        $report = array();
        foreach ($ids as $id) {
            $trx = new Transfer();
            $trx = $trx->LoadById($id);
            $trx->LoadDetails();
            $report[] = $trx;
        }
        $this->Set("report", $report);
    }

    public function approve() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("inventory.transfer");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $transfer = new Transfer();
            $transfer = $transfer->FindById($id);
            /** @var $transfer Transfer */
            // process po
            if($transfer->NpbStatus == 0){
                $rs = $transfer->Approve($transfer->Id,$uid);
                if ($rs) {
                    $infos[] = sprintf("Data Transfer No.: '%s' (%s) telah berhasil di-approve.", $transfer->NpbNo, $transfer->NpbDescs);
                } else {
                    $errors[] = sprintf("Maaf, Gagal proses approve Data Transfer: '%s'. Message: %s", $transfer->NpbNo, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Transfer No.%s sudah berstatus -Posted- !",$transfer->NpbNo);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("inventory.transfer");
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $transfer = new Transfer();
            if ($sJnsLaporan == 1) {
                $reports = $transfer->Load4Reports($this->userCompanyId, $sCabangId, $sStartDate, $sEndDate);
            }else{
                $reports = $transfer->LoadRekap4Reports($this->userCompanyId, $sCabangId, $sStartDate, $sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sJnsLaporan = 1;
            $sOutput = 0;
            $reports = null;
        }
        $company = new Company($this->userCompanyId);
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        if ($this->userLevel > 3){
            $cabang = $loader->LoadByEntityId($this->userCompanyId);
        }else{
            $cabang = $loader->LoadById($this->userCabangId);
            $cabCode = $cabang->Kode;
            $cabName = $cabang->Cabang;
        }
        //kirim ke view
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("CabangId",$sCabangId);
        $this->Set("cabangs", $cabang);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
        $this->Set("company_name", $company->CompanyName);
    }
}


// End of File: estimasi_controller.php
