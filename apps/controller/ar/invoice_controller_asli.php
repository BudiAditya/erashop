<?php
class InvoiceController extends AppController {
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;
    private $trxMonth;
    private $trxYear;

    protected function Initialize() {
        require_once(MODEL . "ar/invoice.php");
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
        $settings["columns"][] = array("name" => "a.cabang_code", "display" => "Cabang", "width" => 80);
        $settings["columns"][] = array("name" => "a.invoice_date", "display" => "Tanggal", "width" => 60);
        $settings["columns"][] = array("name" => "a.invoice_no", "display" => "No. Invoice", "width" => 80);
        $settings["columns"][] = array("name" => "a.customer_name", "display" => "Nama Customer", "width" => 150);
        $settings["columns"][] = array("name" => "a.sales_name", "display" => "Salesman", "width" => 100);
        $settings["columns"][] = array("name" => "a.invoice_descs", "display" => "Keterangan", "width" => 150);
        $settings["columns"][] = array("name" => "if(a.payment_type = 0,'Cash','Credit')", "display" => "Cara Bayar", "width" => 60);
        $settings["columns"][] = array("name" => "format(a.total_amount,0)", "display" => "Nilai Penjualan", "width" => 90, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.paid_amount,0)", "display" => "Terbayar", "width" => 90, "align" => "right");
        $settings["columns"][] = array("name" => "format(a.balance_amount,0)", "display" => "OutStanding", "width" => 90, "align" => "right");
        $settings["columns"][] = array("name" => "a.due_date", "display" => "JTP", "width" => 60);
        $settings["columns"][] = array("name" => "a.admin_name", "display" => "Admin", "width" => 80);
        $settings["columns"][] = array("name" => "if(a.invoice_status = 0,'Draft',if(a.invoice_status = 1,'Posted',if(a.invoice_status = 2,'Approved','Void')))", "display" => "Status", "width" => 40);

        $settings["filters"][] = array("name" => "a.invoice_no", "display" => "No. Invoice");
        $settings["filters"][] = array("name" => "a.cabang_code", "display" => "Kode Cabang");
        $settings["filters"][] = array("name" => "a.invoice_date", "display" => "Tanggal");
        $settings["filters"][] = array("name" => "a.customer_name", "display" => "Nama Customer");
        $settings["filters"][] = array("name" => "a.sales_name", "display" => "Nama Sales");
        $settings["filters"][] = array("name" => "a.admin_name", "display" => "Nama Admin");
        $settings["filters"][] = array("name" => "if(a.invoice_status = 0,'Draft',if(a.invoice_status = 1,'Posted',if(a.invoice_status = 2,'Approved','Void')))", "display" => "Status");

        $settings["def_filter"] = 0;
        $settings["def_order"] = 3;
        $settings["def_direction"] = "asc";
        $settings["singleSelect"] = false;

        if (!$router->IsAjaxRequest) {
            $acl = AclManager::GetInstance();
            $settings["title"] = "Daftar Transaksi Penjualan";

            if ($acl->CheckUserAccess("ar.invoice", "add")) {
                $settings["actions"][] = array("Text" => "Add", "Url" => "ar.invoice/add/0", "Class" => "bt_add", "ReqId" => 0);
            }
            if ($acl->CheckUserAccess("ar.invoice", "edit")) {
                $settings["actions"][] = array("Text" => "Edit", "Url" => "ar.invoice/add/%s", "Class" => "bt_edit", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Invoice terlebih dahulu sebelum proses edit.\nPERHATIAN: Pilih tepat 1 data invoice",
                    "Confirm" => "");
            }
            if ($acl->CheckUserAccess("ar.invoice", "delete")) {
                $settings["actions"][] = array("Text" => "Void", "Url" => "ar.invoice/void/%s", "Class" => "bt_delete", "ReqId" => 1);
            }
            if ($acl->CheckUserAccess("ar.invoice", "view")) {
                $settings["actions"][] = array("Text" => "View", "Url" => "ar.invoice/view/%s", "Class" => "bt_view", "ReqId" => 1,
                    "Error" => "Maaf anda harus memilih Data Invoice terlebih dahulu.\nPERHATIAN: Pilih tepat 1 data invoice","Confirm" => "");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.invoice", "print")) {
                $settings["actions"][] = array("Text" => "Print Invoice", "Url" => "ar.invoice/invoice_print/invoice","Class" => "bt_pdf", "ReqId" => 2, "Confirm" => "Cetak Invoice yang dipilih?");
                $settings["actions"][] = array("Text" => "Print D/O", "Url" => "ar.invoice/invoice_print/do","Class" => "bt_pdf", "ReqId" => 2, "Confirm" => "Cetak D/O Invoice yang dipilih?");
                $settings["actions"][] = array("Text" => "Print Surat Jalan", "Url" => "ar.invoice/invoice_print/suratjalan","Class" => "bt_pdf", "ReqId" => 2, "Confirm" => "Cetak Surat Jalan Invoice yang dipilih?");
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.invoice", "view")) {
                $settings["actions"][] = array("Text" => "Laporan", "Url" => "ar.invoice/report","Target"=>"_blank","Class" => "bt_report", "ReqId" => 0);
            }
            $settings["actions"][] = array("Text" => "separator", "Url" => null);
            if ($acl->CheckUserAccess("ar.invoice", "approve")) {
                $settings["actions"][] = array("Text" => "Approve Invoice", "Url" => "ar.invoice/approve", "Class" => "bt_approve", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Invoice terlebih dahulu sebelum proses approval.",
                    "Confirm" => "Apakah anda menyetujui data invoice yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
            if ($acl->CheckUserAccess("ar.invoice", "approve")) {
                $settings["actions"][] = array("Text" => "Batal Approve", "Url" => "ar.invoice/unapprove", "Class" => "bt_reject", "ReqId" => 2,
                    "Error" => "Mohon memilih Data Invoice terlebih dahulu sebelum proses pembatalan.",
                    "Confirm" => "Apakah anda mau membatalkan approval data invoice yang dipilih ?\nKlik OK untuk melanjutkan prosedur");
            }
        } else {
            $settings["from"] = "vw_ar_invoice_master AS a";
            if ($_GET["query"] == "") {
                $_GET["query"] = null;
                $settings["where"] = "a.cabang_id = " . $this->userCabangId ." And year(a.invoice_date) = ".$this->trxYear." And month(a.invoice_date) = ".$this->trxMonth;
            } else {
                $settings["where"] = "a.cabang_id = " . $this->userCabangId;
            }
        }

        $dispatcher = Dispatcher::CreateInstance();
        $dispatcher->Dispatch("utilities", "flexigrid", array(), $settings, null, true);
    }

	/* entry data penjualan*/
    public function add($invoiceId = 0) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        require_once(MODEL . "master/contacts.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $invoice = new Invoice();
        $custCreditLimit = 0;
        if ($invoiceId > 0 ) {
            $invoice = $invoice->LoadById($invoiceId);
            if ($invoice == null) {
                $this->persistence->SaveState("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
                redirect_url("ar.invoice");
            }
            if ($invoice->PaidAmount > 0 && $invoice->PaymentType == 1) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah terbayar. Tidak boleh diubah lagi..", $invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            if ($invoice->QtyReturn($invoiceId) > 0) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s ada item yg diretur. Tidak boleh diubah lagi..", $invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            if ($invoice->InvoiceStatus == 2) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Approve- Tidak boleh diubah lagi..", $invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            if ($invoice->InvoiceStatus == 3) {
                $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Void- Tidak boleh diubah lagi..", $invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            if ($invoice->CreatebyId <> AclManager::GetInstance()->GetCurrentUser()->Id && $this->userLevel == 1){
                $this->persistence->SaveState("error", sprintf("Maaf Anda tidak boleh mengubah data ini!",$invoice->InvoiceNo));
                redirect_url("ar.invoice");
            }
            $customer = new Contacts();
            $customer = $customer->LoadById($invoice->CustomerId);
            $custCreditLimit = $customer->CreditLimit;
        }
        // load details
        $invoice->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabang = $loader->LoadById($this->userCabangId);
        if ($cabang->CabType == 2){
            $this->persistence->SaveState("error", "Maaf Cabang %s dalam mode Gudang, tidak boleh digunakan untuk transaksi!",$cabang->Kode);
            redirect_url("ar.invoice");
        }
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $cabRPM = $cabang->RawPrintMode;
        $cabRPN = $cabang->RawPrinterName;
        $cabAlMin = $cabang->AllowMinus;
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
        $loader = new Cabang();
        $gudangs = $loader->LoadByType($this->userCompanyId,1,"<>");
        //kirim ke view
        $this->Set("gudangs", $gudangs);
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCompId", $this->userCompanyId);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabAlMin", $cabAlMin);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("userCabRpm", $cabRPM);
        $this->Set("userCabRpn", $cabRPN);
        $this->Set("sales", $sales);
        $this->Set("cabangs", $cabang);
        $this->Set("invoice", $invoice);
        $this->Set("creditLimit", $custCreditLimit);
        $this->Set("acl", $acl);
        $this->Set("itemsCount", $this->InvoiceItemsCount($invoiceId));
        $router = Router::GetInstance();
        $this->Set("userIpAdd",$router->IpAddress);
    }

    public function proses_master($invoiceId = 0) {
        $invoice = new Invoice();
        $log = new UserAdmin();
        if (count($this->postData) > 0) {
            $invoice->Id = $invoiceId;
            $invoice->CabangId = $this->GetPostValue("CabangId");
            $invoice->GudangId = $this->GetPostValue("GudangId");
            $invoice->InvoiceDate = date('Y-m-d',strtotime($this->GetPostValue("InvoiceDate")));
            $invoice->InvoiceNo = $this->GetPostValue("InvoiceNo");
            $invoice->InvoiceDescs = $this->GetPostValue("InvoiceDescs");
            $invoice->CustomerId = $this->GetPostValue("CustomerId");
            $invoice->CustLevel = $this->GetPostValue("CustLevel") == null ? 0 : $this->GetPostValue("CustLevel");
            $invoice->SalesId = $this->GetPostValue("SalesId");
            if ($this->GetPostValue("InvoiceStatus") == null || $this->GetPostValue("InvoiceStatus") == 0){
                $invoice->InvoiceStatus = 1;
            }else{
                $invoice->InvoiceStatus = $this->GetPostValue("InvoiceStatus");
            }
            $invoice->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $invoice->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            if($this->GetPostValue("PaymentType") == null){
                $invoice->PaymentType = 0;
                $invoice->InvoiceStatus = 1;
            }else{
                $invoice->PaymentType = $this->GetPostValue("PaymentType");
                if ($invoice->PaymentType == 0){
                    $invoice->InvoiceStatus = 1;
                }
            }
            if($this->GetPostValue("CreditTerms") == null){
                $invoice->CreditTerms = 0;
            }else{
                $invoice->CreditTerms = $this->GetPostValue("CreditTerms");
            }
            $invoice->BaseAmount = $this->GetPostValue("BaseAmount") == null ? 0 : $this->GetPostValue("BaseAmount");
            $invoice->Disc1Pct = $this->GetPostValue("Disc1Pct") == null ? 0 : $this->GetPostValue("Disc1Pct");
            $invoice->Disc1Amount = $this->GetPostValue("Disc1Amount") == null ? 0 : $this->GetPostValue("Disc1Amount");
            $invoice->Disc2Pct = 0;
            $invoice->Disc2Amount = 0;
            $invoice->PaidAmount = 0;
            $invoice->TaxPct = $this->GetPostValue("TaxPct") == null ? 0 : $this->GetPostValue("TaxPct");
            $invoice->TaxAmount = $this->GetPostValue("TaxAmount") == null ? 0 : $this->GetPostValue("TaxAmount");
            $invoice->OtherCosts = $this->GetPostValue("OtherCosts") == null ? 0 : $this->GetPostValue("OtherCosts");
            $invoice->OtherCostsAmount = str_replace(",","",$this->GetPostValue("OtherCostsAmount") == null ? 0 : $this->GetPostValue("OtherCostsAmount"));
            if ($invoice->Id == 0) {
                $invoice->InvoiceNo = $invoice->GetInvoiceDocNo();
                $rs = $invoice->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add New Invoice',$invoice->InvoiceNo,'Success');
                    printf("OK|A|%d",$invoice->Id);
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add New Invoice',$invoice->InvoiceNo,'Failed');
                    printf("ER|A|%d",$invoice->Id);
                }
            }else{
                $rs = $invoice->Update($invoice->Id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Update Invoice',$invoice->InvoiceNo,'Success');
                    printf("OK|U|%d",$invoice->Id);
                }else{
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Update Invoice',$invoice->InvoiceNo,'Failed');
                    printf("ER|U|%d",$invoice->Id);
                }
            }
        }else{
            printf("ER|X|%d",$invoiceId);
        }
    }

