<?php

class MainController extends AppController {
	private $validSbu = array(1 => "C01", 2 => "C02", 3 => "C03", 4 => "C04", 5 => "C05", 6 => "C06", 7 => "C07");

	protected function Initialize() { }

	public function index() {
		if ($this->persistence->StateExists("info")) {
			$this->Set("info", $this->persistence->LoadState("info"));
			$this->persistence->DestroyState("info");
		} else {
			if ($this->persistence->LoadState("is_corporate")) {
				$sbuId = $this->persistence->LoadState("entity_id");
/*
				if ($sbuId == 1 || $sbuId == null) {
					$this->Set("info", "Login Corporate Terditeksi anda dapat menggunakan fitur impersonate untuk menyamarkan sebagai Entity lain.");
				} else {
					$this->Set("info", "INFO: Saat ini login CORPORATE anda terdeteksi sebagai: " . $this->validSbu[$sbuId]);
				}
*/
			}
		}
/*
		require_once(LIBRARY . "user_notification.php");
    	$this->Set("notifications", Notification::GetCurrentUserNotifications());
*/
        require_once(MODEL . "master/attention.php");
        $loader = new Attention();
        $attentions = $loader->LoadAll();
        $this->Set("attentions",$attentions);

		if ($this->persistence->StateExists("error")) {
			$this->Set("error", $this->persistence->LoadState("error"));
			$this->persistence->DestroyState("error");
		}
	}

	public function impersonate($sbuId) {
		$isCorporate = $this->persistence->LoadState("is_corporate");
		if (!$isCorporate) {
			$this->persistence->SaveState("error", "Maaf impersonate Entity hanya untuk user dengan akses CORPORATE");
			redirect_url("main");
		}

		if (!array_key_exists($sbuId, $this->validSbu)) {
			$this->persistence->SaveState("error", "Maaf Entity yang diminta tidak terdaftar !");
			redirect_url("main");
		}

		$this->persistence->SaveState("entity_id", $sbuId);
		$this->persistence->SaveState("info", sprintf("Impersonate sebagai %s telah berhasil.", $this->validSbu[$sbuId]));

		$referer = $_SERVER["HTTP_REFERER"];
		if ($referer != null) {
			Dispatcher::Redirect($referer);
		} else {
			redirect_url("main");
		}
	}

	public function change_password() {
		if ($this->persistence->StateExists("info")) {
			$this->Set("info", $this->persistence->LoadState("info"));
			$this->persistence->DestroyState("info");
		}

		if (count($this->postData) == 0) {
			return;
		}

		// OK mari kita ganti passwordnya
		$old = $this->GetPostValue("Old");
		$new = $this->GetPostValue("New");
		$retype = $this->GetPostValue("Retype");

		if ($old == "") {
			$this->Set("error", "Maaf mohon mengetikkan password lama anda");
			return;
		}
		if ($new == "") {
			$this->Set("error", "Maaf mohon mengetikkan password baru anda");
			return;
		}
		if ($new == $old) {
			$this->Set("error", "Password lama dan password baru sama.");
			return;
		}
		if ($new != $retype) {
			$this->Set("error", "Password baru dan ulangi tidak sama");
			return;
		}

		$old = md5($old);
		$new = md5($new);

		$this->connector->CommandText = "UPDATE sys_users SET user_pwd = ?new WHERE user_uid = ?id AND user_pwd = ?old";
		$this->connector->AddParameter("?new", $new);
		$this->connector->AddParameter("?id", AclManager::GetInstance()->GetCurrentUser()->Id);
		$this->connector->AddParameter("?old", $old);

		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->persistence->SaveState("info", "Password anda telah berhasil dirubah. Password baru akan efektif pada login berikutnya.");
			redirect_url("main/change_password");
		} else {
			$this->Set("error", "Maaf password lama anda salah.");
		}
	}

	public function set_periode() {
		if (count($this->postData) > 0) {
			$year = $this->GetPostValue("year");
			$month = $this->GetPostValue("month");

			$this->persistence->SaveState("acc_year", $year);
			$this->persistence->SaveState("acc_month", $month);

			// OK karena simpan persistence sifatnya void kita asumsikan berhasil
			redirect_url("main");
		} else {
			if ($this->persistence->StateExists("acc_year")) {
				$year = $this->persistence->LoadState("acc_year");
			} else {
				$year = date("Y");
			}
			if ($this->persistence->StateExists("acc_month")) {
				$month = $this->persistence->LoadState("acc_month");
			} else {
				$month = date("n");
			}

		}

		$this->Set("year", $year);
		$this->Set("month", $month);

		if ($this->persistence->StateExists("error")) {
			$this->Set("error", $this->persistence->LoadState("error"));
			$this->persistence->DestroyState("error");
		}
	}

	public function aclview($uid = 0) {
		//load acl
		require_once(MODEL . "master/user_admin.php");
		require_once(MODEL . "master/user_acl.php");
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
}
