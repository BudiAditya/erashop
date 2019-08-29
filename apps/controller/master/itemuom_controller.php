<?php
class ItemUomController extends AppController {
	private $userUid;
    private $userCabangId;

	protected function Initialize() {
		require_once(MODEL . "master/itemuom.php");
        require_once(MODEL . "master/user_admin.php");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
	}

	public function index() {
        // index script here
        $acl = AclManager::GetInstance();
        $this->Set("acl",$acl);
	}

	private function ValidateData(ItemUom $itemuom) {

		return true;
	}

	public function add() {
        $log = new UserAdmin();
        $itemuom = new ItemUom();
        if (count($this->postData) > 0) {
            $itemuom->Skode = $this->GetPostValue("Skode");
            $itemuom->Snama = $this->GetPostValue("Snama");
            if ($this->ValidateData($itemuom)) {
                $itemuom->CreatebyId = $this->userUid;
                $rs = $itemuom->Insert();
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Add New Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Data Satuan: %s (%s) sudah berhasil disimpan", $itemuom->Snama, $itemuom->Skode));
                    redirect_url("master.itemuom");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Add New Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Failed');
                    $this->Set("error", "Gagal pada saat menyimpan data.. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }
        $this->Set("itemuom", $itemuom);
	}

	public function edit($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses edit.");
            redirect_url("master.itemuom");
        }
        $log = new UserAdmin();
        $itemuom = new ItemUom();
        if (count($this->postData) > 0) {
            $itemuom->Sid = $id;
            $itemuom->Skode = $this->GetPostValue("Skode");
            $itemuom->Snama = $this->GetPostValue("Snama");
            if ($this->ValidateData($itemuom)) {
                $itemuom->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $itemuom->Update($id);
                if ($rs == 1) {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Update Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Success');
                    $this->persistence->SaveState("info", sprintf("Perubahan data satuan: %s (%s) sudah berhasil disimpan", $itemuom->Snama, $itemuom->Skode));
                    redirect_url("master.itemuom");
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Update Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Failed');
                    $this->Set("error", "Gagal pada saat merubah data satuan. Message: " . $this->connector->GetErrorMessage());
                }
            }
        }else{
            $itemuom = $itemuom->LoadById($id);
            if ($itemuom == null || $itemuom->IsDeleted) {
                $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
                redirect_url("master.itemuom");
            }
        }
        $this->Set("itemuom", $itemuom);
	}

	public function delete($id = null) {
        if ($id == null) {
            $this->persistence->SaveState("error", "Harap memilih data terlebih dahulu sebelum melakukan proses penghapusan data.");
            redirect_url("master.itemuom");
        }
        $log = new UserAdmin();
        $itemuom = new ItemUom();
        $itemuom = $itemuom->LoadById($id);
        if ($itemuom == null || $itemuom->IsDeleted) {
            $this->persistence->SaveState("error", "Maaf data yang diminta tidak dapat ditemukan atau sudah dihapus.");
            redirect_url("master.itemuom");
        }
        $rs = $itemuom->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Delete Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Success');
            $this->persistence->SaveState("info", sprintf("Data Satuan Barang: %s (%s) sudah dihapus", $itemuom->Snama, $itemuom->Skode));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Delete Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Failed');
            $this->persistence->SaveState("error", sprintf("Gagal menghapus jenis kontak: %s (%s). Error: %s", $itemuom->Snama, $itemuom->Skode, $this->connector->GetErrorMessage()));
        }
		redirect_url("master.itemuom");
	}

    public function get_data(){
        /*Default request pager params dari jeasyUI*/
        $uoms = new ItemUom();
        $offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 15;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'skode';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
        $sfield = isset($_POST['sfield']) ? strval($_POST['sfield']) : '';
        $scontent = isset($_POST['scontent']) ? strval($_POST['scontent']) : '';
        $offset = ($offset-1)*$limit;
        $data   = $uoms->GetData($offset,$limit,$sfield,$scontent,$sort,$order);
        echo json_encode($data); //return nya json
    }

    public function update($id = null) {
        $itemuom = new ItemUom();
        $log = new UserAdmin();
        $itemuom->Sid = $id;
        $itemuom->Skode = $this->GetPostValue("skode");
        $itemuom->Snama = $this->GetPostValue("snama");
        if ($this->ValidateData($itemuom)) {
            $itemuom->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $rs = $itemuom->Update($id);
            if ($rs == 1) {
                $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Update Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Success');
                echo json_encode(array(
                    'sid' => $itemuom->Sid,
                    'skode' => $itemuom->Skode,
                    'snama' => $itemuom->Snama
                ));
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Update Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Failed');
                echo json_encode(array('errorMsg'=>'Some errors occured.'));
            }
        }
    }

    public function save() {
        $itemuom = new ItemUom();
        $log = new UserAdmin();
        $itemuom->Skode = $this->GetPostValue("skode");
        $itemuom->Snama = $this->GetPostValue("snama");
        if ($this->ValidateData($itemuom)) {
            $itemuom->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
            $rs = $itemuom->Insert();
            if ($rs > 0) {
                $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Add New Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Success');
                echo json_encode(array(
                    'sid' => $rs,
                    'skode' => $itemuom->Skode,
                    'snama' => $itemuom->Snama
                ));
            } else {
                $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Add New Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Failed');
                echo json_encode(array('errorMsg'=>'Some errors occured.'));
            }
        }
    }

    public function hapus($id = null) {
        $itemuom = new ItemUom();
        $log = new UserAdmin();
        $itemuom = $itemuom->LoadById($id);
        if ($itemuom == null || $itemuom->IsDeleted) {
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
        $rs = $itemuom->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Delete Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Success');
            echo json_encode(array('success'=>true));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'master.itemuom','Delete Item UOM -> Kode: '.$itemuom->Skode.' - '.$itemuom->Snama,'-','Failed');
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
    }
}

// End of file: itemuom_controller.php
