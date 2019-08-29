<?php
class CoaGroup extends EntityBase {
	public $Id;
	public $Kategori;
	public $KdInduk;
    public $KdKelompok;
    public $PSaldo;
    public $Tk;
    public $MRekap;
    public $UpdateById;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->Kategori = $row["kategori"];
		$this->KdInduk = $row["kd_induk"];
        $this->KdKelompok = $row["kd_kelompok"];
        $this->PSaldo = $row["psaldo"];
        $this->Tk = $row["tk"];
        $this->MRekap = $row["mrekap"];
        $this->UpdateById = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Coa[]
	 */
	public function LoadAll($orderBy = "a.kd_induk") {
		$this->connector->CommandText = "SELECT a.* FROM m_lk_rekap_detail AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new CoaGroup();
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
	public function LoadByKdKelompok($kdKelompok) {
		$this->connector->CommandText = "SELECT a.* FROM m_lk_rekap_detail AS a WHERE a.kd_kelompok = ?kdKelompok ORDER BY a.kd_induk";
		$this->connector->AddParameter("?kdKelompok", $kdKelompok);
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new CoaGroup();
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
		$this->connector->CommandText = "SELECT a.* FROM m_lk_rekap_detail AS a WHERE a.id = ?id";
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
	public function FindByKdInduk($kdInduk) {
		$this->connector->CommandText = "SELECT a.* FROM m_lk_rekap_detail AS a WHERE a.kd_induk = ?kd_induk";
		$this->connector->AddParameter("?kd_induk", $kdInduk);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_lk_rekap_detail(kategori,kd_induk,kd_kelompok,psaldo,tk,mrekap,updateby_id,update_time) VALUES(?kategori,?kd_induk,?kd_kelompok,?psaldo,?tk,?mrekap,?updateby_id,now())';
		$this->connector->AddParameter("?kategori", $this->Kategori, "varchar");
        $this->connector->AddParameter("?kd_induk", $this->KdInduk,"varchar");
        $this->connector->AddParameter("?kd_kelompok", $this->KdKelompok, "varchar");
        $this->connector->AddParameter("?tk", $this->Tk);
        $this->connector->AddParameter("?psaldo", $this->PSaldo);
        $this->connector->AddParameter("?mrekap", $this->MRekap);
        $this->connector->AddParameter("?updateby_id", $this->UpdateById);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_lk_rekap_detail SET kategori = ?kategori, kd_induk = ?kd_induk, kd_kelompok = ?kd_kelompok, psaldo = ?psaldo, tk = ?tk, mrekap = ?mrekap, updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
        $this->connector->AddParameter("?kategori", $this->Kategori, "varchar");
        $this->connector->AddParameter("?kd_induk", $this->KdInduk,"varchar");
        $this->connector->AddParameter("?kd_kelompok", $this->KdKelompok, "varchar");
        $this->connector->AddParameter("?tk", $this->Tk);
        $this->connector->AddParameter("?psaldo", $this->PSaldo);
        $this->connector->AddParameter("?mrekap", $this->MRekap);
        $this->connector->AddParameter("?updateby_id", $this->UpdateById);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function CheckKategoriUsed($kd_induk){
        // periksa apakah akun sudah terpakai pada jurnal akuntansi
        $this->connector->CommandText = 'Select kd_induk From m_account Where kd_induk = ?kd_induk';
        $this->connector->AddParameter("?kd_induk", $kd_induk);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return 0;
        }else{
            return 1;
        }
    }

	public function Delete($id) {
		$this->connector->CommandText = 'Delete From m_lk_rekap_detail WHERE id = ?id';
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

}
