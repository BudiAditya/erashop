<?php

class OrderDetail extends EntityBase {
	public $Id;
    public $CabangId;
	public $PoId;
    public $PoNo;
	public $ItemDescs;
    public $ItemCode;
    public $ItemId;
    public $Lqty;
    public $Sqty;
	public $OrderQty;
    public $ReceiptQty;
	public $Price;
    public $DiscFormula;
    public $DiscAmount;
    public $SubTotal;
    public $SatBesar;
    public $SatKecil;

	// Helper Variable;
	public $MarkedForDeletion = false;


	public function FillProperties(array $row) {
		$this->Id = $row["id"];        
		$this->PoId = $row["po_id"];
        $this->CabangId = $row["cabang_id"];
        $this->PoNo = $row["po_no"];
        $this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
		$this->ItemDescs = $row["item_descs"];                
        $this->Lqty = $row["l_qty"];
        $this->Sqty = $row["s_qty"];
		$this->OrderQty = $row["order_qty"];
        $this->ReceiptQty = $row["receipt_qty"];
		$this->Price = $row["price"];
        $this->DiscFormula = $row["disc_formula"];
        $this->DiscAmount = $row["disc_amount"];
        $this->SubTotal = $row["sub_total"];
        $this->SatBesar = $row["bsatbesar"];
        $this->SatKecil = $row["bsatkecil"];
	}

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_po_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_po_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByPoId($poId, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_po_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.po_id = ?poId ORDER BY $orderBy";
		$this->connector->AddParameter("?poId", $poId);
		$result = array();
		$rs = $this->connector->ExecuteQuery();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new OrderDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByPoNo($poNo, $orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_po_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.po_no = ?poNo ORDER BY $orderBy";
        $this->connector->AddParameter("?poNo", $poNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new PoDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_ap_po_detail(po_id, cabang_id, po_no, item_id, item_code, item_descs, l_qty, s_qty, order_qty, receipt_qty, price, disc_formula, disc_amount, sub_total)
VALUES(?po_id, ?cabang_id, ?po_no, ?item_id, ?item_code, ?item_descs, ?l_qty, ?s_qty, ?order_qty, ?receipt_qty, ?price, ?disc_formula, ?disc_amount, ?sub_total)";
		$this->connector->AddParameter("?po_id", $this->PoId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?po_no", $this->PoNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
		$this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?l_qty", $this->Lqty);
        $this->connector->AddParameter("?s_qty", $this->Sqty);
		$this->connector->AddParameter("?order_qty", $this->OrderQty);
        $this->connector->AddParameter("?receipt_qty", $this->ReceiptQty);
		$this->connector->AddParameter("?price", $this->Price);
        $this->connector->AddParameter("?disc_formula", $this->DiscFormula);
        $this->connector->AddParameter("?disc_amount", $this->DiscAmount);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
		$rs = $this->connector->ExecuteNonQuery();
        $rsx = null;
        if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            //update po
            $this->UpdatePoMaster($this->PoId);
		}
		return $rs;
	}

	public function Update($id) {
        $this->connector->CommandText =
"UPDATE t_ap_po_detail SET
	  po_id = ?po_id
	, cabang_id = ?cabang_id
	, po_no = ?po_no
	, item_descs = ?item_descs
	, order_qty = ?order_qty
	, receipt_qty = ?receipt_qty
	, price = ?price
	, sub_total = ?sub_total
	, item_code = ?item_code
	, item_id = ?item_id
	, l_qty = ?l_qty
	, s_qty = ?s_qty
	, disc_formula = ?disc_formula
	, disc_amount = ?disc_amount
WHERE id = ?id";
        $this->connector->AddParameter("?po_id", $this->PoId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?po_no", $this->PoNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?l_qty", $this->Lqty);
        $this->connector->AddParameter("?s_qty", $this->Sqty);
        $this->connector->AddParameter("?order_qty", $this->OrderQty);
        $this->connector->AddParameter("?receipt_qty", $this->ReceiptQty);
        $this->connector->AddParameter("?price", $this->Price);
        $this->connector->AddParameter("?disc_formula", $this->DiscFormula);
        $this->connector->AddParameter("?disc_amount", $this->DiscAmount);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            //update po master
            $this->UpdatePoMaster($this->PoId);
        }
        return $rs;
	}

	public function Delete($id) {
        //hapus detail
		$this->connector->CommandText = "DELETE FROM t_ap_po_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdatePoMaster($this->PoId);
        }
        return $rs;
	}

    public function UpdatePoMaster($poId){
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
// End of File: estimasi_detail.php
