<?php
class UserAdmin extends EntityBase {
	public $UserUid;
	public $IsAktif;
	public $UserId;
	public $EntityId;
	public $EntityCd;
    public $CompanyName;
	public $CabangId;
	public $CabangKode;
    public $CabangName;
	public $UserName;
	public $UserEmail;
	public $Status = 7;		// By Default Logged Out
	public $LoginTime;
	public $LoginFrom;
	public $UserLvl;
	public $ShortDesc;
	public $AllowMultipleLogin;
	public $UserPwd1;
	public $UserPwd2;
	public $SessionId;
	public $IsForceAccountingPeriod = false;
    public $SysStartDate;
    public $AreaId;
    public $EmployeeId;
    public $EmpDepId;
	public $ACabangId;

	// Helper Variable
	public function FillProperties(array $row) {
		$this->UserUid = $row["user_uid"];
		$this->IsAktif = $row["is_aktif"];
		$this->UserId = $row["user_id"];
		$this->EntityId = $row["entity_id"];
		$this->EntityCd = $row["entity_cd"];
        $this->EmployeeId = $row["employee_id"];
        $this->EmpDepId = $row["dept_id"];
        $this->CompanyName = $row["company_name"];
		$this->CabangId = $row["cabang_id"];
		$this->CabangKode = $row["kd_cabang"];
        $this->CabangName = $row["nm_cabang"];
        if ($this->EmployeeId > 0){
            $this->UserName = $row["nama"];
        }else{
            $this->UserName = $row["user_name"];
        }
		$this->UserEmail = $row["user_email"];
		$this->Status = $row["status"];
		$this->LoginTime = $row["login_time"];
		$this->LoginFrom = $row["login_from"];
		$this->UserLvl = $row["user_lvl"];
		$this->ShortDesc = $row["short_desc"];
		$this->AllowMultipleLogin = $row["allow_multiple_login"];
		$this->UserPwd1 = $row["user_pwd"];
		$this->UserPwd2 = $row["user_pwd"];
        $this->SysStartDate = $row["start_date"];
        $this->AreaId = $row["area_id"];
		$this->IsForceAccountingPeriod = $row["force_accounting_period"] == 1;
		$this->ACabangId = $row["a_cabang_id"];
	}

