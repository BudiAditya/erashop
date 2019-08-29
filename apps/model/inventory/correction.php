<?php
class Correction extends EntityBase {
	public $Id;
	public $CabangId;
    public $CorrNo;
	public $ItemId;
    public $ItemCode;
    public $CorrDate;   
    public $CorrQty;
    public $CorrReason;
    public $CorrStatus;
    public $SysQty;
    public $WhsQty;
    public $CreatebyId;
    public $UpdatebyId;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->CabangId = $row["cabang_id"];
        $this->CorrNo = $row["corr_no"];
        $this->CorrDate = strtotime($row["corr_date"]);
		$this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
        $this->CorrReason = $row["corr_reason"];
        $this->SysQty = $row["sys_qty"];
        $this->WhsQty = $row["whs_qty"];
        $this->CorrQty = $row["corr_qty"];
        $this->CorrStatus = $row["corr_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($cabangId = 0,$orderBy = "a.cabang_id, a.item_code") {
        if ($cabangId == 0){
            $this->connector->CommandText = "SELECT a.* FROM t_ic_stockcorrection AS a ORDER BY $orderBy";
        }else{
            $this->connector->CommandText = "SELECT a.* FROM t_ic_stockcorrection AS a Where a.cabang_id = $cabangId ORDER BY $orderBy";
        }
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Correction();
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
		$this->connector->CommandText = "SELECT a.* FROM t_ic_stockcorrection AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();

		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindByKode($cabangId,$itemCode) {
        $this->connector->CommandText = "SELECT a.* FROM t_ic_stockcorrection AS a WHERE a.cabang_id = ?cabangId And a.item_code = ?itemCode";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?itemCode", $itemCode);
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

	public function Insert() {
        $sql = 'INSERT INTO t_ic_stockcorrection (cabang_id,corr_no,corr_date,item_id,item_code,corr_reason,sys_qty,whs_qty,corr_qty,corr_status,createby_id,create_time)';
        $sql.= ' VALUES(?cabang_id,?corr_no,?corr_date,?item_id,?item_code,?corr_reason,?sys_qty,?whs_qty,?corr_qty,?corr_status,?createby_id,now())';
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?corr_no", $this->CorrNo,"char");
        $this->connector->AddParameter("?corr_date", $this->CorrDate);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode,"char");
        $this->connector->AddParameter("?corr_reason", $this->CorrReason);
        $this->connector->AddParameter("?sys_qty", $this->SysQty);
        $this->connector->AddParameter("?whs_qty", $this->WhsQty);
        $this->connector->AddParameter("?corr_qty", $this->CorrQty);
        $this->connector->AddParameter("?corr_status", $this->CorrStatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        $ret = 0;
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            $ret = $this->Id;
            $this->connector->CommandText = "SELECT fc_ic_stockcorrection_post($ret) As valresult;";
            $rs = $this->connector->ExecuteQuery();
            $row = $rs->FetchAssoc();
            return strval($row["valresult"]);
        }
		return $ret;
	}

	public function Delete($id) {
        $this->connector->CommandText = "SELECT fc_ic_stockcorrection_unpost($id) As valresult;";
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
		$this->connector->CommandText = 'Delete From t_ic_stockcorrection Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function Void($id) {
        $this->connector->CommandText = "SELECT fc_ic_stockcorrection_unpost($id) As valresult;";
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        $this->connector->CommandText = 'Update t_ic_stockcorrection a Set a.corr_status = 3 Where a.id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function GetData($cabangId = 0,$offset,$limit,$field,$search='',$sort = 'a.item_code',$order = 'ASC') {
        $sql = "SELECT a.* FROM vw_ic_stockcorrection as a Where a.item_id > 0 ";
        if ($cabangId > 0){
            $sql.= " And cabang_id = ".$cabangId;
        }
        if ($search !='' && $field !=''){
            $sql.= " And $field Like '%{$search}%' ";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order Limit {$offset},{$limit}";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $rows = array();
        if ($rs != null) {
            $i = 0;
            while ($row = $rs->FetchAssoc()) {
                $rows[$i]['id'] = $row['id'];
                $rows[$i]['cabang_id'] = $row['cabang_id'];
                $rows[$i]['cabang_code'] = $row['cabang_code'];
                $rows[$i]['corr_date'] = $row['corr_date'];
                $rows[$i]['corr_no'] = $row['corr_no'];
                $rows[$i]['corr_reason'] = $row['corr_reason'];
                $rows[$i]['item_id'] = $row['item_id'];
                $rows[$i]['item_code'] = $row['item_code'];
                $rows[$i]['sys_qty'] = $row['sys_qty'];
                $rows[$i]['whs_qty'] = $row['whs_qty'];
                $rows[$i]['corr_qty'] = $row['corr_qty'];
                $rows[$i]['bnama'] = $row['bnama'];
                $rows[$i]['bsatbesar'] = $row['bsatbesar'];
                $rows[$i]['bsatkecil'] = $row['bsatkecil'];
                $i++;
            }
        }
        //data hasil query yang dikirim kembali dalam format json
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetCorrectionDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'ICR';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->CorrDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }
}
