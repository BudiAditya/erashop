<?php

require_once("apreturn_detail.php");

class ApReturn extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $RbStatusCodes = array(
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
	public $RbNo;
	public $RbDate;
    public $SupplierId;
    public $SupplierCode;
    public $SupplierName;
    public $SupplierAddress;
	public $RbDescs;
	public $RbAmount;
    public $RbStatus;
	public $CreatebyId;
	public $CreateTime;
	public $UpdatebyId;
	public $UpdateTime;

	/** @var ApReturnDetail[] */
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
        $this->RbNo = $row["rb_no"];
        $this->RbDate = strtotime($row["rb_date"]);
        $this->SupplierId = $row["supplier_id"];
        $this->SupplierCode = $row["supplier_code"];
        $this->SupplierName = $row["supplier_name"];
        $this->SupplierAddress = $row["supplier_address"];
        $this->RbDescs = $row["rb_descs"];
        $this->RbAmount = $row["rb_amount"];
        $this->RbStatus = $row["rb_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->CreateTime = $row["create_time"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->UpdateTime = $row["update_time"];
	}

	public function FormatRbDate($format = HUMAN_DATE) {
		return is_int($this->RbDate) ? date($format, $this->RbDate) : date($format, strtotime(date('Y-m-d')));
	}

	/**
	 * @return ApReturnDetail[]
	 */
	public function LoadDetails() {
		if ($this->Id == null) {
			return $this->Details;
		}
		$detail = new ApReturnDetail();
		$this->Details = $detail->LoadByRbId($this->Id);
		return $this->Details;
	}

	/**
	 * @param int $id
	 * @return ApReturn
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ap_return_master AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ap_return_master AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByRbNo($rbNo,$cabangId) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ap_return_master AS a WHERE a.rb_no = ?rbNo And a.cabang_id = ?cabangId";
		$this->connector->AddParameter("?rbNo", $rbNo);
        $this->connector->AddParameter("?cabangId", $cabangId);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ap_return_master AS a WHERE a.entity_id = ?entityId";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new ApReturn();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ap_return_master AS a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new ApReturn();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
        $sql = "INSERT INTO t_ap_return_master (cabang_id, rb_no, rb_date, supplier_id, rb_descs, rb_amount, rb_status, createby_id, create_time)";
        $sql.= "VALUES(?cabang_id, ?rb_no, ?rb_date, ?supplier_id, ?rb_descs, ?rb_amount, ?rb_status, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?rb_no", $this->RbNo, "char");
		$this->connector->AddParameter("?rb_date", $this->RbDate);
        $this->connector->AddParameter("?supplier_id", $this->SupplierId);
		$this->connector->AddParameter("?rb_descs", $this->RbDescs);
        $this->connector->AddParameter("?rb_amount", $this->RbAmount);
        $this->connector->AddParameter("?rb_status", $this->RbStatus);
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
"UPDATE t_ap_return_master SET
	cabang_id = ?cabang_id
	, rb_no = ?rb_no
	, rb_date = ?rb_date
	, supplier_id = ?supplier_id
	, rb_descs = ?rb_descs
	, rb_amount = ?rb_amount
	, rb_status = ?rb_status
	, updateby_id = ?updateby_id
	, update_time = NOW()
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?rb_no", $this->RbNo, "char");
        $this->connector->AddParameter("?rb_date", $this->RbDate);
        $this->connector->AddParameter("?supplier_id", $this->SupplierId);
        $this->connector->AddParameter("?rb_descs", $this->RbDescs);
        $this->connector->AddParameter("?rb_amount", $this->RbAmount);
        $this->connector->AddParameter("?rb_status", $this->RbStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1){
            $this->RecalculateApReturnMaster($id);
        }
        return $rs;
	}

	public function Delete($id) {
        //fc_ar_rbmaster_unpost
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ap_returnmaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus rbnya
		$this->connector->CommandText = "Delete From t_ap_return_master WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id) {
        //fc_ar_rbmaster_unpost
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ap_returnmaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus rbnya
        $this->connector->CommandText = "Update t_ap_return_master a Set a.rb_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetApReturnDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'RBL';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->RbDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    //$reports = $rb->Load4Reports($sCabangId,$sSupplierId,$sSalesId,$sStatus,$sPaymentStatus,$sStartDate,$sEndDate);
    public function Load4Reports($entityId,$cabangId = 0, $supplierId = 0, $rbStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_ap_return_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.rb_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($rbStatus > -1){
            $sql.= " and a.rb_status = ".$rbStatus;
        }else{
            $sql.= " and a.rb_status <> 3";
        }
        if ($supplierId > 0){
            $sql.= " and a.supplier_id = ".$supplierId;
        }
        $sql.= " Order By a.rb_date,a.rb_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsDetail($entityId, $cabangId = 0, $supplierId = 0, $rbStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT	a.*, b.item_code,b.ex_grn_no,b.item_descs,b.qty_retur,b.price,b.sub_total FROM vw_ap_return_master AS a JOIN t_ap_return_detail b ON a.rb_no = b.rb_no";
        $sql.= " WHERE a.is_deleted = 0 and a.rb_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($rbStatus > -1){
            $sql.= " and a.rb_status = ".$rbStatus;
        }else{
            $sql.= " and a.rb_status <> 3";
        }
        if ($supplierId > 0){
            $sql.= " and a.supplier_id = ".$supplierId;
        }
        $sql.= " Order By a.rb_date,a.rb_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsRekapItem($entityId, $cabangId = 0, $supplierId = 0, $rbStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT b.item_code,b.item_descs,c.bsatkecil as satuan,coalesce(sum(b.qty_retur),0) as sum_qty,coalesce(sum(b.sub_total),0) as sum_total";
        $sql.= " FROM vw_ap_return_master AS a Join t_ap_return_detail AS b On a.rb_no = b.rb_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.rb_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($rbStatus > -1){
            $sql.= " and a.rb_status = ".$rbStatus;
        }
        if ($supplierId > 0){
            $sql.= " and a.supplier_id = ".$supplierId;
        }
        $sql.= " Group By b.item_code,b.item_descs,c.bsatkecil Order By b.item_descs,b.item_code";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function RecalculateApReturnMaster($returnId){
        $sql = 'Update t_ap_return_master a Set a.rb_amount = 0 Where a.id = ?returnId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?returnId", $returnId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ap_return_master a
Join (Select c.rb_id, sum(c.sub_total) As sumReturn From t_ap_return_detail c Group By c.rb_id) b
On a.id = b.rb_id Set a.rb_amount = b.sumReturn Where a.id = ?returnId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?returnId", $returnId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }
}


// End of File: estimasi.php
