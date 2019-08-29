<?php

require_once("transfer_detail.php");

class Transfer extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $NpbStatusCodes = array(
		0 => "DRAFT",
		1 => "POSTED",
        2 => "CLOSED",
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
	public $NpbNo;
	public $NpbDate;
    public $NpbDescs;
    public $ToCabangId;
    public $ToCabangCode;
    public $NpbStatus;
	public $CreatebyId;
	public $CreateTime;
	public $UpdatebyId;
	public $UpdateTime;
    public $AdminName;

	/** @var TransferDetail[] */
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
        $this->CompanyName = $row["company_name"];
        $this->CabangId = $row["cabang_id"];
        $this->CabangCode = $row["cabang_code"];
        $this->NpbNo = $row["npb_no"];
        $this->NpbDate = strtotime($row["npb_date"]);
        $this->ToCabangId = $row["to_cabang_id"];
        $this->ToCabangCode = $row["to_cabang_code"];
        $this->NpbDescs = $row["npb_descs"];
        $this->NpbStatus = $row["npb_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->CreateTime = $row["create_time"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->UpdateTime = $row["update_time"];
        $this->AdminName = $row["admin_name"];
	}

	public function FormatNpbDate($format = HUMAN_DATE) {
		return is_int($this->NpbDate) ? date($format, $this->NpbDate) : date($format, strtotime(date('Y-m-d')));
	}

	/**
	 * @return TransferDetail[]
	 */
	public function LoadDetails() {
		if ($this->Id == null) {
			return $this->Details;
		}
		$detail = new TransferDetail();
		$this->Details = $detail->LoadByNpbId($this->Id);
		return $this->Details;
	}

	/**
	 * @param int $id
	 * @return Npb
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ic_transfer_master AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_transfer_master AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByNpbNo($npbNo,$cabangId) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ic_transfer_master AS a WHERE a.npb_no = ?npbNo And a.cabang_id = ?cabangId";
		$this->connector->AddParameter("?npbNo", $npbNo);
        $this->connector->AddParameter("?cabangId", $cabangId);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_transfer_master AS a WHERE a.entity_id = ?entityId";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Transfer();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_transfer_master AS a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Transfer();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
        $sql = "INSERT INTO t_ic_transfer_master (cabang_id, npb_no, npb_date, to_cabang_id, npb_descs, npb_status, createby_id, create_time)";
        $sql.= "VALUES(?cabang_id, ?npb_no, ?npb_date, ?to_cabang_id, ?npb_descs, ?npb_status, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?npb_no", $this->NpbNo, "char");
		$this->connector->AddParameter("?npb_date", $this->NpbDate);
        $this->connector->AddParameter("?to_cabang_id", $this->ToCabangId);
		$this->connector->AddParameter("?npb_descs", $this->NpbDescs);
        $this->connector->AddParameter("?npb_status", $this->NpbStatus);
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
"UPDATE t_ic_transfer_master SET
	cabang_id = ?cabang_id
	, npb_no = ?npb_no
	, npb_date = ?npb_date
	, to_cabang_id = ?to_cabang_id
	, npb_descs = ?npb_descs
	, npb_status = ?npb_status
	, updateby_id = ?updateby_id
	, update_time = NOW()
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?npb_no", $this->NpbNo, "char");
        $this->connector->AddParameter("?npb_date", $this->NpbDate);
        $this->connector->AddParameter("?to_cabang_id", $this->ToCabangId);
        $this->connector->AddParameter("?npb_descs", $this->NpbDescs);
        $this->connector->AddParameter("?npb_status", $this->NpbStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
        return $rs;
	}

	public function Delete($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ic_transfermaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //hapus data npb_
        $this->connector->CommandText = "Delete From t_ic_transfer_master WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ic_transfermaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //hapus data npb_
        $this->connector->CommandText = "Update t_ic_transfer_master a Set a.npb_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetNpbDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'NPB';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->NpbDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    public function Load4Reports($entityId,$cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.*,b.item_code,b.item_descs as item_name,c.bsatkecil as satuan,b.qty";
        $sql.= " FROM vw_ic_transfer_master AS a Join t_ic_transfer_detail AS b On a.npb_no = b.npb_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.npb_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Order By a.npb_date,a.npb_no,b.item_descs,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadRekap4Reports($entityId,$cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.cabang_code,a.to_cabang_code,b.item_code,b.item_descs as item_name,c.bsatkecil as satuan,sum(b.qty) as sum_qty";
        $sql.= " FROM vw_ic_transfer_master AS a Join t_ic_transfer_detail AS b On a.npb_no = b.npb_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.npb_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Group By a.cabang_code,a.to_cabang_code,b.item_code,b.item_descs,c.bsatkecil Order By a.cabang_code,b.item_descs";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }
}


// End of File: estimasi.php
