<?php
class Area extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $AreaDescs;
    public $AreaName;

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
        $this->AreaDescs = $row["area_descs"];
        $this->AreaName = $row["area_name"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Area[]
	 */
	public function LoadAll($orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_area AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_area AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.is_deleted = 0
ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Area();
				$temp->FillProperties($row);

				$result[] = $temp;
			}
		}

		return $result;
	}

	/**
	 * @param int $id
	 * @return Area
	 */
	public function FindById($id) {
		$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_area AS a
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
	 * @return Area
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Area[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_area AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.entity_id = ?eti
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd
FROM m_area AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
WHERE a.is_deleted = 0 AND a.entity_id = ?eti
ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Area();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText =
        'INSERT INTO m_area(entity_id,area_name,area_descs) VALUES(?entity_id,?area_name,?area_descs)';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?area_name", $this->AreaId);
        $this->connector->AddParameter("?area_descs", $this->AreaDescs);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText =
'UPDATE m_area SET
	entity_id = ?entity_id,
	area_name = ?area_id,
	area_descs = ?area_descs
WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?area_name", $this->AreaId);
        $this->connector->AddParameter("?area_descs", $this->AreaDescs);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE m_area SET is_deleted = 1 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);

		return $this->connector->ExecuteNonQuery();
	}
}
