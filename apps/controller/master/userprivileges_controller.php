<?php
class UserPrivilegesController extends AppController {
	protected function Initialize() {
		require_once(MODEL . "master/user_admin.php");
		require_once(MODEL . "master/user_privileges.php");
	}

	public function edit($uid = null) {
        $userPrivileges = null;
		$loader = null;
		// find user data
        $log = new UserAdmin();
		$userdata = new UserAdmin();
		$userdata = $userdata->FindById($uid);
        $jdt = 0;
        $issetup = false;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$rsId = $this->GetPostValue("resourceId");
            $mdIs = $this->GetPostValue("maxDiscount");
            $jdt = count($rsId);
            if ($jdt > 0){
                $userPrivileges = new UserPrivileges();
                $rs = $userPrivileges->Delete($uid);
                for($i=0;$i<$jdt;$i++){
                    $userPrivileges = new UserPrivileges();
                    $userPrivileges->UserUid = $uid;
                    $userPrivileges->ResourceId = $rsId[$i];
                    $userPrivileges->MaxDiscount = $mdIs[$i];
                    $rs = $userPrivileges->Insert();
                }
                $log = $log->UserActivityWriter($this->userCabangId,'master.userprivileges','Update System User Privileges -> User: '.$userdata->UserId.' - '.$userdata->UserName,'-','Success');
                $this->persistence->SaveState("info", sprintf("Data Privileges User: '%s' telah berhasil disimpan.", $userdata->UserId));
                redirect_url("master.useradmin");
            }
		} else {
			$userPrivileges = new UserPrivileges();
            $userPrivileges = $userPrivileges->LoadPrivileges($uid);
            if ($userPrivileges != null){
                $issetup = true;
            }
		}
		// load resource data
		$loader = new UserPrivileges();
		$resources = $loader->LoadPrivilegesResource();
        $this->Set("userdata", $userdata);
        $this->Set("resources", $resources);
        $this->Set("issetup", $issetup);
		$this->Set("privileges", $userPrivileges);
	}

    public function getDiscPrivileges($resId){
        $userId = AclManager::GetInstance()->GetCurrentUser()->Id;
        $rst = -1;
        $privileges = new UserPrivileges();
        $privileges = $privileges->FindByResourceId($userId,$resId);
        if ($privileges != null){
            /** @var $privileges UserPrivileges */
            $rst = $privileges->MaxDiscount;
        }

        print $rst;
    }
}
