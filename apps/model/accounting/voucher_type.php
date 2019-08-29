<?php
class VoucherType extends EntityBase {
	public $Id;
	public $VoucherCd;
	public $VoucherDesc;
	public $VoucherTable;

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->VoucherCd = $row["voucher_cd"];
		$this->VoucherDesc = $row["voucher_desc"];
	}

	/**
	 * @param string $orderBy
	 * @return VoucherType[]
	 */
	public function LoadAll($orderBy = "a.voucher_cd") {
		$this->connector->CommandText = "SELECT a.* FROM sys_voucher_type AS a ORDER BY $orderBy";
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new VoucherType();
				$temp->FillProperties($row);

				$result[] = $temp;
			}
		}

		return $result;
	}

	/**
	 * @param int $id
	 * @return VoucherType
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM sys_voucher_type AS a WHERE a.id = ?id";
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
	 * @return VoucherType
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $docTypeId
	 * @return VoucherType
	 */
	public function LoadByDocumentType($docTypeId) {
		$this->connector->CommandText = "SELECT a.* FROM sys_voucher_type AS a WHERE a.id = (SELECT aa.vouchertype_id FROM sys_doctype AS aa WHERE aa.id = ?docType)";
		$this->connector->AddParameter("?docType", $docTypeId);
		$rs = $this->connector->ExecuteQuery();

		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}

		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

    public function LoadByDocumentCode($docCode) {
        $this->connector->CommandText = "SELECT a.* FROM sys_voucher_type AS a WHERE a.voucher_cd = ?docCode";
        $this->connector->AddParameter("?docCode", $docCode);
        $rs = $this->connector->ExecuteQuery();

        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }

        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO sys_voucher_type(voucher_cd,voucher_desc) VALUES(?voucher_cd,?voucher_desc)';
		$this->connector->AddParameter("?voucher_cd", $this->VoucherCd);
		$this->connector->AddParameter("?voucher_desc", $this->VoucherDesc);

		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText = 'UPDATE sys_voucher_type SET voucher_cd = ?voucher_cd, voucher_desc = ?voucher_desc WHERE id = ?id';
		$this->connector->AddParameter("?voucher_cd", $this->VoucherCd);
		$this->connector->AddParameter("?voucher_desc", $this->VoucherDesc);
		$this->connector->AddParameter("?id", $id);

		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$this->connector->CommandText = 'DELETE FROM sys_voucher_type WHERE id = ?id';
		$this->connector->AddParameter("?id", $id);

		return $this->connector->ExecuteNonQuery();
	}

}
