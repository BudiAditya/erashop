<?php

class InvoiceDetail extends EntityBase {
	public $Id;
    public $CabangId;
	public $InvoiceId;
    public $InvoiceNo;
	public $ItemDescs;
    public $ItemCode;
    public $ItemId;
    public $Lqty;
    public $Sqty;
	public $Qty;
	public $Price;
    public $DiscFormula;
    public $DiscAmount;
    public $SubTotal;
    public $SatBesar;
    public $SatKecil;
    public $ItemHpp;
    public $ItemNote;
    public $IsFree;
	// Helper Variable;
	public $MarkedForDeletion = false;


	public function FillProperties(array $row) {
		$this->Id = $row["id"];        
		$this->InvoiceId = $row["invoice_id"];
        $this->CabangId = $row["cabang_id"];
        $this->InvoiceNo = $row["invoice_no"];
        $this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
		$this->ItemDescs = $row["item_descs"];                
        $this->Lqty = $row["l_qty"];
        $this->Sqty = $row["s_qty"];
		$this->Qty = $row["qty"];
		$this->Price = $row["price"];
        $this->DiscFormula = $row["disc_formula"];
        $this->DiscAmount = $row["disc_amount"];
        $this->SubTotal = $row["sub_total"];
        $this->SatBesar = $row["bsatbesar"];
        $this->SatKecil = $row["bsatkecil"];
        $this->ItemHpp = $row["item_hpp"];
        $this->ItemNote = $row["item_note"];
        $this->IsFree = $row["is_free"];
    }

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ar_invoice_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ar_invoice_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

    public function FindDuplicate($cabId,$invId,$itemId,$itemPrice,$discFormula,$discAmount,$isFree = 0) {
        $sql = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ar_invoice_detail AS a Join m_barang AS b On a.item_code = b.bkode";
        $sql.= " WHERE a.invoice_id = $invId And a.cabang_id = $cabId And a.item_id = $itemId And a.price = $itemPrice And a.disc_formula = $discFormula And a.disc_amount = $discAmount And a.is_free = $isFree;";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }



	public function LoadByInvoiceId($invoiceId, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ar_invoice_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.invoice_id = ?invoiceId ORDER BY $orderBy";
		$this->connector->AddParameter("?invoiceId", $invoiceId);
		$result = array();
		$rs = $this->connector->ExecuteQuery();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new InvoiceDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByInvoiceNo($invoiceNo, $orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ar_invoice_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.invoice_no = ?invoiceNo ORDER BY $orderBy";
        $this->connector->AddParameter("?invoiceNo", $invoiceNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new InvoiceDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_ar_invoice_detail(is_free,invoice_id, cabang_id, invoice_no, item_id, item_code, item_descs, l_qty, s_qty, qty, price, disc_formula, disc_amount, sub_total,item_hpp,item_note)
VALUES(?is_free,?invoice_id, ?cabang_id, ?invoice_no, ?item_id, ?item_code, ?item_descs, ?l_qty, ?s_qty, ?qty, ?price, ?disc_formula, ?disc_amount, ?sub_total,?item_hpp,?item_note)";
		$this->connector->AddParameter("?invoice_id", $this->InvoiceId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?invoice_no", $this->InvoiceNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
		$this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?l_qty", $this->Lqty);
        $this->connector->AddParameter("?s_qty", $this->Sqty);
		$this->connector->AddParameter("?qty", $this->Qty);
		$this->connector->AddParameter("?price", $this->Price);
        $this->connector->AddParameter("?disc_formula", $this->DiscFormula);
        $this->connector->AddParameter("?disc_amount", $this->DiscAmount);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?item_hpp", $this->ItemHpp);
        $this->connector->AddParameter("?item_note", $this->ItemNote);
        $this->connector->AddParameter("?is_free", $this->IsFree);
		$rs = $this->connector->ExecuteNonQuery();
        $rsx = null;
        $did = 0;
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $did = $this->Id;
            //potong stock
            $this->connector->CommandText = "SELECT fc_ar_invoicedetail_post($did) As valresult;";
            $rsx = $this->connector->ExecuteQuery();
            $this->UpdateInvoiceMaster($this->InvoiceId);
		}
		return $rs;
	}

	public function Update($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ar_invoicedetail_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
		$this->connector->CommandText =
"UPDATE t_ar_invoice_detail SET
	  invoice_id = ?invoice_id
	, cabang_id = ?cabang_id
	, invoice_no = ?invoice_no
	, item_descs = ?item_descs
	, qty = ?qty
	, price = ?price
	, sub_total = ?sub_total
	, item_code = ?item_code
	, item_id = ?item_id
	, l_qty = ?l_qty
	, s_qty = ?s_qty
	, disc_formula = ?disc_formula
	, disc_amount = ?disc_amount
	, item_hpp = ?item_hpp
	, item_note = ?item_note
	, is_free = ?is_free
WHERE id = ?id";
        $this->connector->AddParameter("?invoice_id", $this->InvoiceId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?invoice_no", $this->InvoiceNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?l_qty", $this->Lqty);
        $this->connector->AddParameter("?s_qty", $this->Sqty);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?price", $this->Price);
        $this->connector->AddParameter("?disc_formula", $this->DiscFormula);
        $this->connector->AddParameter("?disc_amount", $this->DiscAmount);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?item_hpp", $this->ItemHpp);
        $this->connector->AddParameter("?item_note", $this->ItemNote);
        $this->connector->AddParameter("?is_free", $this->IsFree);
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            //potong stock lagi
            $this->connector->CommandText = "SELECT fc_ar_invoicedetail_post($id) As valresult;";
            $rsx = $this->connector->ExecuteQuery();
            $this->UpdateInvoiceMaster($this->InvoiceId);
        }
        return $rs;
	}

	public function Delete($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ar_invoicedetail_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus detail
		$this->connector->CommandText = "DELETE FROM t_ar_invoice_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdateInvoiceMaster($this->InvoiceId);
        }
        return $rs;
	}

    public function UpdateInvoiceMaster($invoiceId){
        $sql = 'Update t_ar_invoice_master a Set a.base_amount = 0, a.tax_amount = 0, a.disc1_amount = 0, a.total_hpp = 0 Where a.id = ?invoiceId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_invoice_master a
Join (Select c.invoice_id, sum(c.sub_total) As sumPrice, sum(c.qty * c.item_hpp) as sumHpp From t_ar_invoice_detail c Group By c.invoice_id) b
On a.id = b.invoice_id Set a.base_amount = b.sumPrice, a.disc1_amount = if(a.disc1_pct > 0,round(b.sumPrice * (a.disc1_pct/100),0),0), a.total_hpp = b.sumHpp Where a.id = ?invoiceId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_invoice_master a Set a.tax_amount = if(a.tax_pct > 0 And (a.base_amount - a.disc1_amount) > 0,round((a.base_amount - a.disc1_amount)  * (a.tax_pct/100),0),0) Where a.id = ?invoiceId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_invoice_master a Set a.paid_amount = (a.base_amount - a.disc1_amount) + a.tax_amount + a.other_costs_amount Where a.id = ?invoiceId And a.payment_type = 0;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?invoiceId", $invoiceId);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }
}
// End of File: estimasi_detail.php
