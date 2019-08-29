<?php
class Department extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
	public $DeptCd;
	public $DeptName;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->EntityId = $row["entity_id"];
		$this->EntityCd = $row["entity_cd"];
		$this->DeptCd = $row["dept_cd"];
		$this->DeptName = $row["dept_name"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Department[]
	 */
	public function LoadAll($orderBy = "a.dept_cd", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM sys_dept AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM sys_dept AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.is_deleted = 0
ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Department();
				$temp->FillProperties($row);

				$result[] = $temp;
			}
		}

		return $result;
	}

	/**
	 * @param int $id
	 * @return Department
	 */
	public function FindById($id) {
		$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM sys_dept AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.id = ?id";
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
	 * @return Department
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Department[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.dept_cd", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM sys_dept AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.entity_id = ?eti
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM sys_dept AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.is_deleted = 0 AND a.entity_id = ?eti
ORDER BY $orderBy";
		}

		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Department();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText =
        'INSERT INTO sys_dept(entity_id,dept_cd,dept_name) VALUES(?entity_id,?dept_cd,?dept_name)';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?dept_cd", $this->DeptCd);
        $this->connector->AddParameter("?dept_name", $this->DeptName);
		
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText =
'UPDATE sys_dept SET
	entity_id = ?entity_id,
	dept_cd = ?dept_cd,
	dept_name = ?dept_name
WHERE id = ?id';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?dept_cd", $this->DeptCd);
        $this->connector->AddParameter("?dept_name", $this->DeptName);
		$this->connector->AddParameter("?id", $id);

		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE sys_dept SET is_deleted = 1 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);

		return $this->connector->ExecuteNonQuery();
	}
}
