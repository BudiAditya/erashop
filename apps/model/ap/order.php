<?php

require_once("order_detail.php");

class Order extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $PoStatusCodes = array(
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
	public $PoNo;
	public $PoDate;
    public $SupplierId;
    public $SupplierCode;
    public $SupplierName;
    public $SalesName;
	public $PoDescs;
	public $ExReffNo;
    public $RequestDate;
	public $BaseAmount;
    public $Disc1Pct;
    public $Disc1Amount;
    public $Disc2Pct;
    public $Disc2Amount;
    public $TaxPct;
	public $TaxAmount;
    public $OtherCosts;
    public $OtherCostsAmount;
    public $TotalAmount;
	public $PaidAmount;
    public $CreditTerms;
    public $PoStatus;
	public $CreatebyId;
	public $CreateTime;
	public $UpdatebyId;
	public $UpdateTime;
    public $PaymentType;

	/** @var PoDetail[] */
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
        $this->PoNo = $row["po_no"];
        $this->PoDate = strtotime($row["po_date"]);
        $this->SupplierId = $row["supplier_id"];
        $this->SupplierCode = $row["supplier_code"];
        $this->SupplierName = $row["supplier_name"];
        $this->SalesName = $row["sales_name"];
        $this->PoDescs = $row["po_descs"];
        $this->ExReffNo = $row["ex_reff_no"];
        $this->BaseAmount = $row["base_amount"];
        $this->Disc1Pct = $row["disc1_pct"];
        $this->Disc1Amount = $row["disc1_amount"];
        $this->Disc2Pct = $row["disc2_pct"];
        $this->Disc2Amount = $row["disc2_amount"];
        $this->TaxPct = $row["tax_pct"];
        $this->TaxAmount = $row["tax_amount"];
        $this->OtherCosts = $row["other_costs"];
        $this->OtherCostsAmount = $row["other_costs_amount"];
        $this->TotalAmount = $row["total_amount"];
        $this->PaidAmount = $row["paid_amount"];
        $this->CreditTerms = $row["credit_terms"];
        $this->RequestDate = strtotime($row["request_date"]);
        $this->PoStatus = $row["po_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->CreateTime = $row["create_time"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->UpdateTime = $row["update_time"];
        $this->PaymentType = $row["payment_type"];
	}

	public function FormatPoDate($format = HUMAN_DATE) {
		return is_int($this->PoDate) ? date($format, $this->PoDate) : date($format, strtotime(date('Y-m-d')));
	}

    public function FormatRequestDate($format = HUMAN_DATE) {
        return is_int($this->RequestDate) ? date($format, $this->RequestDate) : null;
    }

	/**
	 * @return PoDetail[]
	 */
	public function LoadDetails() {
		if ($this->Id == null) {
			return $this->Details;
		}
		$detail = new OrderDetail();
		$this->Details = $detail->LoadByPoId($this->Id);
		return $this->Details;
	}

	/**
	 * @param int $id
	 * @return Po
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ap_po_master AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ap_po_master AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByPoNo($invNo,$cabangId) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ap_po_master AS a WHERE a.po_no = ?invNo And a.cabang_id = ?cabangId";
		$this->connector->AddParameter("?invNo", $invNo);
        $this->connector->AddParameter("?cabangId", $cabangId);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ap_po_master AS a WHERE a.entity_id = ?entityId";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Order();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ap_po_master AS a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Order();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
        $sql = "INSERT INTO t_ap_po_master (cabang_id, po_no, po_date, request_date, supplier_id, sales_name, po_descs, ex_reff_no, base_amount, disc1_pct, disc1_amount, disc2_pct, disc2_amount, tax_pct, tax_amount, other_costs, other_costs_amount, paid_amount, payment_type, credit_terms, po_status, createby_id, create_time)";
        $sql.= "VALUES(?cabang_id, ?po_no, ?po_date, ?request_date, ?supplier_id, ?sales_name, ?po_descs, ?ex_reff_no, ?base_amount, ?disc1_pct, ?disc1_amount, ?disc2_pct, ?disc2_amount, ?tax_pct, ?tax_amount, ?other_costs, ?other_costs_amount, ?paid_amount, ?payment_type, ?credit_terms, ?po_status, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?po_no", $this->PoNo, "char");
		$this->connector->AddParameter("?po_date", $this->PoDate);
        $this->connector->AddParameter("?request_date", $this->RequestDate);
        $this->connector->AddParameter("?supplier_id", $this->SupplierId);
        $this->connector->AddParameter("?sales_name", $this->SalesName);
		$this->connector->AddParameter("?po_descs", $this->PoDescs);
        $this->connector->AddParameter("?ex_reff_no", $this->ExReffNo);
        $this->connector->AddParameter("?base_amount", $this->BaseAmount);
        $this->connector->AddParameter("?disc1_pct", $this->Disc1Pct);
        $this->connector->AddParameter("?disc1_amount", $this->Disc1Amount);
        $this->connector->AddParameter("?disc2_pct", $this->Disc2Pct);
        $this->connector->AddParameter("?disc2_amount", $this->Disc2Amount);
        $this->connector->AddParameter("?tax_pct", $this->TaxPct);
        $this->connector->AddParameter("?tax_amount", $this->TaxAmount);
        $this->connector->AddParameter("?other_costs", $this->OtherCosts);
        $this->connector->AddParameter("?other_costs_amount", $this->OtherCostsAmount);
        $this->connector->AddParameter("?paid_amount", $this->PaidAmount);
        $this->connector->AddParameter("?payment_type", $this->PaymentType);
        $this->connector->AddParameter("?credit_terms", $this->CreditTerms);
        $this->connector->AddParameter("?po_status", $this->PoStatus);
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
"UPDATE t_ap_po_master SET
	cabang_id = ?cabang_id
	, po_no = ?po_no
	, po_date = ?po_date
	, request_date = ?request_date
	, supplier_id = ?supplier_id
	, sales_name = ?sales_name
	, po_descs = ?po_descs
	, ex_reff_no = ?ex_reff_no
	, base_amount = ?base_amount
	, disc1_pct = ?disc1_pct
	, disc1_amount = ?disc1_amount
	, disc2_pct = ?disc2_pct
	, disc2_amount = ?disc2_amount
	, tax_pct = ?tax_pct
	, tax_amount = ?tax_amount
	, other_costs = ?other_costs
	, other_costs_amount = ?other_costs_amount
	, paid_amount = ?paid_amount
	, payment_type = ?payment_type
	, credit_terms = ?credit_terms
	, po_status = ?po_status
	, updateby_id = ?updateby_id
	, update_time = NOW()
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?po_no", $this->PoNo, "char");
        $this->connector->AddParameter("?po_date", $this->PoDate);
        $this->connector->AddParameter("?request_date", $this->RequestDate);
        $this->connector->AddParameter("?supplier_id", $this->SupplierId);
        $this->connector->AddParameter("?sales_name", $this->SalesName);
        $this->connector->AddParameter("?po_descs", $this->PoDescs);
        $this->connector->AddParameter("?ex_reff_no", $this->ExReffNo);
        $this->connector->AddParameter("?base_amount", $this->BaseAmount);
        $this->connector->AddParameter("?disc1_pct", $this->Disc1Pct);
        $this->connector->AddParameter("?disc1_amount", $this->Disc1Amount);
        $this->connector->AddParameter("?disc2_pct", $this->Disc2Pct);
        $this->connector->AddParameter("?disc2_amount", $this->Disc2Amount);
        $this->connector->AddParameter("?tax_pct", $this->TaxPct);
        $this->connector->AddParameter("?tax_amount", $this->TaxAmount);
        $this->connector->AddParameter("?other_costs", $this->OtherCosts);
        $this->connector->AddParameter("?other_costs_amount", $this->OtherCostsAmount);
        $this->connector->AddParameter("?paid_amount", $this->PaidAmount);
        $this->connector->AddParameter("?payment_type", $this->PaymentType);
        $this->connector->AddParameter("?credit_terms", $this->CreditTerms);
        $this->connector->AddParameter("?po_status", $this->PoStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1){
            $this->RecalculatePoMaster($id);
        }
        return $rs;
	}

	public function Delete($id) {
        //hapus data po
        $this->connector->CommandText = "Delete From t_ap_po_master WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id) {
        //hapus data po
        $this->connector->CommandText = "Update t_ap_po_master a Set a.po_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetPoDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'POR';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->PoDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }
    
    public function RecalculatePoMaster($poId){
        $sql = 'Update t_ap_po_master a Set a.base_amount = 0, a.tax_amount = 0, a.disc1_amount = 0 Where a.id = ?poId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?poId", $poId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ap_po_master a
Join (Select c.po_id, sum(c.sub_total) As sumPrice From t_ap_po_detail c Group By c.po_id) b
On a.id = b.po_id Set a.base_amount = b.sumPrice, a.disc1_amount = if(a.disc1_pct > 0,round(b.sumPrice * (a.disc1_pct/100),0),0) Where a.id = ?poId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?poId", $poId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ap_po_master a Set a.tax_amount = if(a.tax_pct > 0 And (a.base_amount - a.disc1_amount) > 0,round((a.base_amount - a.disc1_amount)  * (a.tax_pct/100),0),0) Where a.id = ?poId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?poId", $poId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }
}


// End of File: estimasi.php
