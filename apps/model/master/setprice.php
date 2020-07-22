<?php
class SetPrice extends EntityBase {
	public $Id;
	public $CabangId = 0;
    public $CabangCode;
	public $ItemId = 0;
    public $ItemCode;
    public $ItemName;
    public $Satuan;
    public $PriceDate;
    public $HrgBeli = 0;
    public $MaxDisc = 0;
    public $Markup1 = 0;
    public $Markup2 = 0;
    public $Markup3 = 0;
    public $Markup4 = 0;
    public $Markup5 = 0;
    public $Markup6 = 0;
    public $nMarkup1 = 0;
    public $nMarkup2 = 0;
    public $nMarkup3 = 0;
    public $nMarkup4 = 0;
    public $nMarkup5 = 0;
    public $nMarkup6 = 0;
    public $HrgJual1 = 0;
    public $HrgJual2 = 0;
    public $HrgJual3 = 0;
    public $HrgJual4 = 0;
    public $HrgJual5 = 0;
    public $HrgJual6 = 0;
    public $HrgJual1k = 0;
    public $HrgJual2k = 0;
    public $HrgJual3k = 0;
    public $HrgJual4k = 0;
    public $HrgJual5k = 0;
    public $HrgJual6k = 0;
    public $QtyStock = 0;
    public $CreatebyId = 0;
    public $UpdatebyId = 0;
    public $SatBesar;
    public $SatKecil;
    public $IsiKecil = 0;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->CabangId = $row["cabang_id"];
        $this->CabangCode = $row["cabang_code"];
		$this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
        $this->ItemName = $row["item_name"];
        $this->Satuan = $row["satuan"];
        $this->SatBesar = $row["bsatbesar"];
        $this->SatKecil = $row["bsatkecil"];
        $this->PriceDate = strtotime($row["price_date"]);
        $this->HrgBeli = $row["hrg_beli"];
        $this->MaxDisc = $row["max_disc"];
        $this->Markup1 = $row["markup1"];
        $this->Markup2 = $row["markup2"];
        $this->Markup3 = $row["markup3"];
        $this->Markup4 = $row["markup4"];
        $this->Markup5 = $row["markup5"];
        $this->Markup6 = $row["markup6"];
        $this->nMarkup1 = $row["nmarkup1"];
        $this->nMarkup2 = $row["nmarkup2"];
        $this->nMarkup3 = $row["nmarkup3"];
        $this->nMarkup4 = $row["nmarkup4"];
        $this->nMarkup5 = $row["nmarkup5"];
        $this->nMarkup6 = $row["nmarkup6"];
        $this->HrgJual1 = $row["hrg_jual1"];
        $this->HrgJual2 = $row["hrg_jual2"];
        $this->HrgJual3 = $row["hrg_jual3"];
        $this->HrgJual4 = $row["hrg_jual4"];
        $this->HrgJual5 = $row["hrg_jual5"];
        $this->HrgJual6 = $row["hrg_jual6"];
        $this->HrgJual1k = $row["hrg_jual1k"];
        $this->HrgJual2k = $row["hrg_jual2k"];
        $this->HrgJual3k = $row["hrg_jual3k"];
        $this->HrgJual4k = $row["hrg_jual4k"];
        $this->HrgJual5k = $row["hrg_jual5k"];
        $this->HrgJual6k = $row["hrg_jual6k"];
        $this->QtyStock = $row["qty_stock"];
        $this->IsiKecil = $row["bisisatkecil"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

    public function FormatPriceDate($format = HUMAN_DATE) {
        return is_int($this->PriceDate) ? date($format, $this->PriceDate) : date($format, strtotime(date('Y-m-d')));
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($cabangId = 0,$orderBy = "a.cabang_id, a.item_code") {
        $sqx = "SELECT a.* FROM vw_m_itemprice AS a";
        if ($cabangId > 0) {
            $sqx .= " Where a.cabang_id = $cabangId";
        }
        $sqx.= " ORDER BY $orderBy;";
        $this->connector->CommandText = $sqx;
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new SetPrice();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Location
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_m_itemprice AS a WHERE a.id = ?id Limit 1";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();

		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByKode($cabangId=0,$itemCode) {
        if ($cabangId > 0){
            $this->connector->CommandText = "SELECT a.* FROM vw_m_itempricestock AS a WHERE a.cabang_id = ?cabangId And a.item_code = ?itemCode";
        }else{
            $this->connector->CommandText = "SELECT a.* FROM vw_m_itempricestock AS a WHERE a.item_code = ?itemCode Limit 1";
        }
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?itemCode", $itemCode, "varchar");
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

	/**
	 * @param int $id
	 * @return Location
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

    public function  FindPriceByKode($cabangId,$itemCode){
        $sql = "Select coalesce(a.id,0) as ValResult From m_set_price a Where a.cabang_id = $cabangId And a.item_code = '".$itemCode."'";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["ValResult"]);
    }

	public function Insert() {
        $sql = 'INSERT INTO m_set_price(satuan,cabang_id,item_id,item_code,price_date,hrg_beli,max_disc,markup1,markup2,markup3,markup4,markup5,markup6,nmarkup1,nmarkup2,nmarkup3,nmarkup4,nmarkup5,nmarkup6,hrg_jual1,hrg_jual2,hrg_jual3,hrg_jual4,hrg_jual5,hrg_jual6,hrg_jual1k,hrg_jual2k,hrg_jual3k,hrg_jual4k,hrg_jual5k,hrg_jual6k,createby_id,create_time)';
        $sql.= ' VALUES(?satuan,?cabang_id,?item_id,?item_code,?price_date,?hrg_beli,?max_disc,?markup1,?markup2,?markup3,?markup4,?markup5,?markup6,?nmarkup1,?nmarkup2,?nmarkup3,?nmarkup4,?nmarkup5,?nmarkup6,?hrg_jual1,?hrg_jual2,?hrg_jual3,?hrg_jual4,?hrg_jual5,?hrg_jual6,?hrg_jual1k,?hrg_jual2k,?hrg_jual3k,?hrg_jual4k,?hrg_jual5k,?hrg_jual6k,?createby_id,now())';
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode,"char");
        $this->connector->AddParameter("?price_date", $this->PriceDate);
        $this->connector->AddParameter("?hrg_beli", $this->HrgBeli);
        $this->connector->AddParameter("?max_disc", $this->MaxDisc);
        $this->connector->AddParameter("?markup1", $this->Markup1);
        $this->connector->AddParameter("?markup2", $this->Markup2);
        $this->connector->AddParameter("?markup3", $this->Markup3);
        $this->connector->AddParameter("?markup4", $this->Markup4);
        $this->connector->AddParameter("?markup5", $this->Markup5);
        $this->connector->AddParameter("?markup6", $this->Markup6);
        $this->connector->AddParameter("?nmarkup1", $this->nMarkup1);
        $this->connector->AddParameter("?nmarkup2", $this->nMarkup2);
        $this->connector->AddParameter("?nmarkup3", $this->nMarkup3);
        $this->connector->AddParameter("?nmarkup4", $this->nMarkup4);
        $this->connector->AddParameter("?nmarkup5", $this->nMarkup5);
        $this->connector->AddParameter("?nmarkup6", $this->nMarkup6);
        $this->connector->AddParameter("?hrg_jual1", $this->HrgJual1);
        $this->connector->AddParameter("?hrg_jual2", $this->HrgJual2);
        $this->connector->AddParameter("?hrg_jual3", $this->HrgJual3);
        $this->connector->AddParameter("?hrg_jual4", $this->HrgJual4);
        $this->connector->AddParameter("?hrg_jual5", $this->HrgJual5);
        $this->connector->AddParameter("?hrg_jual6", $this->HrgJual6);
        $this->connector->AddParameter("?hrg_jual1k", $this->HrgJual1k);
        $this->connector->AddParameter("?hrg_jual2k", $this->HrgJual2k);
        $this->connector->AddParameter("?hrg_jual3k", $this->HrgJual3k);
        $this->connector->AddParameter("?hrg_jual4k", $this->HrgJual4k);
        $this->connector->AddParameter("?hrg_jual5k", $this->HrgJual5k);
        $this->connector->AddParameter("?hrg_jual6k", $this->HrgJual6k);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
        }
		return $rs;
	}

	public function Update($id) {
        $this->connector->CommandText = 'Insert Into m_set_price_history Select a.* From m_set_price as a Where a.id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'UPDATE m_set_price SET satuan = ?satuan,cabang_id = ?cabang_id,item_id = ?item_id,item_code = ?item_code,price_date = ?price_date,hrg_beli = ?hrg_beli,max_disc = ?max_disc,markup1 = ?markup1,markup2 = ?markup2,markup3 = ?markup3,markup4 = ?markup4,markup5 = ?markup5,markup6 = ?markup6,nmarkup1 = ?nmarkup1,nmarkup2 = ?nmarkup2,nmarkup3 = ?nmarkup3,nmarkup4 = ?nmarkup4,nmarkup5 = ?nmarkup5,nmarkup6 = ?nmarkup6,hrg_jual1 = ?hrg_jual1,hrg_jual2 = ?hrg_jual2,hrg_jual3 = ?hrg_jual3,hrg_jual4 = ?hrg_jual4,hrg_jual5 = ?hrg_jual5,hrg_jual6 = ?hrg_jual6,hrg_jual1k = ?hrg_jual1k,hrg_jual2k = ?hrg_jual2k,hrg_jual3k = ?hrg_jual3k,hrg_jual4k = ?hrg_jual4k,hrg_jual5k = ?hrg_jual5k,hrg_jual6k = ?hrg_jual6k,updateby_id = ?updateby_id,update_time = now() WHERE id = ?id';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode,"char");
        $this->connector->AddParameter("?price_date", $this->PriceDate);
        $this->connector->AddParameter("?hrg_beli", $this->HrgBeli);
        $this->connector->AddParameter("?max_disc", $this->MaxDisc);
        $this->connector->AddParameter("?markup1", $this->Markup1);
        $this->connector->AddParameter("?markup2", $this->Markup2);
        $this->connector->AddParameter("?markup3", $this->Markup3);
        $this->connector->AddParameter("?markup4", $this->Markup4);
        $this->connector->AddParameter("?markup5", $this->Markup5);
        $this->connector->AddParameter("?markup6", $this->Markup6);
        $this->connector->AddParameter("?nmarkup1", $this->nMarkup1);
        $this->connector->AddParameter("?nmarkup2", $this->nMarkup2);
        $this->connector->AddParameter("?nmarkup3", $this->nMarkup3);
        $this->connector->AddParameter("?nmarkup4", $this->nMarkup4);
        $this->connector->AddParameter("?nmarkup5", $this->nMarkup5);
        $this->connector->AddParameter("?nmarkup6", $this->nMarkup6);
        $this->connector->AddParameter("?hrg_jual1", $this->HrgJual1);
        $this->connector->AddParameter("?hrg_jual2", $this->HrgJual2);
        $this->connector->AddParameter("?hrg_jual3", $this->HrgJual3);
        $this->connector->AddParameter("?hrg_jual4", $this->HrgJual4);
        $this->connector->AddParameter("?hrg_jual5", $this->HrgJual5);
        $this->connector->AddParameter("?hrg_jual6", $this->HrgJual6);
        $this->connector->AddParameter("?hrg_jual1k", $this->HrgJual1k);
        $this->connector->AddParameter("?hrg_jual2k", $this->HrgJual2k);
        $this->connector->AddParameter("?hrg_jual3k", $this->HrgJual3k);
        $this->connector->AddParameter("?hrg_jual4k", $this->HrgJual4k);
        $this->connector->AddParameter("?hrg_jual5k", $this->HrgJual5k);
        $this->connector->AddParameter("?hrg_jual6k", $this->HrgJual6k);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
        return $rs;
	}

    public function CopyData($fCabangId,$tCabangId){
        $this->connector->CommandText = 'Delete From m_set_price Where cabang_id = ?tcabang_id';
        $this->connector->AddParameter("?tcabang_id", $tCabangId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'INSERT INTO m_set_price (cabang_id,item_id,item_code,price_date,hrg_beli,max_disc,markup1,markup2,markup3,markup4,markup5,markup6,hrg_jual1,hrg_jual2,hrg_jual3,hrg_jual4,hrg_jual5,hrg_jual6,createby_id,create_time)';
        $sql.= ' Select ?tcabang_id,item_id,item_code,price_date,hrg_beli,max_disc,markup1,markup2,markup3,markup4,markup5,markup6,hrg_jual1,hrg_jual2,hrg_jual3,hrg_jual4,hrg_jual5,hrg_jual6,createby_id,now()';
        $sql.= ' From m_set_price Where cabang_id = ?fcabang_id Order By price_date,item_code;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?fcabang_id", $fCabangId);
        $this->connector->AddParameter("?tcabang_id", $tCabangId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

	public function Delete($id) {
        $this->connector->CommandText = 'Insert Into m_set_price_history Select a.* From m_set_price as a Where a.id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
		$this->connector->CommandText = 'Delete From m_set_price Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function DeleteByKode($cabangId,$itemCode) {
        $this->connector->CommandText = 'Insert Into m_set_price_history Select a.* From m_set_price as a Where a.cabang_id = ?cabangId And a.item_code = ?itemCode';
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?itemCode", $itemCode, "varchar");
        $rs = $this->connector->ExecuteNonQuery();
        $this->connector->CommandText = 'Delete From m_set_price Where cabang_id = ?cabangId And item_code = ?itemCode';
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?itemCode", $itemCode, "varchar");
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function GetData($cabangId,$offset,$limit,$field,$search='',$sort = 'a.bkode',$order = 'ASC') {
        if ($cabangId > 0){
           $sql = "SELECT a.* FROM vw_m_itemprice as a Where a.cabang_id = $cabangId ";
        }else{
            $sql = "SELECT a.* FROM vw_m_itemprice as a Where a.cabang_id > 0 ";
        }
        if ($search !='' && $field !=''){
            $sql.= "And $field Like '%{$search}%' ";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= "Order By $sort $order Limit {$offset},{$limit}";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $rows = array();
        if ($rs != null) {
            $i = 0;
            while ($row = $rs->FetchAssoc()) {
                $rows[$i]['item_id'] = $row['item_id'];
                $rows[$i]['cabang_id'] = $row['cabang_id'];
                $rows[$i]['cabang_code'] = $row['cabang_code'];
                $rows[$i]['cabang_name'] = $row['cabang_name'];
                $rows[$i]['item_code'] = $row['item_code'];
                $rows[$i]['satuan'] = $row['satuan'];
                $rows[$i]['price_date'] = $row['price_date'];
                $rows[$i]['hrg_beli'] = $row['hrg_beli'];
                $rows[$i]['max_disc'] = $row['max_disc'];
                $rows[$i]['markup1'] = $row['markup1'];
                $rows[$i]['markup2'] = $row['markup2'];
                $rows[$i]['markup3'] = $row['markup3'];
                $rows[$i]['markup4'] = $row['markup4'];
                $rows[$i]['markup5'] = $row['markup5'];
                $rows[$i]['markup6'] = $row['markup6'];
                $rows[$i]['nmarkup1'] = $row['nmarkup1'];
                $rows[$i]['nmarkup2'] = $row['nmarkup2'];
                $rows[$i]['nmarkup3'] = $row['nmarkup3'];
                $rows[$i]['nmarkup4'] = $row['nmarkup4'];
                $rows[$i]['nmarkup5'] = $row['nmarkup5'];
                $rows[$i]['nmarkup6'] = $row['nmarkup6'];
                $rows[$i]['hrg_jual1'] = $row['hrg_jual1'];
                $rows[$i]['hrg_jual2'] = $row['hrg_jual2'];
                $rows[$i]['hrg_jual3'] = $row['hrg_jual3'];
                $rows[$i]['hrg_jual4'] = $row['hrg_jual4'];
                $rows[$i]['hrg_jual5'] = $row['hrg_jual5'];
                $rows[$i]['hrg_jual6'] = $row['hrg_jual6'];
                $rows[$i]['hrg_jual1k'] = $row['hrg_jual1k'];
                $rows[$i]['hrg_jual2k'] = $row['hrg_jual2k'];
                $rows[$i]['hrg_jual3k'] = $row['hrg_jual3k'];
                $rows[$i]['hrg_jual4k'] = $row['hrg_jual4k'];
                $rows[$i]['hrg_jual5k'] = $row['hrg_jual5k'];
                $rows[$i]['hrg_jual6k'] = $row['hrg_jual6k'];
                $rows[$i]['item_name'] = $row['item_name'];
                $rows[$i]['bsatbesar'] = $row['bsatbesar'];
                $rows[$i]['bsatkecil'] = $row['bsatkecil'];
                $rows[$i]['supplier_name'] = $row['supplier_name'];
                $i++;
            }
        }
        //data hasil query yang dikirim kembali dalam format json
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetJSonItemPriceStock($level = 0,$cabang_id,$filter = null,$sort = 'a.item_name',$order = 'ASC') {
        $sql = "SELECT a.item_id, a.item_code, a.satuan, a.item_name, a.satuan, a.bsatbesar, a.bsatkecil, a.qty_stock, a.hrg_beli,";
        if($level == -1){
            $sql.= "if(a.hrg_beli = 0, a.hrg_jual1, a.hrg_beli) as hrg_jual,";
            $sql.= "if(a.hrg_beli = 0, a.hrg_jual1k, a.hrg_beli) as hrg_jualk";
        }elseif($level == 1){
            $sql.= "if(a.hrg_jual2 = 0, a.hrg_jual1, a.hrg_jual2) as hrg_jual,";
            $sql.= "if(a.hrg_jual2k = 0, a.hrg_jual1k, a.hrg_jual2k) as hrg_jualk";
        }elseif($level == 2){
            $sql.= "if(a.hrg_jual3 = 0, a.hrg_jual1, a.hrg_jual3) as hrg_jual,";
            $sql.= "if(a.hrg_jual3k = 0, a.hrg_jual1k, a.hrg_jual3k) as hrg_jualk";
        }elseif($level == 3){
            $sql.= "if(a.hrg_jual4 = 0, a.hrg_jual1, a.hrg_jual4) as hrg_jual,";
            $sql.= "if(a.hrg_jual4k = 0, a.hrg_jual1k, a.hrg_jual4k) as hrg_jualk";
        }elseif($level == 4){
            $sql.= "if(a.hrg_jual5 = 0, a.hrg_jual1, a.hrg_jual5) as hrg_jual,";
            $sql.= "if(a.hrg_jual5k = 0, a.hrg_jual1k, a.hrg_jual5k) as hrg_jualk";
        }elseif($level == 5){
            $sql.= "if(a.hrg_jual6 = 0, a.hrg_jual1, a.hrg_jual6) as hrg_jual,";
            $sql.= "if(a.hrg_jual6k = 0, a.hrg_jual1k, a.hrg_jual6k) as hrg_jualk";
        }else{
            $sql.= "a.hrg_jual1 as hrg_jual,";
            $sql.= "a.hrg_jual1k as hrg_jualk";
        }
        if ($cabang_id == 2){
            $sql.= " FROM vw_m_itempricestock as a Where a.cabang_id = $cabang_id";
        }else{
            $sql.= " FROM vw_m_itempricestock as a Where a.cabang_id = $cabang_id and a.qty_stock >= 0";
        }
        //filtering
        if ($filter != null){
            $sql.= " And (a.item_code Like '%$filter%' Or a.item_name Like '%$filter%')";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetJSonItemStock($alomin = 0,$cabang_id,$filter = null,$sort = 'b.bnama',$order = 'ASC') {
        $sql = "SELECT a.item_id,a.item_code,b.bnama as item_name,b.bsatkecil as satkecil,b.bsatbesar as satbesar,b.bisisatkecil as isikecil,a.qty_stock,0 as qty_order, 0 as price FROM t_ic_stockcenter AS a INNER JOIN m_barang AS b ON a.item_code = b.bkode";
        if ($alomin == 1){
            $sql.= " Where a.cabang_id = $cabang_id";
        }else{
            $sql.= " Where a.cabang_id = $cabang_id and a.qty_stock >= 0";
        }
        if ($filter != null){
            $sql.= " And (a.item_code Like '%$filter%' Or b.bnama Like '%$filter%')";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetJSonItemPrice($entityId,$cabangId,$filter,$sort = 'a.bnama',$order = 'ASC') {
        $sql = "SELECT b.cabang_id,a.bid as item_id,a.bkode as item_code,a.bnama as item_name,a.bsatbesar as sat_besar,a.bsatkecil as sat_kecil,coalesce(b.hrg_beli,0) as hrg_beli,coalesce(b.hrg_jual,0) as hrg_jual,b.satuan";
        $sql.= " FROM m_barang as a LEFT JOIN (Select c.cabang_id,c.item_code,c.satuan,max(c.hrg_beli) as hrg_beli, max(c.hrg_jual1) as hrg_jual From m_set_price as c Group By c.cabang_id,c.item_code,c.satuan) as b";
        //$sql.= " ON a.bkode = b.item_code Left Join m_cabang as d On a.def_cabang_id = d.id Where a.bisaktif = 1";
        $sql.= " ON a.bkode = b.item_code Where a.bisaktif = 1";
        if ($filter != null){
            $sql.= " And (a.bkode Like '%$filter%' Or a.bnama Like '%$filter%')";
        }
        if ($cabangId > 0){
            $sql .= " And b.cabang_id = ".$cabangId;
        }
        //$sql.= " And Not (a.item_level = 1 And d.entity_id <> ".$entityId.") And Not (a.item_level = 2 And a.def_cabang_id <>".$cabangId.")";
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetItemPrice($cabId = 0,$itemCode,$level){
        $sql = "SELECT a.hrg_beli,";
        if($level == -1){
            $sql.= "if(a.hrg_beli = 0, a.hrg_jual1, a.hrg_beli) as hrg_jual,";
            $sql.= "if(a.hrg_beli = 0, a.hrg_jual1k, a.hrg_beli) as hrg_jualk";
        }elseif($level == 1){
            $sql.= "if(a.hrg_jual2 = 0, a.hrg_jual1, a.hrg_jual2) as hrg_jual,";
            $sql.= "if(a.hrg_jual2k = 0, a.hrg_jual1k, a.hrg_jual2k) as hrg_jualk";
        }elseif($level == 2){
            $sql.= "if(a.hrg_jual3 = 0, a.hrg_jual1, a.hrg_jual3) as hrg_jual,";
            $sql.= "if(a.hrg_jual3k = 0, a.hrg_jual1k, a.hrg_jual3k) as hrg_jualk";
        }elseif($level == 3){
            $sql.= "if(a.hrg_jual4 = 0, a.hrg_jual1, a.hrg_jual4) as hrg_jual,";
            $sql.= "if(a.hrg_jual4k = 0, a.hrg_jual1k, a.hrg_jual4k) as hrg_jualk";
        }elseif($level == 4){
            $sql.= "if(a.hrg_jual5 = 0, a.hrg_jual1, a.hrg_jual5) as hrg_jual,";
            $sql.= "if(a.hrg_jual5k = 0, a.hrg_jual1k, a.hrg_jual5k) as hrg_jualk";
        }elseif($level == 5){
            $sql.= "if(a.hrg_jual6 = 0, a.hrg_jual1, a.hrg_jual6) as hrg_jual,";
            $sql.= "if(a.hrg_jual6k = 0, a.hrg_jual1k, a.hrg_jual6k) as hrg_jualk";
        }else{
            $sql.= "a.hrg_jual1 as hrg_jual,";
            $sql.= "a.hrg_jual1k as hrg_jualk";
        }
        if ($cabId > 0) {
            $sql .= " FROM vw_m_itemprice as a Where a.cabang_id = $cabId And a.item_code = '" . $itemCode . "';";
        }else{
            $sql .= " FROM vw_m_itemprice as a Where a.item_code = '" . $itemCode . "';";
        }
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $result = '0|0|0';
        if ($rs) {
            $row = $rs->FetchAssoc();
            $result = $row["hrg_beli"].'|'.$row["hrg_jual"].'|'.$row["hrg_jualk"];
        }
        return $result;
    }
}
