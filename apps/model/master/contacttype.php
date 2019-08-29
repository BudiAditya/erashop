<?php
class ContactType extends EntityBase {
	public $Id;
	public $IsDeleted = false;
	public $TypeCode;
	public $TypeDescs;
    public $CreatebyId;
    public $UpdatebyId;
	public $ArAccNo;
	public $ApAccNo;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->TypeCode = $row["type_code"];
		$this->TypeDescs = $row["type_descs"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
		$this->ArAccNo = $row["ar_acc_no"];
		$this->ApAccNo = $row["ap_acc_no"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($orderBy = "a.type_code", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.* FROM m_contacttype AS a ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.* FROM m_contacttype AS a WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}

		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ContactType();
				$temp->FillProperties($row);

				$result[] = $temp;
			}
		}

		return $result;
	}

	/**
	 * @param int $id
	 * @return Location
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_contacttype AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();

		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}

		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	/**
	 * @param int $id
	 * @return Location
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_contacttype(ar_acc_no,ap_acc_no,type_code,type_descs,createby_id,create_time) VALUES(?ar_acc_no,?ap_acc_no,?type_code,?type_descs,?createby_id,now())';
		$this->connector->AddParameter("?type_code", $this->TypeCode);
        $this->connector->AddParameter("?type_descs", $this->TypeDescs);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		$this->connector->AddParameter("?ar_acc_no", $this->ArAccNo);
		$this->connector->AddParameter("?ap_acc_no", $this->ApAccNo);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_contacttype SET ar_acc_no = ?ar_acc_no, ap_acc_no = ?ap_acc_no, type_code = ?type_code, type_descs = ?type_descs, updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
		$this->connector->AddParameter("?type_code", $this->TypeCode);
        $this->connector->AddParameter("?type_descs", $this->TypeDescs);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?ar_acc_no", $this->ArAccNo);
		$this->connector->AddParameter("?ap_acc_no", $this->ApAccNo);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = 'UPDATE m_contacttype SET is_deleted = 1,updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
		$this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		return $this->connector->ExecuteNonQuery();
	}

}
