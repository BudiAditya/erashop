<?php
class Cabang extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
    public $AreaId;
    public $AreaName;
	public $Kode;
	public $Cabang;
    public $Alamat;
    public $Pic;
    public $FLogo;
    public $NamaCabang;
	public $CompanyName;
	public $RawPrintMode;
	public $RawPrinterName;
	public $CreatebyId;
	public $UpdatebyId;
	public $CabType;
	public $AllowMinus = 0;

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
		$this->Kode = $row["kode"];
		$this->Cabang = $row["cabang"];
        $this->Alamat = $row["alamat"];
        $this->Pic = $row["pic"];
        $this->AreaId = $row["area_id"];
        $this->AreaName = $row["area_name"];
        $this->FLogo = $row["flogo"];
        $this->NamaCabang = $row["nama_outlet"];
		$this->CompanyName = $row["company_name"];
		$this->RawPrintMode = $row["raw_print_mode"];
		$this->RawPrinterName = $row["raw_printer_name"];
		$this->CreatebyId = $row["createby_id"];
		$this->UpdatebyId = $row["updateby_id"];
		$this->CabType = $row["cab_type"];
		$this->AllowMinus = $row["allow_minus"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Cabang[]
	 */
	public function LoadAll($orderBy = "a.kode", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd, c.id as area_id, c.area_name,b.company_name
FROM m_cabang AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN m_area As c On a.area_id = c.id
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd, c.id as area_id, c.area_name,b.company_name
FROM m_cabang AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN m_area As c On a.area_id = c.id
WHERE a.is_deleted = 0
ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Cabang();
				$temp->FillProperties($row);

				$result[] = $temp;
			}
		}
		return $result;
	}

	public function LoadByType($entityId = 0, $cabType = 0, $operator = "=",$orderBy = "a.kode") {
		$sql = "SELECT a.*, b.entity_cd, c.id as area_id, c.area_name,b.company_name FROM m_cabang AS a";
		$sql.= " JOIN sys_company AS b ON a.entity_id = b.entity_id JOIN m_area As c On a.area_id = c.id";
		if ($entityId > 0){
			$sql.= " WHERE a.is_deleted = 0 And a.cab_type $operator $cabType And a.entity_id = $entityId ORDER BY $orderBy";
		}else{
			$sql.= " WHERE a.is_deleted = 0 And a.cab_type $operator $cabType ORDER BY $orderBy";
		}
		$this->connector->CommandText = $sql;
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Cabang();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return Cabang
	 */
	public function FindById($id) {
		$this->connector->CommandText =
"SELECT a.*, b.entity_cd, c.id as area_id, c.area_name,b.company_name
FROM m_cabang AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN m_area As c On a.area_id = c.id
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
	 * @return Cabang
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Cabang[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.kode", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd, c.id as area_id, c.area_name,b.company_name
FROM m_cabang AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN m_area As c On a.area_id = c.id
WHERE a.entity_id = ?eti
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
"SELECT a.*, b.entity_cd, c.id as area_id, c.area_name,b.company_name
FROM m_cabang AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN m_area As c On a.area_id = c.id
WHERE a.is_deleted = 0 AND a.entity_id = ?eti
ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Cabang();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function LoadByEntityId1($eti) {
		$this->connector->CommandText = "SELECT a.*, b.entity_cd, c.id as area_id, c.area_name,b.company_name FROM m_cabang AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id JOIN m_area As c On a.area_id = c.id WHERE a.entity_id = ?eti Order By a.id Limit 1";
		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}

		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function LoadByAreaId($ari, $orderBy = "a.kode", $includeDeleted = false) {
        if ($includeDeleted) {
            $this->connector->CommandText =
                "SELECT a.*, b.entity_cd, c.id as area_id, c.area_name,b.company_name
FROM m_cabang AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN m_area As c On a.area_id = c.id
WHERE a.area_id = ?ari
ORDER BY $orderBy";
        } else {
            $this->connector->CommandText =
                "SELECT a.*, b.entity_cd, c.id as area_id, c.area_name,b.company_name
FROM m_cabang AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN m_area As c On a.area_id = c.id
WHERE a.is_deleted = 0 AND a.area_id = ?ari
ORDER BY $orderBy";
        }

        $this->connector->AddParameter("?ari", $ari);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Cabang();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
		$this->connector->CommandText =
        'INSERT INTO m_cabang(allow_minus,cab_type,entity_id,area_id,kode,cabang,alamat,pic,flogo,nama_outlet,raw_print_mode,raw_printer_name,createby_id,create_time) VALUES(?allow_minus,?cab_type,?entity_id,?area_id,?kode,?cabang,?alamat,?pic,?flogo,?nama_outlet,?raw_print_mode,?raw_printer_name,?createby_id,now())';
		$this->connector->AddParameter("?allow_minus", $this->AllowMinus);
		$this->connector->AddParameter("?cab_type", $this->CabType);
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?area_id", $this->AreaId);
        $this->connector->AddParameter("?kode", $this->Kode);
        $this->connector->AddParameter("?cabang", $this->Cabang);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?pic", $this->Pic);
        $this->connector->AddParameter("?flogo", $this->FLogo);
        $this->connector->AddParameter("?nama_outlet", $this->NamaCabang);
		$this->connector->AddParameter("?raw_print_mode", $this->RawPrintMode);
		$this->connector->AddParameter("?raw_printer_name", $this->RawPrinterName);
		$this->connector->AddParameter("?createby_id", $this->CreatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
        if ($this->FLogo == null){
            $sql = 'UPDATE m_cabang SET allow_minus = ?allow_minus, cab_type = ?cab_type, entity_id = ?entity_id, area_id = ?area_id,	kode = ?kode, cabang = ?cabang,	alamat = ?alamat, pic = ?pic, nama_outlet = ?nama_outlet, raw_print_mode = ?raw_print_mode, raw_printer_name = ?raw_printer_name, updateby_id = ?updateby_id WHERE id = ?id';
        }else{
            $sql = 'UPDATE m_cabang SET allow_minus = ?allow_minus, cab_type = ?cab_type, entity_id = ?entity_id,	area_id = ?area_id,	kode = ?kode, cabang = ?cabang,	alamat = ?alamat, pic = ?pic, flogo = ?flogo, nama_outlet = ?nama_outlet, raw_print_mode = ?raw_print_mode, raw_printer_name = ?raw_printer_name, updateby_id = ?updateby_id WHERE id = ?id';
        }
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?allow_minus", $this->AllowMinus);
		$this->connector->AddParameter("?cab_type", $this->CabType);
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?kode", $this->Kode);
        $this->connector->AddParameter("?area_id", $this->AreaId);
        $this->connector->AddParameter("?cabang", $this->Cabang);
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?pic", $this->Pic);
		$this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?flogo", $this->FLogo);
        $this->connector->AddParameter("?nama_outlet", $this->NamaCabang);
		$this->connector->AddParameter("?raw_print_mode", $this->RawPrintMode);
		$this->connector->AddParameter("?raw_printer_name", $this->RawPrinterName);
		$this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE m_cabang SET is_deleted = 1 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function GetJSonCabangs($entityId = 0) {
		$sql = "SELECT a.id,a.kode,a.cabang,b.entity_cd,a.cab_type FROM m_cabang as a Join sys_company as b On a.entity_id = b.entity_id";
		if ($entityId > 0) {
			$sql.= " Where a.entity_id = " . $entityId;
		}
		$this->connector->CommandText = $sql;
		$data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
		$sql.= " Order By a.kode";
		$this->connector->CommandText = $sql;
		$rows = array();
		$rs = $this->connector->ExecuteQuery();
		while ($row = $rs->FetchAssoc()){
			$rows[] = $row;
		}
		$result = array('total'=>$data['count'],'rows'=>$rows);
		return $result;
	}

	public function GetComboJSonCabangs($entityId = 0) {
		$sql = "SELECT a.id,a.kode,a.cabang,b.entity_cd,a.cab_type FROM m_cabang as a Join sys_company as b On a.entity_id = b.entity_id";
		if ($entityId > 0) {
			$sql.= " Where a.entity_id = " . $entityId;
		}
		$this->connector->CommandText = $sql;
		$sql.= " Order By a.kode";
		$this->connector->CommandText = $sql;
		$rows = array();
		$rs = $this->connector->ExecuteQuery();
		while ($row = $rs->FetchAssoc()){
			$rows[] = $row;
		}
		$result = $rows;
		return $result;
	}
}
