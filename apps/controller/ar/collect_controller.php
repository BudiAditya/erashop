<?php
class CollectController extends AppController {
    private $userCompanyId;
    private $userCabangId;

    protected function Initialize() {
        require_once(MODEL . "ar/collect.php");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
    }

    public function index() {
        $router = Router::GetInstance();
        $settings = array();

        $settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
        $settings["columns"][] = array("name" => "a.kd_cabang", "display" => "Cabang", "width" => 50);
        $settings["columns"][] = array("name" => "a.collect_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.collect_no", "display" => "No. Collect", "width" => 80);
        $settings["columns"][] = array("name" => "a.nm_collector", "display" => "Nama Collector", "width" => 150);
        $settings["columns"][] = array("name" => "format(a.collect_amount,0)", "display" => "Jumlah Tagihan", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.paid_amount,0)", "display" => "Jumlah Terbayar", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.collect_amount - a.paid_amount,0)", "display" => "Sisa", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "a.collect_descs", "display" => "Keterangan", "width" => 250);
        $settings["columns"][] = array("name" => "a.status_collect", "display" => "Status", "width" => 80);

        $settings["filters"][] = array("name" => "a.kd_cabang", "display" => "Kode Cabang");
        $settings["filters"][] = array("name" => "a.collect_no", "display" => "No. Collect");
        $settings["filters"][] = array("name" => "a.collect_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.nm_collector", "display" => "Nama Collector");
        $settings["filters"][] = array("name" => "a.collect_descs", "display" => "Keterangan");
        $settings["filters"][] = array("name" => "a.status_collect", "display" => "Status");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = false;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Penagihan";

