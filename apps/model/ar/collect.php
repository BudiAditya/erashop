<?php

require_once("collect_detail.php");

class Collect extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $CollectStatusCodes = array(
		0 => "DRAFT",
		1 => "IN PROCESS",
        2 => "CLOSE",
		3 => "VOID"
	);

	public $Id;
    public $IsDeleted = false;
    public $CabangId;
	public $CollectNo;
	public $CollectDate;
    public $CollectorId;
	public $CollectDescs;
	public $CollectAmount;
	public $PaidAmount;
    public $BalanceAmount;
    public $CollectStatus;
	public $CreatebyId;
	public $CreateTime;
	public $UpdatebyId;
	public $UpdateTime;

	/** @var CollectDetail[] */
	public $Details = array();

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
        $this->IsDeleted = $row["is_deleted"] == 1;
		$this->Id = $row["id"];
        $this->CabangId = $row["cabang_id"];
		$this->CollectNo = $row["collect_no"];
		$this->CollectDate = strtotime($row["collect_date"]);
        $this->CollectorId = $row["collector_id"];
		$this->CollectDescs = $row["collect_descs"];
		$this->CollectAmount = $row["collect_amount"];
		$this->PaidAmount = $row["paid_amount"];
        $this->BalanceAmount = $row["collect_amount"] - $row["paid_amount"];
        $this->CollectStatus = $row["collect_status"];
		$this->CreatebyId = $row["createby_id"];
		$this->CreateTime = strtotime($row["create_time"]);
		$this->UpdatebyId = $row["updateby_id"];
		$this->UpdateTime = strtotime($row["update_time"]);
	}

	public function FormatCollectDate($format = HUMAN_DATE) {
		return is_int($this->CollectDate) ? date($format, $this->CollectDate) : null;
	}

	/**
	 * @return CollectDetail[]
	 */
	public function LoadDetails() {
		if ($this->Id == null) {
			return $this->Details;
		}
		$detail = new CollectDetail();
		$this->Details = $detail->LoadByCollectId($this->Id);
		return $this->Details;
	}

	/**
	 * @param int $id
	 * @return Collect
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_ar_collect_master AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM t_ar_collect_master AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByCollectNo($collectNo) {
		$this->connector->CommandText = "SELECT a.* FROM t_ar_collect_master AS a WHERE a.collect_no = ?collectNo";
		$this->connector->AddParameter("?collectNo", $collectNo);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM t_ar_collect_master AS a Join m_cabang AS b On a.cabang_id = b.id WHERE b.entity_id = ?entityId";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Collect();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM t_ar_collect_master AS a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Collect();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    //$reports = $collect->Load4Reports($sCabangId,$sJnsBarangId,$sCollectorId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
    public function Load4Reports($cabangId = 0, $jnsBarangId = 0, $customerId = 0, $salesId = 0, $collectStatus = -1, $paymentStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_ar_collect_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.collect_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }
        if ($collectStatus > -1){
            $sql.= " and a.collect_status = ".$collectStatus;
        }
        if ($paymentStatus == 0){
            $sql.= " and (a.collect_amount + a.paid_amount) > a.paid_amount";
        }elseif ($paymentStatus == 1){
            $sql.= " and (a.collect_amount + a.paid_amount) <= a.paid_amount";
        }
        if ($customerId > 0){
            $sql.= " and a.collector_id = ".$customerId;
        }
        if ($jnsBarangId > 0){
            $sql.= " and a.jnsbarang_id = ".$jnsBarangId;
        }
        if ($salesId > 0){
            $sql.= " and a.sales = ".$salesId;
        }
        $sql.= " Order By a.collect_date,a.collect_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadUnpaidCollect($debtorId, $invStatus = 1) {
        if ($invStatus == 1){
            $sql = "SELECT a.* FROM t_ar_collect_master AS a WHERE collect_status = 1 and is_deleted = 0 and a.debtor_id = ?debtorId and a.paid_amount < (a.collect_amount + a.paid_amount) Order By a.collect_no";
        }else{
            $sql = "SELECT a.* FROM t_ar_collect_master AS a WHERE is_deleted = 0 and a.debtor_id = ?debtorId and a.paid_amount < (a.collect_amount + a.paid_amount) Order By a.collect_no";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?debtorId", $debtorId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Collect();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
        $sql = "INSERT INTO t_ar_collect_master (cabang_id, collect_no, collect_date, collector_id, collect_descs, collect_amount, paid_amount, collect_status, createby_id, create_time)";
        $sql.= "VALUES(?cabang_id, ?collect_no, ?collect_date, ?collector_id, ?collect_descs, ?collect_amount, ?paid_amount, ?collect_status, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?collect_no", $this->CollectNo, "char");
		$this->connector->AddParameter("?collect_date", $this->CollectDate);
		$this->connector->AddParameter("?collect_descs", $this->CollectDescs);
        $this->connector->AddParameter("?collect_amount", $this->CollectAmount);
        $this->connector->AddParameter("?paid_amount", $this->PaidAmount);
        $this->connector->AddParameter("?collect_status", $this->CollectStatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $this->connector->AddParameter("?collector_id", $this->CollectorId);
        $rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_ar_collect_master SET
	cabang_id = ?cabang_id
	, collect_no = ?collect_no
	, collect_date = ?collect_date
	, collect_descs = ?collect_descs
	, collect_amount = ?collect_amount
	, paid_amount = ?paid_amount
	, collect_status = ?collect_status
	, updateby_id = ?updateby_id
	, update_time = NOW()
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?collect_no", $this->CollectNo, "char");
        $this->connector->AddParameter("?collect_date", $this->CollectDate);
        $this->connector->AddParameter("?collect_descs", $this->CollectDescs);
        $this->connector->AddParameter("?collect_amount", $this->CollectAmount);
        $this->connector->AddParameter("?paid_amount", $this->PaidAmount);
        $this->connector->AddParameter("?collect_status", $this->CollectStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?collector_id", $this->CollectorId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete From t_ar_collect_master WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id) {
        $this->connector->CommandText = "Update t_ar_collect_master a Set a.collect_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function Recall($id) {
        $this->connector->CommandText = "Update t_ar_collect_master a Set a.collect_status = 0 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetCollectDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'ARC';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->CollectDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    public function Approve($id = null, $uid = null){
        $this->connector->CommandText = "SELECT fc_ar_collect_approve($id,$uid) As valresult;";
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?uid", $uid);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function Unapprove($id = null, $uid = null){
        $this->connector->CommandText = "SELECT fc_ar_collect_unapprove($id,$uid) As valresult;";
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?uid", $uid);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function LoadDueDateInvoice($cabangId = 0, $collectDate = null, $minDay = -1){
        $collectDate = date('Y-m-d',$collectDate);
        if ($cabangId > 0){
            $sql = "select a.* from vw_ar_invoice_master as a where a.cabang_id = ?cabangId and date_add(a.due_date, INTERVAL ?minDay DAY) <= ?collectDate and a.paid_amount < a.base_amount and a.collect_status = 0;";
        }else{
            $sql = "select a.* from vw_ar_invoice_master as a where date_add(a.due_date, INTERVAL ?minDay DAY) <= ?collectDate and a.paid_amount < a.base_amount and a.collect_status = 0;";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?collectDate", $collectDate);
        $this->connector->AddParameter("?minDay", $minDay);
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

}


// End of File: estimasi.php
