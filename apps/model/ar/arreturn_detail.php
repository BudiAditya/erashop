<?php

class ArReturnDetail extends EntityBase {
	public $Id;
    public $CabangId;
	public $RjId;
    public $RjNo;
	public $ItemDescs;
    public $ItemCode;
    public $ItemId;
    public $ExInvoiceId;
    public $ExInvoiceNo;
    public $ExInvDetailId;
    public $QtyJual;
	public $QtyRetur;
	public $Price;
    public $SubTotal;
    public $SatBesar;
    public $SatKecil;

	public function FillProperties(array $row) {
		$this->Id = $row["id"];        
		$this->RjId = $row["rj_id"];
        $this->CabangId = $row["cabang_id"];
        $this->RjNo = $row["rj_no"];
        $this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
		$this->ItemDescs = $row["item_descs"];                
        $this->ExInvoiceId = $row["ex_invoice_id"];
        $this->ExInvoiceNo = $row["ex_invoice_no"];
        $this->ExInvDetailId = $row["ex_invdetail_id"];
        $this->QtyJual = $row["qty_jual"];
		$this->QtyRetur = $row["qty_retur"];
		$this->Price = $row["price"];
        $this->SubTotal = $row["sub_total"];
        $this->SatBesar = $row["bsatbesar"];
        $this->SatKecil = $row["bsatkecil"];
	}

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ar_return_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ar_return_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByRjId($rjId, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ar_return_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.rj_id = ?rjId ORDER BY $orderBy";
		$this->connector->AddParameter("?rjId", $rjId);
		$result = array();
		$rs = $this->connector->ExecuteQuery();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ArReturnDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByRjNo($invoiceNo, $orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ar_return_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.rj_no = ?invoiceNo ORDER BY $orderBy";
        $this->connector->AddParameter("?invoiceNo", $invoiceNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new ArReturnDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_ar_return_detail(rj_id, cabang_id, rj_no, item_id, item_code, item_descs, ex_invoice_id, ex_invoice_no, qty_jual, qty_retur, price, sub_total, ex_invdetail_id)
VALUES(?rj_id, ?cabang_id, ?rj_no, ?item_id, ?item_code, ?item_descs, ?ex_invoice_id, ?ex_invoice_no, ?qty_jual, ?qty_retur, ?price, ?sub_total, ?ex_invdetail_id)";
		$this->connector->AddParameter("?rj_id", $this->RjId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?rj_no", $this->RjNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
		$this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?ex_invoice_id", $this->ExInvoiceId);
        $this->connector->AddParameter("?ex_invoice_no", $this->ExInvoiceNo);
        $this->connector->AddParameter("?qty_jual", $this->QtyJual);
		$this->connector->AddParameter("?qty_retur", $this->QtyRetur);
		$this->connector->AddParameter("?price", $this->Price);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?ex_invdetail_id", $this->ExInvDetailId);
		$rs = $this->connector->ExecuteNonQuery();
        $rsx = null;
        $did = 0;
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $did = $this->Id;
            //tambah stock
            $this->connector->CommandText = "SELECT fc_ar_returndetail_post($did) As valresult;";
            $rsx = $this->connector->ExecuteQuery();
            $this->UpdateArReturnMaster($this->RjId);
		}
		return $rs;
	}

	public function Delete($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ar_returndetail_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus detail
		$this->connector->CommandText = "DELETE FROM t_ar_return_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdateArReturnMaster($this->RjId);
        }
        return $rs;
	}

    public function UpdateArReturnMaster($rjId){
        $sql = 'Update t_ar_return_master a Set a.rj_amount = 0 Where a.id = ?rjId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?rjId", $rjId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ar_return_master a Join (Select c.rj_id, sum(c.sub_total) As sumPrice From t_ar_return_detail c Group By c.rj_id) b';
        $sql.= ' On a.id = b.rj_id Set a.rj_amount = b.sumPrice Where a.id = ?rjId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?rjId", $rjId);
        $rs = $this->connector->ExecuteNonQuery();        
        return $rs;
    }
}
// End of File: estimasi_detail.php
