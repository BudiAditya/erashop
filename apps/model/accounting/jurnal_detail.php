<?php

class JurnalDetail extends EntityBase {
	public $Id;
	public $NoUrut;
    public $NoVoucher;
	public $AcDebetNo;
    public $AcKreditNo;
    public $Uraian;
	public $Jumlah;
	public $CabangId;
    public $RelasiId;
    public $KdCabang;
    public $NmCabang;
    public $RelasiCd;
    public $RelasiName;

	// Helper Variable;
	public $MarkedForDeletion = false;


	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->NoUrut = $row["no_urut"];
        $this->NoVoucher = $row["no_voucher"];
		$this->AcDebetNo = $row["acdebet_no"];
        $this->AcKreditNo = $row["ackredit_no"];
        $this->Uraian = $row["uraian"];;
		$this->Jumlah = $row["jumlah"];
		$this->CabangId = $row["cabang_id"];
        $this->RelasiId = $row["relasi_id"];
        $this->KdCabang = $row["kd_cabang"];
        $this->NmCabang = $row["nm_cabang"];
        $this->RelasiCd = $row["customer_cd"];
        $this->RelasiName = $row["customer_name"];
	}

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_gl_voucherdetail AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_gl_voucherdetail AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByNoVoucher($voucherNo, $orderBy = "a.no_urut") {
        $this->connector->CommandText = "SELECT a.* FROM vw_gl_voucherdetail AS a WHERE a.no_voucher = ?voucherNo ORDER BY $orderBy";
        $this->connector->AddParameter("?voucherNo", $voucherNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new JurnalDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert($entityId,$cabangId) {
		$this->connector->CommandText =
"INSERT INTO t_gl_voucher_detail(no_urut, no_voucher, acdebet_no, ackredit_no, uraian, jumlah, cabang_id, relasi_id)
VALUES(?no_urut, ?no_voucher, ?acdebet_no, ?ackredit_no, ?uraian, ?jumlah, ?cabang_id, ?relasi_id)";
		$this->connector->AddParameter("?no_urut", $this->NoUrut);
        $this->connector->AddParameter("?no_voucher", $this->NoVoucher);
		$this->connector->AddParameter("?acdebet_no", $this->AcDebetNo);
        $this->connector->AddParameter("?ackredit_no", $this->AcKreditNo);
        $this->connector->AddParameter("?uraian", $this->Uraian);
		$this->connector->AddParameter("?jumlah", $this->Jumlah);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?relasi_id", $this->RelasiId);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $this->UpdateJurnalMaster($entityId,$cabangId,$this->NoVoucher);
		}
		return $rs;
	}

	public function Update($entityId,$cabangId,$id) {
		$this->connector->CommandText =
"UPDATE t_gl_voucher_detail SET
	  no_urut = ?no_urut
	, no_voucher = ?no_voucher
	, acdebet_no = ?acdebet_no
	, jumlah = ?jumlah
	, cabang_id = ?cabang_id
	, relasi_id = ?relasi_id
	, ackredit_no = ?ackredit_no
	, uraian = ?uraian
WHERE id = ?id";
        $this->connector->AddParameter("?no_urut", $this->NoUrut);
        $this->connector->AddParameter("?no_voucher", $this->NoVoucher);
        $this->connector->AddParameter("?acdebet_no", $this->AcDebetNo);
        $this->connector->AddParameter("?ackredit_no", $this->AcKreditNo);
        $this->connector->AddParameter("?uraian", $this->Uraian);
        $this->connector->AddParameter("?jumlah", $this->Jumlah);
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?relasi_id", $this->RelasiId);
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdateJurnalMaster($entityId,$cabangId,$this->NoVoucher);
        }
        return $rs;
	}

	public function Delete($entityId,$cabangId,$id) {
		$this->connector->CommandText = "DELETE FROM t_gl_voucher_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs) {
            $this->UpdateJurnalMaster($entityId,$cabangId,$this->NoVoucher);
        }
        return $rs;
	}

    public function UpdateJurnalMaster($entityId,$cabangId,$noVoucher){
        $sql = "Update t_gl_voucher_master a Set a.doc_amount = 0 Where a.no_voucher = '".$noVoucher."' And a.entity_id = ".$entityId." And a.cabang_id = ".$cabangId;
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteNonQuery();
        $sql = 'Update t_gl_voucher_master a
Join (Select c.no_voucher, coalesce(sum(c.jumlah),0) As sumJumlah From t_gl_voucher_detail c Group By c.no_voucher) b
On a.no_voucher = b.no_voucher Set a.doc_amount = b.sumJumlah Where a.no_voucher = ?noVoucher And a.entity_id ='.$entityId.' And a.cabang_id ='.$cabangId;
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?noVoucher", $noVoucher);
        $rs = $this->connector->ExecuteNonQuery();
        return $rs;
    }
}
// End of File: estimasi_detail.php