	public function LoadAll($orderBy = "a.user_id", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText =
				"SELECT a.*, b.entity_cd, b.company_name, b.start_date, c.short_desc,d.kode as kd_cabang,d.cabang as nm_cabang,d.area_id,e.dept_id,e.nama
FROM sys_users AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN sys_status_code AS c ON a.status = c.code AND c.key = 'login_audit'
	JOIN m_cabang As d ON a.entity_id = d.entity_id And a.cabang_id = d.id
	LEFT JOIN m_karyawan e ON a.employee_id = e.id
ORDER BY $orderBy";
		} else {
			$this->connector->CommandText =
				"SELECT a.*, b.entity_cd, b.company_name,b.start_date, c.short_desc,d.kode as kd_cabang,d.cabang as nm_cabang,d.area_id,e.dept_id,e.nama
FROM sys_users AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN sys_status_code AS c ON a.status = c.code AND c.key = 'login_audit'
	JOIN m_cabang As d ON a.entity_id = d.entity_id And a.cabang_id = d.id
	LEFT JOIN m_karyawan e ON a.employee_id = e.id
WHERE a.is_aktif = 1
ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new UserAdmin();
				$temp->FillProperties($row);

				$result[] = $temp;
			}
		}

		return $result;
	}

	public function FindById($id) {
		$this->connector->CommandText =
			"SELECT a.*, b.entity_cd, b.company_name, b.start_date,c.short_desc,d.kode as kd_cabang,d.cabang as nm_cabang,d.area_id,e.dept_id,e.nama
FROM sys_users AS a
	JOIN sys_company AS b ON a.entity_id = b.entity_id
	JOIN sys_status_code AS c ON a.status = c.code AND c.key = 'login_audit'
	JOIN m_cabang As d ON a.entity_id = d.entity_id And a.cabang_id = d.id
	LEFT JOIN m_karyawan e ON a.employee_id = e.id
WHERE a.user_uid = ?id";
		$this->connector->AddParameter("?id", $id);
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
"INSERT INTO sys_users(is_aktif, user_id, entity_id, cabang_id, user_pwd, user_lvl, user_name, user_email, allow_multiple_login, force_accounting_period, employee_id,a_cabang_id)
VALUES(?is_aktif, ?user_id, ?entity_id, ?cabang_id, ?user_pwd, ?user_lvl, ?user_name, ?user_email, ?allow_multiple_login, ?force_accounting_period, ?employee_id, ?a_cabang_id)";
		$this->connector->AddParameter("?is_aktif", $this->IsAktif);
		$this->connector->AddParameter("?user_id", $this->UserId);
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?user_pwd", md5($this->UserPwd1));
		$this->connector->AddParameter("?user_lvl", $this->UserLvl);
		$this->connector->AddParameter("?user_name", $this->UserName);
		$this->connector->AddParameter("?user_email", $this->UserEmail);
		$this->connector->AddParameter("?allow_multiple_login", $this->AllowMultipleLogin);
		$this->connector->AddParameter("?force_accounting_period", $this->IsForceAccountingPeriod);
        $this->connector->AddParameter("?employee_id", $this->EmployeeId);
		$this->connector->AddParameter("?a_cabang_id", $this->ACabangId);
		return $this->connector->ExecuteNonQuery();
	}

	public function Update($id) {
		if (strlen($this->UserPwd1) > 0) {
			$this->connector->CommandText =
'UPDATE sys_users SET
	is_aktif = ?is_aktif
	, user_id = ?user_id
	, entity_id = ?entity_id
	, cabang_id = ?cabang_id
	, user_pwd = ?user_pwd
	, user_lvl = ?user_lvl
	, user_name = ?user_name
	, user_email = ?user_email
	, allow_multiple_login = ?allow_multiple_login
	, force_accounting_period = ?force_accounting_period
	, employee_id = ?employee_id
	, a_cabang_id = ?a_cabang_id
WHERE user_uid = ?id';
			$this->connector->AddParameter("?user_pwd", md5($this->UserPwd1));
		} else {
			$this->connector->CommandText =
'UPDATE sys_users SET
	is_aktif = ?is_aktif
	, user_id = ?user_id
	, entity_id = ?entity_id
	, cabang_id = ?cabang_id
	, user_lvl = ?user_lvl
	, user_name = ?user_name
	, user_email = ?user_email
	, allow_multiple_login = ?allow_multiple_login
	, force_accounting_period = ?force_accounting_period
	, employee_id = ?employee_id
	, a_cabang_id = ?a_cabang_id
WHERE user_uid = ?id';
		}
		$this->connector->AddParameter("?is_aktif", $this->IsAktif);
		$this->connector->AddParameter("?user_id", $this->UserId);
		$this->connector->AddParameter("?entity_id", $this->EntityId);
		$this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?user_lvl", $this->UserLvl);
		$this->connector->AddParameter("?user_name", $this->UserName);
		$this->connector->AddParameter("?user_email", $this->UserEmail);
		$this->connector->AddParameter("?allow_multiple_login", $this->AllowMultipleLogin);
		$this->connector->AddParameter("?force_accounting_period", $this->IsForceAccountingPeriod);
        $this->connector->AddParameter("?employee_id", $this->EmployeeId);
		$this->connector->AddParameter("?a_cabang_id", $this->ACabangId);
		$this->connector->AddParameter("?id", $id);

		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($id) {
		$rs = null;
		$this->connector->CommandText = 'DELETE FROM sys_users WHERE user_uid = ?id';
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs) {
			$this->connector->CommandText = 'DELETE FROM sys_user_rights WHERE user_uid = ?id';
			$this->connector->AddParameter("?id", $id);
			$rs = $this->connector->ExecuteNonQuery();
		}
		return $rs;
	}

	public function LoginRecord($uid) {
		$this->connector->CommandText =
'UPDATE sys_users SET
	status = ?status
	, login_time = ?login_time
	, login_from = ?login_from
	, session_id = ?session_id
WHERE user_uid = ?uid';
		$this->connector->AddParameter("?status", $this->Status);
		$this->connector->AddParameter("?login_time", $this->LoginTime);
		$this->connector->AddParameter("?login_from", $this->LoginFrom);
		$this->connector->AddParameter("?session_id", $this->SessionId);
		$this->connector->AddParameter("?uid", $uid);
		return $this->connector->ExecuteNonQuery();
	}

    public function LoginActivityWriter($lCabangId,$lUserId,$lStatus){;
        $sqx = "Insert Into sys_login_logs (cabang_id,user_id,log_time,from_ipad,browser_app,ref_info,login_status)";
        $sqx.= " Values (?cabang_id,?user_id,now(),?ipad,?browser,?ref,?lstatus)";
        $this->connector->CommandText = $sqx;
		$this->connector->AddParameter("?cabang_id", $lCabangId);
		$this->connector->AddParameter("?user_id", $lUserId);
		$this->connector->AddParameter("?ipad", getenv('REMOTE_ADDR'));
		$this->connector->AddParameter("?browser", getenv('HTTP_USER_AGENT'));
		$this->connector->AddParameter("?ref", getenv('HTTP_REFERER'));
		$this->connector->AddParameter("?lstatus", $lStatus);
        return $this->connector->ExecuteNonQuery();
    }

	public function UserActivityWriter($cabang_id,$resource,$process,$doc_no,$status){
		$sqx = "Insert Into sys_user_activity (cabang_id,user_uid,log_time,resource,process,doc_no,status)";
		$sqx.= " Values (?cabang_id,?user_uid,now(),?res,?process,?doc_no,?status)";
		$this->connector->CommandText = $sqx;
		$this->connector->AddParameter("?cabang_id", $cabang_id);
		$this->connector->AddParameter("?user_uid", AclManager::GetInstance()->GetCurrentUser()->Id);
		$this->connector->AddParameter("?res", $resource);
		$this->connector->AddParameter("?process", $process);
		$this->connector->AddParameter("?doc_no", $doc_no);
		$this->connector->AddParameter("?status", $status);
		return $this->connector->ExecuteNonQuery();
	}

	public function GetSysUserActivity($userUid,$stDate,$enDate){
		$sql = "Select a.* From vw_sys_user_activity a Where a.user_uid = ?userUid and a.log_time BETWEEN ?stDate and ?enDate Order By a.log_time;";
		$this->connector->CommandText = $sql;
		$this->connector->AddParameter("?userUid", $userUid);
		$this->connector->AddParameter("?stDate", date('Y-m-d',$stDate).' 00:00:00');
		$this->connector->AddParameter("?enDate", date('Y-m-d',$enDate).' 23:59:59');
		$rs = $this->connector->ExecuteQuery();
		return $rs;
	}

	public function GetUserLevel($getLevel = 5,$operator = '<'){
		$sql = "SELECT a.* FROM `sys_status_code` a WHERE a.`key` = 'user_level' AND a.`code` $operator $getLevel Order By a.code;";
		$this->connector->CommandText = $sql;
		$rs = $this->connector->ExecuteQuery();
		return $rs;
	}
}
