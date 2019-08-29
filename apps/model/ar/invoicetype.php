<?php

class InvoiceType extends EntityBase {
	public $Id;
	public $IsDeleted = false;
	public $IvcType;
	public $Description;
	public $RevAccId;
    public $RevAccNo;
    public $RevAccName;
    public $ArAccId;
    public $ArAccNo;
    public $ArAccName;
    public $CreateById;
    public $CreateTime;
	public $UpdateById;
	public $UpdateTime;

    public function __construct($id = null) {
        parent::__construct();
        if (is_numeric($id)) {
            $this->LoadById($id);
        }
    }

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->IvcType = $row["invoicetype"];
		$this->Description = $row["description"];
		$this->RevAccId = $row["rev_acc_id"];
        $this->RevAccNo = $row["rev_acc_no"];
        $this->RevAccName = $row["rev_acc_name"];
        $this->ArAccId = $row["ar_acc_id"];
        $this->ArAccNo = $row["ar_acc_no"];
        $this->ArAccName = $row["ar_acc_name"];
		$this->CreateById = $row["createby_id"];
		$this->CreateTime = strtotime($row["create_time"]);
        $this->UpdateById = $row["updateby_id"];
        $this->UpdateTime = strtotime($row["update_time"]);
	}

	public function LoadAll($orderBy = "a.invoicetype, a.description") {
		$this->connector->CommandText = "SELECT a.* FROM vw_ar_invoicetype AS a WHERE a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new InvoiceType();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ar_invoicetype AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

	public function Insert() {
		$this->connector->CommandText = "INSERT INTO m_ar_invoicetype(invoicetype, description, rev_acc_id, ar_acc_id, createby_id, create_time) VALUES(?invoicetype, ?description, ?rev_acc_id, ?ar_acc_id, ?user, NOW())";
		$this->connector->AddParameter("?invoicetype", $this->IvcType);
		$this->connector->AddParameter("?description", $this->Description);
		$this->connector->AddParameter("?rev_acc_id", $this->RevAccId);
        $this->connector->AddParameter("?ar_acc_id", $this->ArAccId);
		$this->connector->AddParameter("?user", $this->UpdateById);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE m_ar_invoicetype SET
	invoicetype = ?invoicetype
	, description = ?description
	, rev_acc_id = ?rev_acc_id
	, ar_acc_id = ?ar_acc_id
	, updateby_id = ?user
	, update_time = NOW()
WHERE id = ?id";
        $this->connector->AddParameter("?invoicetype", $this->IvcType);
        $this->connector->AddParameter("?description", $this->Description);
        $this->connector->AddParameter("?rev_acc_id", $this->RevAccId);
        $this->connector->AddParameter("?ar_acc_id", $this->ArAccId);
        $this->connector->AddParameter("?user", $this->UpdateById);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE m_ar_invoicetype SET is_deleted = 1 , updateby_id = ?user , update_time = NOW() WHERE id = ?id";
		$this->connector->AddParameter("?user", $this->UpdateById);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}

// End of file: bank.php
