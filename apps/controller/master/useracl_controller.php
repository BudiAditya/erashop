<?php
class UseraclController extends AppController {
	private $userCabangId;
	protected function Initialize() {
		require_once(MODEL . "master/user_admin.php");
		require_once(MODEL . "master/user_acl.php");
		$this->userCabangId = $this->persistence->LoadState("cabang_id");
	}

	public function add($uid,$cbi) {
		require_once(MODEL . "master/cabang.php");
		$loader = null;
		$skema = null;
		$userlist = null;
		// find user data
		$log = new UserAdmin();
		$userdata = new UserAdmin();
		$userdata = $userdata->FindById($uid);
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$cabid = $this->GetPostValue("CabangId");
			$skema = $this->GetPostValue("hakakses");
			$prevResId = null;
			$hak = null;
			$cbi = $cabid;
			if ($cabid == null || $cabid == 0 || $cabid == ''){
				$userAcl = new UserAcl();
				$userAcl->Delete($uid,0);
				$this->persistence->SaveState("info", sprintf("Data Hak Akses User: '%s' telah berhasil dihapus..", $userdata->UserId));
				redirect_url("master.useradmin");
			}else {
				$userAcl = new UserAcl();
				$userAcl->Delete($uid, $cabid);
				foreach ($skema As $aturan) {
					$tokens = explode("|", $aturan);
					$resid = $tokens[0];
					$hak = $tokens[1];
					if ($prevResId != $resid) {
						if ($userAcl->Rights != "") {
							$userAcl->Insert();
						}
						$prevResId = $resid;
						$userAcl = new UserAcl();
						$userAcl->ResourceId = $resid;
						$userAcl->UserUid = $uid;
						$userAcl->CabangId = $cabid;
						$userAcl->Rights = "";
					}
					$userAcl->Rights .= $hak;
				}
				if ($userAcl->Rights != "") {
					$userAcl->Insert();
					$log = $log->UserActivityWriter($this->userCabangId,'master.useracl','Setting User ACL -> User: '.$userdata->UserId.' - '.$userdata->UserName,'-','Success');
				}
				$this->persistence->SaveState("info", sprintf("Data Hak Akses User: '%s' telah berhasil disimpan.", $userdata->UserId));
				redirect_url("master.useradmin");
			}
		} else {
			$userAcl = new UserAcl();
			$hak = $userAcl->LoadAcl($uid,$cbi);
            $loader = new UserAcl();
            $userCab = $loader->LoadUserCabAcl($uid);
		}
		// load resource data
		$loader = new UserAcl();
		$resources = $loader->LoadAllResources();
		$loader = new Cabang();
		$cabangs = $loader->LoadAll();
		$loader = new UserAcl();
		$userlist = $loader->GetListUserCabAcl();
		$this->Set("resources", $resources);
		$this->Set("userdata", $userdata);
		$this->Set("userlist", $userlist);
		$this->Set("hak", $hak);
        $this->Set("userCab", $userCab);
		$this->Set("cabangs", $cabangs);
		$this->Set("ucabId", $cbi);
	}

	public function view($uid = 0) {
		//load acl
		if ($uid == 0){
			$uid = AclManager::GetInstance()->GetCurrentUser()->Id;
		}
		$userId = null;
		$userdata = new UserAdmin();
		$userdata = $userdata->FindById($uid);
		$userId = $userdata->UserId.' ['.$userdata->UserName.']';
		$userAcl = new UserAcl();
		$aclists = $userAcl->GetUserAclList($uid);
		$this->Set("userId", $userId);
		$this->Set("aclists", $aclists);
	}


	public function copy($uid = null) {
		$srcUid = null;
		$cbi = 0;
		if (count($this->postData) > 0) {
			// OK user ada kirim data kita proses
			$cdata = $this->GetPostValue("copyFrom");
			$cbi = $this->GetPostValue("tCabangId");
			$cdata = explode("|",$cdata);
			$srcUid = $cdata[0];
			$srcCbi = $cdata[1];
			$userAcl = new UserAcl();
			$userAcl->Delete($uid,$cbi);
			$userAcl->Copy($srcUid,$srcCbi,$uid,$cbi);
			$this->persistence->SaveState("info", sprintf("Data Hak Akses telah berhasil disalin.."));
			Dispatcher::RedirectUrl("master.useracl/add/".$uid."/".$cbi);
		} else {
			$userAcl = new UserAcl();
			$hak = $userAcl->LoadAcl($uid,$cbi);
			Dispatcher::RedirectUrl("master.useracl/add/".$uid."/".$cbi);
		}
	}


}
