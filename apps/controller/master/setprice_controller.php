<?php

class SetPriceController extends AppController {
	private $userUid;
    private $userCompanyId;
    private $userCabangId;
    private $userLevel;

	protected function Initialize() {
		require_once(MODEL . "master/setprice.php");
		$this->userUid = AclManager::GetInstance()->GetCurrentUser()->Id;
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
        $this->userCabangId = $this->persistence->LoadState("cabang_id");
        $this->userLevel = $this->persistence->LoadState("user_lvl");
	}

	public function index() {
        // index script here
        require_once(MODEL . "master/cabang.php");
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
        $this->Set("userLevel", $this->userLevel);
        $this->Set("userCabId", $this->userCabangId);
        $this->Set("userCabCode", $cabCode);
        $this->Set("userCabName", $cabName);
        $this->Set("cabangs", $cabang);
	}

	private function ValidateData(SetPrice $setprice) {
		return true;
	}

	public function get_data(){
        /*Default request pager params dari jeasyUI*/
        //if ($this->userLevel > 3){
            $cabangId = 0;
        //}else{
        //    $cabangId = $this->userCabangId;
        //}
        $prices = new SetPrice();
        $offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 15;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'item_code';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
        $sfield = isset($_POST['sfield']) ? strval($_POST['sfield']) : '';
        $scontent = isset($_POST['scontent']) ? strval($_POST['scontent']) : '';
        $offset = ($offset-1)*$limit;
        $data   = $prices->GetData($cabangId,$offset,$limit,$sfield,$scontent,$sort,$order);
        echo json_encode($data); //return nya json
    }

    public function update($id = null) {
        require_once(MODEL . "master/items.php");
        $setprice = new SetPrice($id);
        if ($setprice == null){
            echo json_encode(array('errorMsg'=>'Data Harga Barang tidak ditemukan..'));
        }else{
            $setprice->ItemId = $this->GetPostValue("item_id");
            $items = new Items($setprice->ItemId);
            if ($items == null){
                echo json_encode(array('errorMsg'=>'Data Barang tidak ditemukan..'));
            }else{
                $setprice->ItemCode = $items->Bkode;
                $setprice->CabangId = $this->userCabangId;
                $setprice->PriceDate = $this->GetPostValue("price_date");
                $setprice->MaxDisc = $this->GetPostValue("max_disc");
                $setprice->HrgBeli = $this->GetPostValue("hrg_beli");
                $setprice->Markup1 = $this->GetPostValue("markup1");
                $setprice->HrgJual1 = $this->GetPostValue("hrg_jual1");
                $setprice->Markup2 = $this->GetPostValue("markup2");
                $setprice->HrgJual2 = $this->GetPostValue("hrg_jual2");
                $setprice->Markup3 = $this->GetPostValue("markup3");
                $setprice->HrgJual3 = $this->GetPostValue("hrg_jual3");
                $setprice->Markup4 = $this->GetPostValue("markup4");
                $setprice->HrgJual4 = $this->GetPostValue("hrg_jual4");
                $setprice->Markup5 = $this->GetPostValue("markup5");
                $setprice->HrgJual5 = $this->GetPostValue("hrg_jual5");
                $setprice->Markup6 = $this->GetPostValue("markup6");
                $setprice->HrgJual6 = $this->GetPostValue("hrg_jual6");
                if ($this->ValidateData($setprice)) {
                    $setprice->UpdatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                    $rs = $setprice->Update($id);
                    if ($rs == 1) {
                        echo json_encode(array(
                            'id' => $setprice->Id,
                            'item_id' => $setprice->ItemId,
                            'item_code' => $setprice->ItemCode
                        ));
                    } else {
                        echo json_encode(array('errorMsg'=>'Proses update data gagal..('.$rs.')->'.$this->connector->GetErrorMessage()));
                    }
                }
            }
        }
    }

