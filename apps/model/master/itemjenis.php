<?php
class ItemJenis extends EntityBase {
	public $Id;
	public $JnsBarang;
	public $Keterangan;
	public $IvtAccNo;
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
		$this->JnsBarang = $row["jenis"];
		$this->Keterangan = $row["keterangan"];
		$this->IvtAccNo = $row["ivt_acc_no"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($orderBy = "a.jenis") {
		$this->connector->CommandText = "SELECT a.* FROM m_barang_jenis AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ItemJenis();
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
		$this->connector->CommandText = "SELECT a.* FROM m_barang_jenis AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	public function FindByJenis($iJenis) {
		$this->connector->CommandText = "SELECT a.* FROM m_barang_jenis AS a WHERE a.jenis = ?iJenis";
		$this->connector->AddParameter("?iJenis", $iJenis);
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
		$this->connector->CommandText = 'INSERT INTO m_barang_jenis(ivt_acc_no,jenis,keterangan,createby_id,create_time) VALUES(?ivt_acc_no,?jenis,?keterangan,?createby_id,now())';
		$this->connector->AddParameter("?jenis", $this->JnsBarang);
		$this->connector->AddParameter("?ivt_acc_no", $this->IvtAccNo);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE m_barang_jenis SET ivt_acc_no = ?ivt_acc_no, jenis = ?jenis, keterangan = ?keterangan, updateby_id = ?updateby_id, update_time = now() WHERE id = ?id';
		$this->connector->AddParameter("?jenis", $this->JnsBarang);
		$this->connector->AddParameter("?ivt_acc_no", $this->IvtAccNo);
        $this->connector->AddParameter("?keterangan", $this->Keterangan);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = 'Delete From m_barang_jenis WHERE id = ?id';
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

}