	private function ValidateMaster(Invoice $invoice) {
        if ($invoice->CustomerId == 0 || $invoice->CustomerId == null || $invoice->CustomerId == ''){
            $this->Set("error", "Customer tidak boleh kosong!");
            return false;
        }
        if ($invoice->SalesId == 0 || $invoice->SalesId == null || $invoice->SalesId == ''){
            $this->Set("error", "Salesman tidak boleh kosong!");
            return false;
        }
        if ($invoice->PaymentType == 1 && $invoice->CreditTerms == 0){
            $this->Set("error", "Lama kredit belum diisi!");
            return false;
        }
		return true;
	}

	public function view($invoiceId = null) {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        $acl = AclManager::GetInstance();
        $loader = null;
        $invoice = new Invoice();
        $invoice = $invoice->LoadById($invoiceId);
        if($invoice == null){
            $this->persistence->SaveState("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.invoice");
        }
        // load details
        $invoice->LoadDetails();
        //load data cabang
        $loader = new Cabang();
        $cabCode = null;
        $cabName = null;
        $cabang = $loader->LoadById($this->userCabangId);
        $cabCode = $cabang->Kode;
        $cabName = $cabang->Cabang;
        $cabRPM = $cabang->RawPrintMode;
        $cabRPN = $cabang->RawPrinterName;
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
        $loader = new Cabang();
        $gudangs = $loader->LoadByType($this->userCompanyId,1,"<>");
        //kirim ke view
        $this->Set("gudangs", $gudangs);
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("sales", $sales);
        $this->Set("cabangs", $cabang);
        $this->Set("invoice", $invoice);
        $this->Set("acl", $acl);
        $this->Set("userCabRpm", $cabRPM);
        $this->Set("userCabRpn", $cabRPN);
        $router = Router::GetInstance();
        $this->Set("userIpAdd",$router->IpAddress);
	}

