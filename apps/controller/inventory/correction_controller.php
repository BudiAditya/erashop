<?php

class CorrectionController extends AppController {
	private $userUid;
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;

	protected function Initialize() {
		require_once(MODEL . "inventory/correction.php");
        require_once(MODEL . "master/user_admin.php");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
        // index script here
        require_once(MODEL . "master/cabang.php");
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
        $this->Set("cabangs", $cabang);
	}

	private function ValidateData(Correction $koreksi) {
		return true;
	}

	public function get_data(){
        /*Default request pager params dari jeasyUI*/
        $cabangId = $this->userCabangId;
        $koreksi = new Correction();
        $offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 15;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
        $sfield = isset($_POST['sfield']) ? strval($_POST['sfield']) : '';
        $scontent = isset($_POST['scontent']) ? strval($_POST['scontent']) : '';
        $offset = ($offset-1)*$limit;
        $data   = $koreksi->GetData($cabangId,$offset,$limit,$sfield,$scontent,$sort,$order);
        echo json_encode($data); //return nya json
    }

    public function save() {
        require_once(MODEL . "master/items.php");
        $koreksi = new Correction();
        $log = new UserAdmin();
        $koreksi->ItemId = $this->GetPostValue("aItemId");
        $items = new Items($koreksi->ItemId);
        if ($items == null){
            echo json_encode(array('errorMsg'=>'Data Barang tidak ditemukan'));
        }else{
            $koreksi->ItemCode = $items->Bkode;
            $koreksi->CabangId = $this->GetPostValue("aCabangId");
            $koreksi->CorrDate = $this->GetPostValue("aCorrDate");
            $koreksi->CorrQty = $this->GetPostValue("aCorrQty");
            $koreksi->SysQty = 0;
            $koreksi->WhsQty = 0;
            $koreksi->CorrStatus = 1;
            $koreksi->CorrReason = $this->GetPostValue("aCorrReason");
            if ($this->ValidateData($koreksi)) {
                $koreksi->CorrNo = $koreksi->GetCorrectionDocNo();
                $koreksi->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $koreksi->Insert();
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.correction','Add Stock Correction - Date: '.date('Y-m-d',$koreksi->CorrDate).' Item Code: '.$koreksi->ItemCode.' = '.$koreksi->CorrQty,$koreksi->CorrNo,'Success');
                    echo json_encode(array(
                        'id' => $rs,
                        'item_id' => $koreksi->ItemId,
                        'item_code' => $koreksi->ItemCode
                    ));
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.correction','Add Stock Correction - Date: '.date('Y-m-d',$koreksi->CorrDate).' Item Code: '.$koreksi->ItemCode.' = '.$koreksi->CorrQty,$koreksi->CorrNo,'Failed');
                    echo json_encode(array('errorMsg'=>'Gagal proses simpan data..'));
                }
            }
        }
    }

    public function hapus($id = null) {
        $log = new UserAdmin();
        $koreksi = new Correction();
        $koreksi = $koreksi->LoadById($id);
        if ($koreksi == null) {
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
        $rs = $koreksi->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.correction','Delete Stock Correction - Date: '.date('Y-m-d',$koreksi->CorrDate).' Item Code: '.$koreksi->ItemCode.' = '.$koreksi->CorrQty,$koreksi->CorrNo,'Success');
            echo json_encode(array('success'=>true));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.correction','Delete Stock Correction - Date: '.date('Y-m-d',$koreksi->CorrDate).' Item Code: '.$koreksi->ItemCode.' = '.$koreksi->CorrQty,$koreksi->CorrNo,'Failed');
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
    }
}

// End of file: koreksi_controller.php
