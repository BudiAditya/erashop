<?php

require_once("arreturn_detail.php");

class ArReturn extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $RjStatusCodes = array(
		0 => "DRAFT",
		1 => "POSTED",
        2 => "CLOSED",
		3 => "VOID"
	);

    public static $CollectStatusCodes = array(
        0 => "ON HOLD",
        1 => "ON PROCESS",
        2 => "PAID",
        3 => "VOID"
    );

	public $Id;
    public $IsDeleted = false;
    public $EntityId;
    public $AreaId;
    public $EntityCode;
    public $CompanyName;
    public $CabangId;
    public $CabangCode;
	public $RjNo;
	public $RjDate;
    public $CustomerId;
    public $CustomerCode;
    public $CustomerName;
    public $CustomerAddress;
	public $RjDescs;
	public $RjAmount;
    public $RjAllocate;
    public $RjStatus;
	public $CreatebyId;
	public $CreateTime;
	public $UpdatebyId;
	public $UpdateTime;

	/** @var ArReturnDetail[] */
	public $Details = array();

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
        $this->Id = $row["id"];
        $this->IsDeleted = $row["is_deleted"] == 1;
        $this->EntityCode = $row["entity_cd"];
        $this->EntityId = $row["entity_id"];
        $this->AreaId = $row["area_id"];
        $this->CompanyName = $row["company_name"];
        $this->CabangId = $row["cabang_id"];
        $this->CabangCode = $row["cabang_code"];
        $this->RjNo = $row["rj_no"];
        $this->RjDate = strtotime($row["rj_date"]);
        $this->CustomerId = $row["customer_id"];
        $this->CustomerCode = $row["customer_code"];
        $this->CustomerName = $row["customer_name"];
        $this->CustomerAddress = $row["customer_address"];
        $this->RjDescs = $row["rj_descs"];
        $this->RjAmount = $row["rj_amount"];
        $this->RjAllocate = $row["rj_allocate"];
        $this->RjStatus = $row["rj_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->CreateTime = $row["create_time"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->UpdateTime = $row["update_time"];
	}

	public function FormatRjDate($format = HUMAN_DATE) {
		return is_int($this->RjDate) ? date($format, $this->RjDate) : date($format, strtotime(date('Y-m-d')));
	}

	/**
	 * @return ArReturnDetail[]
	 */
	public function LoadDetails() {
		if ($this->Id == null) {
			return $this->Details;
		}
		$detail = new ArReturnDetail();
		$this->Details = $detail->LoadByRjId($this->Id);
		return $this->Details;
	}

	/**
	 * @param int $id
	 * @return ArReturn
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ar_return_master AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ar_return_master AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByRjNo($rjNo,$cabangId) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ar_return_master AS a WHERE a.rj_no = ?rjNo And a.cabang_id = ?cabangId";
		$this->connector->AddParameter("?rjNo", $rjNo);
        $this->connector->AddParameter("?cabangId", $cabangId);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ar_return_master AS a WHERE a.entity_id = ?entityId";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new ArReturn();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ar_return_master AS a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new ArReturn();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
        $sql = "INSERT INTO t_ar_return_master (cabang_id, rj_no, rj_date, customer_id, rj_descs, rj_amount, rj_allocate, rj_status, createby_id, create_time)";
        $sql.= "VALUES(?cabang_id, ?rj_no, ?rj_date, ?customer_id, ?rj_descs, ?rj_amount, ?rj_allocate, ?rj_status, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?rj_no", $this->RjNo, "char");
		$this->connector->AddParameter("?rj_date", $this->RjDate);
        $this->connector->AddParameter("?customer_id", $this->CustomerId);
		$this->connector->AddParameter("?rj_descs", $this->RjDescs);
        $this->connector->AddParameter("?rj_amount", $this->RjAmount);
        $this->connector->AddParameter("?rj_allocate", $this->RjAllocate);
        $this->connector->AddParameter("?rj_status", $this->RjStatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_ar_return_master SET
	cabang_id = ?cabang_id
	, rj_no = ?rj_no
	, rj_date = ?rj_date
	, customer_id = ?customer_id
	, rj_descs = ?rj_descs
	, rj_amount = ?rj_amount
	, rj_allocate = ?rj_allocate
	, rj_status = ?rj_status
	, updateby_id = ?updateby_id
	, update_time = NOW()
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?rj_no", $this->RjNo, "char");
        $this->connector->AddParameter("?rj_date", $this->RjDate);
        $this->connector->AddParameter("?customer_id", $this->CustomerId);
        $this->connector->AddParameter("?rj_descs", $this->RjDescs);
        $this->connector->AddParameter("?rj_amount", $this->RjAmount);
        $this->connector->AddParameter("?rj_allocate", $this->RjAllocate);
        $this->connector->AddParameter("?rj_status", $this->RjStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1){
            $this->RecalculateArReturnMaster($id);
        }
        return $rs;
	}

	public function Delete($id) {
        //fc_ar_rjmaster_unpost
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ar_returnmaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus rjnya
        //if ($rsx > 0){
            $this->connector->CommandText = "Delete From t_ar_return_master WHERE id = ?id";
            $this->connector->AddParameter("?id", $id);
            return $this->connector->ExecuteNonQuery();
        //}else{
        //    return $rsx;
        //}
	}

    public function Void($id) {
        //fc_ar_rjmaster_unpost
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ar_returnmaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus rjnya
        //if ($rsx > 0){
        $this->connector->CommandText = "Update t_ar_return_master a Set a.rj_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
        //}else{
        //    return $rsx;
        //}
    }

    public function GetArReturnDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'RJL';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->RjDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    //$reports = $rj->Load4Reports($sCabangId,$sCustomerId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
    public function Load4Reports($entityId, $cabangId = 0, $customerId = 0, $rjStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_ar_return_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.rj_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($rjStatus > -1){
            $sql.= " and a.rj_status = ".$rjStatus;
        }else{
            $sql.= " and a.rj_status <> 3";
        }
        if ($customerId > 0){
            $sql.= " and a.customer_id = ".$customerId;
        }
        $sql.= " Order By a.rj_date,a.rj_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsDetail($entityId, $cabangId = 0, $customerId = 0, $rjStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT	a.*, b.item_code,b.ex_invoice_no,b.item_descs,b.qty_retur,b.price,b.sub_total FROM vw_ar_return_master AS a JOIN t_ar_return_detail b ON a.rj_no = b.rj_no";
        $sql.= " WHERE a.is_deleted = 0 and a.rj_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($rjStatus > -1){
            $sql.= " and a.rj_status = ".$rjStatus;
        }else{
            $sql.= " and a.rj_status <> 3";
        }
        if ($customerId > 0){
            $sql.= " and a.customer_id = ".$customerId;
        }
        $sql.= " Order By a.rj_date,a.rj_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsRekapItem($entityId, $cabangId = 0, $customerId = 0, $rjStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT b.item_code,b.item_descs,c.bsatkecil as satuan,coalesce(sum(b.qty_retur),0) as sum_qty,coalesce(sum(b.sub_total),0) as sum_total";
        $sql.= " FROM vw_ar_return_master AS a Join t_ar_return_detail AS b On a.rj_no = b.rj_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.rj_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($rjStatus > -1){
            $sql.= " and a.rj_status = ".$rjStatus;
        }else{
            $sql.= " and a.rj_status <> 3";
        }
        if ($customerId > 0){
            $sql.= " and a.customer_id = ".$customerId;
        }
        $sql.= " Group By b.item_code,b.item_descs,c.bsatkecil Order By b.item_descs,b.item_code";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function GetJSonArReturns($cabangId,$customerId) {
        $sql = "SELECT a.id,a.rj_no,a.rj_date,a.rj_amount - a.rj_allocate as rj_balance FROM t_ar_return_master as a Where a.rj_amount > a.rj_allocate And a.is_deleted = 0 And a.cabang_id = ".$cabangId." And a.customer_id = ".$customerId;
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By a.rj_no Asc";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function RecalculateArReturnMaster($returnId){
        $sql = 'Update t_ar_return_master a Set a.rj_amount = 0, a.rj_allocate = 0 Where a.id = ?returnId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?returnId", $returnId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_return_master a
Join (Select c.rj_id, sum(c.sub_total) As sumReturn From t_ar_return_detail c Group By c.rj_id) b
On a.id = b.rj_id Set a.rj_amount = b.sumReturn Where a.id = ?returnId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?returnId", $returnId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }
}


// End of File: estimasi.php
