<?php

class TransferDetail extends EntityBase {
	public $Id;
    public $CabangId;
	public $NpbId;
    public $NpbNo;
	public $ItemDescs;
    public $ItemCode;
    public $ItemId;
    public $Lqty;
    public $Sqty;
	public $Qty;    
    public $SatBesar;
    public $SatKecil;

	// Helper Variable;
	public $MarkedForDeletion = false;


	public function FillProperties(array $row) {
		$this->Id = $row["id"];        
		$this->NpbId = $row["npb_id"];
        $this->CabangId = $row["cabang_id"];
        $this->NpbNo = $row["npb_no"];
        $this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
		$this->ItemDescs = $row["item_descs"];                
        $this->Lqty = $row["l_qty"];
        $this->Sqty = $row["s_qty"];
		$this->Qty = $row["qty"];
        $this->SatBesar = $row["bsatbesar"];
        $this->SatKecil = $row["bsatkecil"];
	}

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ic_transfer_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ic_transfer_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByNpbId($npbId, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ic_transfer_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.npb_id = ?npbId ORDER BY $orderBy";
		$this->connector->AddParameter("?npbId", $npbId);
		$result = array();
		$rs = $this->connector->ExecuteQuery();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new TransferDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByNpbNo($npbNo, $orderBy = "a.id") {
        $this->connector->CommandText = "SELECT a.*,b.bsatbesar,b.bsatkecil FROM t_ic_transfer_detail AS a Join m_barang AS b On a.item_code = b.bkode WHERE a.npb_no = ?npbNo ORDER BY $orderBy";
        $this->connector->AddParameter("?npbNo", $npbNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new TransferDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_ic_transfer_detail(npb_id, cabang_id, npb_no, item_id, item_code, item_descs, l_qty, s_qty, qty) VALUES(?npb_id, ?cabang_id, ?npb_no, ?item_id, ?item_code, ?item_descs, ?l_qty, ?s_qty, ?qty)";
		$this->connector->AddParameter("?npb_id", $this->NpbId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?npb_no", $this->NpbNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
		$this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?l_qty", $this->Lqty);
        $this->connector->AddParameter("?s_qty", $this->Sqty);
		$this->connector->AddParameter("?qty", $this->Qty);
		$rs = $this->connector->ExecuteNonQuery();
        $rsx = null;
        $did = 0;
        if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $did = $this->Id;
            //tambah stock
            $this->connector->CommandText = "SELECT fc_ic_transferdetail_post($did) As valresult;";
            $rsx = $this->connector->ExecuteQuery();
		}
		return $rs;
	}

	public function Update($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ic_transferdetail_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        $this->connector->CommandText =
"UPDATE t_ic_transfer_detail SET
	  npb_id = ?npb_id
	, cabang_id = ?cabang_id
	, npb_no = ?npb_no
	, item_descs = ?item_descs
	, qty = ?qty
	, item_code = ?item_code
	, item_id = ?item_id
	, l_qty = ?l_qty
	, s_qty = ?s_qty
WHERE id = ?id";
        $this->connector->AddParameter("?npb_id", $this->NpbId);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?npb_no", $this->NpbNo);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode, "char");
        $this->connector->AddParameter("?item_descs", $this->ItemDescs);
        $this->connector->AddParameter("?l_qty", $this->Lqty);
        $this->connector->AddParameter("?s_qty", $this->Sqty);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            //potong stock lagi
            $this->connector->CommandText = "SELECT fc_ic_transferdetail_post($id) As valresult;";
            $rsx = $this->connector->ExecuteQuery();
        }
        return $rs;
	}

	public function Delete($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ic_transferdetail_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //hapus detail
		$this->connector->CommandText = "DELETE FROM t_ic_transfer_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
	}
}
// End of File: estimasi_detail.php