            if ($acl->CheckUserAccess("ar.collect", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "ar.collect/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("ar.collect", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "ar.collect/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Collect terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data rekonsil",
                    "Confirm" => "Anda akan dibawa ke halaman untuk editing Collect.\nDetail data akan di-isi pada halaman berikutnya.\n\nKlik 'OK' untuk berpindah halaman.");
            }
            if ($acl->CheckUserAccess("ar.collect", "delete")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "ar.collect/delete/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("ar.collect", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "ar.collect/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Collect terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data rekonsil","Confirm" => "");
            }
/*
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.collect", "approve")) {
                $settings["actions"][] = array("Text" => "Approve Collect", "Url" => "ar.collect/approve", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Collect terlebih dahulu sebelum proses approval.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda menyetujui data collect yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            if ($acl->CheckUserAccess("ar.collect", "approve")) {
                $settings["actions"][] = array("Text" => "Batal Approve", "Url" => "ar.collect/unapprove", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Collect terlebih dahulu sebelum proses pembatalan.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda mau membatalkan approval data collect yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
*/
        } else {
            $settings["from"] = "vw_ar_collect_master AS a";
            //if ($_GET["query"] == "") {
            //    $_GET["query"] = null;
            //    $settings["where"] = "a.is_deleted = 0 AND (a.base_amount + a.tax_amount) > a.paid_amount";
            //} else {
                $settings["where"] = "a.is_deleted = 0";
            //}
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

	/* Untuk entry data estimasi perbaikan dan penggantian spare part */
	public function add() {
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");

        $loader = null;
		$collect = new Collect();
        $collect->CabangId = $this->userCabangId;

		if (count($this->postData) > 0) {
			$collect->CabangId = $this->GetPostValue("CabangId");
			$collect->CollectDate = $this->GetPostValue("CollectDate");
			$collect->CollectNo = $this->GetPostValue("CollectNo");
            $collect->CollectDescs = $this->GetPostValue("CollectDescs");
            $collect->CollectorId = $this->GetPostValue("CollectorId");
            $collect->CollectAmount = $this->GetPostValue("CollectAmount");
            $collect->PaidAmount = $this->GetPostValue("PaidAmount");
            if ($this->GetPostValue("CollectStatus") == null || $this->GetPostValue("CollectStatus") == 0){
                $collect->CollectStatus = 1;
            }else{
                $collect->CollectStatus = $this->GetPostValue("CollectStatus");
            }
            $collect->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
			if ($this->ValidateMaster($collect)) {
                if ($collect->CollectNo == null || $collect->CollectNo == "-" || $collect->CollectNo == ""){
                    $collect->CollectNo = $collect->GetCollectDocNo();
                }
                $rs = $collect->Insert();
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->Set("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                }else{
                    redirect_url("ar.collect/edit/".$collect->Id);
                    //$this->persistence->SaveState("info", sprintf("Data Penagihan No.: '%s' Tanggal: %s telah berhasil disimpan..", $collect->CollectNo, $collect->CollectDate));
                    //redirect_url("ar.collect");
                }
			}
		}
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Karyawan();
        $collector = $loader->LoadAll();
        //kirim ke view
        $this->Set("collector", $collector);
        $this->Set("cabangs", $cabang);
        $this->Set("collect", $collect);
	}

	private function ValidateMaster(Collect $collect) {
		return true;
	}

    public function edit($collectId = null) {
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");

        $loader = null;
        $outinvoices = null;
        $collect = new Collect();
        if (count($this->postData) > 0) {
            $collect->Id = $collectId;
            $collect->CabangId = $this->GetPostValue("CabangId");
            $collect->CollectDate = $this->GetPostValue("CollectDate");
            $collect->CollectNo = $this->GetPostValue("CollectNo");
            $collect->CollectDescs = $this->GetPostValue("CollectDescs");
            $collect->CollectorId = $this->GetPostValue("CollectorId");
            $collect->CollectAmount = $this->GetPostValue("CollectAmount");
            $collect->PaidAmount = $this->GetPostValue("PaidAmount");
            if ($this->GetPostValue("CollectStatus") == null || $this->GetPostValue("CollectStatus") == 0){
                $collect->CollectStatus = 1;
            }else{
                $collect->CollectStatus = $this->GetPostValue("CollectStatus");
            }
            $collect->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($this->ValidateMaster($collect)) {
                $rs = $collect->Update($collect->Id);
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->persistence->SaveState("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                }else{
                    $this->persistence->SaveState("info", sprintf("Data Collect/Nota Tagihan No.: '%s' Tanggal: %s telah berhasil diubah..", $collect->CollectNo, $collect->CollectDate));
                    redirect_url("ar.collect");
                }
            }
        }else{
            $collect = $collect->LoadById($collectId);
            if($collect == null){
               $this->persistence->SaveState("error", "Maaf Data Collect dimaksud tidak ada pada database. Mungkin sudah dihapus!");
               redirect_url("ar.collect");
            }
            $loader = new Collect();
            $outinvoices = $loader->LoadDueDateInvoice($collect->CabangId,$collect->CollectDate,-2);
        }
        // load details
        $collect->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Karyawan();
        $collector = $loader->LoadAll();
        //kirim ke view
        $this->Set("collector", $collector);
        $this->Set("cabangs", $cabang);
        $this->Set("collect", $collect);
        $this->Set("outinvoices", $outinvoices);
    }

	public function view($collectId = null) {
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        $loader = null;
        $collect = new Collect();
        $collect = $collect->LoadById($collectId);
        if($collect == null){
            $this->Set("error", "Maaf Data Collect dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.collect");
        }
        // load details
        $collect->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        $loader = new Karyawan();
        $collector = $loader->LoadAll();
        //kirim ke view
        $this->Set("collector", $collector);
        $this->Set("cabangs", $cabang);
        $this->Set("collect", $collect);
	}

    public function delete($collectId) {
        // Cek datanya
        $collect = new Collect();
        $collect = $collect->FindById($collectId);
        if($collect == null){
            $this->Set("error", "Maaf Data Penagihan dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.collect");
        }
        $colldetail = new CollectDetail();
        $colldetail = $colldetail->LoadByCollectId($collectId);
        if($colldetail != null){
            $this->Set("error", "Maaf Detail Penagihan masih ada. Silahkan dihapus detailnya dulu!");
            redirect_url("ar.collect");
        }
        /** @var $collect Collect */
        // periksa status collect
        if($collect->CollectStatus < 2){
            $collect->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if ($collect->Delete($collectId) == 1) {
                $this->persistence->SaveState("info", sprintf("Data Collect No: %s sudah berhasil dihapus", $collect->CollectNo));
            }else{
                $this->persistence->SaveState("error", sprintf("Maaf, Data Collect No: %s gagal dihapus", $collect->CollectNo));
            }
        }else{
            $this->persistence->SaveState("error", sprintf("Maaf, Data Collect No: %s sudah berstatus -CLOSE-", $collect->CollectNo));
        }
        redirect_url("ar.collect");
    }

	public function add_detail($collectId = null) {
        $collect = new Collect($collectId);
        if (count($this->postData) > 0) {
            $ivcpilih = $this->GetPostValue("pilihInvoices");
            $sqn = 0;
            $oke = 0;
            $tokens = null;
            $invoices = null;
            $coldetail = null;
            foreach ($ivcpilih as $invoices){
                $sqn++;
                $tokens = explode("|",$invoices);
                $coldetail = new CollectDetail();
                $coldetail->DeleteByInvoiceId($collectId,$tokens[0]);
                $coldetail = new CollectDetail();
                $coldetail->CollectId = $collectId;
                $coldetail->SeqNo = $sqn;
                $coldetail->InvoiceId = $tokens[0];
                $coldetail->OutstandingAmount = $tokens[1];
                $coldetail->PaidAmount = $tokens[2];
                $coldetail->DetailStatus = 1;
                if ($coldetail->Insert()== 1){
                    $oke++;
                }
            }
            if ($oke > 0){
                printf('Simpan %d data berhasil..',$oke);
            }else{
                print('Simpan data gagal..');
            }
        }else{
            print('Maaf, Belum ada data invoice yang dipilih..');
        }
	}

    public function edit_detail($collectId = 0) {
        $collect = new Collect($collectId);
        $coldetail = null;
        if (count($this->postData) > 0) {
            $did = $this->GetPostValue("dId");
            $ivi = 0;
            $sqn = 0;
            $coldetail = new CollectDetail();
            $coldetail->FindById($did);
            if($coldetail != null){
                $sqn = $coldetail->SeqNo;
                $ivi = $coldetail->InvoiceId;
                $coldetail = new CollectDetail();
                $coldetail->CollectId = $collectId;
                $coldetail->SeqNo = $sqn;
                $coldetail->InvoiceId = $ivi;
                $coldetail->OutstandingAmount = $this->GetPostValue("dOutstandingAmount");
                $coldetail->PaidAmount = $this->GetPostValue("dPaidAmount");
                $coldetail->DetailStatus = $this->GetPostValue("dDetailStatus");
                $coldetail->RecollectDate = $this->GetPostValue("dRecollectDate");
                $rs = $coldetail->Update($did);
                if ($rs){
                    print('Update data berhasil..');
                }else{
                    print('Maaf, Update data gagal..');
                }
            }
        }else{
            print('Maaf, Belum ada data invoice yang dipilih..');
        }
    }

    public function delete_detail($id) {
        // Cek datanya
        $coldetail = new CollectDetail();
        $coldetail = $coldetail->FindById($id);
        if ($coldetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($coldetail->Delete($id) == 1) {
            printf("Data Detail Collect ID: %d berhasil dihapus!",$id);
        }else{
            printf("Maaf, Data Detail Collect ID: %d gagal dihapus!",$id);
        }
    }

    public function print_pdf($collectId = null) {
        require_once(MODEL . "trx/rekonsil.php");
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/asuransi.php");
        require_once(MODEL . "master/plservice.php");
        require_once(MODEL . "master/sparepart.php");
        require_once(MODEL . "master/contacts.php");

        // Cek datanya
        $collect = new Collect();
        $collect = $collect->LoadById($collectId);
        if ($collect == null) {
            redirect_url("trx.estimasi");
            return;
        }
        $loader = null;
        $rekonsil = new Collect($collect->EntityId);
        // Untuk Detail yang lainnya kita dynamic loading saja....
        $collect->LoadDetails();
        $services = array();
        $parts = array();
        foreach ($collect->Details as $detail) {
            if (!array_key_exists($detail->ServiceId, $services)) {
                $services[$detail->ServiceId] = new PlService($detail->ServiceId);
            }
            if (!array_key_exists($detail->PartId, $parts)) {
                $parts[$detail->PartId] = new SparePart($detail->PartId);
            }
        }
        $loader = new PlService();
        $plservice = $loader->LoadAll();
        $loader = new SparePart();
        if($rekonsil->Merk == null){
            $sparepart = $loader->LoadAll();
        }else{
            $sparepart = $loader->LoadByMerk($rekonsil->Merk);
        }
        $asuransi = new Asuransi($rekonsil->CollectTypeId);
        $customer = new Customer($rekonsil->CustomerId);
        $loader = new Company($rekonsil->EntityId);
        $this->Set("company_name", $loader->CompanyName);
        $this->Set("rekonsil",$rekonsil);
        $this->Set("estimasi", $collect);
        $this->Set("plservices", $plservice);
        $this->Set("spareparts", $sparepart);
        $this->Set("services",$services);
        $this->Set("parts",$parts);
        $this->Set("asuransi",$asuransi);
        $this->Set("customer",$customer);
        $loader = new CollectDetail();
        $qcrepair = $loader->GetSumByType(1,$collectId);
        $qcpart = $loader->GetSumByType(2,$collectId);
        $this->Set("qcrepair",$qcrepair);
        $this->Set("qcpart",$qcpart);
    }

    public function approve() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ar.collect");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $collect = new Collect();
            $collect = $collect->FindById($id);
            /** @var $collect Collect */
            // process collect
            if($collect->CollectStatus == 0){
                $rs = $collect->Approve($collect->Id,$uid);
                if ($rs) {
                    $infos[] = sprintf("Data Collect No.: '%s' (%s) telah berhasil di-approve.", $collect->CollectNo, $collect->CollectDescs);
                } else {
                    $errors[] = sprintf("Maaf, Gagal proses approve Data Collect: '%s'. Message: %s", $collect->CollectNo, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Collect No.%s sudah berstatus -Posted- !",$collect->CollectNo);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ar.collect");
    }

    public function unapprove() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ar.collect");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $collect = new Collect();
            $collect = $collect->FindById($id);
            /** @var $collect Collect */
            // process collect
            if($collect->CollectStatus == 1){
                $rs = $collect->Unapprove($collect->Id,$uid);
                if ($rs) {
                    $infos[] = sprintf("Data Collect No.: '%s' (%s) telah berhasil di-batalkan.", $collect->CollectNo, $collect->CollectDescs);
                } else {
                    $errors[] = sprintf("Maaf, Gagal proses pembatalan Data Collect: '%s'. Message: %s", $collect->CollectNo, $this->connector->GetErrorMessage());
                }
            }else{
                if ($collect->CollectStatus == 2){
                    $errors[] = sprintf("Data Collect No.%s sudah terbayar !",$collect->CollectNo);
                }else{
                    $errors[] = sprintf("Data Collect No.%s masih berstatus -Draft- !",$collect->CollectNo);
                }
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ar.collect");
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/itemjenis.php");
        require_once(MODEL . "master/karyawan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sJnsBarangId = $this->GetPostValue("JnsBarangId");
            $sCabangId = $this->GetPostValue("CabangId");
            $sCustomerId = $this->GetPostValue("CustomerId");
            $sCollectorId = $this->GetPostValue("CollectorId");
            $sStatus = $this->GetPostValue("Status");
            $sPaymentStatus = $this->GetPostValue("PaymentStatus");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $collect = new Collect();
            $reports = $collect->Load4Reports($sCabangId,$sJnsBarangId,$sCustomerId,$sCollectorId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
        }else{
            $sCabangId = 0;
            $sJnsBarangId = 0;
            $sCustomerId = 0;
            $sCollectorId = 0;
            $sStatus = -1;
            $sPaymentStatus = -1;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sOutput = 0;
            $reports = null;
        }
        $customer = new Customer();
        $customer = $customer->LoadAll();
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
        $loader = new JenisBarang();
        $jnsbarang = $loader->LoadAll();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadByEntityId($this->userCompanyId);
        // kirim ke view
        $this->Set("cabangs", $cabang);
        $this->Set("customers",$customer);
        $this->Set("jnsbarang",$jnsbarang);
        $this->Set("sales",$sales);
        $this->Set("JnsBarangId",$sJnsBarangId);
        $this->Set("CabangId",$sCabangId);
        $this->Set("CustomerId",$sCustomerId);
        $this->Set("CollectorId",$sCollectorId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Status",$sStatus);
        $this->Set("PaymentStatus",$sPaymentStatus);
        $this->Set("Output",$sOutput);
        $this->Set("Reports",$reports);
    }
}


// End of File: estimasi_controller.php
