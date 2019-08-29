<?php

class ApReturnDetail extends EntityBase {
	public $Id;
    public $CabangId;
	public $RbId;
    public $RbNo;
	public $ItemDescs;
    public $ItemCode;
    public $ItemId;
    public $ExGrnId;
    public $ExGrnNo;
    public $ExGrnDetailId;
    public $QtyBeli;
	public $QtyRetur;
	public $Price;
    public $SubTotal;
    public $SatBesar;
    public $SatKecil;

	public function FillProperties(array $row) {
		$this->Id = $row["id"];        
		$this->RbId = $row["rb_id"];
        $this->CabangId = $row["cabang_id"];
        $this->RbNo = $row["rb_no"];
        $this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
		$this->ItemDescs = $row["item_descs"];                
        $this->ExGrnId = $row["ex_grn_id"];
        $this->ExGrnNo = $row["ex_grn_no"];
        $this->ExGrnDetailId = $row["ex_grndetail_id"];
        $this->QtyBeli = $row["qty_beli"];
		$this->QtyRetur = $row["qty_retur"];
		$this->Price = $row["price"];
        $this->SubTotal = $row["sub_total"];
        $this->SatBesar = $row["bsatbesar"];
        $this->SatKecil = $row["bsatkecil"];
	}

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_return_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_return_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByRbId($rbId, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_return_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.rb_id = ?rbId ORDER BY $orderBy";
		$this->connector->AddParameter("?rbId", $rbId);
		$result = array();
		$rs = $this->connector->ExecuteQuery();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ApReturnDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByRbNo($grnNo, $orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ap_return_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.rb_no = ?grnNo ORDER BY $orderBy";
        $this->connector->AddParameter("?grnNo", $grnNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new ApReturnDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_ap_return_detail(rb_id, cabang_id, rb_no, item_id, item_code, item_descs, ex_grn_id, ex_grn_no, qty_beli, qty_retur, price, sub_total, ex_grndetail_id)
VALUES(?rb_id, ?cabang_id, ?rb_no, ?item_id, ?item_code, ?item_descs, ?ex_grn_id, ?ex_grn_no, ?qty_beli, ?qty_retur, ?price, ?sub_total, ?ex_grndetail_id)";
		$this->connector->AddParameter("?rb_id", $this->RbId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?rb_no", $this->RbNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
		$this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?ex_grn_id", $this->ExGrnId);
        $this->connector->AddParameter("?ex_grn_no", $this->ExGrnNo);
        $this->connector->AddParameter("?qty_beli", $this->QtyBeli);
		$this->connector->AddParameter("?qty_retur", $this->QtyRetur);
		$this->connector->AddParameter("?price", $this->Price);
        $this->connector->AddParameter("?sub_total", $this->SubTotal);
        $this->connector->AddParameter("?ex_grndetail_id", $this->ExGrnDetailId);
		$rs = $this->connector->ExecuteNonQuery();
        $rsx = null;
        $did = 0;
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $did = $this->Id;
            //tambah stock
            $this->connector->CommandText = "SELECT fc_ap_returndetail_post($did) As valresult;";
            $rsx = $this->connector->ExecuteQuery();
            $this->UpdateApReturnMaster($this->RbId);
		}
		return $rs;
	}

	public function Delete($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ap_returndetail_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //baru hapus detail
		$this->connector->CommandText = "DELETE FROM t_ap_return_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdateApReturnMaster($this->RbId);
        }
        return $rs;
	}

    public function UpdateApReturnMaster($rbId){
        $sql = 'Update t_ap_return_master a Set a.rb_amount = 0 Where a.id = ?rbId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?rbId", $rbId);
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_ap_return_master a Join (Select c.rb_id, sum(c.sub_total) As sumPrice From t_ap_return_detail c Group By c.rb_id) b';
        $sql.= ' On a.id = b.rb_id Set a.rb_amount = b.sumPrice Where a.id = ?rbId;';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?rbId", $rbId);
        $rs = $this->connector->ExecuteNonQuery();        
        return $rs;
    }
}
// End of File: estimasi_detail.php
