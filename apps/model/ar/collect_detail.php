<?php

class CollectDetail extends EntityBase {
	public $Id;
	public $CollectId;
	public $SeqNo;
    public $DetailStatus; // 0 = Draft, 1 = Ditagih, 2 = Terbayar, 3 = Ditunda
	public $Keterangan;
    public $WarkatNo;
    public $InvoiceId;
    public $RecollectDate;
	public $OutstandingAmount;
	public $PaidAmount;
    public $BalanceAmount;
    public $CustomerName;
    public $InvoiceNo;
    public $InvoiceDate;
    public $InvoiceDueDate;

	// Helper Variable;
	public $MarkedForDeletion = false;


	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->CollectId = $row["collect_id"];
		$this->SeqNo = $row["seq_no"];
        $this->DetailStatus = $row["detail_status"];
		$this->Keterangan = $row["keterangan"];
        $this->WarkatNo = $row["warkat_no"];
        $this->InvoiceId = $row["invoice_id"];
        $this->RecollectDate = $row["recollect_date"];
		$this->OutstandingAmount = $row["outstanding_amount"];
		$this->PaidAmount = $row["paid_amount"];
        $this->BalanceAmount = $row["outstanding_amount"] - $row["paid_amount"];
        $this->CustomerName = $row["customer_name"];
        $this->InvoiceNo = $row["invoice_no"];
        $this->InvoiceDate = $row["invoice_date"];
        $this->InvoiceDueDate = $row["due_date"];
	}
    
    public function FormatRecollectDate($format = HUMAN_DATE) {
        return is_int($this->RecollectDate) ? date($format, $this->RecollectDate) : null;
    }
    
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ar_collect_detail AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ar_collect_detail AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByCollectId($collectId, $orderBy = "a.seq_no") {
		$this->connector->CommandText = "SELECT a.* FROM vw_ar_collect_detail AS a WHERE a.collect_id = ?collectId ORDER BY $orderBy";
		$this->connector->AddParameter("?collectId", $collectId);
		$result = array();
		$rs = $this->connector->ExecuteQuery();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new CollectDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByCollectNo($collectNo, $orderBy = "a.seq_no") {
        $this->connector->CommandText = "SELECT a.* FROM vw_ar_collect_detail AS a Join t_ar_collect_master as b On a.collect_id = b.id WHERE b.collect_no = ?collectNo ORDER BY $orderBy";
        $this->connector->AddParameter("?collectNo", $collectNo);
        $result = array();
        $rs = $this->connector->ExecuteQuery();
        if ($rs) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new CollectDetail();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_ar_collect_detail(collect_id, seq_no, keterangan, warkat_no, invoice_id, recollect_date, outstanding_amount, paid_amount, detail_status)
VALUES(?collect_id, ?seq_no, ?keterangan, ?warkat_no, ?invoice_id, ?recollect_date, ?outstanding_amount, ?paid_amount, ?detail_status)";
		$this->connector->AddParameter("?collect_id", $this->CollectId);
		$this->connector->AddParameter("?seq_no", $this->SeqNo);
		$this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?warkat_no", $this->WarkatNo);
        $this->connector->AddParameter("?invoice_id", $this->InvoiceId);
        $this->connector->AddParameter("?recollect_date", $this->RecollectDate);
		$this->connector->AddParameter("?outstanding_amount", $this->OutstandingAmount);
		$this->connector->AddParameter("?paid_amount", $this->PaidAmount);
        $this->connector->AddParameter("?detail_status", $this->DetailStatus);
		$rs = $this->connector->ExecuteNonQuery();
        $rx = 0;
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $this->UpdateCollectMaster($this->CollectId);
            $this->connector->CommandText = "Insert Into t_ar_collect_detail_temporary Select a.* From t_ar_collect_detail a Where a.id = ?detail_id;";
            $this->connector->AddParameter("?detail_id", $this->Id);
            $rx = $this->connector->ExecuteNonQuery();
            if ($rx == 1){
                $this->UpdateInvoiceCollectStatus($this->CollectId,1);
            }
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_ar_collect_detail SET
	  collect_id = ?collect_id
	, seq_no = ?seq_no
	, keterangan = ?keterangan
	, outstanding_amount = ?outstanding_amount
	, paid_amount = ?paid_amount
	, warkat_no = ?warkat_no
	, invoice_id = ?invoice_id
	, recollect_date = ?recollect_date
	, detail_status = ?detail_status
WHERE id = ?id";
        $this->connector->AddParameter("?collect_id", $this->CollectId);
        $this->connector->AddParameter("?seq_no", $this->SeqNo);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?warkat_no", $this->WarkatNo);
        $this->connector->AddParameter("?invoice_id", $this->InvoiceId);
        $this->connector->AddParameter("?recollect_date", $this->RecollectDate);
        $this->connector->AddParameter("?outstanding_amount", $this->OutstandingAmount);
        $this->connector->AddParameter("?paid_amount", $this->PaidAmount);
        $this->connector->AddParameter("?detail_status", $this->DetailStatus);
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdateCollectMaster($this->CollectId);
            $this->connector->CommandText = "Insert Into t_ar_collect_detail_temporary Select a.* From t_ar_collect_detail a Where a.id = ?detail_id;";
            $this->connector->AddParameter("?detail_id", $id);
            $rx = $this->connector->ExecuteNonQuery();
            if ($rx == 1){
                $this->UpdateInvoiceCollectStatus($this->CollectId,1);
            }
        }
        return $rs;
	}

	public function Delete($id) {
        $rx = 0;
        $this->connector->CommandText = "Insert Into t_ar_collect_detail_temporary Select a.* From t_ar_collect_detail a Where a.id = ?detail_id;";
        $this->connector->AddParameter("?detail_id", $id);
        $rx = $this->connector->ExecuteNonQuery();
		$this->connector->CommandText = "DELETE FROM t_ar_collect_detail WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdateCollectMaster($this->CollectId);
            if ($rx == 1){
                $this->UpdateInvoiceCollectStatus($this->CollectId,0);
            }
        }
        return $rs;
	}

    public function UpdateCollectMaster($collectId){
        $this->connector->CommandText = "SELECT fc_ar_collect_master_update(?collectId) As valresult;";
        $this->connector->AddParameter("?collectId", $collectId);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }

    public function DeleteByInvoiceId($collectId = 0, $invoiceId = 0) {
        $rx = 0;
        $this->connector->CommandText = "Insert Into t_ar_collect_detail_temporary Select a.* From t_ar_collect_detail a Where collect_id = ?collectId and invoice_id = ?invoiceId;";
        $this->connector->AddParameter("?collectId",$collectId);
        $this->connector->AddParameter("?invoiceId",$invoiceId);
        $rx = $this->connector->ExecuteNonQuery();
        $this->connector->CommandText = "DELETE FROM t_ar_collect_detail WHERE collect_id = ?collectId and invoice_id = ?invoiceId;";
        $this->connector->AddParameter("?collectId",$collectId);
        $this->connector->AddParameter("?invoiceId",$invoiceId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->UpdateCollectMaster($collectId);
            if ($rx == 1){
                $this->UpdateInvoiceCollectStatus($this->CollectId,0);
            }
        }
        return $rs;
    }

    public function UpdateInvoiceCollectStatus($collectId = 0, $collectStatus = 0){
        $this->connector->CommandText = "SELECT fc_ar_collect_update_invoice_status(?collectId,?collectStatus) As valresult;";
        $this->connector->AddParameter("?collectId", $collectId);
        $this->connector->AddParameter("?collectStatus", $collectStatus);
        $rs = $this->connector->ExecuteQuery();
        $row = $rs->FetchAssoc();
        return strval($row["valresult"]);
    }
}
// End of File: estimasi_detail.php
