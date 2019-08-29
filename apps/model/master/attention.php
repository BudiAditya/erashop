<?php
class Attention extends EntityBase {
	public $Id;
	public $IsAktif;
    public $AttJenis;
	public $AttFrom;
    public $AttHeader;
    public $AttContent;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->FindById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsAktif = $row["is_aktif"];
		$this->AttJenis = $row["att_jenis"];
		$this->AttFrom = $row["att_from"];
        $this->AttHeader = $row["att_header"];
        $this->AttContent = $row["att_content"];
	}

	public function LoadAll($orderBy = "a.id", $includeNonAktif = false) {
		if ($includeNonAktif) {
			$this->connector->CommandText = "SELECT a.* FROM sys_attention AS a ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.* FROM sys_attention AS a WHERE a.is_aktif = 1 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Attention();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Attention
	 */
	public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM sys_attention AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	public function LoadById($id) {
		return $this->FindById($id);
	}

	public function LoadByJenis($jns, $orderBy = "a.id", $includeNonAktif = false) {
		if ($includeNonAktif) {
			$this->connector->CommandText = "SELECT a.* FROM sys_attention AS a WHERE a.att_jenis = ?jns ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.* FROM sys_attention AS a WHERE a.is_aktif = 1 AND a.att_jenis = ?jns ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?jns", $jns);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Attention();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function Insert() {
		$this->connector->CommandText =
        'INSERT INTO sys_attention(att_jenis,att_header,att_from,is_aktif,att_content) VALUES(?att_jenis,?att_header,?att_from,?is_aktif,?att_content)';
		$this->connector->AddParameter("?att_jenis", $this->AttJenis);
        $this->connector->AddParameter("?att_header", $this->AttHeader);
        $this->connector->AddParameter("?att_from", $this->AttFrom);
        $this->connector->AddParameter("?is_aktif", $this->IsAktif);
        $this->connector->AddParameter("?att_content", $this->AttContent);		
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE sys_attention SET att_jenis = ?att_jenis,att_header = ?att_header,att_from = ?att_from,is_aktif = ?is_aktif,att_content = ?att_content WHERE id = ?id';
		$this->connector->AddParameter("?att_jenis", $this->AttJenis);
        $this->connector->AddParameter("?att_from", $this->AttFrom);
        $this->connector->AddParameter("?att_header", $this->AttHeader);
        $this->connector->AddParameter("?is_aktif", $this->IsAktif);
        $this->connector->AddParameter("?att_content", $this->AttContent);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "Delete From sys_attention WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}
