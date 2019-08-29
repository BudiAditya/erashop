<?php
class Karyawan extends EntityBase {
	public $Id;
	public $IsDeleted = false;
    public $EntityId;
	public $EntityCd;
	public $Nik;
    public $NmPanggilan;
	public $Nama;
    public $DeptId;
    public $DeptCd;
    public $Jabatan;
    public $MulaiKerja;
    public $Agama;
    public $Status;
    public $Jkelamin;
    public $T4Lahir;
    public $TglLahir;
    public $Alamat;
    public $Pendidikan;
    public $FpNo;
    public $TlpRumah;
    public $Handphone;
    public $Npwp;
    public $BpjsNo;
    public $BpjsDate;
    public $ResignDate;
    public $IsAktif;
    public $Fphoto;


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
		$this->Nik = $row["nik"];
        $this->NmPanggilan = $row["nm_panggilan"];
		$this->Nama = $row["nama"];
        $this->DeptId = $row["dept_id"];
        $this->DeptCd = $row["dept_cd"];
        $this->Jabatan = $row["jabatan"];
        $this->MulaiKerja = strtotime($row["mulai_kerja"]);
        $this->Agama = $row["agama"];
        $this->Status = $row["status"];
        $this->Jkelamin = $row["jkelamin"];
        $this->T4Lahir = $row["t4_lahir"];
        $this->TglLahir = strtotime($row["tgl_lahir"]);
        $this->Alamat = $row["alamat"];
        $this->Pendidikan = $row["pendidikan"];
        $this->FpNo = $row["fp_no"];
        $this->TlpRumah = $row["tlp_rumah"];
        $this->Handphone = $row["handphone"];
        $this->Npwp = $row["npwp"];
        $this->BpjsNo = $row["bpjs_no"];
        $this->BpjsDate = strtotime($row["bpjs_date"]);
        $this->ResignDate = strtotime($row["resign_date"]);
        $this->IsAktif = $row["is_aktif"];
        $this->Fphoto = $row["fphoto"];
	}

    public function FormatMulaiKerja($format = HUMAN_DATE) {
        return is_int($this->MulaiKerja) ? date($format, $this->MulaiKerja) : null;
    }

    public function FormatTglLahir($format = HUMAN_DATE) {
        return is_int($this->TglLahir) ? date($format, $this->TglLahir) : null;
    }

    public function FormatBpjsDate($format = HUMAN_DATE) {
        return is_int($this->BpjsDate) ? date($format, $this->BpjsDate) : null;
    }

    public function FormatResignDate($format = HUMAN_DATE) {
        return is_int($this->ResignDate) ? date($format, $this->ResignDate) : null;
    }

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Nama[]
	 */
	public function LoadAll($orderBy = "a.nama", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
            "SELECT a.*, b.entity_cd, c.dept_cd
            FROM m_karyawan AS a
                JOIN sys_company AS b ON a.entity_id = b.entity_id JOIN sys_dept As c On a.dept_id = c.id
            ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
            "SELECT a.*, b.entity_cd, c.dept_cd
            FROM m_karyawan AS a
                JOIN sys_company AS b ON a.entity_id = b.entity_id JOIN sys_dept As c On a.dept_id = c.id
            WHERE a.is_deleted = 0
            ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Karyawan();
				$temp->FillProperties($row);

				$result[] = $temp;
			}
		}

		return $result;
	}

	/**
	 * @param int $id
	 * @return Nama
	 */
	public function FindById($id) {
		$this->connector->CommandText =
        "SELECT a.*, b.entity_cd, c.dept_cd
        FROM m_karyawan AS a
            JOIN sys_company AS b ON a.entity_id = b.entity_id JOIN sys_dept As c On a.dept_id = c.id
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
	 * @return Nama
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $eti
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Nama[]
	 */
	public function LoadByEntityId($eti, $orderBy = "a.nik", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
            "SELECT a.*, b.entity_cd, c.dept_cd
            FROM m_karyawan AS a
                JOIN sys_company AS b ON a.entity_id = b.entity_id JOIN sys_dept As c On a.dept_id = c.id
            WHERE a.entity_id = ?eti
            ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
            "SELECT a.*, b.entity_cd, c.dept_cd
            FROM m_karyawan AS a
                JOIN sys_company AS b ON a.entity_id = b.entity_id JOIN sys_dept As c On a.dept_id = c.id
            WHERE a.is_deleted = 0 AND a.entity_id = ?eti
            ORDER BY $orderBy";
		}

		$this->connector->AddParameter("?eti", $eti);
		$rs = $this->connector->ExecuteQuery();
        $result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Karyawan();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function Insert() {
		$this->connector->CommandText =
        'INSERT INTO m_karyawan(fphoto,entity_id,nik,nm_panggilan,nama,dept_id,jabatan,mulai_kerja,agama,status,jkelamin,t4_lahir,tgl_lahir,alamat,pendidikan,fp_no,tlp_rumah,handphone,npwp,bpjs_no,bpjs_date,resign_date,is_aktif)
        VALUES(?fphoto,?entity_id,?nik,?nm_panggilan,?nama,?dept_id,?jabatan,?mulai_kerja,?agama,?status,?jkelamin,?t4_lahir,?tgl_lahir,?alamat,?pendidikan,?fp_no,?tlp_rumah,?handphone,?npwp,?bpjs_no,?bpjs_date,?resign_date,?is_aktif)';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?nik", $this->Nik);
        $this->connector->AddParameter("?nm_panggilan", $this->NmPanggilan);
        $this->connector->AddParameter("?nama", $this->Nama);
        $this->connector->AddParameter("?dept_id", $this->DeptId);
        $this->connector->AddParameter("?jabatan", $this->Jabatan);
        $this->connector->AddParameter("?mulai_kerja", $this->FormatMulaiKerja(SQL_DATETIME));
        $this->connector->AddParameter("?agama", $this->Agama);
        $this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?jkelamin", $this->Jkelamin);
        $this->connector->AddParameter("?t4_lahir", $this->T4Lahir);
        $this->connector->AddParameter("?tgl_lahir", $this->FormatTglLahir(SQL_DATETIME));
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?pendidikan", $this->Pendidikan);
        $this->connector->AddParameter("?fp_no", $this->FpNo,"char");
        $this->connector->AddParameter("?tlp_rumah", $this->TlpRumah,"char");
        $this->connector->AddParameter("?handphone", $this->Handphone,"char");
        $this->connector->AddParameter("?npwp", $this->Npwp,"char");
        $this->connector->AddParameter("?bpjs_no", $this->BpjsNo,"char");
        $this->connector->AddParameter("?bpjs_date", $this->FormatBpjsDate(SQL_DATETIME));
        $this->connector->AddParameter("?resign_date", $this->FormatResignDate(SQL_DATETIME));
        $this->connector->AddParameter("?is_aktif", $this->IsAktif);
        $this->connector->AddParameter("?fphoto", $this->Fphoto);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText =
        'UPDATE m_karyawan SET
            entity_id = ?entity_id,
            nik = ?nik,
            nm_panggilan = ?nm_panggilan,
            nama = ?nama,
            dept_id = ?dept_id,
            jabatan = ?jabatan,
            mulai_kerja = ?mulai_kerja,
            agama = ?agama,
            status = ?status,
            jkelamin = ?jkelamin,
            t4_lahir = ?t4_lahir,
            tgl_lahir = ?tgl_lahir,
            alamat = ?alamat,
            pendidikan = ?pendidikan,
            fp_no = ?fp_no,
            tlp_rumah = ?tlp_rumah,
            handphone = ?handphone,
            npwp = ?npwp,
            bpjs_no = ?bpjs_no,
            bpjs_date = ?bpjs_date,
            resign_date = ?resign_date,
            is_aktif = ?is_aktif,
            fphoto = ?fphoto
        WHERE id = ?id';
        $this->connector->AddParameter("?entity_id", $this->EntityId);
        $this->connector->AddParameter("?nik", $this->Nik);
        $this->connector->AddParameter("?nm_panggilan", $this->NmPanggilan);
        $this->connector->AddParameter("?nama", $this->Nama);
        $this->connector->AddParameter("?dept_id", $this->DeptId);
        $this->connector->AddParameter("?jabatan", $this->Jabatan);
        $this->connector->AddParameter("?mulai_kerja", $this->FormatMulaiKerja(SQL_DATETIME));
        $this->connector->AddParameter("?agama", $this->Agama);
        $this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?jkelamin", $this->Jkelamin);
        $this->connector->AddParameter("?t4_lahir", $this->T4Lahir);
        $this->connector->AddParameter("?tgl_lahir", $this->FormatTglLahir(SQL_DATETIME));
        $this->connector->AddParameter("?alamat", $this->Alamat);
        $this->connector->AddParameter("?pendidikan", $this->Pendidikan);
        $this->connector->AddParameter("?fp_no", $this->FpNo,"char");
        $this->connector->AddParameter("?tlp_rumah", $this->TlpRumah,"char");
        $this->connector->AddParameter("?handphone", $this->Handphone,"char");
        $this->connector->AddParameter("?npwp", $this->Npwp,"char");
        $this->connector->AddParameter("?bpjs_no", $this->BpjsNo,"char");
        $this->connector->AddParameter("?bpjs_date", $this->FormatBpjsDate(SQL_DATETIME));
        $this->connector->AddParameter("?resign_date", $this->FormatResignDate(SQL_DATETIME));
        $this->connector->AddParameter("?is_aktif", $this->IsAktif);
		$this->connector->AddParameter("?id", $id);
        $this->connector->AddParameter("?fphoto", $this->Fphoto);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "UPDATE m_karyawan SET is_deleted = 1 WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);

		return $this->connector->ExecuteNonQuery();
	}

    public function GetAutoNik($cabangId = 1) {
        // function untuk menggenerate nik karyawan
        $xnik = null;
        $cnik = null;
        $this->connector->CommandText = "SELECT max(a.nik) as maxNik FROM m_karyawan as a WHERE a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        if ($rs != null) {
            $row = $rs->FetchAssoc();
            $xnik = $row["maxNik"];
            $cnik = $xnik +1;
        } else {
            $cnik = ltrim($cabangId).'001';
        }
        return $cnik;
    }
}
