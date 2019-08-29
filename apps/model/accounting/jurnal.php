<?php

require_once("jurnal_detail.php");

class Jurnal extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $DocStatusCodes = array(
		0 => "DRAFT",
		1 => "APPROVED",
        2 => "VERIFIED",
		3 => "VOID"
	);

	public $Id;
    public $IsDeleted = false;
	public $EntityId;
    public $CabangId;
	public $NoVoucher;
	public $TglVoucher;
	public $KdVoucher;
	public $Keterangan;
	public $DocAmount;
	public $DocStatus;
    public $CreateMode;
	public $CreatebyId;
	public $CreateTime;
	public $UpdatebyId;
	public $UpdateTime;
    public $ReffNo;
    public $ReffSource;

	/** @var JurnalDetail[] */
	public $Details = array();
    /** @var VoucherTypes */
    public $VoucherTypes;
    /** @var Company */
    public $Company;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
        $this->IsDeleted = $row["is_deleted"] == 1;
		$this->Id = $row["id"];
		$this->EntityId = $row["entity_id"];
        $this->CabangId = $row["cabang_id"];
		$this->NoVoucher = $row["no_voucher"];
		$this->TglVoucher = strtotime($row["tgl_voucher"]);
		$this->KdVoucher = $row["kd_voucher"];
		$this->Keterangan = $row["keterangan"];
		$this->DocAmount = $row["doc_amount"];
		$this->DocStatus = $row["doc_status"];
        $this->CreateMode = $row["create_mode"];
		$this->CreatebyId = $row["createby_id"];
		$this->CreateTime = strtotime($row["create_time"]);
		$this->UpdatebyId = $row["updateby_id"];
		$this->UpdateTime = strtotime($row["update_time"]);
        $this->ReffNo = $row["reff_no"];
        $this->ReffSource = $row["reff_source"];
	}

	public function FormatTglVoucher($format = HUMAN_DATE) {
		return is_int($this->TglVoucher) ? date($format, $this->TglVoucher) : null;
	}

	/**
	 * @return JurnalDetail[]
	 */
	public function LoadDetails() {
		if ($this->Id == null) {
			return $this->Details;
		}
		$detail = new JurnalDetail();
		$this->Details = $detail->LoadByNoVoucher($this->NoVoucher);
		return $this->Details;
	}

    /**
     * @return Company
     */
    public function LoadCompany() {
        require_once(MODEL . "master/company.php");
        if ($this->Id == null || $this->EntityId == null) {
            $this->Company = null;
            return null;
        }

        $this->Company = new Company($this->EntityId);
        return $this->Company;
    }

    /**
     * @return VoucherTypes
     */
    public function LoadVoucherTypes() {
        require_once("voucher_type.php");
        if ($this->Id == null || $this->KdVoucher == null) {
            $this->VoucherTypes = null;
            return null;
        }

        $this->VoucherTypes = new VoucherType();
        $this->VoucherTypes->LoadByDocumentCode($this->KdVoucher);

        return $this->VoucherTypes;
    }

	/**
	 * @param int $id
	 * @return Jurnal
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM t_gl_voucher_master AS a WHERE a.id = ?id and a.is_deleted = 0";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM t_gl_voucher_master AS a WHERE a.id = ?id and a.is_deleted = 0";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByNoVoucher($noVoucher) {
		$this->connector->CommandText = "SELECT a.* FROM t_gl_voucher_master AS a WHERE a.no_voucher = ?noVoucher and a.is_deleted = 0";
		$this->connector->AddParameter("?noVoucher", $noVoucher);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM t_gl_voucher_master AS a WHERE a.entity_id = ?entityId and a.is_deleted = 0";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Jurnal();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM t_gl_voucher_master AS a WHERE a.cabang_id = ?cabangId and a.is_deleted = 0";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Jurnal();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_gl_voucher_master(entity_id, cabang_id, no_voucher, tgl_voucher, kd_voucher, keterangan, doc_amount, doc_status, createby_id, create_time,create_mode,reff_no,reff_source)
VALUES(?entity_id, ?cabang_id, ?no_voucher, ?tgl_voucher, ?kd_voucher, ?keterangan, ?doc_amount, ?doc_status, ?createby_id, NOW(), ?create_mode, ?reff_no, ?reff_source)";
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?no_voucher", $this->NoVoucher);
		$this->connector->AddParameter("?tgl_voucher", $this->TglVoucher);
		$this->connector->AddParameter("?kd_voucher", $this->KdVoucher);
		$this->connector->AddParameter("?keterangan", $this->Keterangan);
		$this->connector->AddParameter("?doc_amount", $this->DocAmount);
		$this->connector->AddParameter("?doc_status", $this->DocStatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $this->connector->AddParameter("?create_mode", $this->CreateMode);
        $this->connector->AddParameter("?reff_no", $this->ReffNo);
        $this->connector->AddParameter("?reff_source", $this->ReffSource);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_gl_voucher_master SET
	entity_id = ?entity_id
	, cabang_id = ?cabang_id
	, no_voucher = ?no_voucher
	, tgl_voucher = ?tgl_voucher
	, kd_voucher = ?kd_voucher
	, keterangan = ?keterangan
	, doc_amount = ?doc_amount
	, doc_status = ?doc_status
	, updateby_id = ?updateby_id
	, update_time = NOW()
	, reff_no = ?reff_no
	, reff_source = ?reff_source
WHERE id = ?id";
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?no_voucher", $this->NoVoucher);
        $this->connector->AddParameter("?tgl_voucher", $this->TglVoucher);
        $this->connector->AddParameter("?kd_voucher", $this->KdVoucher);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?doc_amount", $this->DocAmount);
        $this->connector->AddParameter("?doc_status", $this->DocStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
        $this->connector->AddParameter("?reff_no", $this->ReffNo);
        $this->connector->AddParameter("?reff_source", $this->ReffSource);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete a From t_gl_voucher_master a WHERE a.id = ?id And a.doc_status = 0";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id) {
        $this->connector->CommandText = "Update t_gl_voucher_master a Set a.doc_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetJurnalDocNo($txc){
        $sql = 'Select fc_sys_getdocno(?eti,?txc,?txd) As valout;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?eti", $this->EntityId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->TglVoucher);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    public function Approve($id) {
        $this->connector->CommandText = "Update t_gl_voucher_master Set doc_status = 1, updateby_id = ?updateById, update_time = NOW() WHERE id = ?id";
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateById", $this->UpdatebyId);
        return $this->connector->ExecuteNonQuery();
    }

    public function Unapprove($id) {
        $this->connector->CommandText = "Update t_gl_voucher_master Set doc_status = 0, updateby_id = ?updateById, update_time = NOW() WHERE id = ?id";
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateById", $this->UpdatebyId);
        return $this->connector->ExecuteNonQuery();
    }

    public function Verify($id) {
        $this->connector->CommandText = "Update t_gl_voucher_master Set doc_status = 2, updateby_id = ?updateById, update_time = NOW() WHERE id = ?id";
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateById", $this->UpdatebyId);
        return $this->connector->ExecuteNonQuery();
    }

    public function Unverify($id) {
        $this->connector->CommandText = "Update t_gl_voucher_master Set doc_status = 1, updateby_id = ?updateById, update_time = NOW() WHERE id = ?id";
        $this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateById", $this->UpdatebyId);
        return $this->connector->ExecuteNonQuery();
    }

    public function LoadVoucherType(){
        $sql = 'Select a.* From sys_voucher_type as a Order By a.voucher_cd';
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }
}


// End of File: estimasi.php