    public function save() {
        require_once(MODEL . "master/items.php");
        $setprice = new SetPrice();
        $setprice->ItemId = $this->GetPostValue("item_id");
        $items = new Items($setprice->ItemId);
        if ($items == null){
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }else{
            $setprice->ItemCode = $items->Bkode;
            $setprice->CabangId = $this->userCabangId;
            $setprice->PriceDate = $this->GetPostValue("price_date");
            $setprice->MaxDisc = $this->GetPostValue("max_disc");
            $setprice->HrgBeli = $this->GetPostValue("hrg_beli");
            $setprice->Markup1 = $this->GetPostValue("markup1");
            $setprice->HrgJual1 = $this->GetPostValue("hrg_jual1");
            $setprice->Markup2 = $this->GetPostValue("markup2");
            $setprice->HrgJual2 = $this->GetPostValue("hrg_jual2");
            $setprice->Markup3 = $this->GetPostValue("markup3");
            $setprice->HrgJual3 = $this->GetPostValue("hrg_jual3");
            $setprice->Markup4 = $this->GetPostValue("markup4");
            $setprice->HrgJual4 = $this->GetPostValue("hrg_jual4");
            $setprice->Markup5 = $this->GetPostValue("markup5");
            $setprice->HrgJual5 = $this->GetPostValue("hrg_jual5");
            $setprice->Markup6 = $this->GetPostValue("markup6");
            $setprice->HrgJual6 = $this->GetPostValue("hrg_jual6");
            if ($this->ValidateData($setprice)) {
                $setprice->CreatebyId = AclManager::GetInstance()->GetCurrentUser()->Id;
                $rs = $setprice->Insert();
                if ($rs > 0) {
                    echo json_encode(array(
                        'id' => $rs,
                        'item_id' => $setprice->ItemId,
                        'item_code' => $setprice->ItemCode
                    ));
                } else {
                    echo json_encode(array('errorMsg'=>'Some errors occured.'));
                }
            }
        }
    }

    public function copy_data(){
        // proses copy data harga antar cabang
        if (count($this->postData) > 0) {
            $setprice = new SetPrice();
            $fCabangId = $this->GetPostValue("frCabangId");
            $tCabangId = $this->GetPostValue("toCabangId");
            $setprice = $setprice->LoadAll($fCabangId);
            if ($setprice == null) {
                echo json_encode(array('errorMsg'=>'Data Harga tidak belum ada..'));
            }else{
                $setprice = new SetPrice();
                $rs = $setprice->CopyData($fCabangId,$tCabangId);
                if ($rs > 0) {
                    echo json_encode(array('success'=>true));
                } else {
                    echo json_encode(array('errorMsg'=>'Proses Copy data gagal.. (rs = '.$rs.' Error: )'.$this->connector->GetErrorMessage()));
                }
            }
        }else{
            echo json_encode(array('errorMsg'=>'Proses Copy data gagal.. (No data sended)'));
        }
    }

    public function hapus($id = null) {
        $setprice = new SetPrice();
        $setprice = $setprice->LoadById($id);
        if ($setprice == null) {
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
        $rs = $setprice->Delete($id);
        if ($rs > 0) {
            echo json_encode(array('success'=>true));
        } else {
            echo json_encode(array('errorMsg'=>'Some errors occured.'));
        }
    }

    public function getitempricestock_plain($cabangId,$bkode,$level){
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

    public function getitemprices_plain($cabangId,$bkode){
        require_once(MODEL . "master/items.php");
        $ret = 'ER|0';
        if($bkode != null || $bkode != ''){
            /** @var $setprice SetPrice */
            /** @var $items Items  */
            $items = new Items();
            $items = $items->LoadByKode($bkode);
            $hrg_beli = 0;
            $hrg_jual = 0;
            $setprice = null;
            if ($items != null){
                $setprice = new SetPrice();
                $setprice = $setprice->FindByKode($cabangId,$bkode);
                if ($setprice != null){
                    $hrg_beli = $setprice->HrgBeli;
                    $hrg_jual = $setprice->HrgJual1;
                }
                if($hrg_beli == null){
                    $hrg_beli = 0;
                }
                if($hrg_jual == null){
                    $hrg_jual = 0;
                }
                $ret = "OK|".$items->Bid.'|'.$items->Bnama.'|'.$items->Bsatbesar.'|'.$hrg_beli.'|'.$hrg_jual;
            }
        }
        print $ret;
    }

    public function getitempricestock_json($level,$cabangId){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $setprice = new SetPrice();
        $itemlists = $setprice->GetJSonItemPriceStock($level,$cabangId,$filter);
        echo json_encode($itemlists);
    }

    public function getitemprices_json($order="a.bnama"){
        $filter = isset($_POST['q']) ? strval($_POST['q']) : '';
        $setprice = new SetPrice();
        $itemlists = $setprice->GetJSonItemPrice($filter,$order);
        echo json_encode($itemlists);
    }
}

// End of file: setprice_controller.php
