<?php

class AwalController extends AppController {
	private $userUid;
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;

	protected function Initialize() {
		require_once(MODEL . "inventory/awal.php");
        require_once(MODEL . "master/user_admin.php");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
        // index script here
        $acl = AclManager::GetInstance();
        $this->Set("cabangId",$this->userCabangId);
        $this->Set("acl",$acl);
	}

	private function ValidateData(Awal $awal) {
		return true;
	}

	public function get_data(){
        /*Default request pager params dari jeasyUI*/
        if ($this->userLevel == 1){
            $entityId = 0;
            $cabangId = $this->userCabangId;
        }elseif ($this->userLevel > 1 && $this->userLevel < 4){
            $entityId = $this->userCompanyId;
            $cabangId = 0;
        }else{
            $cabangId = 0;
            $entityId = 0;
        }
        $awal = new Awal();
        $offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 15;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
        $sfield = isset($_POST['sfield']) ? strval($_POST['sfield']) : '';
        $scontent = isset($_POST['scontent']) ? strval($_POST['scontent']) : '';
        $offset = ($offset-1)*$limit;
        $data   = $awal->GetData($entityId,$cabangId,$offset,$limit,$sfield,$scontent,$sort,$order);
        echo json_encode($data); //return nya json
    }

    public function save() {
        require_once(MODEL . "master/items.php");
        $awal = new Awal();
        $log = new UserAdmin();
        $awal->ItemId = $this->GetPostValue("item_id");
        $items = new Items($awal->ItemId);
        if ($items == null){
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }else{
            $awal->ItemCode = $items->Bkode;
            $awal->CabangId = $this->userCabangId;
            $awal->OpDate = $this->GetPostValue("op_date");
            $awal->OpQty = $this->GetPostValue("op_qty");
            if ($this->ValidateData($awal)) {
                $awal->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $awal->Insert();
                if ($rs > 0) {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.awal','Add New Saldo Awal -> Date: '.date('Y-m-d',$awal->OpDate).' - Item Code: '.$awal->ItemCode.' = '.$awal->OpQty,'-','Success');
                    echo json_encode(array(
                        'id' => $rs,
                        'item_id' => $awal->ItemId,
                        'item_code' => $awal->ItemCode
                    ));
                } else {
                    $log = $log->UserActivityWriter($this->userCabangId,'inventory.awal','Add New Saldo Awal -> Date: '.date('Y-m-d',$awal->OpDate).' - Item Code: '.$awal->ItemCode.' = '.$awal->OpQty,'-','Failed');
                    echo json_encode(array('errorMsg'=>'Some errors occured.'));
                }
            }
        }
    }

    public function hapus($id = null) {
        $awal = new Awal();
        $log = new UserAdmin();
        $awal = $awal->LoadById($id);
        if ($awal == null) {
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
        $rs = $awal->Delete($id);
        if ($rs == 1) {
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.awal','Delete Saldo Awal -> Date: '.date('Y-m-d',$awal->OpDate).' - Item Code: '.$awal->ItemCode.' = '.$awal->OpQty,'-','Success');
            echo json_encode(array('success'=>true));
        } else {
            $log = $log->UserActivityWriter($this->userCabangId,'inventory.awal','Delete Saldo Awal -> Date: '.date('Y-m-d',$awal->OpDate).' - Item Code: '.$awal->ItemCode.' = '.$awal->OpQty,'-','Failed');
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
    }

    public function getjson_items(){
        require_once(MODEL . "master/items.php");
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $items = new Items();
        $itemlists = $items->GetJSonItems($this->userCompanyId,$this->userCabangId,$filter);
        echo json_encode($itemlists);
    }

    public function getplain_items($bkode){
        require_once(MODEL . "master/items.php");
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $items Items */
            $items = new Items();
            $items = $items->FindByKode($bkode);
            if ($items != null){
                $ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$items->Bqtystock.'|'.$items->Bhargabeli.'|'.$items->Bhargajual;
            }
        }
        print $ret;
    }
}

// End of file: awal_controller.php
