<?php
class CoaDetail extends EntityBase {
	public $Id;
	public $IsDeleted = false;
	public $EntityId;
	public $CabangId;
	public $Kode;
	public $KdInduk;
    public $Perkiraan;
    public $CreateTime;
    public $CreateById;
	public $UpdateTime;
	public $UpdateById;
    public $PosisiSaldo;
	public $XMode;


	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->Kode = $row["kode"];
		$this->KdInduk = $row["kd_induk"];
        $this->Perkiraan = $row["perkiraan"];
		$this->CreateTime = $row["create_time"];
		$this->CreateById = $row["createby_id"];
        $this->UpdateTime = $row["update_time"];
        $this->UpdateById = $row["updateby_id"];
        $this->PosisiSaldo = $row["psaldo"];
		$this->XMode = $row["xmode"];
	}

    public function IsOpeningBalanceRequired() {
        if ($this->Kode == null) {
            throw new Exception("MissingPropertyException ! AccNo to be filled !");
        }

        // Untuk semua acc yang kepala 1xx, 2xx, 3xx WAJIB ADA
        return $this->Kode[0] < 4;
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Coa[]
	 */
	public function LoadAll($entityId = 0, $cabangId = 0, $orderBy = "a.kode") {
		$sql = "SELECT a.*, b.psaldo FROM m_account AS a JOIN m_lk_rekap_detail AS b ON a.kd_induk = b.kd_induk WHERE a.is_deleted = 0";
		if ($entityId > 0){
			$sql.= " And a.entity_id = ".$entityId;
		}
		if ($cabangId > 0){
			$sql.= " And (a.cabang_id = ".$cabangId." or a.xmode = 0)";
		}
		$sql.= " ORDER BY $orderBy";
		$this->connector->CommandText = $sql;
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new CoaDetail();
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
	public function LoadByKdInduk($entityId = 0, $cabangId = 0, $kdInduk) {
		$operator = is_array($kdInduk) ? "IN" : "=";
		$sql = "SELECT a.*, b.psaldo FROM m_account AS a JOIN m_lk_rekap_detail AS b ON a.kd_induk = b.kd_induk WHERE a.is_deleted = 0 AND a.kd_induk $operator ?kdInduk";
		if ($entityId > 0){
			$sql.= " And a.entity_id = ".$entityId;
		}
		if ($cabangId > 0){
			$sql.= " And (a.cabang_id = ".$cabangId." or a.xmode = 0)";
		}
		$sql.= " ORDER BY a.kode";
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?kdInduk", $kdInduk);
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new CoaDetail();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}
    public function LoadCashBookAccount($entityId = 0, $cabangId = 0) {
		$sql = "SELECT a.*, b.psaldo FROM m_account AS a JOIN m_lk_rekap_detail AS b ON a.kd_induk = b.kd_induk WHERE left(a.kode,3) IN ('110','111')";
		if ($entityId > 0){
			$sql.= " And a.entity_id = ".$entityId;
		}
		if ($cabangId > 0){
			$sql.= " And (a.cabang_id = ".$cabangId." or a.xmode = 0)";
		}
		$sql.= " ORDER BY a.kode";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new CoaDetail();
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
		$this->connector->CommandText = "SELECT a.*, b.psaldo FROM m_account AS a JOIN m_lk_rekap_detail AS b ON a.kd_induk = b.kd_induk WHERE a.id = ?id";
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
	public function FindByKode($entityId,$kode) {
		$sql = "SELECT a.*, b.psaldo FROM m_account AS a JOIN m_lk_rekap_detail AS b ON a.kd_induk = b.kd_induk WHERE a.kode = ?kode And a.entity_id = ?entity_id";
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?entity_id", $entityId);
		$this->connector->AddParameter("?kode", $kode);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_account(entity_id,cabang_id,kode,kd_induk,perkiraan,xmode,createby_id,create_time) VALUES(?entity_id,?cabang_id,?kode,?kd_induk,?perkiraan,?xmode,?createby_id,NOW())';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?kode", $this->Kode, "varchar");
        $this->connector->AddParameter("?kd_induk", $this->KdInduk,"varchar");
        $this->connector->AddParameter("?perkiraan", $this->Perkiraan, "varchar");
        $this->connector->AddParameter("?xmode", $this->XMode);
		$this->connector->AddParameter("?createby_id", $this->CreateById);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_account SET entity_id = ?entity_id, cabang_id = ?cabang_id, kode = ?kode, kd_induk = ?kd_induk, perkiraan = ?perkiraan, xmode = ?xmode, update_time = NOW(), updateby_id = ?updateby_id WHERE id = ?id';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?kode", $this->Kode, "varchar");
        $this->connector->AddParameter("?kd_induk", $this->KdInduk,"varchar");
        $this->connector->AddParameter("?perkiraan", $this->Perkiraan, "varchar");
		$this->connector->AddParameter("?xmode", $this->XMode);
        $this->connector->AddParameter("?updateby_id", $this->UpdateById);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function CheckAkunUsed($id){
        // periksa apakah akun sudah terpakai pada jurnal akuntansi
        $this->connector->CommandText = 'Select acdebet_id From t_gl_voucher_detail Where acdebet_id = ?id';
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            $this->connector->CommandText = 'Select ackredit_id From t_gl_voucher_detail Where ackredit_id = ?id';
            $this->connector->AddParameter("?id", $id);
            $rs = $this->connector->ExecuteQuery();
            if ($rs == null || $rs->GetNumRows() == 0) {
                return 0;
            }else{
                return 1;
            }
        }else{
            return 1;
        }
    }

	public function Delete($id) {
		$this->connector->CommandText = 'Delete From m_account WHERE id = ?id';
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

}