    public function delete($invoiceId) {
        // Cek datanya
        $invoice = new Invoice();
        $log = new UserAdmin();
        $invoice = $invoice->FindById($invoiceId);
        if($invoice == null){
            $this->Set("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.invoice");
        }
        /** @var $invoice Invoice */
        if($invoice->PaidAmount > 0 && $invoice->PaymentType == 1){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah terbayar. Tidak boleh dihapus..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->QtyReturn($invoiceId) > 0){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s ada item yg diretur. Tidak boleh dihapus..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->InvoiceStatus == 2){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Approve- Tidak boleh dihapus..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if ($invoice->Delete($invoiceId) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice',$invoice->InvoiceNo,'Success');
            $this->persistence->SaveState("info", sprintf("Data Invoice No: %s sudah berhasil dihapus", $invoice->InvoiceNo));
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice',$invoice->InvoiceNo,'Failed');
            $this->persistence->SaveState("error", sprintf("Maaf, Data Invoice No: %s gagal dihapus", $invoice->InvoiceNo));
        }
        redirect_url("ar.invoice");
    }

    public function void($invoiceId) {
        // Cek datanya
        $invoice = new Invoice();
        $log = new UserAdmin();
        $invoice = $invoice->FindById($invoiceId);
        if($invoice == null){
            $this->Set("error", "Maaf Data Invoice dimaksud tidak ada pada database. Mungkin sudah dihapus!");
            redirect_url("ar.invoice");
        }
        /** @var $invoice Invoice */
        if($invoice->PaidAmount > 0 && $invoice->PaymentType == 1){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah terbayar. Tidak boleh dibatalkan..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->QtyReturn($invoiceId) > 0){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s ada item yg diretur. Tidak boleh dibatalkan..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->InvoiceStatus == 2){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Approve- Tidak boleh dibatalkan..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if($invoice->InvoiceStatus == 3){
            $this->persistence->SaveState("error", sprintf("Maaf Invoice No. %s sudah di-Void- Tidak boleh dibatalkan..",$invoice->InvoiceNo));
            redirect_url("ar.invoice");
        }
        if ($invoice->Void($invoiceId) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice',$invoice->InvoiceNo,'Success');
            $this->persistence->SaveState("info", sprintf("Data Invoice No: %s sudah berhasil dibatalkan", $invoice->InvoiceNo));
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice',$invoice->InvoiceNo,'Failed');
            $this->persistence->SaveState("error", sprintf("Maaf, Data Invoice No: %s gagal dibatalkan", $invoice->InvoiceNo));
        }
        redirect_url("ar.invoice");
    }


	public function add_detail($invoiceId = null) {
        require_once(MODEL . "master/items.php");
        $log = new UserAdmin();
        $invoice = new Invoice($invoiceId);
        $invdetail = new InvoiceDetail();
        $invdetail->InvoiceId = $invoiceId;
        $invdetail->InvoiceNo = $invoice->InvoiceNo;
        $invdetail->CabangId = $invoice->CabangId;
        $items = null;
        $is_item_exist = false;
        if (count($this->postData) > 0) {
            $invdetail->ItemId = $this->GetPostValue("aItemId");
            $invdetail->Qty = $this->GetPostValue("aQty");
            $invdetail->Price = $this->GetPostValue("aPrice");
            if ($this->GetPostValue("aDiscFormula") == ''){
                $invdetail->DiscFormula = 0;
            }else{
                $invdetail->DiscFormula = $this->GetPostValue("aDiscFormula");
            }
            $invdetail->DiscAmount = $this->GetPostValue("aDiscAmount");
            $invdetail->SubTotal = $this->GetPostValue("aSubTotal");
            $invdetail->ItemHpp = $this->GetPostValue("aItemHpp");
            $invdetail->ItemNote = $this->GetPostValue("aItemNote");
            $invdetail->IsFree = $this->GetPostValue("aIsFree");
            // periksa apa sudah ada item dengan harga yang sama, kalo ada gabungkan saja
            $invdetail_exists = new InvoiceDetail();
            $invdetail_exists = $invdetail_exists->FindDuplicate($invdetail->CabangId,$invdetail->InvoiceId,$invdetail->ItemId,$invdetail->Price,$invdetail->DiscFormula,$invdetail->DiscAmount,$invdetail->IsFree);
            if ($invdetail_exists != null){
                // proses penggabungan disini
                /** @var $invdetail_exists InvoiceDetail */
                $is_item_exist = true;
                $invdetail->Qty+= $invdetail_exists->Qty;
                $invdetail->DiscAmount+= $invdetail_exists->DiscAmount;
                $invdetail->SubTotal+= $invdetail_exists->SubTotal;
            }
            $items = new Items($invdetail->ItemId);
            if ($items != null){
                $invdetail->ItemCode = $items->Bkode;
                $invdetail->ItemDescs = $items->Bnama;
                $invdetail->Lqty = 0;
                $invdetail->Sqty = 0;
                // insert ke table
                if ($is_item_exist){
                    // sudah ada item yg sama gabungkan..
                    $rs = $invdetail->Update($invdetail_exists->Id);
                    if ($rs > 0) {
                        $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Merge Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Success');
                        print('OK|Proses simpan update berhasil!');
                    } else {
                        $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Merge Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Failed');
                        print('ER|Gagal proses update data!');
                    }
                }else {
                    // item baru simpan
                    $rs = $invdetail->Insert() == 1;
                    if ($rs > 0) {
                        $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Success');
                        print('OK|Proses simpan data berhasil!');
                    } else {
                        $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Add Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Failed');
                        print('ER|Gagal proses simpan data!');
                    }
                }
            }else{
                print('ER|Data barang tidak ditemukan!');
            }
        }
	}

    public function edit_detail($invoiceId = null) {
        require_once(MODEL . "master/items.php");
        $log = new UserAdmin();
        $invoice = new Invoice($invoiceId);
        $invdetail = new InvoiceDetail();
        $invdetail->InvoiceId = $invoiceId;
        $invdetail->InvoiceNo = $invoice->InvoiceNo;
        $invdetail->CabangId = $invoice->CabangId;
        $items = null;
        if (count($this->postData) > 0) {
            $invdetail->Id = $this->GetPostValue("aId");
            $invdetail->ItemId = $this->GetPostValue("aItemId");
            $invdetail->Qty = $this->GetPostValue("aQty");
            $invdetail->Price = $this->GetPostValue("aPrice");
            if ($this->GetPostValue("aDiscFormula") == ''){
                $invdetail->DiscFormula = 0;
            }else{
                $invdetail->DiscFormula = $this->GetPostValue("aDiscFormula");
            }
            $invdetail->DiscAmount = $this->GetPostValue("aDiscAmount");
            $invdetail->SubTotal = $this->GetPostValue("aSubTotal");
            $invdetail->ItemHpp = $this->GetPostValue("aItemHpp");
            $invdetail->ItemNote = $this->GetPostValue("aItemNote");
            $invdetail->IsFree = $this->GetPostValue("aIsFree");
            $items = new Items($invdetail->ItemId);
            if ($items != null){
                $invdetail->ItemCode = $items->Bkode;
                $invdetail->ItemDescs = $items->Bnama;
                $invdetail->Lqty = 0;
                $invdetail->Sqty = 0;
                // insert ke table
                $rs = $invdetail->Update($invdetail->Id);
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Edit Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Success');
                    print('OK|Proses update data berhasil!');
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Edit Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invoice->InvoiceNo,'Failed');
                    print('ER|Gagal update data!');
                }
            }else{
                print('ER|Data barang tidak ditemukan!');
            }
        }
    }


