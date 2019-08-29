<?php

class Npkb extends EntityBase {
	public $Id;
	public $IsDeleted = false;
	public $EntityId;
	public $CabangId;
	public $NpkbDate;
    public $NpkbNo;
    public $RequestDate;
    public $TrxTypeId;
    public $RequestDescs;
    public $RequestAmount;
    public $ReffNo;
    public $RequestBy;
    public $NpkbStatus;
    public $TglCair;
    public $NoBkk;
    public $CreateById;
    public $CreateTime;
	public $UpdateById;
	public $UpdateTime;

    public function __construct($id = null) {
        parent::__construct();
        if (is_numeric($id)) {
            $this->LoadById($id);
        }
    }

    public function FormatNpkbDate($format = HUMAN_DATE) {
        return is_int($this->NpkbDate) ? date($format, $this->NpkbDate) : null;
    }

    public function FormatRequestDate($format = HUMAN_DATE) {
        return is_int($this->RequestDate) ? date($format, $this->RequestDate) : null;
    }

    public function FormatTglCair($format = HUMAN_DATE) {
        return is_int($this->TglCair) ? date($format, $this->TglCair) : null;
    }

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
        $this->EntityId = $row["entity_id"];
        $this->CabangId = $row["cabang_id"];
        $this->NpkbDate = strtotime($row["npkb_date"]);
        $this->NpkbNo = $row["npkb_no"];
        $this->RequestDate = strtotime($row["request_date"]);
        $this->RequestDescs = $row["request_descs"];
        $this->RequestAmount = $row["request_amount"];
        $this->ReffNo = $row["reff_no"];
        $this->RequestBy = $row["request_by"];
        $this->NpkbStatus = $row["npkb_status"];
        $this->TglCair = strtotime($row["tgl_cair"]);
        $this->NoBkk = $row["no_bkk"];
        $this->TrxTypeId = $row["trxtype_id"];
        $this->CreateById = $row["createby_id"];
        $this->CreateTime = $row["create_time"];
        $this->UpdateById = $row["updateby_id"];
        $this->UpdateTime = $row["update_time"];
	}

	/**
	 * @param string $orderBy
	 * @return TrxType[]
	 */
	public function LoadAll($orderBy = "a.npkb_no") {
		$this->connector->CommandText = "SELECT a.* FROM t_cb_npkb AS a WHERE a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Npkb();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return TrxType
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_cb_npkb AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByNpkbNo($npkbNo) {
        $this->connector->CommandText = "SELECT a.* FROM t_cb_npkb AS a WHERE a.npkb_no = ?npkbNo";
        $this->connector->AddParameter("?npkbNo", $npkbNo);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	/**
	 * Mencari data bank berdasarkan akun CoA nya
	 *
	 * @param int $sbu
	 * @param int $accId
	 * @return TrxType
	 */
	public function LoadByEntityId($entityId) {
		$this->connector->CommandText = "SELECT a.* FROM t_cb_npkb AS a WHERE a.is_deleted = 0 and a.entity_id = ?entityId";
		$this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Npkb();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
	}

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM t_cb_npkb AS a WHERE a.is_deleted = 0 and a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Npkb();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangTrxTypeId($cabangId,$trxtypeId) {
        $this->connector->CommandText = "SELECT a.* FROM t_cb_npkb AS a WHERE a.is_deleted = 0 and a.cabang_id = ?cabangId and a.trxtype_id = ?trxtypeId and npkb_status = 0";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?trxtypeId", $trxtypeId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Npkb();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText = "INSERT INTO t_cb_npkb(entity_id,cabang_id,npkb_date,npkb_no,request_date,request_descs,request_amount,reff_no,request_by,npkb_status,tgl_cair,no_bkk,trxtype_id,createby_id,create_time)
		VALUES(?entity_id,?cabang_id,?npkb_date,?npkb_no,?request_date,?request_descs,?request_amount,?reff_no,?request_by,?npkb_status,?tgl_cair,?no_bkk,?trxtype_id,?createby_id,NOW())";
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?npkb_date", $this->NpkbDate);
        $this->connector->AddParameter("?npkb_no", $this->NpkbNo);
        $this->connector->AddParameter("?request_date", $this->RequestDate);
        $this->connector->AddParameter("?request_descs", $this->RequestDescs);
        $this->connector->AddParameter("?request_amount", $this->RequestAmount);
        $this->connector->AddParameter("?reff_no", $this->ReffNo);
        $this->connector->AddParameter("?request_by", $this->RequestBy);
        $this->connector->AddParameter("?npkb_status", $this->NpkbStatus);
        $this->connector->AddParameter("?tgl_cair", $this->TglCair);
        $this->connector->AddParameter("?no_bkk", $this->NoBkk);
        $this->connector->AddParameter("?trxtype_id", $this->TrxTypeId);
        $this->connector->AddParameter("?createby_id", $this->CreateById);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_cb_npkb SET
    entity_id = ?entity_id
    , cabang_id = ?cabang_id
    , npkb_date = ?npkb_date
	, npkb_no = ?npkb_no
	, request_date = ?request_date
	, request_descs = ?request_descs
	, request_amount = ?request_amount
	, reff_no = ?reff_no
	, request_by = ?request_by
	, npkb_status = ?npkb_status
	, tgl_cair = ?tgl_cair
	, no_bkk = ?no_bkk
	, trxtype_id = ?trxtype_id
	, updateby_id = ?updateby_id
	, update_time = NOW()
WHERE id = ?id";
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?npkb_date", $this->NpkbDate);
        $this->connector->AddParameter("?npkb_no", $this->NpkbNo);
        $this->connector->AddParameter("?request_date", $this->RequestDate);
        $this->connector->AddParameter("?request_descs", $this->RequestDescs);
        $this->connector->AddParameter("?request_amount", $this->RequestAmount);
        $this->connector->AddParameter("?reff_no", $this->ReffNo);
        $this->connector->AddParameter("?request_by", $this->RequestBy);
        $this->connector->AddParameter("?npkb_status", $this->NpkbStatus);
        $this->connector->AddParameter("?tgl_cair", $this->TglCair);
        $this->connector->AddParameter("?no_bkk", $this->NoBkk);
        $this->connector->AddParameter("?trxtype_id", $this->TrxTypeId);
        $this->connector->AddParameter("?updateby_id", $this->UpdateById);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE t_cb_npkb SET is_deleted = 1 , updateby_id = ?user , update_time = NOW() WHERE id = ?id";
        $this->connector->AddParameter("?user", $this->UpdateById);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function GetCbNpkbNo(){
        $sql = 'Select fc_sys_getdocno(?eti,?txc,?txd) As valout;';
        $txc = null;
        $txc = 'NPK';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?eti", $this->EntityId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->NpkbDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
           $row = $rs->FetchAssoc();
           $val = $row["valout"];
        }
        return $val;
    }
}

// End of file: bank.php
