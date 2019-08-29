<?php
class Satuan extends EntityBase {
	public $Id;
    public $Satuan;
	public $Keterangan;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->Satuan = $row["satuan"];
		$this->Keterangan = $row["keterangan"];
	}

    public function LoadAll($orderBy = "a.satuan") {
		$this->connector->CommandText = "SELECT a.* FROM m_satuan AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Satuan();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_satuan AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_satuan AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function FindBySatuan($satuan) {
        $this->connector->CommandText = "SELECT a.* FROM m_satuan AS a WHERE a.satuan = ?satuan";
        $this->connector->AddParameter("?satuan", $satuan);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_satuan (satuan,keterangan) VALUES (?satuan,?keterangan)';
		$this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_satuan SET satuan = ?satuan, keterangan = ?keterangan WHERE id = ?id';
        $this->connector->AddParameter("?satuan", $this->Satuan);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
        $this->connector->CommandText = "Delete From m_satuan WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

}

