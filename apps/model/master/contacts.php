<?php
class Contacts extends EntityBase {
	public $Id;
	public $IsDeleted = false;
	public $CreatedById;
	public $CreatedDate;
	public $UpdatedById;
	public $UpdatedDate;
	public $EntityId;
	public $CabangId;
	public $ContactCode;
    public $ContactTypeId;
	public $ContactName;
	public $Address;
	public $City;
	public $PostCd;
	public $TelNo;
	public $HandPhone;
	public $FaxNo;
	public $Remark;
	public $Gender;
	public $Birthday;
	public $Nationality = "INA";
	public $MaritalStatus;
	public $Npwp;
	public $MailAddr;
	public $MailCity;
	public $MailPostCd;
	public $ContactPerson;
	public $Position;
	public $IdCard;
	public $CreditTerms;
	public $Reminder;
	public $Interest;
	public $EmailAdd;
	public $WebSite;
    public $AppName;
	public $Status;
    public $ContactLevel;
    public $PointSum;
    public $PointRedem;
    public $CreditLimit;
    public $CreditToDate;

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
		$this->Id = $row["id"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->EntityId = $row["entity_id"];
		$this->CreatedById = $row["createby_id"];
		$this->CreatedDate = strtotime($row["create_time"]);
		$this->UpdatedById = $row["updateby_id"];
		$this->UpdatedDate = strtotime($row["update_time"]);
		$this->CabangId = $row["cabang_id"];
		$this->ContactCode = $row["contact_code"];
		$this->ContactTypeId = $row["contacttype_id"];
		$this->ContactName = $row["contact_name"];
		$this->Address = $row["address"];
		$this->City = $row["city"];
		$this->PostCd = $row["post_cd"];
		$this->TelNo = $row["tel_no"];
		$this->HandPhone = $row["hand_phone"];
		$this->FaxNo = $row["fax_no"];
		$this->Remark = $row["remark"];
		$this->Gender = $row["gender"];
		$this->Birthday = $row["birth_date"];
		$this->Nationality = $row["nationality"];
		$this->MaritalStatus = $row["marital_status"];
		$this->Npwp = $row["npwp"];
		$this->MailAddr = $row["mail_addr"];
		$this->MailCity = $row["mail_city"];
		$this->MailPostCd = $row["mail_post_cd"];
		$this->ContactPerson = $row["contact_person"];
		$this->Position = $row["position"];
		$this->IdCard = $row["id_card"];
		$this->CreditTerms = $row["credit_terms"];
		$this->Reminder = $row["reminder"];
		$this->Interest = $row["interest"];
		$this->EmailAdd = $row["email_add"];
		$this->WebSite = $row["web_site"];
        $this->AppName = $row["app_name"];
		$this->Status = $row["status"];
        $this->ContactLevel = $row["contactlevel"];
        $this->PointSum = $row["pointsum"];
        $this->PointRedem = $row["pointredem"];
        $this->CreditLimit = $row["creditlimit"];
        $this->CreditToDate = $row["credittodate"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Contact[]
	 */
	public function LoadAll($orderBy = "a.contact_code", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
"SELECT a.*
FROM m_contacts AS a
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
"SELECT a.*
FROM m_contacts AS a
WHERE a.is_deleted = 0
ORDER BY $orderBy";
		}

		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Contacts();
				$temp->FillProperties($row);

				$result[] = $temp;
			}
		}

