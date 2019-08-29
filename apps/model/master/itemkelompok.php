<?php
class ItemKelompok extends EntityBase {
	public $Id;
	public $IsDeleted = false;
	public $Kelompok;
	public $Keterangan;
    public $CreatebyId;
    public $UpdatebyId;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->Kelompok = $row["kelompok"];
		$this->Keterangan = $row["keterangan"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($orderBy = "a.kelompok", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.* FROM m_barang_kelompok AS a ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.* FROM m_barang_kelompok AS a WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ItemKelompok();
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
		$this->connector->CommandText = "SELECT a.* FROM m_barang_kelompok AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	public function FindByKelompok($iKelompok) {
		$this->connector->CommandText = "SELECT a.* FROM m_barang_kelompok AS a WHERE a.kelompok = ?iKelompok";
		$this->connector->AddParameter("?iKelompok", $iKelompok);
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
		$this->connector->CommandText = 'INSERT INTO m_barang_kelompok(kelompok,keterangan,createby_id,create_time) VALUES(?kelompok,?keterangan,?createby_id,now())';
		$this->connector->AddParameter("?kelompok", $this->Kelompok);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_barang_kelompok SET kelompok = ?kelompok, keterangan = ?keterangan, updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
		$this->connector->AddParameter("?kelompok", $this->Kelompok);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		//$this->connector->CommandText = 'UPDATE m_barang_kelompok SET is_deleted = 1,updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
		$this->connector->CommandText = 'Delete From m_barang_kelompok WHERE id = ?id';
		$this->connector->AddParameter("?id", $id);
        //$this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		return $this->connector->ExecuteNonQuery();
	}

}
