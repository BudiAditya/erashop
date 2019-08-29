<?php

class Bank extends EntityBase {
	public $Id;
	public $IsDeleted = false;
	public $EntityId;
	public $CabangId;
	public $Name;
	public $Branch;
	public $Address;
	public $NoRekening;
	public $CurrencyCode = "IDR";
	public $AccNo;
	public $CostAccNo;
	public $RevAccNo;
	public $CreateById;
	public $CreateTime;
	public $UpdateById;
	public $UpdateTime;

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->EntityId = $row["entity_id"];
		$this->CabangId = $row["cabang_id"];
		$this->Name = $row["bank_name"];
		$this->Branch = $row["branch"];
		$this->Address = $row["address"];
		$this->NoRekening = $row["rek_no"];
		$this->CurrencyCode = $row["currency_cd"];
		$this->AccNo = $row["acc_no"];
		$this->CostAccNo = $row["cost_acc_no"];
		$this->RevAccNo = $row["rev_acc_no"];
		$this->CreateById = $row["createby_id"];
		$this->CreateTime = $row["create_time"];
		$this->UpdateById = $row["updateby_id"];
		$this->UpdateTime = $row["update_time"];
	}

	/**
	 * @param string $orderBy
	 * @return Bank[]
	 */
	public function LoadAll($orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* FROM m_bank_account AS a WHERE a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Bank();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function LoadAllNonCash($sbu,$orderBy = "a.bank_name") {
		$this->connector->CommandText = "SELECT a.* FROM m_bank_account AS a WHERE a.cabang_id = $sbu And a.bank_name <> 'TUNAI' and a.is_deleted = 0 ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Bank();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}
	/**
	 * @param int $sbu
	 * @param string $orderBy
	 * @return Bank[]
	 */
	public function LoadByEntityId($sbu, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* FROM m_bank_account AS a WHERE a.is_deleted = 0 AND a.entity_id = ?Entity ORDER BY $orderBy";
		$this->connector->AddParameter("?Entity", $sbu);
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Bank();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function LoadByCabangId($cbi, $orderBy = "a.id") {
		$this->connector->CommandText = "SELECT a.* FROM m_bank_account AS a WHERE a.is_deleted = 0 AND a.cabang_id = ?cabang_id ORDER BY $orderBy";
		$this->connector->AddParameter("?cabang_id", $cbi);
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Bank();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Bank
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_bank_account AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

	/**
	 * Mencari data bank berdasarkan akun CoA nya
	 *
	 * @param int $sbu
	 * @param int $accNo
	 * @return Bank
	 */
	public function LoadByAccNo($sbu, $accNo) {
		$this->connector->CommandText = "SELECT a.* FROM m_bank_account AS a WHERE a.entity_id = ?Entity AND a.acc_no = ?id And a.is_deleted = 0";
		$this->connector->AddParameter("?Entity", $sbu);
		$this->connector->AddParameter("?id", $accNo);

		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}

		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO m_bank_account(entity_id, cabang_id, bank_name, branch, address, rek_no, currency_cd, acc_no, cost_acc_no, rev_acc_no, createby_id, create_time)
VALUES(?Entity, ?cabang_id, ?name, ?branch, ?address, ?noRek, ?currency, ?accNo, ?costAccNo, ?revAccNo, ?user, NOW())";

		$this->connector->AddParameter("?Entity", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?name", $this->Name);
		$this->connector->AddParameter("?branch", $this->Branch);
		$this->connector->AddParameter("?address", $this->Address);
		$this->connector->AddParameter("?noRek", $this->NoRekening, "varchar");
		$this->connector->AddParameter("?currency", $this->CurrencyCode, "varchar");
		$this->connector->AddParameter("?accNo", $this->AccNo);
		$this->connector->AddParameter("?costAccNo", $this->CostAccNo);
		$this->connector->AddParameter("?revAccNo", $this->RevAccNo);
		$this->connector->AddParameter("?user", $this->CreateById);

		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
		}

		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE m_bank_account SET
	bank_name = ?name
	, branch = ?branch
	, address = ?address
	, rek_no = ?noRek
	, currency_cd = ?currency
	, acc_no = ?accNo
	, cost_acc_no = ?costAccNo
	, rev_acc_no = ?revAccNo
	, updateby_id = ?user
	, update_time = NOW()
	, entity_id = ?entity_id
	, cabang_id = ?cabang_id
WHERE id = ?id";
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?name", $this->Name);
		$this->connector->AddParameter("?branch", $this->Branch);
		$this->connector->AddParameter("?address", $this->Address);
		$this->connector->AddParameter("?noRek", $this->NoRekening, "varchar");
		$this->connector->AddParameter("?currency", $this->CurrencyCode, "varchar");
		$this->connector->AddParameter("?accNo", $this->AccNo);
		$this->connector->AddParameter("?costAccNo", $this->CostAccNo);
		$this->connector->AddParameter("?revAccNo", $this->RevAccNo);
		$this->connector->AddParameter("?user", $this->UpdateById);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE m_bank_account SET is_deleted = 1, updateby_id = ?user, update_time = NOW() WHERE id = ?id";
		$this->connector->AddParameter("?user", $this->UpdateById);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}

// End of file: bank.php
