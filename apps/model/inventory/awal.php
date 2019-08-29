<?php
class Awal extends EntityBase {
	public $Id;
	public $CabangId;
	public $ItemId;
    public $ItemCode;
    public $OpDate;   
    public $OpQty;
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
		$this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
        $this->OpDate = strtotime($row["op_date"]);        
        $this->OpQty = $row["op_qty"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($cabangId = null,$orderBy = "a.cabang_id, a.item_code") {
        if ($cabangId == null){
            $this->connector->CommandText = "SELECT a.* FROM t_ic_saldoawal AS a ORDER BY $orderBy";
        }else{
            $this->connector->CommandText = "SELECT a.* FROM t_ic_saldoawal AS a Where a.cabang_id = $cabangId ORDER BY $orderBy";
        }
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Awal();
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
		$this->connector->CommandText = "SELECT a.* FROM t_ic_saldoawal AS a WHERE a.id = ?id";
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
        $this->connector->CommandText = "SELECT a.* FROM t_ic_saldoawal AS a WHERE a.cabang_id = ?cabangId And a.item_code = ?itemCode";
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
        $sql = 'INSERT INTO t_ic_saldoawal (cabang_id,item_id,item_code,op_date,op_qty,createby_id,create_time)';
        $sql.= ' VALUES(?cabang_id,?item_id,?item_code,?op_date,?op_qty,?createby_id,now())';
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode,"char");
        $this->connector->AddParameter("?op_date", $this->OpDate);
        $this->connector->AddParameter("?op_qty", $this->OpQty);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        $ret = 0;
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Id = (int)$this->connector->ExecuteScalar();
            $ret = $this->Id;
            $this->connector->CommandText = "SELECT fc_ic_saldoawal_post($ret) As valresult;";
            $rs = $this->connector->ExecuteQuery();
            $row = $rs->FetchAssoc();
            //return strval($row["valresult"]);
        }
		return $ret;
	}

	public function Delete($id) {
        $this->connector->CommandText = "SELECT fc_ic_saldoawal_unpost($id) As valresult;";
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
		$this->connector->CommandText = 'Delete From t_ic_saldoawal Where id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function GetData($entityId = 0,$cabangId = 0,$offset,$limit,$field,$search='',$sort = 'a.item_code',$order = 'ASC') {
        $sql = "SELECT a.* FROM vw_ic_saldoawal as a Where a.item_id > 0 ";
        if ($cabangId > 0){
            $sql.= " And a.cabang_id = ".$cabangId;
        }
        if ($entityId > 0){
            $sql.= " And a.entity_id = ".$entityId;
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
                $rows[$i]['item_id'] = $row['item_id'];
                $rows[$i]['item_code'] = $row['item_code'];
                $rows[$i]['op_date'] = $row['op_date'];
                $rows[$i]['op_qty'] = $row['op_qty'];
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
}
