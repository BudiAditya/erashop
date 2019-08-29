<?php
class JurnalController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userAccMonth;
    private $userAccYear;

    protected function Initialize() {
        require_once(MODEL . "accounting/jurnal.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userAccMonth = $this->persistence->LoadState("acc_month");
        $this->userAccYear = $this->persistence->LoadState("acc_year");
    }

    public function index() {
        $router = Router::GetInstance();
        $settings = array();

        $settings["columns"][] = array("name" => "a.id", "display" => "ID", "width" => 40);
        $settings["columns"][] = array("name" => "a.no_voucher", "display" => "No. Jurnal", "width" => 80);
        $settings["columns"][] = array("name" => "a.tgl_voucher", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.keterangan", "display" => "Keterangan", "width" => 400);
        $settings["columns"][] = array("name" => "a.reff_no", "display" => "No. Refferensi", "width" => 80);
        $settings["columns"][] = array("name" => "a.reff_source", "display" => "Sumber Data", "width" => 100);
        $settings["columns"][] = array("name" => "format(a.doc_amount,0)", "display" => "Jumlah", "width" => 100, "align" => "right");
        $settings["columns"][] = array("name" => "if(a.doc_status = 0,'Draft',if(a.doc_status = 1, 'Approved','Verified'))", "display" => "Status", "width" => 50);
        $settings["columns"][] = array("name" => "if(a.create_mode = 0,'Manual','Auto')", "display" => "Mode", "width" => 50);

        $settings["filters"][] = array("name" => "a.no_voucher", "display" => "No. Jurnal");
        $settings["filters"][] = array("name" => "a.tgl_voucher", "display" => "Tanggal Jurnal");
        $settings["filters"][] = array("name" => "a.keterangan", "display" => "Keterangan");
        $settings["filters"][] = array("name" => "a.kd_voucher", "display" => "Jenis Jurnal");
        $settings["filters"][] = array("name" => "if(a.doc_status = 0,'Draft',if(a.doc_status = 1, 'Approved','Verified'))", "display" => "Status");
        $settings["filters"][] = array("name" => "if(a.create_mode = 0,'Manual','Auto')", "display" => "Mode");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 2;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = false;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Jurnal Akuntansi";

            if ($acl->CheckUserAccess("accounting.jurnal", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "accounting.jurnal/add", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("accounting.jurnal", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "accounting.jurnal/edit/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Jurnal terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data rekonsil",
                    "Confirm" => "Anda akan dibawa ke halaman untuk editing Jurnal.\nDetail data akan di-isi pada halaman berikutnya.\n\nKlik 'OK' untuk berpindah halaman.");
            }
            if ($acl->CheckUserAccess("accounting.jurnal", "delete")) {
                $settings["actions"][] = array("Text" => "Delete", "Url" => "accounting.jurnal/delete/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("accounting.jurnal", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "accounting.jurnal/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Jurnal terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data rekonsil","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("accounting.jurnal", "approve")) {
                $settings["actions"][] = array("Text" => "Approve Jurnal", "Url" => "accounting.jurnal/approve", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Jurnal terlebih dahulu sebelum proses approval.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda menyetujui data jurnal yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            if ($acl->CheckUserAccess("accounting.jurnal", "approve")) {
                $settings["actions"][] = array("Text" => "Batal Approve", "Url" => "accounting.jurnal/unapprove", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Jurnal terlebih dahulu sebelum proses pembatalan.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda mau membatalkan approval data jurnal yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("accounting.jurnal", "verify")) {
                $settings["actions"][] = array("Text" => "Verifikasi Jurnal", "Url" => "accounting.jurnal/verify", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Jurnal terlebih dahulu sebelum proses approval.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda menyetujui data jurnal yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            if ($acl->CheckUserAccess("accounting.jurnal", "verify")) {
                $settings["actions"][] = array("Text" => "Batal Verifikasi", "Url" => "accounting.jurnal/unverify", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Jurnal terlebih dahulu sebelum proses pembatalan.\nPERHATIAN: Mohon memilih tepat satu data.",
                    "Confirm" => "Apakah anda mau membatalkan approval data jurnal yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }

        } else {
            $settings["from"] = "vw_gl_vouchermaster AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "(month(a.tgl_voucher) = ".$this->userAccMonth." And year(a.tgl_voucher) = ".$this->userAccYear.") And doc_status < 3 And a.entity_id = ".$this->userCompanyId." And a.cabang_id = ".$this->userCabangId;
            } else {
                //$settings["where"] = "a.entity_id = ".$this->userCompanyId." And a.cabang_id = ".$this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

	/* Untuk entry data estimasi perbaikan dan penggantian spare part */
	public function add() {
        $loader = null;
        $log = new UserAdmin();
		$jurnal = new Jurnal();

		if (count($this->postData) > 0) {
            $jurnal->EntityId = $this->userCompanyId;
            $jurnal->CabangId = $this->userCabangId;
			$jurnal->TglVoucher = $this->GetPostValue("TglVoucher");
			$jurnal->NoVoucher = $this->GetPostValue("NoVoucher");
			$jurnal->KdVoucher = $this->GetPostValue("KdVoucher");
            $jurnal->CreateMode = 0;
            $jurnal->Keterangan = $this->GetPostValue("Keterangan");
            $jurnal->DocStatus = $this->GetPostValue("DocStatus");
            $jurnal->ReffNo = $this->GetPostValue("ReffNo");
            $jurnal->ReffSource = $this->GetPostValue("ReffSource");
            $jurnal->DocAmount = $this->GetPostValue("DocAmount");
            $jurnal->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;

			if ($this->ValidateMaster($jurnal)) {
                $jurnal->NoVoucher = $jurnal->GetJurnalDocNo($jurnal->KdVoucher);
                $rs = $jurnal->Insert();
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->Set("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Add New Jurnal',$jurnal->NoVoucher,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Add New Jurnal',$jurnal->NoVoucher,'Success');
                    redirect_url("accounting.jurnal/edit/".$jurnal->Id);
                }

			}
		}
        // load data company for combo box
        $loader = new Jurnal();
        $jurnaltype = $loader->LoadVoucherType();
        //kirim ke view
        $this->Set("jurnal", $jurnal);
        $this->Set("vouchertypes", $jurnaltype);
	}

	private function ValidateMaster(Jurnal $jurnal) {
		return true;
	}

    public function edit($jurnalId = null) {
        require_once(MODEL . "master/coadetail.php");

        $loader = null;
        $log = new UserAdmin();
        $jurnal = new Jurnal();
        if (count($this->postData) > 0) {
            $jurnal->Id = $this->GetPostValue("Id");
            $jurnal->EntityId = $this->userCompanyId;
            $jurnal->CabangId = $this->userCabangId;
            $jurnal->TglVoucher = $this->GetPostValue("TglVoucher");
            $jurnal->NoVoucher = $this->GetPostValue("NoVoucher");
            $jurnal->KdVoucher = $this->GetPostValue("KdVoucher");
            $jurnal->CreateMode = 0;
            $jurnal->Keterangan = $this->GetPostValue("Keterangan");
            $jurnal->ReffNo = $this->GetPostValue("ReffNo");
            $jurnal->ReffSource = $this->GetPostValue("ReffSource");
            $jurnal->DocStatus = $this->GetPostValue("DocStatus");
            $jurnal->DocAmount = $this->GetPostValue("DocAmount");
            $jurnal->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;

            if ($this->ValidateMaster($jurnal)) {
                $rs = $jurnal->Update($jurnal->Id);
                if ($rs != 1) {
                    if ($this->connector->IsDuplicateError()) {
                        $this->Set("error", "Maaf Nomor Dokumen sudah ada pada database.");
                    } else {
                        $this->persistence->SaveState("error", "Maaf error saat simpan master dokumen. Message: " . $this->connector->GetErrorMessage());
                    }
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Update Jurnal',$jurnal->NoVoucher,'Failed');
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Update Jurnal',$jurnal->NoVoucher,'Success');
                    redirect_url("accounting.jurnal");
                }
            }
        }else{
            $jurnal = $jurnal->LoadById($jurnalId);
            if($jurnal == null){
               $this->persistence->SaveState("error", "Maaf Data Jurnal dimaksud tidak ada pada database. Mungkin sudah dihapus!");
               redirect_url("accounting.jurnal");
            }
            if($jurnal->DocStatus > 0){
                $this->persistence->SaveState("error", sprintf("Maaf Data Jurnal No. %s tidak berstatus -DRAFT-",$jurnal->NoVoucher));
                redirect_url("accounting.jurnal");
            }
        }
        // load details
        $jurnal->LoadDetails();
        // load data company for combo box
        $loader = new Jurnal();
        $jurnaltype = $loader->LoadVoucherType();
        $loader = new CoaDetail();
        $coas = $loader->LoadAll($this->userCompanyId);
        //kirim ke view
        $this->Set("jurnal", $jurnal);
        $this->Set("vouchertypes", $jurnaltype);
        $this->Set("coas", $coas);
    }

	public function view($jurnalId = null) {
        require_once(MODEL . "master/coadetail.php");

        $loader = null;
        $jurnal = new Jurnal();
        $jurnal = $jurnal->LoadById($jurnalId);
        if($jurnal == null){
            $this->persistence->SaveState("error", "Maaf Data Jurnal dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("accounting.jurnal");
        }
        // load details
        $jurnal->LoadDetails();
        $loader = new Jurnal();
        $jurnaltype = $loader->LoadVoucherType();
        //kirim ke view
        $this->Set("jurnal", $jurnal);
        $this->Set("vouchertypes", $jurnaltype);
	}

    public function delete($jurnalId) {
        // Cek datanya
        $log = new UserAdmin();
        $jurnal = new Jurnal();
        $jurnal = $jurnal->FindById($jurnalId);
        if($jurnal == null){
            $this->Set("error", "Maaf Data Jurnal dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("accounting.jurnal");
        }
        /** @var $jurnal Jurnal */
        // periksa status jurnal
        if($jurnal->DocStatus == 0){
            if ($jurnal->Delete($jurnalId) == 1) {
                $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Delete Jurnal',$jurnal->NoVoucher,'Success');
                $this->persistence->SaveState("info", sprintf("Data Jurnal No: %s sudah berhasil dihapus", $jurnal->NoVoucher));
            }else{
                $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Delete Jurnal',$jurnal->NoVoucher,'Failed');
                $this->persistence->SaveState("error", sprintf("Maaf, Data Jurnal No: %s gagal dihapus", $jurnal->NoVoucher));
            }
        }else{
            $this->persistence->SaveState("error", sprintf("Maaf, Data Jurnal No: %s sudah berstatus -POSTED-", $jurnal->NoVoucher));
        }
        redirect_url("accounting.jurnal");
    }

	public function add_detail($jurnalId = null) {
        require_once(MODEL . "master/coadetail.php");
        $log = new UserAdmin();
        $jurnal = new Jurnal($jurnalId);
        $jurdetail = new JurnalDetail();
        $jurdetail->NoVoucher = $jurnal->NoVoucher;
        $coa = null;
        if (count($this->postData) > 0) {
            $jurdetail->Uraian = $this->GetPostValue("Uraian");
            $jurdetail->AcDebetNo = $this->GetPostValue("AcDebetNo");
            $jurdetail->AcKreditNo = $this->GetPostValue("AcKreditNo");
            $jurdetail->Jumlah= $this->GetPostValue("Jumlah");
            $jurdetail->NoUrut = 1;
            $jurdetail->CabangId = $this->userCabangId;
            // insert ke table
            if ($jurdetail->Insert($this->userCompanyId,$this->userCabangId)== 1){
                $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Add Jurnal detail -> Akun: '.$jurdetail->AcDebetNo.' - '.$jurdetail->AcKreditNo.' -> '.$jurdetail->Uraian.' = '.$jurdetail->Jumlah,$jurdetail->NoVoucher,'Success');
                print('Simpan data berhasil.. ID:'.$jurnalId);
            }else{
                $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Add Jurnal detail -> Akun: '.$jurdetail->AcDebetNo.' - '.$jurdetail->AcKreditNo.' -> '.$jurdetail->Uraian.' = '.$jurdetail->Jumlah,$jurdetail->NoVoucher,'Failed');
                print('Simpan data gagal.. ID:'.$jurnalId.' Error:'.$this->connector->GetErrorMessage());
            }
        }
	}

    public function delete_detail($id) {
        // Cek datanya
        $log = new UserAdmin();
        $jurdetail = new JurnalDetail();
        $jurdetail = $jurdetail->FindById($id);
        if ($jurdetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($jurdetail->Delete($this->userCompanyId,$this->userCabangId,$id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Delete Jurnal detail -> Akun: '.$jurdetail->AcDebetNo.' - '.$jurdetail->AcKreditNo.' -> '.$jurdetail->Uraian.' = '.$jurdetail->Jumlah,$jurdetail->NoVoucher,'Success');
            printf("Data Detail Jurnal ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Delete Jurnal detail -> Akun: '.$jurdetail->AcDebetNo.' - '.$jurdetail->AcKreditNo.' -> '.$jurdetail->Uraian.' = '.$jurdetail->Jumlah,$jurdetail->NoVoucher,'Failed');
            printf("Maaf, Data Detail Jurnal ID: %d gagal dihapus!",$id);
        }
    }

    public function approve() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("accounting.jurnal");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $log = new UserAdmin();
            $jurnal = new Jurnal();
            $jurnal = $jurnal->FindById($id);
            /** @var $jurnal Jurnal */
            // process jurnal
            if($jurnal->DocStatus == 0){
                $rs = $jurnal->Approve($jurnal->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Approve Jurnal',$jurnal->NoVoucher,'Success');
                    $infos[] = sprintf("Data Jurnal No.: '%s' (%s) telah berhasil di-approve.", $jurnal->NoVoucher, $jurnal->Keterangan);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Approve Jurnal',$jurnal->NoVoucher,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses approve Data Jurnal: '%s'. Message: %s", $jurnal->NoVoucher, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Jurnal No.%s bukan berstatus -Draft- !",$jurnal->NoVoucher);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("accounting.jurnal");
    }

    public function unapprove() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di un-approve !");
            redirect_url("accounting.jurnal");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $log = new UserAdmin();
            $jurnal = new Jurnal();
            $jurnal = $jurnal->FindById($id);
            /** @var $jurnal Jurnal */
            // process jurnal
            if($jurnal->DocStatus == 1){
                $rs = $jurnal->Unapprove($jurnal->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Un-approve Jurnal',$jurnal->NoVoucher,'Success');
                    $infos[] = sprintf("Data Jurnal No.: '%s' (%s) telah berhasil di-batalkan.", $jurnal->NoVoucher, $jurnal->Keterangan);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Un-approve Jurnal',$jurnal->NoVoucher,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses pembatalan Data Jurnal: '%s'. Message: %s", $jurnal->NoVoucher, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Jurnal No.%s bukan berstatus -Approved- !",$jurnal->NoVoucher);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("accounting.jurnal");
    }

    public function verify() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di verifikasi !");
            redirect_url("accounting.jurnal");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $log = new UserAdmin();
            $jurnal = new Jurnal();
            $jurnal = $jurnal->FindById($id);
            /** @var $jurnal Jurnal */
            // process jurnal
            if($jurnal->DocStatus == 1){
                $rs = $jurnal->Verify($jurnal->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Verify Jurnal',$jurnal->NoVoucher,'Success');
                    $infos[] = sprintf("Data Jurnal No.: '%s' (%s) telah berhasil di-Verifikasi.", $jurnal->NoVoucher, $jurnal->Keterangan);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Verify Jurnal',$jurnal->NoVoucher,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses verifikasi Data Jurnal: '%s'. Message: %s", $jurnal->NoVoucher, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Jurnal No.%s bukan berstatus -Approved- !",$jurnal->NoVoucher);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("accounting.jurnal");
    }

    public function unverify() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di unverify !");
            redirect_url("accounting.jurnal");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $log = new UserAdmin();
            $jurnal = new Jurnal();
            $jurnal = $jurnal->FindById($id);
            /** @var $jurnal Jurnal */
            // process jurnal
            if($jurnal->DocStatus == 2){
                $rs = $jurnal->Unverify($jurnal->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Un-verify Jurnal',$jurnal->NoVoucher,'Success');
                    $infos[] = sprintf("Data Jurnal No.: '%s' (%s) telah berhasil di-batalkan.", $jurnal->NoVoucher, $jurnal->Keterangan);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'accounting.jurnal','Un-verify Jurnal',$jurnal->NoVoucher,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses pembatalan Data Jurnal: '%s'. Message: %s", $jurnal->NoVoucher, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Jurnal No.%s masih berstatus -Approved- !",$jurnal->NoVoucher);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("accounting.jurnal");
    }

    public function _print() {
        $ids = $this->GetGetValue("id", array());
        $output = $this->GetNamedValue("output", "pdf");
        set_time_limit(600);

        $result = array();
        foreach ($ids as $id) {
            $jurnal = new Jurnal();
            $jurnal->LoadById($id);
            $jurnal->LoadDetails();
            $jurnal->LoadCompany();
            $jurnal->LoadVoucherTypes();
/*
            foreach($jurnal->Details as $detail) {
                $detail->LoadAccount();
                $detail->LoadDept();
            }
*/
            $result[] = $jurnal;
        }

        $this->Set("output", $output);
        $this->Set("report", $result);
    }

    public function print_all() {
        $sortableColumns = array(
            array("column" => "b.doc_code", "display" => "Jenis Dokumen"),
            array("column" => "a.no_voucher", "display" => "Nomor Dokumen"),
            array("column" => "a.tgl_voucher", "display" => "Tgl Dokumen")
        );

        if (count($this->getData) > 0) {
            $start = strtotime($this->GetGetValue("start"));
            $end = strtotime($this->GetGetValue("end"));
            $status = $this->GetGetValue("status", -1);
            $sort1 = $this->GetGetValue("sort1", 0);
            $sort2 = $this->GetGetValue("sort2", 0);
            $sort3 = $this->GetGetValue("sort3", 0);

            // Pastikan tidak ada yang iseng
            $sort1 = min($sort1, 2);
            $sort2 = min($sort2, 2);
            $sort3 = min($sort3, 2);

            $where = "";
            if ($status != -1) {
                $where .= " AND a.doc_status = ?status";
                $this->connector->AddParameter("?status", $status);
            }

            $orderBy[] = $sortableColumns[$sort1]["column"];
            if ($sort2 != -1) {
                $orderBy[] = $sortableColumns[$sort2]["column"];
            }
            if ($sort3 != -1) {
                $orderBy[] = $sortableColumns[$sort3]["column"];
            }

            if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
                $query =
                    "SELECT a.id, d.entity_cd, b.doc_code, b.description, a.no_voucher, a.tgl_voucher, a.doc_status, c.short_desc, a.keterangan, b.vouchertype_id
                    FROM t_gl_voucher_master AS a
                        JOIN sys_doctype AS b ON a.kd_voucher = b.doc_code
                        JOIN sys_status_code AS c ON a.doc_status = c.code AND c.key = 'voucher_status'
                        JOIN sys_company AS d ON a.entity_id = d.entity_id
                    WHERE a.tgl_voucher BETWEEN ?start AND ?end %s
                    ORDER BY %s";
            } else {
                $query =
                    "SELECT a.id, d.entity_cd, b.doc_code, b.description, a.no_voucher, a.tgl_voucher, a.doc_status, c.short_desc, a.keterangan, b.vouchertype_id
                    FROM t_gl_voucher_master AS a
                        JOIN sys_doctype AS b ON a.kd_voucher = b.doc_code
                        JOIN sys_status_code AS c ON a.doc_status = c.code AND c.key = 'voucher_status'
                        JOIN sys_company AS d ON a.entity_id = d.entity_id
                    WHERE a.entity_id = ?sbu AND a.tgl_voucher BETWEEN ?start AND ?end %s
                    ORDER BY %s";
                $this->connector->AddParameter("?sbu", $this->userCompanyId);
            }
            $this->connector->AddParameter("?start", date(SQL_DATETIME, $start));
            $this->connector->AddParameter("?end", date(SQL_DATETIME, $end));

            $this->connector->CommandText = sprintf($query, $where, implode(", ", $orderBy));
            $reader = $this->connector->ExecuteQuery();
        } else {
            $start = mktime(0, 0, 0, date("n"), 1);
            $end = mktime(0, 0, 0);
            $status = 4;
            $sort1 = 0;
            $sort2 = 2;
            $sort3 = -1;

            $reader = null;
        }

        $this->Set("sortableColumns", $sortableColumns);
        $this->Set("start", $start);
        $this->Set("end", $end);
        $this->Set("status", $status);
        $this->Set("sort1", $sort1);
        $this->Set("sort2", $sort2);
        $this->Set("sort3", $sort3);
        $this->Set("reader", $reader);
        if ($reader !== null) {
            require_once(MODEL . "accounting/voucher_type.php");
            $type = new VoucherType();
            $this->Set("types", $type->LoadAll());
        } else {
            $this->Set("types", null);
        }
    }
}


// End of File: estimasi_controller.php
