<?php

class UserPrivileges extends EntityBase {
    // public variables
    public $Id;
    public $UserUid;
    public $ResourceId;
    public $ResourceName;
    public $ResourcePath;
    public $MaxDiscount;


    // Helper Variable
	public function FillProperties(array $row){
        $this->Id = $row["id"];
        $this->UserUid = $row["user_uid"];
        $this->MaxDiscount = $row["max_discount"];
        $this->ResourceId = $row["resource_id"];
        $this->ResourceName = $row["resource_name"];
        $this->ResourcePath = $row["resource_path"];
    }

    public function LoadPrivileges($uId){
        $sql = "SELECT a.*,b.resource_name,b.resource_path FROM sys_user_privileges AS a Join sys_resource AS b On a.resource_id = b.id Where a.user_uid = ".$uId." ORDER BY b.resource_name";
        $this->connector->CommandText = $sql;
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new UserPrivileges();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
    }

    public function FindByResourceId($uId,$rsId) {
        $sql = "SELECT a.*,b.resource_name,b.resource_path FROM sys_user_privileges AS a Join sys_resource AS b On a.resource_id = b.id Where a.user_uid = ".$uId." And a.resource_id = ".$rsId;
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $row = $rs->FetchAssoc();
        $this->FillProperties($row);
        return $this;
    }

    public function Insert(){
        $this->connector->CommandText = 'INSERT INTO sys_user_privileges(user_uid,resource_id,max_discount) VALUES(?user_uid,?resource_id,?max_discount)';
		$this->connector->AddParameter("?user_uid", $this->UserUid);
        $this->connector->AddParameter("?resource_id", $this->ResourceId);
        $this->connector->AddParameter("?max_discount", $this->MaxDiscount);
        return $this->connector->ExecuteNonQuery();
    }

    public function Delete($uid){
        $this->connector->CommandText = 'Delete From sys_user_privileges WHERE user_uid = ?uid';
		$this->connector->AddParameter("?uid", $uid);
		return $this->connector->ExecuteNonQuery();
    }

    public function LoadPrivilegesResource(){
        $sql = "SELECT a.id,a.resource_name,a.resource_path FROM sys_resource AS a Where a.is_privileges = 1 ORDER BY a.resource_name";
        $this->connector->CommandText = $sql;
        return $this->connector->ExecuteQuery();
    }
}

