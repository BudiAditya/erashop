<?php

require_once("purchase_detail.php");

class Purchase extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $GrnStatusCodes = array(
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
	public $GrnNo;
	public $GrnDate;
    public $SupplierId;
    public $SupplierCode;
    public $SupplierName;
    public $SalesName;
	public $GrnDescs;
	public $ExPoNo;
    public $ReceiptDate;
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
    public $GrnStatus;
	public $CreatebyId;
	public $CreateTime;
	public $UpdatebyId;
	public $UpdateTime;
    public $PaymentType;
    public $GudangId;
    public $GudangCode;
    public $DueDate;
    public $AdminName;

	/** @var PurchaseDetail[] */
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
        $this->GrnNo = $row["grn_no"];
        $this->GrnDate = strtotime($row["grn_date"]);
        $this->SupplierId = $row["supplier_id"];
        $this->SupplierCode = $row["supplier_code"];
        $this->SupplierName = $row["supplier_name"];
        $this->SalesName = $row["sales_name"];
        $this->GrnDescs = $row["grn_descs"];
        $this->ExPoNo = $row["ex_po_no"];
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
        $this->ReceiptDate = strtotime($row["receipt_date"]);
        $this->GrnStatus = $row["grn_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->CreateTime = $row["create_time"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->UpdateTime = $row["update_time"];
        $this->PaymentType = $row["payment_type"];
        $this->GudangId = $row["gudang_id"];
        $this->GudangCode = $row["gudang_code"];
        $this->DueDate = $row["due_date"];
        $this->AdminName = $row["admin_name"];
	}

	public function FormatGrnDate($format = HUMAN_DATE) {
		return is_int($this->GrnDate) ? date($format, $this->GrnDate) : date($format, strtotime(date('Y-m-d')));
	}

    public function FormatReceiptDate($format = HUMAN_DATE) {
        return is_int($this->ReceiptDate) ? date($format, $this->ReceiptDate) : date($format, strtotime(date('Y-m-d')));
    }

    public function FormatDueDate($format = HUMAN_DATE) {
        return is_int($this->DueDate) ? date($format, $this->DueDate) : null;
    }

	/**
	 * @return PurchaseDetail[]
	 */
	public function LoadDetails() {
		if ($this->Id == null) {
			return $this->Details;
		}
		$detail = new PurchaseDetail();
		$this->Details = $detail->LoadByGrnId($this->Id);
		return $this->Details;
	}

	/**
	 * @param int $id
	 * @return Grn
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ap_purchase_master AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ap_purchase_master AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByGrnNo($grnNo,$cabangId) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ap_purchase_master AS a WHERE a.grn_no = ?grnNo And a.cabang_id = ?cabangId";
		$this->connector->AddParameter("?grnNo", $grnNo);
        $this->connector->AddParameter("?cabangId", $cabangId);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ap_purchase_master AS a WHERE a.entity_id = ?entityId";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Purchase();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ap_purchase_master AS a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Purchase();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function GetUnpaidGrns($cabangId = 0,$supplierId = 0,$grnNo = null) {
        $sql = "SELECT a.* FROM vw_ap_purchase_master AS a";
        $sql.= " Where a.grn_status > 0 and a.is_deleted = 0 and a.balance_amount > 0 And a.grn_no = ?grnNo";
        if ($cabangId > 0){
            $sql.= " And a.cabang_id = ?cabangId";
        }
        if ($supplierId > 0){
            $sql.= " And a.supplier_id = ?supplierId";
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?supplierId", $supplierId);
        $this->connector->AddParameter("?grnNo", $grnNo);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function Insert() {
        $sql = "INSERT INTO t_ap_purchase_master (gudang_id,cabang_id, grn_no, grn_date, receipt_date, supplier_id, sales_name, grn_descs, ex_po_no, base_amount, disc1_pct, disc1_amount, disc2_pct, disc2_amount, tax_pct, tax_amount, other_costs, other_costs_amount, paid_amount, payment_type, credit_terms, grn_status, createby_id, create_time)";
        $sql.= "VALUES(?gudang_id,?cabang_id, ?grn_no, ?grn_date, ?receipt_date, ?supplier_id, ?sales_name, ?grn_descs, ?ex_po_no, ?base_amount, ?disc1_pct, ?disc1_amount, ?disc2_pct, ?disc2_amount, ?tax_pct, ?tax_amount, ?other_costs, ?other_costs_amount, ?paid_amount, ?payment_type, ?credit_terms, ?grn_status, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?gudang_id", $this->GudangId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?grn_no", $this->GrnNo, "char");
		$this->connector->AddParameter("?grn_date", $this->GrnDate);
        $this->connector->AddParameter("?receipt_date", $this->ReceiptDate);
        $this->connector->AddParameter("?supplier_id", $this->SupplierId);
        $this->connector->AddParameter("?sales_name", $this->SalesName);
		$this->connector->AddParameter("?grn_descs", $this->GrnDescs);
        $this->connector->AddParameter("?ex_po_no", $this->ExPoNo);
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
        $this->connector->AddParameter("?grn_status", $this->GrnStatus);
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
"UPDATE t_ap_purchase_master SET
	cabang_id = ?cabang_id
	, gudang_id = ?gudang_id
	, grn_no = ?grn_no
	, grn_date = ?grn_date
	, receipt_date = ?receipt_date
	, supplier_id = ?supplier_id
	, sales_name = ?sales_name
	, grn_descs = ?grn_descs
	, ex_po_no = ?ex_po_no
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
	, grn_status = ?grn_status
	, updateby_id = ?updateby_id
	, update_time = NOW()
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?gudang_id", $this->GudangId);
        $this->connector->AddParameter("?grn_no", $this->GrnNo, "char");
        $this->connector->AddParameter("?grn_date", $this->GrnDate);
        $this->connector->AddParameter("?receipt_date", $this->ReceiptDate);
        $this->connector->AddParameter("?supplier_id", $this->SupplierId);
        $this->connector->AddParameter("?sales_name", $this->SalesName);
        $this->connector->AddParameter("?grn_descs", $this->GrnDescs);
        $this->connector->AddParameter("?ex_po_no", $this->ExPoNo);
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
        $this->connector->AddParameter("?grn_status", $this->GrnStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1){
            $this->RecalculateGrnMaster($id);
        }
        return $rs;
	}

	public function Delete($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ap_purchasemaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //hapus data grn_
        $this->connector->CommandText = "Delete From t_ap_purchase_master WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ap_purchasemaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //hapus data grn_
        $this->connector->CommandText = "Update t_ap_purchase_master a Set a.grn_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetGrnDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'GRN';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->GrnDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }
    
    public function RecalculateGrnMaster($grn_Id){
        $sql = 'Update t_ap_purchase_master a Set a.base_amount = 0, a.tax_amount = 0, a.disc1_amount = 0 Where a.id = ?grn_Id;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?grn_Id", $grn_Id);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ap_purchase_master a
Join (Select c.grn_id, sum(c.sub_total) As sumPrice From t_ap_purchase_detail c Group By c.grn_id) b
On a.id = b.grn_id Set a.base_amount = b.sumPrice, a.disc1_amount = if(a.disc1_pct > 0,round(b.sumPrice * (a.disc1_pct/100),0),0) Where a.id = ?grn_Id;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?grn_Id", $grn_Id);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ap_purchase_master a Set a.tax_amount = if(a.tax_pct > 0 And (a.base_amount - a.disc1_amount) > 0,round((a.base_amount - a.disc1_amount)  * (a.tax_pct/100),0),0) Where a.id = ?grn_Id;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?grn_Id", $grn_Id);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ap_purchase_master a Set a.paid_amount = (a.base_amount - a.disc1_amount) + a.tax_amount + a.other_costs_amount Where a.id = ?grn_Id And a.payment_type = 0;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?grn_Id", $grn_Id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function Load4Reports($entityId,$cabangId = 0, $supplierId = 0, $grnStatus = -1, $paymentStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_ap_purchase_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.grn_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($grnStatus > -1){
            $sql.= " and a.grn_status = ".$grnStatus;
        }else{
            $sql.= " and a.grn_status <> 3";
        }
        if ($paymentStatus == 0){
            $sql.= " and (a.balance_amount) > a.paid_amount";
        }elseif ($paymentStatus == 1){
            $sql.= " and (a.balance_amount) <= a.paid_amount";
        }
        if ($supplierId > 0){
            $sql.= " and a.supplier_id = ".$supplierId;
        }
        $sql.= " Order By a.grn_date,a.grn_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsDetail($entityId,$cabangId = 0, $supplierId = 0, $grnStatus = -1, $paymentStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT a.*,b.item_code,b.item_descs,b.purchase_qty as qty,b.price,b.disc_formula,b.disc_amount,b.sub_total";
        $sql.= " FROM vw_ap_purchase_master AS a Join t_ap_purchase_detail AS b On a.grn_no = b.grn_no";
        $sql.= " WHERE a.is_deleted = 0 and a.grn_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($grnStatus > -1){
            $sql.= " and a.grn_status = ".$grnStatus;
        }else{
            $sql.= " and a.grn_status <> 3";
        }
        if ($paymentStatus == 0){
            $sql.= " and (a.balance_amount) > a.paid_amount";
        }elseif ($paymentStatus == 1){
            $sql.= " and (a.balance_amount) <= a.paid_amount";
        }
        if ($supplierId > 0){
            $sql.= " and a.supplier_id = ".$supplierId;
        }
        $sql.= " Order By a.grn_date,a.grn_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function Load4ReportsRekapItem($entityId,$cabangId = 0, $supplierId = 0, $grnStatus = -1, $paymentStatus = -1, $startDate = null, $endDate = null) {
        $sql = "SELECT b.item_code,b.item_descs,c.bsatkecil as satuan,coalesce(sum(b.purchase_qty),0) as sum_qty,coalesce(sum(b.sub_total),0) as sum_total";
        $sql.= " FROM vw_ap_purchase_master AS a Join t_ap_purchase_detail AS b On a.grn_no = b.grn_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.grn_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        if ($grnStatus > -1){
            $sql.= " and a.grn_status = ".$grnStatus;
        }else{
            $sql.= " and a.grn_status <> 3";
        }
        if ($paymentStatus == 0){
            $sql.= " and (a.balance_amount) > a.paid_amount";
        }elseif ($paymentStatus == 1){
            $sql.= " and (a.balance_amount) <= a.paid_amount";
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

    public function GetJSonGrns($cabangId,$supplierId) {
        $sql = "SELECT a.id,a.grn_no,a.grn_date FROM t_ap_purchase_master as a Where a.grn_status <> 3 And a.is_deleted = 0 And a.cabang_id = ".$cabangId." And a.supplier_id = ".$supplierId;
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By a.grn_no Asc";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetJSonGrnItems($grnId = 0) {
        $sql = "SELECT a.id,a.item_id,a.item_code,a.item_descs,a.purchase_qty - a.return_qty as qty_beli,b.bsatbesar as satuan,round(a.sub_total/a.purchase_qty,0) as price FROM t_ap_purchase_detail AS a";
        $sql.= " JOIN m_barang AS b ON a.item_code = b.bkode Join t_ap_purchase_master c On a.grn_no = c.grn_no Where c.grn_status <> 3 And (a.purchase_qty - a.return_qty) > 0 And a.grn_id = ".$grnId;
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By a.item_code Asc";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

    public function GetGrnItemCount($grnId){
        $this->connector->CommandText = "Select count(*) As valresult From t_ap_purchase_detail as a Where a.grn_id = ?grnId;";
        $this->connector->AddParameter("?grnId", $grnId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }
}


// End of File: estimasi.php