		return $result;
	}

	/**
	 * @param $companyId
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Contacts[]
	 */
	public function LoadByEntity($companyId, $orderBy = "a.contact_code", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
            "SELECT a.*
            FROM m_contacts AS a
            WHERE a.entity_id = ?Entity
            ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
            "SELECT a.*
            FROM m_contacts AS a
            WHERE a.is_deleted = 0 AND a.entity_id = ?Entity
            ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?Entity", $companyId);
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Contacts();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	public function LoadByCabang($cabangId, $orderBy = "a.contact_code", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
					"SELECT a.*
            FROM m_contacts AS a
            WHERE a.cabang_id = ?cabangId
            ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
					"SELECT a.*
            FROM m_contacts AS a
            WHERE a.is_deleted = 0 AND a.cabang_id = ?cabangId
            ORDER BY $orderBy";
		}
		$this->connector->AddParameter("?cabangId", $cabangId);
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new Contacts();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

    public function LoadByType($custType, $operator = "=", $orderBy = "a.contact_code", $includeDeleted = false) {
        if ($includeDeleted) {
            $this->connector->CommandText =
                "SELECT a.*
            FROM m_contacts AS a
            WHERE a.contacttype_id ".$operator." ?contacttype_id
            ORDER BY $orderBy";
        } else {
            $this->connector->CommandText =
                "SELECT a.*
            FROM m_contacts AS a
            WHERE a.is_deleted = 0 AND a.contacttype_id ".$operator." ?contacttype_id
            ORDER BY $orderBy";
        }
        $this->connector->AddParameter("?contacttype_id", $custType);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Contacts();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	/**
	 * @param int $id
	 * @return Contact
	 */
	public function LoadById($id) {
		return $this->FindById($id);
	}

	/**
	 * @param int $id
	 * @return Contact
	 */
	public function FindById($id) {
		$this->connector->CommandText = "SELECT a.* FROM m_contacts AS a WHERE a.id = ?id AND a.is_deleted = 0";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	public function FindBySupplierCode($cKode) {
		$this->connector->CommandText = "SELECT a.* FROM m_contacts AS a WHERE a.contacttype_id = 2 And a.contact_code = ?cKode AND a.is_deleted = 0";
		$this->connector->AddParameter("?cKode", $cKode);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	public function FindByCustomerCode($cKode) {
		$this->connector->CommandText = "SELECT a.* FROM m_contacts AS a WHERE a.contacttype_id = 1 And a.contact_code = ?cKode AND a.is_deleted = 0";
		$this->connector->AddParameter("?cKode", $cKode);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	public function Insert() {
		$this->connector->CommandText =
'INSERT INTO m_contacts(createby_id, create_time, entity_id,cabang_id,app_name,contacttype_id,contact_code,contact_name,address,city,post_cd,tel_no,hand_phone,fax_no,remark,gender,birth_date,nationality,marital_status,npwp,mail_addr,mail_city,mail_post_cd,contact_person,position,id_card,credit_terms,reminder,interest,email_add,web_site,status,contactlevel,creditlimit,credittodate,pointsum,pointredem)
VALUES (?user, NOW(), ?entity_id,?cabang_id,?app_name,?contacttype_id,?contact_code,?contact_name,?address,?city,?post_cd,?tel_no,?hand_phone,?fax_no,?remark,?gender,?birth_date,?nationality,?marital_status,?npwp,?mail_addr,?mail_city,?mail_post_cd,?contact_person,?position,?id_card,?credit_terms,?reminder,?interest,?email_add,?web_site,?status,?contactlevel,?creditlimit,?credittodate,?pointsum,?pointredem)';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?app_name", $this->AppName);
		$this->connector->AddParameter("?contacttype_id", $this->ContactTypeId);
		$this->connector->AddParameter("?contact_code", $this->ContactCode);
		$this->connector->AddParameter("?contact_name", $this->ContactName);
		$this->connector->AddParameter("?address", $this->Address);
		$this->connector->AddParameter("?city", $this->City);
		$this->connector->AddParameter("?post_cd", $this->PostCd);
		$this->connector->AddParameter("?tel_no", $this->TelNo,"char");
		$this->connector->AddParameter("?hand_phone", $this->HandPhone,"char");
		$this->connector->AddParameter("?fax_no", $this->FaxNo,"char");
		$this->connector->AddParameter("?remark", $this->Remark);
		$this->connector->AddParameter("?gender", $this->Gender);
		if ($this->Birthday == "") {
			$this->connector->AddParameter("?birth_date", null);
		} else {
			$this->connector->AddParameter("?birth_date", $this->Birthday);
		}
		$this->connector->AddParameter("?nationality", $this->Nationality);
		$this->connector->AddParameter("?marital_status", $this->MaritalStatus);
		$this->connector->AddParameter("?npwp", $this->Npwp);
		$this->connector->AddParameter("?mail_addr", $this->MailAddr);
		$this->connector->AddParameter("?mail_city", $this->MailCity);
		$this->connector->AddParameter("?mail_post_cd", $this->MailPostCd);
		$this->connector->AddParameter("?contact_person", $this->ContactPerson);
		$this->connector->AddParameter("?position", $this->Position);
		$this->connector->AddParameter("?id_card", $this->IdCard);
		$this->connector->AddParameter("?credit_terms", $this->CreditTerms == '' ? 0 : $this->CreditTerms);
		$this->connector->AddParameter("?reminder", $this->Reminder);
		$this->connector->AddParameter("?interest", $this->Interest);
		$this->connector->AddParameter("?email_add", $this->EmailAdd);
		$this->connector->AddParameter("?web_site", $this->WebSite);
		$this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?contactlevel", $this->ContactLevel);
        $this->connector->AddParameter("?creditlimit", $this->CreditLimit == '' ? 0 : $this->CreditLimit);
        $this->connector->AddParameter("?credittodate", $this->CreditToDate == '' ? 0 : $this->CreditToDate);
        $this->connector->AddParameter("?pointsum", $this->PointSum == '' ? 0 : $this->PointSum);
        $this->connector->AddParameter("?pointredem", $this->PointRedem == '' ? 0 : $this->PointRedem);
		$this->connector->AddParameter("?user", $this->CreatedById);

		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		$this->connector->CommandText =
'UPDATE m_contacts SET
	entity_id=?entity_id,
	cabang_id=?cabang_id,
	app_name=?app_name,
	contacttype_id=?contacttype_id,
	contact_code=?contact_code,
	contact_name=?contact_name,
	address=?address,
	city=?city,
	post_cd=?post_cd,
	tel_no=?tel_no,
	hand_phone=?hand_phone,
	fax_no=?fax_no,
	remark=?remark,
	gender=?gender,
	birth_date=?birth_date,
	nationality=?nationality,
	marital_status=?marital_status,
	npwp=?npwp,
	mail_addr=?mail_addr,
	mail_city=?mail_city,
	mail_post_cd=?mail_post_cd,
	contact_person=?contact_person,
	position=?position,
	id_card=?id_card,
	credit_terms=?credit_terms,
	reminder=?reminder,
	interest=?interest,
	email_add=?email_add,
	web_site=?web_site,
	status=?status,
	contactlevel=?contactlevel,
	creditlimit=?creditlimit,
	credittodate=?credittodate,
	pointsum=?pointsum,
	pointredem=?pointredem,
	updateby_id=?user,
	update_time=NOW()
WHERE id = ?id';
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?app_name", $this->AppName);
		$this->connector->AddParameter("?contacttype_id", $this->ContactTypeId);
		$this->connector->AddParameter("?contact_code", $this->ContactCode);
		$this->connector->AddParameter("?contact_name", $this->ContactName);
		$this->connector->AddParameter("?address", $this->Address);
		$this->connector->AddParameter("?city", $this->City);
		$this->connector->AddParameter("?post_cd", $this->PostCd);
		$this->connector->AddParameter("?tel_no", $this->TelNo,"char");
		$this->connector->AddParameter("?hand_phone", $this->HandPhone,"char");
		$this->connector->AddParameter("?fax_no", $this->FaxNo,"char");
		$this->connector->AddParameter("?remark", $this->Remark);
		$this->connector->AddParameter("?gender", $this->Gender);
		if ($this->Birthday == "") {
			$this->connector->AddParameter("?birth_date", null);
		} else {
			$this->connector->AddParameter("?birth_date", $this->Birthday);
		}
		$this->connector->AddParameter("?nationality", $this->Nationality);
		$this->connector->AddParameter("?marital_status", $this->MaritalStatus);
		$this->connector->AddParameter("?npwp", $this->Npwp);
		$this->connector->AddParameter("?mail_addr", $this->MailAddr);
		$this->connector->AddParameter("?mail_city", $this->MailCity);
		$this->connector->AddParameter("?mail_post_cd", $this->MailPostCd);
		$this->connector->AddParameter("?contact_person", $this->ContactPerson);
		$this->connector->AddParameter("?position", $this->Position);
		$this->connector->AddParameter("?id_card", $this->IdCard);
        $this->connector->AddParameter("?credit_terms", $this->CreditTerms == '' ? 0 : $this->CreditTerms);
        $this->connector->AddParameter("?reminder", $this->Reminder);
        $this->connector->AddParameter("?interest", $this->Interest);
        $this->connector->AddParameter("?email_add", $this->EmailAdd);
        $this->connector->AddParameter("?web_site", $this->WebSite);
        $this->connector->AddParameter("?status", $this->Status);
        $this->connector->AddParameter("?contactlevel", $this->ContactLevel);
        $this->connector->AddParameter("?creditlimit", $this->CreditLimit == '' ? 0 : $this->CreditLimit);
        $this->connector->AddParameter("?credittodate", $this->CreditToDate == '' ? 0 : $this->CreditToDate);
        $this->connector->AddParameter("?pointsum", $this->PointSum == '' ? 0 : $this->PointSum);
        $this->connector->AddParameter("?pointredem", $this->PointRedem == '' ? 0 : $this->PointRedem);
		$this->connector->AddParameter("?user", $this->UpdatedById);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		//$this->connector->CommandText = 'Delete From m_contacts WHERE id = ?id';
		$this->connector->CommandText = 'UPDATE m_contacts SET is_deleted = 1, updateby_id = ?user, update_time = NOW() WHERE id = ?id';
		$this->connector->AddParameter("?user", AclManager::GetInstance()->GetCurrentUser()->Id);
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

	public function GetAutoCode($ctype,$custname = null) {
		// function untuk menggenerate kode contact
		$xcode = null;
		$ckode = null;
		$custcd = null;
		$nol = "0000";
		if ($ctype == 1){
			$ckode = "C";
		}else{
			$ckode = "S";
		}
		$ins = $ckode.strtoupper(substr($custname, 0, 1)) . "-";
		$this->connector->CommandText = "SELECT contact_code FROM m_contacts WHERE LEFT(contact_code,3) = ?ins ORDER BY contact_code DESC LIMIT 1";
		$this->connector->AddParameter("?ins", $ins);
		$rs = $this->connector->ExecuteQuery();
		if ($rs != null) {
			$row = $rs->FetchAssoc();
			$custcd = $row["contact_code"];
			if ($custcd == "") {
				return $xcode = $ins . "0001";
			} else {
				$num = substr($custcd, 3, 4);
				if (is_numeric($num)) {
					$num = $num + 1;
					return $xcode = $ins . substr($nol, 0, 4 - strlen($num)) . $num;
				} else {
					$ins = $ckode.strtoupper(substr($custname, 0, 1)) . "-00";
					$this->connector->CommandText = "select contact_code from m_contacts Where left(contact_code,5) = ?ins Order By contact_code Desc limit 1";
					$this->connector->AddParameter("?ins", $ins);
					$rs = $this->connector->ExecuteQuery();
					if ($rs != null) {
						$row = $rs->FetchAssoc();
						$custcd = $row["contact_code"];
						$num = substr($custcd, 3, 4);
						if (is_numeric($num)) {
							$num = $num + 1;
							return $xcode = $ins . substr($nol, 0, 2 - strlen($num)) . $num;
						} else {
							return $ins . substr($nol, 0, 2) . "1";
						}
					} else {
						return $xcode = $ins . "0001";
					}
				}
			}
		} else {
			return $xcode;
		}
	}

    public function GetJSonContacts($ctype = 0,$entityId = 0, $filter = null,$sort = 'a.contact_code',$order = 'ASC') {
        $sql = "SELECT a.id,a.contact_code,a.contact_name,a.address,a.city,a.contactlevel,a.credit_terms,a.creditlimit FROM vw_m_contacts as a Where a.is_deleted = 0";
		if ($entityId > 0){
			$sql.= " and a.entity_id = $entityId";
		}
		if ($ctype > 0){
			$sql.= " and a.contacttype_id = $ctype";
		}
        if ($filter != null){
            $sql.= " And (a.contact_code Like '%$filter%' Or a.contact_name Like '%$filter%')";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= " Order By $sort $order";
        $this->connector->CommandText = $sql;
        $rows = array();
        $rs = $this->connector->ExecuteQuery();
        while ($row = $rs->FetchAssoc()){
            $rows[] = $row;
        }
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

	public function GetCreditToDate($Id,$Eti=0,$Cbi=0){
		$this->connector->CommandText = "Select fc_get_credit_todate(?id,?eti,?cbi) As valresult;";
		$this->connector->AddParameter("?id", $Id);
		$this->connector->AddParameter("?eti", $Eti);
		$this->connector->AddParameter("?cbi", $Cbi);
		$rs = $this->connector->ExecuteQuery();
		$row = $rs->FetchAssoc();
		return strval($row["valresult"]);
	}
}
