<?php
class CoaHeader extends EntityBase {
	public $Id;
	public $NmLaporan;
	public $NmKelompok;
    public $KdKelompok;
    public $Bagian;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->NmLaporan = $row["nm_laporan"];
		$this->NmKelompok = $row["nm_kelompok"];
        $this->KdKelompok = $row["kd_kelompok"];
        $this->Bagian = $row["bagian"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Coa[]
	 */
	public function LoadAll($orderBy = "a.kd_kelompok") {
		$this->connector->CommandText = "SELECT a.* FROM m_lk_rekap_master AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new CoaHeader();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * Untuk mencari semua akun berdasarkan parent id akun.
	 * Untuk parameter pertama special karena bisa menerima int[] atau int. Jika int[] maka semua parent id yang ada pada param akan di include
	 *
	 * @param int|int[] $parentId
	 * @param bool $includeDeleted
	 * @return Coa[]
	 */
	public function LoadByBagian($bagian) {
		$this->connector->CommandText = "SELECT a.* FROM m_lk_rekap_master AS a WHERE a.bagian = ?bagian ORDER BY a.kd_kelompok";
		$this->connector->AddParameter("?bagian", $bagian);
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new CoaHeader();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param $id
	 * @return Coa|null
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param $id
	 * @return Coa|null
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_lk_rekap_master AS a WHERE a.id = ?id";
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
	 * @param $code
	 * @return Coa|null
	 */
	public function FindByKdKelompok($kdKelompok) {
		$this->connector->CommandText = "SELECT a.* FROM m_lk_rekap_master AS a WHERE a.kd_kelompok = ?kd_kelompok";
		$this->connector->AddParameter("?kd_kelompok", $kdKelompok);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_lk_rekap_master(nm_laporan,nm_kelompok,kd_kelompok,bagian) VALUES(?nm_laporan,?nm_kelompok,?kd_kelompok,?bagian)';
		$this->connector->AddParameter("?nm_laporan", $this->NmLaporan, "varchar");
        $this->connector->AddParameter("?nm_kelompok", $this->NmKelompok,"varchar");
        $this->connector->AddParameter("?kd_kelompok", $this->KdKelompok, "varchar");
        $this->connector->AddParameter("?bagian", $this->Bagian);
        return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_lk_rekap_master SET nm_laporan = ?nm_laporan, nm_kelompok = ?nm_kelompok, kd_kelompok = ?kd_kelompok, bagian = ?bagian WHERE id = ?id';
        $this->connector->AddParameter("?nm_laporan", $this->NmLaporan, "varchar");
        $this->connector->AddParameter("?nm_kelompok", $this->NmKelompok,"varchar");
        $this->connector->AddParameter("?kd_kelompok", $this->KdKelompok, "varchar");
        $this->connector->AddParameter("?bagian", $this->Bagian);
		$this->connector->AddParameter("?id", $id);

		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = 'Delete From m_lk_rekap_master WHERE id = ?id';
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

}
