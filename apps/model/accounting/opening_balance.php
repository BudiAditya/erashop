<?php
require_once(MODEL . "master/coadetail.php");

class OpeningBalance extends EntityBase {
	public $Id;
	public $EntityId;
	public $CabangId;
	public $AccountNo;
	public $OpDate;
	public $DebitAmount = 0;
	public $CreditAmount = 0;
	public $CreatedById;
	public $CreatedDate;
	public $UpdatedById;
	public $UpdatedDate;
	/** @var  CoaDetail */
	private $coadetail;

	// Helper
	public $AccountName;
	public $AccountPosisiSaldo;

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->EntityId = $row["entity_id"];
		$this->CabangId = $row["cabang_id"];
		$this->AccountNo = $row["acc_no"];
		$this->OpDate = strtotime($row["op_date"]);
		$this->DebitAmount = $row["debet"];
		$this->CreditAmount = $row["kredit"];
		$this->CreatedById = $row["createby_id"];
		$this->CreatedDate = strtotime($row["create_time"]);
		$this->UpdatedById = $row["updateby_id"];
		$this->UpdatedDate = strtotime($row["update_time"]);
		$this->AccountName = $row["perkiraan"];
		$this->AccountPosisiSaldo = $row["psaldo"];
	}

	public function FormatDate($format = HUMAN_DATE) {
		return is_int($this->OpDate) ? date($format, $this->OpDate) : null;
	}

	/**
	 * @return CoaDetail
	 */
	public function GetCoa() {
		return $this->coadetail;
	}

	/**
	 * @param int $id
	 * @return OpeningBalance
	 */
	public function LoadById($id) {
		$this->connector->CommandText =
"SELECT a.*, b.kode, b.perkiraan, c.psaldo
FROM t_gl_saldoawal_account AS a
	JOIN m_account AS b ON a.acc_no = b.kode
	JOIN m_lk_rekap_detail AS c ON b.kd_induk = c.kd_induk
WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		$this->coadetail = new CoaDetail($this->AccountNo);
		return $this;
	}

	/**
	 * @param int $accNo
	 * @param int $year
	 * @return OpeningBalance
	 */
	public function LoadByAccount($entityId = 0, $cabangId = 0,$accNo, $year) {
		// Khusus load by account maka COA lsg diload apapun yang terjadi
		//$this->coadetail = new CoaDetail($accNo);
		$sql = "SELECT a.*, b.kode, b.perkiraan, c.psaldo FROM t_gl_saldoawal_account AS a";
		$sql.= " JOIN m_account AS b ON a.acc_no = b.kode JOIN m_lk_rekap_detail AS c ON b.kd_induk = c.kd_induk";
		$sql.= " WHERE a.acc_no = ?accNo AND year(a.op_date) = ?year";
		if ($entityId > 0){
			$sql.= " And a.entity_id = ".$entityId;
		}
		if ($cabangId > 0){
			$sql.= " And a.cabang_id = ".$cabangId;
		}
		$this->connector->CommandText =
		$this->connector->AddParameter("?accNo", $accNo);
		$this->connector->AddParameter("?year", $year);

		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}

		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

	/**
	 * Digunakan untuk mencari pergerakan saldo awal (akibat transaksi) dari akun yang sudah diload dari DBase.
	 * Method ini hanya bisa dipanggil jika sudah diload terlebih dahulu oleh method yang ada pada model ini.
	 * Untuk parameter $currentDate harus berupa int jika di invoke jika tidak akan default ke tanggal hari ini.
	 *
	 * NOTE: Ini akan mencari semua transaksi voucher yang sudah di approve. TIDAK MAKE SENSE MENCARI DATA BERDASARKAN VOUCHER TIDAK DI APPROVE
	 *
	 * @reference OpeningBalance::LoadById()
	 * @reference OpeningBalance::LoadByAccount()
	 *
	 * @param null|int $currentDate
	 * @param int $status Digunakan untuk filter status voucher. By default harus menggunakan voucher berstatus POSTED
	 * @param null|int $cabangId
	 * @throws Exception
	 * @return array("debet" => float, "kredit" => float, "transaksi" => float, "saldo" => float)
	 */
	public function CalculateTransaction($currentDate = null, $status = 2, $cabangId = null) {
		if ($this->coadetail == null || $this->coadetail->Id == null) {
			throw new Exception("Tidak dapat mencari transaksi ! Data Account tidak ada !");
		}

		// Digunakan untuk mencari tanggal awal. Jika ada data maka gunakan tanggal pada OpeningBalan7ce
		// Karena ada beberapa account yang tidak memiliki data OpeningBalance maka untuk tanggal awal akan di auto-detect ke 1 Januari
		if (is_int($this->OpDate)) {
			// Ok kalau masuk sini bearti ada data opening balance
			$temp = $this->OpDate;
		} else {
			// Kalau tidak ada opening balance coba lihat apakah ada data $currentDate / tidak. Jika ada $currentDate maka gunakan tahun $currentDate
			$temp = is_int($currentDate) ? $currentDate : mktime(0, 0, 0);
		}

		// Cari tanggal awal dan akhir periode transaksi yang akan dicari
		$start = mktime(0, 0, 0, 1, 1, date("Y", $temp));
		if (is_int($currentDate)) {
			// Force ke jam 23:59:59 berdasarkan tanggal yang dikirim
			$end = mktime(23, 59, 59, date("n", $currentDate), date("j", $currentDate), date("Y", $currentDate));
		} else {
			// Karena tidak ada tanggal yang dikirim asumsikan hari ini s.d. jam 23:59:59
			$end = mktime(23, 59, 59);
		}

		// Sedikit validasi...
		if ($end < $start) {
			// Tanggal yang diminta kurang dari tanggal Opening Balance...
			// Ini akan kejadian pada report awal bulan yang mana start dimulai dari 1 Januari maka parameter $currentDate akan dikirim 31 Des Bulan sebelumnya
			// Dapat dipastikan tidak ada transaksi dll
			return array(
				"debet" => 0,
				"kredit" => 0,
				"transaksi" => 0,
				"saldo" => $this->coadetail->PosisiSaldo == "DK" ? $this->DebitAmount - $this->CreditAmount : $this->CreditAmount - $this->DebitAmount
			);
		}
        $query = "SELECT SUM(CASE WHEN b.acdebet_id = ?accNo THEN b.jumlah ELSE 0 END) AS amount_debit, SUM(CASE WHEN b.ackredit_id = ?accNo THEN b.jumlah ELSE 0 END) AS amount_credit
        FROM t_gl_voucher_master AS a
            JOIN t_gl_voucher_detail AS b ON a.no_voucher = b.no_voucher
        WHERE a.doc_status = ?status AND a.is_deleted = 0 AND a.tgl_voucher BETWEEN ?start AND ?end
            AND (b.acdebet_id = ?accNo OR b.ackredit_id = ?accNo)";
        if($cabangId > 0){
            $query.= " AND (b.cabang_id = ?cabangId)";
        }
		$this->connector->CommandText = $query;
		$this->connector->AddParameter("?start", date(SQL_DATETIME, $start));
		$this->connector->AddParameter("?end", date(SQL_DATETIME, $end));
		$this->connector->AddParameter("?accNo", $this->coadetail->Id);
        $this->connector->AddParameter("?cabangId", $cabangId);
		if ($status == -1) {
			$this->connector->AddParameter("?status", "a.doc_status", "int");	// Gw mau paksa agar querynya menjadi a.doc_status = a.doc_status (selalu true) bukan a.doc_status = 'a.doc_status'
		} else {
			if ($status > 0 && $status < 5) {
				$this->connector->AddParameter("?status", $status);
			} else {
				$this->connector->AddParameter("?status", 4);
			}
		}

		$rs = $this->connector->ExecuteQuery();
		if ($rs == null) {
			throw new Exception("DBase error: " . $this->connector->GetErrorMessage());
		}

		// Berhubung si FetchAssoc pasti return 1 baris walau hasil sum tidak ada maka...
		$row = $rs->FetchAssoc();
		$row["amount_debit"] = $row["amount_debit"] == null ? 0 : $row["amount_debit"];
		$row["amount_credit"] = $row["amount_credit"] == null ? 0 : $row["amount_credit"];

		// Return result set
		$result = array();
		$result["debet"] = $row["amount_debit"];
		$result["kredit"] = $row["amount_credit"];
		$result["transaksi"] = $this->coadetail->PosisiSaldo == "DK" ? $row["amount_debit"] - $row["amount_credit"] : $row["amount_credit"] - $row["amount_debit"];
		$result["saldo"] = $this->coadetail->PosisiSaldo == "DK" ? $this->DebitAmount - $this->CreditAmount + $result["transaksi"] : $this->CreditAmount - $this->DebitAmount + $result["transaksi"];

		return $result;
	}

	public function Insert() {
		$this->connector->CommandText =
"INSERT INTO t_gl_saldoawal_account(entity_id,cabang_id,acc_no, op_date, debet, kredit, createby_id, create_time)
VALUES(?entity_id,?cabang_id,?acc, ?date, ?debit, ?credit, ?user, NOW())";
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?acc", $this->AccountNo);
		$this->connector->AddParameter("?date", $this->FormatDate(SQL_DATETIME));
		$this->connector->AddParameter("?debit", $this->DebitAmount);
		$this->connector->AddParameter("?credit", $this->CreditAmount);
		$this->connector->AddParameter("?user", $this->CreatedById);

		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_gl_saldoawal_account SET
	acc_no = ?acc
	, op_date = ?date
	, debet = ?debit
	, kredit = ?credit
	, updateby_id = ?user
	, update_time = NOW()
	, entity_id = ?entity_id
	, cabang_id = ?cabang_id
WHERE id = ?id";

		$this->connector->AddParameter("?acc", $this->AccountNo);
		$this->connector->AddParameter("?date", $this->FormatDate(SQL_DATETIME));
		$this->connector->AddParameter("?debit", $this->DebitAmount);
		$this->connector->AddParameter("?credit", $this->CreditAmount);
		$this->connector->AddParameter("?user", $this->UpdatedById);
		$this->connector->AddParameter("?id", $id);
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = "DELETE FROM t_gl_saldoawal_account WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}
}


// End of File: opening_balance.php
