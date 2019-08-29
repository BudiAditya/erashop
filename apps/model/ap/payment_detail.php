<?php

class PaymentDetail extends EntityBase {
	public $Id;
    public $CabangId;
	public $PaymentId;
    public $PaymentNo;
	public $GrnId;
    public $GrnNo;
    public $GrnOutstanding;
    public $AllocateAmount;
    public $GrnAmount;
    public $PotPph;
    public $PotLain;
    public $GrnDate;
    public $DueDate;

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
        $this->CabangId = $row["cabang_id"];
		$this->PaymentId = $row["payment_id"];
        $this->PaymentNo = $row["payment_no"];
		$this->GrnId = $row["grn_id"];
        $this->GrnNo = $row["grn_no"];
        $this->GrnOutstanding = $row["grn_outstanding"];
        $this->AllocateAmount = $row["allocate_amount"];
        $this->GrnAmount = $row["grn_amount"];
        $this->PotPph = $row["pot_pph"];
        $this->PotLain = $row["pot_lain"];
        $this->GrnDate = $row["grn_date"];
        $this->DueDate = $row["due_date"];
	}

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.*,b.grn_date,b.due_date FROM t_ap_payment_detail AS a Join vw_ap_purchase_master AS b On a.grn_id = b.id WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.*,b.grn_date,b.due_date FROM t_ap_payment_detail AS a Join vw_ap_purchase_master AS b On a.grn_id = b.id WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByPaymentId($paymentId, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*,b.grn_date,b.due_date FROM t_ap_payment_detail AS a Join vw_ap_purchase_master AS b On a.grn_id = b.id WHERE a.payment_id = ?paymentId ORDER BY $orderBy";
		$this->connector->AddParameter("?paymentId", $paymentId);
		$result = array();
		$rs = $this->connector->ExecuteQuery();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new PaymentDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByPaymentNo($cabangId,$paymentNo, $orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.*,b.grn_date,b.due_date FROM t_ap_payment_detail AS a Join vw_ap_purchase_master AS b On a.grn_id = b.id WHERE a.cabang_id = ?cabangId And a.payment_no = ?paymentNo ORDER BY $orderBy";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?paymentNo", $paymentNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new PaymentDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_ap_payment_detail(cabang_id,payment_id, payment_no, grn_id, grn_no, grn_outstanding, allocate_amount, grn_amount, pot_pph, pot_lain)
VALUES(?cabang_id,?payment_id, ?payment_no, ?grn_id, ?grn_no, ?grn_outstanding, ?allocate_amount, ?grn_amount, ?pot_pph, ?pot_lain)";
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?payment_id", $this->PaymentId);
        $this->connector->AddParameter("?payment_no", $this->PaymentNo);
		$this->connector->AddParameter("?grn_id", $this->GrnId);
        $this->connector->AddParameter("?grn_no", $this->GrnNo);
        $this->connector->AddParameter("?grn_outstanding", $this->GrnOutstanding);
        $this->connector->AddParameter("?allocate_amount", $this->AllocateAmount);
        $this->connector->AddParameter("?grn_amount", $this->GrnAmount);
        $this->connector->AddParameter("?pot_pph", $this->PotPph);
        $this->connector->AddParameter("?pot_lain", $this->PotLain);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $this->UpdatePaymentMaster($this->PaymentId);
            $this->UpdateGrnPaidAmount($this->GrnId);
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_ap_payment_detail SET
	  cabang_id = ?cabang_id
	, payment_id = ?payment_id
	, payment_no = ?payment_no
	, grn_id = ?grn_id
	, grn_outstanding = ?grn_outstanding
	, allocate_amount = ?allocate_amount
	, grn_no = ?grn_no
	, grn_amount = ?grn_amount
	, pot_pph = ?pot_pph
	, pot_lain = ?pot_lain
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?payment_id", $this->PaymentId);
        $this->connector->AddParameter("?payment_no", $this->PaymentNo);
        $this->connector->AddParameter("?grn_id", $this->GrnId);
        $this->connector->AddParameter("?grn_no", $this->GrnNo);
        $this->connector->AddParameter("?grn_outstanding", $this->GrnOutstanding);
        $this->connector->AddParameter("?allocate_amount", $this->AllocateAmount);
        $this->connector->AddParameter("?grn_amount", $this->GrnAmount);
        $this->connector->AddParameter("?pot_pph", $this->PotPph);
        $this->connector->AddParameter("?pot_lain", $this->PotLain);
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdatePaymentMaster($this->PaymentId);
            $this->UpdateGrnPaidAmount($this->GrnId);
        }
        return $rs;
	}

	public function Delete($id) {
		$this->connector->CommandText = "DELETE FROM t_ap_payment_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdatePaymentMaster($this->PaymentId);
            $this->UpdateGrnPaidAmount($this->GrnId);
        }
        return $rs;
	}

    public function UpdatePaymentMaster($paymentId){
        $sql = 'Update t_ap_payment_master a
Left Join (Select c.payment_id, coalesce(sum(c.allocate_amount),0) As sumAlloc, coalesce(sum(c.grn_amount),0) As sumGrn, coalesce(sum(c.pot_pph),0) As sumPph, coalesce(sum(c.pot_lain),0) As sumLain From t_ap_payment_detail c Group By c.payment_id) b
On a.id = b.payment_id Set a.payment_amount = coalesce(b.sumAlloc,0), a.allocate_amount = coalesce(b.sumAlloc,0), a.grn_amount = coalesce(b.sumGrn,0), a.pot_pph = coalesce(b.sumPph,0), a.pot_lain = coalesce(b.sumLain,0) Where a.id = ?paymentId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?paymentId", $paymentId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function UpdateGrnPaidAmount($grnId){
        $val = $this->GetGrnPaidAmount($grnId);
        if ($val > 0){
            $sql = 'Update t_ap_purchase_master a Set a.paid_amount = ?sumAlloc, a.grn_status = 2 Where a.id = ?grnId;';
        }else{
            $sql = 'Update t_ap_purchase_master a Set a.paid_amount = ?sumAlloc, a.grn_status = 1 Where a.id = ?grnId;';
        }
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?grnId", $grnId);
        $this->connector->AddParameter("?sumAlloc", $val);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function GetGrnPaidAmount($grnId){
        $sql = 'Select coalesce(sum(c.allocate_amount),0) as sumAlloc From t_ap_payment_detail c ';
        $sql.= 'Join t_ap_payment_master d On c.payment_id = d.id';
        $sql.= ' where d.is_deleted = 0 and c.grn_id = ?grn_id;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?grn_id", $grnId);
        $rs = $this->connector->ExecuteQuery();
        $val = 0;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["sumAlloc"];
        }
        return $val;
    }
}
// End of File: estimasi_detail.php