    public function delete_detail($id) {
        // Cek datanya
        $invdetail = new InvoiceDetail();
        $log = new UserAdmin();
        $invdetail = $invdetail->FindById($id);
        if ($invdetail == null) {
            print("Data tidak ditemukan..");
            return;
        }
        if ($invdetail->Delete($id) == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invdetail->InvoiceNo,'Success');
            printf("Data Detail Invoice ID: %d berhasil dihapus!",$id);
        }else{
            $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Delete Invoice Detail-> Item Code: '.$invdetail->ItemCode.' = '.$invdetail->Qty,$invdetail->InvoiceNo,'Failed');
            printf("Maaf, Data Detail Invoice ID: %d gagal dihapus!",$id);
        }
    }

    //proses cetak form invoice
    public function invoice_print($doctype = 'invoice') {
        $ids = $this->GetGetValue("id", array());

        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Harap pilih data yang akan dicetak !");
            redirect_url("ar.invoice");
            return;
        }

        $report = array();
        foreach ($ids as $id) {
            $inv = new Invoice();
            $inv = $inv->LoadById($id);
            $inv->LoadDetails();
            $report[] = $inv;
        }

        $this->Set("doctype", $doctype);
        $this->Set("report", $report);
    }

    public function report(){
        // report rekonsil process
        require_once(MODEL . "master/contacts.php");
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/cabang.php");
        require_once(MODEL . "master/karyawan.php");
        // Intelligent time detection...
        $month = (int)date("n");
        $year = (int)date("Y");
        $loader = null;
        if (count($this->postData) > 0) {
            // proses rekap disini
            $sCabangId = $this->GetPostValue("CabangId");
            $sContactsId = $this->GetPostValue("ContactsId");
            $sSalesId = $this->GetPostValue("SalesId");
            $sStatus = $this->GetPostValue("Status");
            $sPaymentStatus = $this->GetPostValue("PaymentStatus");
            $sStartDate = strtotime($this->GetPostValue("StartDate"));
            $sEndDate = strtotime($this->GetPostValue("EndDate"));
            $sJnsLaporan = $this->GetPostValue("JnsLaporan");
            $sOutput = $this->GetPostValue("Output");
            // ambil data yang diperlukan
            $invoice = new Invoice();
            if ($sJnsLaporan == 1){
                $reports = $invoice->Load4Reports($this->userCompanyId,$sCabangId,$sContactsId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
            }elseif ($sJnsLaporan == 2){
                $reports = $invoice->Load4ReportsDetail($this->userCompanyId,$sCabangId,$sContactsId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
            }else{
                $reports = $invoice->Load4ReportsRekapItem($this->userCompanyId,$sCabangId,$sContactsId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
            }
        }else{
            $sCabangId = 0;
            $sContactsId = 0;
            $sSalesId = 0;
            $sStatus = -1;
            $sPaymentStatus = -1;
            $sStartDate = mktime(0, 0, 0, $month, 1, $year);
            //$sStartDate = date('d-m-Y',$sStartDate);
            $sEndDate = time();
            //$sEndDate = date('d-m-Y',$sEndDate);
            $sJnsLaporan = 1;
            $sOutput = 0;
            $reports = null;
        }
        $customer = new Contacts();
        $customer = $customer->LoadAll();
        $loader = new Company($this->userCompanyId);
        $this->Set("company_name", $loader->CompanyName);
        $loader = new Karyawan();
        $sales = $loader->LoadAll();
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
        // kirim ke view
        $this->Set("cabangs", $cabang);
        $this->Set("customers",$customer);
        $this->Set("sales",$sales);
        $this->Set("CabangId",$sCabangId);
        $this->Set("ContactsId",$sContactsId);
        $this->Set("SalesId",$sSalesId);
        $this->Set("StartDate",$sStartDate);
        $this->Set("EndDate",$sEndDate);
        $this->Set("Status",$sStatus);
        $this->Set("PaymentStatus",$sPaymentStatus);
        $this->Set("Output",$sOutput);
        $this->Set("JnsLaporan",$sJnsLaporan);
        $this->Set("Reports",$reports);
        $this->Set("userCabId",$this->userCabangId);
        $this->Set("userCabCode",$cabCode);
        $this->Set("userCabName",$cabName);
        $this->Set("userLevel",$this->userLevel);
    }

    public function getInvoiceItemRows($id){
        $invoice = new Invoice();
        $rows = $invoice->GetInvoiceItemRow($id);
        print($rows);
    }

    public function InvoiceItemsCount($id){
        $invoice = new Invoice();
        $rows = $invoice->GetInvoiceItemRow($id);
        return $rows;
    }

    public function createTextInvoice($id){
        $invoice = new Invoice($id);
        if ($invoice <> null){
            $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            fwrite($myfile, $invoice->CompanyName);
            fwrite($myfile, "\n".'FAKTUR PENJUALAN');
            fclose($myfile);
        }
    }

    public function getjson_invoicelists($cabangId,$customerId){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $invoices = new Invoice();
        $invlists = $invoices->GetJSonInvoices($cabangId,$customerId,$filter);
        echo json_encode($invlists);
    }

    public function getjson_invoiceitems($invoiceId = 0){
        $invoices = new Invoice();
        $itemlists = $invoices->GetJSonInvoiceItems($invoiceId);
        echo json_encode($itemlists);
    }

    public function approve() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ar.invoice");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $invoice = new Invoice();
            $log = new UserAdmin();
            $invoice = $invoice->FindById($id);
            /** @var $invoice Invoice */
            // process invoice
            if($invoice->InvoiceStatus == 1){
                $rs = $invoice->Approve($invoice->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Approve Invoice',$invoice->InvoiceNo,'Success');
                    $infos[] = sprintf("Data Invoice No.: '%s' (%s) telah berhasil di-approve.", $invoice->InvoiceNo, $invoice->InvoiceDescs);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Approve Invoice',$invoice->InvoiceNo,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses approve Data Invoice: '%s'. Message: %s", $invoice->InvoiceNo, $this->connector->GetErrorMessage());
                }
            }else{
                $errors[] = sprintf("Data Invoice No.%s sudah berstatus -Approved- !",$invoice->InvoiceNo);
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ar.invoice");
    }

    public function unapprove() {
        $ids = $this->GetGetValue("id", array());
        if (count($ids) == 0) {
            $this->persistence->SaveState("error", "Maaf anda belum memilih data yang akan di approve !");
            redirect_url("ar.invoice");
            return;
        }
        $uid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $infos = array();
        $errors = array();
        foreach ($ids as $id) {
            $invoice = new Invoice();
            $log = new UserAdmin();
            $invoice = $invoice->FindById($id);
            /** @var $invoice Invoice */
            // process invoice
            if($invoice->InvoiceStatus == 2){
                $rs = $invoice->Unapprove($invoice->Id,$uid);
                if ($rs) {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Un-approve Invoice',$invoice->InvoiceNo,'Success');
                    $infos[] = sprintf("Data Invoice No.: '%s' (%s) telah berhasil di-batalkan.", $invoice->InvoiceNo, $invoice->InvoiceDescs);
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'ar.invoice','Un-approve Invoice',$invoice->InvoiceNo,'Failed');
                    $errors[] = sprintf("Maaf, Gagal proses pembatalan Data Invoice: '%s'. Message: %s", $invoice->InvoiceNo, $this->connector->GetErrorMessage());
                }
            }else{
                if ($invoice->InvoiceStatus == 1){
                    $errors[] = sprintf("Data Invoice No.%s masih berstatus -POSTED- !",$invoice->InvoiceNo);
                }else{
                    $errors[] = sprintf("Data Invoice No.%s masih berstatus -DRAFT- !",$invoice->InvoiceNo);
                }
            }
        }
        if (count($infos) > 0) {
            $this->persistence->SaveState("info", "<ul><li>" . implode("</li><li>", $infos) . "</li></ul>");
        }
        if (count($errors) > 0) {
            $this->persistence->SaveState("error", "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
        }
        redirect_url("ar.invoice");
    }

    //direct printing
    public function printdirect($invId,$paperType = 0,$prtName = null){
        $invoice = new Invoice($invId);
        $space = 120;
        if ($invoice != null){
            if ($paperType == 1) {
                // kertas ncr biasa 215 mm x 140 mm
                // print ncr paper size
                $text = null;
                /* tulis dan buka koneksi ke printer */
                $prtName = "EPSON LX-300+ II";
                $printer = printer_open($prtName);
                printer_set_option($printer, PRINTER_PAPER_FORMAT, PRINTER_FORMAT_CUSTOM); // Custom paper format
                printer_set_option($printer, PRINTER_PAPER_WIDTH, 215); // 215mm wide
                printer_set_option($printer, PRINTER_PAPER_LENGTH, 140); // 140mm length
                printer_set_option($printer, PRINTER_MODE, "RAW"); // And raw printing mode
                /* write the text to the print job */
                //initialized
                $text.= chr(27).chr(64);
                //set page length in line
                $text.= chr(27).chr(67).chr(33);
                //15 cpi
                $text.= chr(27).chr(103);
                $tx1 = 'I N V O I C E';
                $tx2 = $invoice->CompanyName;
                $txt = $tx1 . str_repeat(' ', 70 - strlen($tx1)) . $tx2;
                $text.= chr(27).chr(69); // bold on
                $text.= $txt."\n";
                $text.= chr(27).chr(70); // bold off
                $tx1 = 'Nomor    : ' . $invoice->InvoiceNo;
                $tx2 = 'Customer : ' . $invoice->CustomerName . ' (' . $invoice->CustomerCode . ')';
                $txt = $tx1 . str_repeat(' ', 70 - strlen($tx1)) . $tx2;
                $text.= $txt."\n";
                $tx1 = 'Tanggal  : ' . $invoice->FormatInvoiceDate(JS_DATE);
                $tx2 = 'Alamat   : ' . $invoice->CustomerAddress;
                $txt = $tx1 . str_repeat(' ', 70 - strlen($tx1)) . $tx2;
                $text.= $txt."\n";
                $tx1 = 'Salesman : ' . $invoice->SalesName.'  -  Cab.: '.$invoice->CabangCode;
                if ($invoice->CreditTerms > 0 || $invoice->PaymentType == 2) {
                    $tx2 = 'JTP      : ' . $invoice->FormatDueDate(JS_DATE) . ' (' . $invoice->CreditTerms . ' hari)';
                } else {
                    $tx2 = 'JTP      : CASH';
                }
                $txt = $tx1 . str_repeat(' ', 70 - strlen($tx1)) . $tx2;
                $text.= $txt."\n";
                //$data[] = '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890';
                $text.= "========================================================================================================================\n";
                $text.= "|   Q T Y   |      KODE DAN NAMA BARANG                                        |   HARGA   | DISCOUNT |   J U M L A H  |\n";
                $text.= "|-----------+------------------------------------------------------------------+-----------+----------+----------------|\n";
                $ket1 = "** Barang yang sudah dibeli, tidak dapat dikembalikan/ditukar **";
                $ket2 = "               Diterima oleh,                      Hormat kami,";
                $ket3 = "               ________________                    ________________";
                $invoice->LoadDetails();
                $cnt = 0;
                $qtotal = 0;
                $qjenis = 0;
                foreach ($invoice->Details as $idx => $detail) {
                    $tx1 = $detail->Qty . ' ' . left($detail->SatBesar, 3);
                    $tx1 = '|' . str_repeat(' ', 11 - strlen($tx1)) . $tx1 . '| ';
                    $tx2 = left($detail->ItemCode . str_repeat(' ', 13 - strlen($detail->ItemCode)) . str_replace('  ', ' ', $detail->ItemDescs), 65);
                    $tx2 = $tx2 . str_repeat(' ', 65 - strlen($tx2)) . '|';
                    $tx3 = number_format($detail->Price, 0);
                    $tx3 = str_repeat(' ', 10 - strlen($tx3)) . $tx3 . ' |';
                    $tx4 = number_format($detail->DiscFormula, 0);
                    $tx4 = str_repeat(' ', 9 - strlen($tx4)) . $tx4 . ' |';
                    if ($detail->IsFree == 0){
                        $tx5 = number_format($detail->SubTotal, 0);
                    }else{
                        $tx5 = '*Free/Bonus*';
                    }
                    $tx5 = str_repeat(' ', 15 - strlen($tx5)) . $tx5 . ' |';
                    $text.= $tx1 . $tx2 . $tx3 . $tx4 . $tx5."\n";
                    $cnt++;
                    $qjenis++;
                    $qtotal += $detail->Qty;
                }
                $cnx = 0;
                if ($cnt < 11) {
                    for ($cnx = $cnt; $cnx < 11; $cnx++) {
                        $text.= "|           |                                                                  |           |          |                |\n";
                    }
                }
                $text.= "|------------------------------------------------------------------------------+----------------------+----------------|\n";
                $tx1 = 'Total: ' . $qtotal . ' Satuan *' . $qjenis . ' macam*';
                $tx2 = number_format($invoice->BaseAmount, 0);
                $tx1 = '|' . $tx1 . str_repeat(' ', 78 - strlen($tx1)) . '| SUB TOTAL            |' . str_repeat(' ', 15 - strlen($tx2)) . $tx2 . ' |';
                $text.= $tx1."\n";
                $text.= "-------------------------------------------------------------------------------+----------------------+----------------|\n";
                $tx2 = number_format($invoice->Disc1Amount, 0);
                $tx3 = $invoice->Disc1Pct == null ? 0 : $invoice->Disc1Pct;
                $tx1 = $ket1 . str_repeat(' ', 79 - strlen($ket1)) . '| DISCOUNT   ' . str_repeat(' ', 8 - strlen($tx3)) . $tx3 . '% |' . str_repeat(' ', 15 - strlen($tx2)) . $tx2 . ' |';
                $text.= $tx1."\n";
                //$text.= str_repeat(' ', 79) . "|----------------------+----------------|\n";
                $tx2 = number_format($invoice->TaxAmount, 0);
                $tx3 = $invoice->TaxPct == null ? 0 : $invoice->TaxPct;
                $tx1 = $ket2 . str_repeat(' ', 79 - strlen($ket2)) . '| PAJAK    ' . str_repeat(' ', 10 - strlen($tx3)) . $tx3 . '% |' . str_repeat(' ', 15 - strlen($tx2)) . $tx2 . ' |';
                $text.= $tx1."\n";
                //$text.= str_repeat(' ', 79) . "|----------------------+----------------|\n";
                $tx2 = $invoice->OtherCosts;
                $tx3 = number_format($invoice->OtherCostsAmount, 0);
                $tx1 = str_repeat(' ', 79) . '| ' . left($tx2, 21) . str_repeat(' ', 21 - strlen($tx2)) . '|' . str_repeat(' ', 15 - strlen($tx3)) . $tx3 . ' |';
                $text.= $tx1."\n";
                //$text.= str_repeat(' ', 79) . "|----------------------+----------------|\n";
                $tx2 = number_format($invoice->TotalAmount, 0);
                $tx1 = str_repeat(' ', 79) . '| GRAND TOTAL          |' . str_repeat(' ', 15 - strlen($tx2)) . $tx2 . ' |';
                $text.= $tx1."\n";
                $text.= $ket3 . str_repeat(' ', 79 - strlen($ket3)) . "=========================================\n";
                $text.= "\n\n";
                //potong kertas
                //$text.= chr(27).chr(105);
                //eject page
                $text.= chr(12);
                printer_write($printer, $text);
                /* close the connection */
                printer_close($printer);
                $invoice->UpdatePrintCounter($invId,AclManager::GetInstance()->GetCurrentUser()->Id);
            }elseif ($paperType == 2){
                //khusus SCM RJ Steel Kairagi
                //kertas form - hanya print datanya saja
                $data = array();
                $tx1 = str_repeat(' ',80).$invoice->CompanyName;
                $data[] = $tx1;
                $tx1 = left($invoice->CustomerName.' ('.$invoice->CustomerCode.')',50);
                $tx2 = $invoice->InvoiceNo;
                $tx3 = $tx1.str_repeat(' ',56-strlen($tx1)).$tx2;
                $data[] = $tx3;
                $tx1 = left($invoice->CustomerAddress,50);
                if ($invoice->CreditTerms > 0 || $invoice->PaymentType == 2) {
                    $tx2 = $invoice->FormatInvoiceDate(JS_DATE).'  JTP : '.$invoice->FormatDueDate(JS_DATE).' ('.$invoice->CreditTerms.' hari)';
                } else {
                    $tx2 = $invoice->FormatInvoiceDate(JS_DATE).' - CASH -';
                }
                $tx3 = $tx1.str_repeat(' ',56-strlen($tx1)).$tx2;
                $data[] = $tx3;
                $data[] = '';
                $data[] = '';
                $data[] = '';
                $invoice->LoadDetails();
                $cnt = 0;
                $qtotal = 0;
                $qjenis = 0;
                $nmr = 1;
                foreach ($invoice->Details as $idx => $detail) {
                    $tx1 = $nmr.str_repeat(' ',3-strlen($nmr));
                    $tx2 = left($detail->ItemCode . str_repeat(' ', 16 - strlen($detail->ItemCode)) . $detail->ItemDescs, 58);
                    $tx2 = $tx2.str_repeat(' ',58 - strlen($tx2));
                    $tx3 = number_format($detail->Qty,0);
                    $tx3 = str_repeat(' ',7-strlen($tx3)).$tx3.' '.left($detail->SatBesar,3);
                    $tx4 = number_format($detail->Price,0);
                    $tx4 = str_repeat(' ',15-strlen($tx4)).$tx4;
                    $tx5 = $detail->DiscFormula;
                    $tx5 = str_repeat(' ',6-strlen($tx5)).$tx5;
                    if ($detail->IsFree == 0){
                        $tx6 = number_format($detail->SubTotal, 0);
                    }else{
                        $tx6 = '*Free/Bonus*';
                    }
                    $tx6 = str_repeat(' ',26-strlen($tx6)).$tx6;
                    $data[] = $tx1.$tx2.$tx3.$tx4.$tx5.$tx6;
                    $nmr++;
                    $cnt++;
                    $qjenis++;
                    $qtotal += $detail->Qty;
                }
                $cnx = 0;
                if ($cnt < 7) {
                    for ($cnx = $cnt; $cnx < 7; $cnx++) {
                        $data[] = '';
                    }
                }
                //$data[] = '';
                $tx1 = 'Sub Total';
                $tx1 = str_repeat(' ',99-strlen($tx1)).$tx1;
                $tx2 = number_format($invoice->BaseAmount,0);
                $tx2 = str_repeat(' ',20-strlen($tx2)).$tx2;
                $data[] = $tx1.$tx2;
                if ($invoice->Disc1Amount > 0) {
                    $tx1 = 'Discount';
                    $tx1 = str_repeat(' ', 99-strlen($tx1)) . $tx1;
                    $tx2 = $invoice->Disc1Pct.'%';
                    $tx2 = str_repeat(' ',6-strlen($tx2)).$tx2;
                    $tx3 = number_format($invoice->Disc1Amount, 0);
                    $tx3 = str_repeat(' ', 14 - strlen($tx3)) . $tx3;
                    $data[] = $tx1 . $tx2 . $tx3;
                }else{
                    $data[] = '';
                }
                if ($invoice->TaxAmount > 0) {
                    $tx1 = 'Pajak';
                    $tx1 = str_repeat(' ', 99-strlen($tx1)) . $tx1;
                    $tx2 = $invoice->TaxPct.'%';
                    $tx2 = str_repeat(' ',6-strlen($tx2)).$tx2;
                    $tx3 = number_format($invoice->TaxAmount, 0);
                    $tx3 = str_repeat(' ', 14 - strlen($tx3)) . $tx3;
                    $data[] = $tx1 . $tx2 . $tx3;
                }else{
                    $data[] = '';
                }
                if ($invoice->OtherCostsAmount > 0) {
                    $tx1 = $invoice->OtherCosts;
                    $tx1 = str_repeat(' ',99-strlen($tx1)).$tx1;
                    $tx2 = number_format($invoice->OtherCostsAmount,0);
                    $tx2 = str_repeat(' ',20-strlen($tx2)).$tx2;
                    $data[] = $tx1.$tx2;
                }else{
                    $data[] = '';
                }
                $tx1 = number_format($invoice->TotalAmount,0);
                $tx1 = str_repeat(' ',119-strlen($tx1)).$tx1;
                $data[] = $tx1;
                $invoice->UpdatePrintCounter($invId,AclManager::GetInstance()->GetCurrentUser()->Id);
                echo json_encode($data);
            }elseif ($paperType == 4){
                // print struk pos mode
                $text = null;
                /* tulis dan buka koneksi ke printer */
                $printer = printer_open($prtName);
                printer_set_option($printer, PRINTER_PAPER_FORMAT, PRINTER_FORMAT_CUSTOM); // Custom paper format
                printer_set_option($printer, PRINTER_PAPER_WIDTH, "80"); // 80mm wide
                printer_set_option($printer, PRINTER_MODE, "RAW"); // And raw printing mode
                /* write the text to the print job */
                //initialized
                $text.= chr(27).chr(64);
                //buka laci
                $text.= chr(27).chr(112).chr(0).chr(25).chr(250);
                $tx1 = left(strtoupper($invoice->CompanyName).' ('.$invoice->CabangCode.')',40);
                $text.= chr(27).chr(69); // bold on
                $text.= str_repeat(' ',round((40-strlen($tx1))/2)).$tx1."\n";
                $text.= chr(27).chr(70); // bold off
                $text.= "            STRUK PENJUALAN\n";
                $text.= "----------------------------------------\n";
                $tx1 = 'No.:'.$invoice->InvoiceNo;
                $tx2 = 'Tgl:'.$invoice->FormatInvoiceDate(JS_DATE);
                $text.= $tx1.str_repeat(' ',40-strlen($tx1.$tx2)).$tx2."\n";
                $text.= 'Cst:'.left($invoice->CustomerName.' ('.$invoice->CustomerCode.')',36)."\n";
                $text.= "----------------------------------------\n";
                $text.= "Kode Barang      Qty    Harga     Jumlah\n";
                $text.= "----------------------------------------\n";
                $invoice->LoadDetails();
                $cnt = 0;
                $qtotal = 0;
                $qjenis = 0;
                $nmr = 1;
                foreach ($invoice->Details as $idx => $detail) {
                    $tx1 = left($detail->ItemCode . str_repeat(' ', 16 - strlen($detail->ItemCode)), 16);
                    $tx1 = $tx1.str_repeat(' ',16 - strlen($tx1));
                    $tx2 = $detail->Qty;
                    $tx2 = str_repeat(' ',4-strlen($tx2)).$tx2;
                    $tx3 = number_format($detail->Price,0);
                    $tx3 = str_repeat(' ',9-strlen($tx3)).$tx3;
                    if ($detail->IsFree == 0){
                        $tx4 = number_format(round($detail->Qty*$detail->Price,0),0);
                    }else{
                        $tx4 = 'Free/Bonus';
                    }
                    $tx4 = str_repeat(' ',11-strlen($tx4)).$tx4;
                    $text.= left($detail->ItemDescs.' ('.trim(left($detail->SatBesar,3)).')',40)."\n";
                    $text.= $tx1.$tx2.$tx3.$tx4."\n";
                    if ($detail->DiscAmount > 0){
                        $tx1 = '-'.$detail->DiscFormula.'% ('.number_format($detail->DiscAmount,0).')';
                        $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                    }
                    $nmr++;
                    $cnt++;
                    $qjenis++;
                    $qtotal += $detail->Qty;
                }
                $text.= "----------------------------------------\n";
                $tx1 = $qtotal.' item(s)';
                $tx1 = $tx1.str_repeat(' ',18-strlen($tx1));
                $tx2 = 'Sub Total '.number_format($invoice->BaseAmount,0);
                $tx2 = str_repeat(' ',22-strlen($tx2)).$tx2;
                $text.= $tx1.$tx2."\n";
                $tx1 = 'Discount '.$invoice->Disc1Pct.'% = '.number_format($invoice->Disc1Amount,0).'-';
                $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                if ($invoice->TaxAmount > 0){
                    $text.= "----------------------------------------\n";
                    $tx1 = 'DPP '.number_format($invoice->BaseAmount - $invoice->Disc1Amount,0).'+';
                    $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                    $tx1 = 'PPN '.$invoice->TaxPct.'% = '.number_format($invoice->TaxAmount,0).'+';
                    $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                }
                if ($invoice->OtherCostsAmount > 0){
                    if (strlen($invoice->OtherCosts) < 5){
                        $tx1 = 'Biaya Lain-lain '.number_format($invoice->OtherCostsAmount,0).'+';
                    }else{
                        $tx1 = left($invoice->OtherCosts,29).' '.number_format($invoice->OtherCostsAmount,0).'+';
                    }
                    $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                }
                $text.= "----------------------------------------\n";
                if ($invoice->PaymentType == 1){
                    $tx1 = '- Kredit '.$invoice->CreditTerms.' hari';
                }else{
                    $tx1 = '- Tunai';
                }
                $tx2 = 'TOTAL '.number_format($invoice->TotalAmount,0);
                $text.= $tx1.str_repeat(' ',40-strlen($tx1.$tx2)).$tx2."\n";
                $text.= "----------------------------------------\n";
                $tx1 = "*".strtoupper($invoice->AdminName)."*";
                $tx2 = date("Y-m-d h:i:s");
                $text.= chr(15); //condensed on
                $text.= $tx1.str_repeat(' ',40-strlen($tx1.$tx2)).$tx2."\n";
                $text.= chr(18); //condensed off
                $text.= "\n";
                $text.= "  BARANG YANG SUDAH DIBELI TIDAK BOLEH\n";
                $text.= "         DITUKAR/DIKEMBALIKAN\n";
                $text.= "     TERIMA KASIH ATAS KUNJUNGAN ANDA\n";
                $text.= "\n\n\n\n\n\n\n";
                //potong kertas
                $text.= chr(27).chr(105);
                printer_write($printer, $text);
                /* close the connection */
                printer_close($printer);
                $invoice->UpdatePrintCounter($invId,AclManager::GetInstance()->GetCurrentUser()->Id);
            }elseif ($paperType == 5){
                // print ncr paper size
                $text = null;
                /* tulis dan buka koneksi ke printer */
                $printer = printer_open($prtName);
                printer_set_option($printer, PRINTER_PAPER_FORMAT, PRINTER_FORMAT_CUSTOM); // Custom paper format
                printer_set_option($printer, PRINTER_PAPER_WIDTH, "80"); // 80mm wide
                printer_set_option($printer, PRINTER_MODE, "RAW"); // And raw printing mode
                /* write the text to the print job */
                //initialized
                $text.= chr(27).chr(64);
                //buka laci
                $text.= chr(27).chr(112).chr(0).chr(25).chr(250);
                $tx1 = left(strtoupper($invoice->CompanyName).' ('.$invoice->CabangCode.')',40);
                $text.= chr(27).chr(69); // bold on
                $text.= str_repeat(' ',round((40-strlen($tx1))/2)).$tx1."\n";
                $text.= chr(27).chr(70); // bold off
                $text.= "            STRUK PENJUALAN\n";
                $text.= "----------------------------------------\n";
                $tx1 = 'No.:'.$invoice->InvoiceNo;
                $tx2 = 'Tgl:'.$invoice->FormatInvoiceDate(JS_DATE);
                $text.= $tx1.str_repeat(' ',40-strlen($tx1.$tx2)).$tx2."\n";
                $text.= 'Cst:'.left($invoice->CustomerName.' ('.$invoice->CustomerCode.')',36)."\n";
                $text.= "----------------------------------------\n";
                $text.= "Kode Barang      Qty    Harga     Jumlah\n";
                $text.= "----------------------------------------\n";
                $invoice->LoadDetails();
                $cnt = 0;
                $qtotal = 0;
                $qjenis = 0;
                $nmr = 1;
                foreach ($invoice->Details as $idx => $detail) {
                    $tx1 = left($detail->ItemCode . str_repeat(' ', 16 - strlen($detail->ItemCode)), 16);
                    $tx1 = $tx1.str_repeat(' ',16 - strlen($tx1));
                    $tx2 = $detail->Qty;
                    $tx2 = str_repeat(' ',4-strlen($tx2)).$tx2;
                    $tx3 = number_format($detail->Price,0);
                    $tx3 = str_repeat(' ',9-strlen($tx3)).$tx3;
                    if ($detail->IsFree == 0){
                        $tx4 = number_format(round($detail->Qty*$detail->Price,0),0);
                    }else{
                        $tx4 = 'Free/Bonus';
                    }
                    $tx4 = str_repeat(' ',11-strlen($tx4)).$tx4;
                    $text.= left($detail->ItemDescs.' ('.trim(left($detail->SatBesar,3)).')',40)."\n";
                    $text.= $tx1.$tx2.$tx3.$tx4."\n";
                    if ($detail->DiscAmount > 0){
                        $tx1 = '-'.$detail->DiscFormula.'% ('.number_format($detail->DiscAmount,0).')';
                        $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                    }
                    $nmr++;
                    $cnt++;
                    $qjenis++;
                    $qtotal += $detail->Qty;
                }
                $text.= "----------------------------------------\n";
                $tx1 = $qtotal.' item(s)';
                $tx1 = $tx1.str_repeat(' ',18-strlen($tx1));
                $tx2 = 'Sub Total '.number_format($invoice->BaseAmount,0);
                $tx2 = str_repeat(' ',22-strlen($tx2)).$tx2;
                $text.= $tx1.$tx2."\n";
                $tx1 = 'Discount '.$invoice->Disc1Pct.'% = '.number_format($invoice->Disc1Amount,0).'-';
                $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                if ($invoice->TaxAmount > 0){
                    $text.= "----------------------------------------\n";
                    $tx1 = 'DPP '.number_format($invoice->BaseAmount - $invoice->Disc1Amount,0).'+';
                    $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                    $tx1 = 'PPN '.$invoice->TaxPct.'% = '.number_format($invoice->TaxAmount,0).'+';
                    $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                }
                if ($invoice->OtherCostsAmount > 0){
                    if (strlen($invoice->OtherCosts) < 5){
                        $tx1 = 'Biaya Lain-lain '.number_format($invoice->OtherCostsAmount,0).'+';
                    }else{
                        $tx1 = left($invoice->OtherCosts,29).' '.number_format($invoice->OtherCostsAmount,0).'+';
                    }
                    $text.= str_repeat(' ',40-strlen($tx1)).$tx1."\n";
                }
                $text.= "----------------------------------------\n";
                if ($invoice->PaymentType == 1){
                    $tx1 = '- Kredit '.$invoice->CreditTerms.' hari';
                }else{
                    $tx1 = '- Tunai';
                }
                $tx2 = 'TOTAL '.number_format($invoice->TotalAmount,0);
                $text.= $tx1.str_repeat(' ',40-strlen($tx1.$tx2)).$tx2."\n";
                $text.= "----------------------------------------\n";
                $tx1 = "*".strtoupper($invoice->AdminName)."*";
                $tx2 = date("Y-m-d h:i:s");
                $text.= chr(15); //condensed on
                $text.= $tx1.str_repeat(' ',40-strlen($tx1.$tx2)).$tx2."\n";
                $text.= chr(18); //condensed off
                $text.= "\n";
                $text.= "  BARANG YANG SUDAH DIBELI TIDAK BOLEH\n";
                $text.= "         DITUKAR/DIKEMBALIKAN\n";
                $text.= "     TERIMA KASIH ATAS KUNJUNGAN ANDA\n";
                $text.= "\n\n\n\n\n\n\n";
                //potong kertas
                $text.= chr(27).chr(105);
                printer_write($printer, $text);
                /* close the connection */
                printer_close($printer);
                $invoice->UpdatePrintCounter($invId,AclManager::GetInstance()->GetCurrentUser()->Id);
            }
        }

    }

    public function getitempricestock_json($level,$cabangId){
        require_once(MODEL . "master/setprice.php");
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $setprice = new SetPrice();
        $itemlists = $setprice->GetJSonItemPriceStock($level,$cabangId,$filter);
        echo json_encode($itemlists);
    }

    public function getitemstock_json($cabangId){
        require_once(MODEL . "master/setprice.php");
        require_once(MODEL . "master/cabang.php");
        $cabang = new Cabang($cabangId);
        $allowMinus = $cabang->AllowMinus;
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $setprice = new SetPrice();
        $itemlists = $setprice->GetJSonItemStock($allowMinus,$cabangId,$filter);
        echo json_encode($itemlists);
    }

    public function getitempricestock_plain($cabangId,$bkode,$level){
        require_once(MODEL . "master/setprice.php");
        require_once(MODEL . "master/items.php");
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $setprice SetPrice */
            /** @var $items Items  */
            $setprice = new SetPrice();
            $setprice = $setprice->FindByKode($cabangId,$bkode);
            $items = null;
            if ($setprice != null){
                $ret = "OK|".$setprice->ItemId.'|'.$setprice->ItemName.'|'.$setprice->Satuan.'|'.$setprice->QtyStock.'|'.$setprice->HrgBeli;
                if ($level == -1 && $setprice->HrgBeli > 0){
                    $ret.= '|'.$setprice->HrgBeli;
                }elseif($level == 1 && $setprice->HrgJual2 > 0){
                    $ret.= '|'.$setprice->HrgJual3;
                }elseif($level == 2 && $setprice->HrgJual3 > 0){
                    $ret.= '|'.$setprice->HrgJual3;
                }elseif($level == 3 && $setprice->HrgJual4 > 0){
                    $ret.= '|'.$setprice->HrgJual4;
                }elseif($level == 4 && $setprice->HrgJual5 > 0){
                    $ret.= '|'.$setprice->HrgJual5;
                }elseif($level == 5 && $setprice->HrgJual6 > 0){
                    $ret.= '|'.$setprice->HrgJual6;
                }else{
                    $ret.= '|'.$setprice->HrgJual1;
                }
            }
        }
        print $ret;
    }

    public function getDiscPrivileges($resId){
        require_once(MODEL . "master/user_privileges.php");
        $userId = AclManager::GetInstance()->GetCurrentUser()->Id;
        $rst = -1;
        $privileges = new UserPrivileges();
        $privileges = $privileges->FindByResourceId($userId,$resId);
        if ($privileges != null){
            /** @var $privileges UserPrivileges */
            $rst = $privileges->MaxDiscount;
        }
        print $rst;
    }

    public function getStockQty($cabangId = 0,$itemCode){
        require_once(MODEL . "inventory/stock.php");
        $sqty = 0;
        $stock = new Stock();
        $sqty = $stock->CheckStock($cabangId,$itemCode);
        print(number_format($sqty,0));
    }

    public function getItemPrice($itemCode,$level = 0){
        require_once(MODEL . "master/setprice.php");
        $price = new SetPrice();
        $price = $price->GetItemPrice($itemCode,$level);
        print($price);
    }
}


// End of File: estimasi_controller.php
