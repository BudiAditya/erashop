<?php

class ReceiptDetail extends EntityBase {
	public $Id;
    public $CabangId;
	public $ReceiptId;
    public $ReceiptNo;
	public $InvoiceId;
    public $InvoiceNo;
    public $InvoiceOutstanding;
    public $AllocateAmount;
    public $InvoiceAmount;
    public $PotPph;
    public $PotLain;
    public $InvoiceDate;
    public $DueDate;

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
        $this->CabangId = $row["cabang_id"];
		$this->ReceiptId = $row["receipt_id"];
        $this->ReceiptNo = $row["receipt_no"];
		$this->InvoiceId = $row["invoice_id"];
        $this->InvoiceNo = $row["invoice_no"];
        $this->InvoiceOutstanding = $row["invoice_outstanding"];
        $this->AllocateAmount = $row["allocate_amount"];
        $this->InvoiceAmount = $row["invoice_amount"];
        $this->PotPph = $row["pot_pph"];
        $this->PotLain = $row["pot_lain"];
        $this->InvoiceDate = $row["invoice_date"];
        $this->DueDate = $row["due_date"];
	}

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.*,b.invoice_date,b.due_date FROM t_ar_receipt_detail AS a Join vw_ar_invoice_master AS b On a.invoice_id = b.id WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.*,b.invoice_date,b.due_date FROM t_ar_receipt_detail AS a Join vw_ar_invoice_master AS b On a.invoice_id = b.id WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByReceiptId($receiptId, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*,b.invoice_date,b.due_date FROM t_ar_receipt_detail AS a Join vw_ar_invoice_master AS b On a.invoice_id = b.id WHERE a.receipt_id = ?receiptId ORDER BY $orderBy";
		$this->connector->AddParameter("?receiptId", $receiptId);
		$result = array();
		$rs = $this->connector->ExecuteQuery();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ReceiptDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByReceiptNo($cabangId,$receiptNo, $orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.*,b.invoice_date,b.due_date FROM t_ar_receipt_detail AS a Join vw_ar_invoice_master AS b On a.invoice_id = b.id WHERE a.cabang_id = ?cabangId And a.receipt_no = ?receiptNo ORDER BY $orderBy";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $this->connector->AddParameter("?receiptNo", $receiptNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new ReceiptDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_ar_receipt_detail(cabang_id,receipt_id, receipt_no, invoice_id, invoice_no, invoice_outstanding, allocate_amount, invoice_amount, pot_pph, pot_lain)
VALUES(?cabang_id,?receipt_id, ?receipt_no, ?invoice_id, ?invoice_no, ?invoice_outstanding, ?allocate_amount, ?invoice_amount, ?pot_pph, ?pot_lain)";
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?receipt_id", $this->ReceiptId);
        $this->connector->AddParameter("?receipt_no", $this->ReceiptNo);
		$this->connector->AddParameter("?invoice_id", $this->InvoiceId);
        $this->connector->AddParameter("?invoice_no", $this->InvoiceNo);
        $this->connector->AddParameter("?invoice_outstanding", $this->InvoiceOutstanding);
        $this->connector->AddParameter("?allocate_amount", $this->AllocateAmount);
        $this->connector->AddParameter("?invoice_amount", $this->InvoiceAmount);
        $this->connector->AddParameter("?pot_pph", $this->PotPph);
        $this->connector->AddParameter("?pot_lain", $this->PotLain);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $this->UpdateReceiptMaster($this->ReceiptId);
            $this->UpdateInvoicePaidAmount($this->InvoiceId);
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_ar_receipt_detail SET
	  cabang_id = ?cabang_id
	, receipt_id = ?receipt_id
	, receipt_no = ?receipt_no
	, invoice_id = ?invoice_id
	, invoice_outstanding = ?invoice_outstanding
	, allocate_amount = ?allocate_amount
	, invoice_no = ?invoice_no
	, invoice_amount = ?invoice_amount
	, pot_pph = ?pot_pph
	, pot_lain = ?pot_lain
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?receipt_id", $this->ReceiptId);
        $this->connector->AddParameter("?receipt_no", $this->ReceiptNo);
        $this->connector->AddParameter("?invoice_id", $this->InvoiceId);
        $this->connector->AddParameter("?invoice_no", $this->InvoiceNo);
        $this->connector->AddParameter("?invoice_outstanding", $this->InvoiceOutstanding);
        $this->connector->AddParameter("?allocate_amount", $this->AllocateAmount);
        $this->connector->AddParameter("?invoice_amount", $this->InvoiceAmount);
        $this->connector->AddParameter("?pot_pph", $this->PotPph);
        $this->connector->AddParameter("?pot_lain", $this->PotLain);
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdateReceiptMaster($this->ReceiptId);
            $this->UpdateInvoicePaidAmount($this->InvoiceId);
        }
        return $rs;
	}

	public function Delete($id) {
		$this->connector->CommandText = "DELETE FROM t_ar_receipt_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdateReceiptMaster($this->ReceiptId);
            $this->UpdateInvoicePaidAmount($this->InvoiceId);
        }
        return $rs;
	}

    public function UpdateReceiptMaster($receiptId){
        $sql = "Update t_ar_receipt_master a Set a.receipt_descs = '' Where a.id = ?receiptId;";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?receiptId", $receiptId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = "Update t_ar_receipt_master a Join (Select c.receipt_id, If(count(*) > 1,GROUP_CONCAT(c.invoice_no),c.invoice_no) as keterangan From t_ar_receipt_detail c Group By c.receipt_id) b";
        $sql.= " On a.id = b.receipt_id Set a.receipt_descs = concat('Penerimaan: ',b.keterangan) Where a.id = ?receiptId;";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?receiptId", $receiptId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_receipt_master a
Left Join (Select c.receipt_id, coalesce(sum(c.allocate_amount),0) As sumAlloc, coalesce(sum(c.invoice_amount),0) As sumInvoice, coalesce(sum(c.pot_pph),0) As sumPph, coalesce(sum(c.pot_lain),0) As sumLain From t_ar_receipt_detail c Group By c.receipt_id) b
On a.id = b.receipt_id Set a.allocate_amount = coalesce(b.sumAlloc,0), a.invoice_amount = coalesce(b.sumInvoice,0), a.pot_pph = coalesce(b.sumPph,0), a.pot_lain = coalesce(b.sumLain,0) Where a.id = ?receiptId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?receiptId", $receiptId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_return_master a Join t_ar_receipt_master b On a.rj_no = b.return_no Set a.rj_allocate = b.allocate_amount Where b.id = ?receiptId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?receiptId", $receiptId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }



    public function UpdateInvoicePaidAmount($invoiceId){
        $val = $this->GetInvoicePaidAmount($invoiceId);
        $sql = 'Update t_ar_invoice_master a Set a.paid_amount = ?sumAlloc Where a.id = ?invoiceId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $this->connector->AddParameter("?sumAlloc", $val);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }

    public function GetInvoicePaidAmount($invoiceId){
        $sql = 'Select coalesce(sum(c.allocate_amount),0) as sumAlloc From t_ar_receipt_detail c ';
        $sql.= 'Join t_ar_receipt_master d On c.receipt_id = d.id';
        $sql.= ' where d.is_deleted = 0 and c.invoice_id = ?invoice_id;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoice_id", $invoiceId);
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
