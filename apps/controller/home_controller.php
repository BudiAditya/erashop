<?php

class HomeController extends AppController {

	protected function Initialize() { }

	public function index() {
		redirect_url("home/login");
	}

	//membuat form tambah data dan proses cek data login
	public function login() {
		require_once(MODEL . "master/user_admin.php");
        require_once(MODEL . "master/cabang.php");
		$this->Set("title", "Login"); //set title form

		// Cek apakah user mengirimkan data username dan password atau tidak
		if (count($this->postData) > 0) {
			// User mengirim data username dan password melalui form login
			$usercabang = $this->GetPostValue("user_cabang_id");
            $username = trim($this->GetPostValue("user_id"));
			$password = md5($this->GetPostValue("user_pwd"));
            $captcha = trim($this->GetPostValue("user_captcha"));
            $month = trim($this->GetPostValue("user_trxmonth"));
            $year = trim($this->GetPostValue("user_trxyear"));
            if ($this->persistence->LoadState("user_captcha") == $captcha){
                //jika login berhasil
                if ($this->doLogin($username, $password, $usercabang)) {
                    $acl = AclManager::GetInstance(); //load class acl untuk session user id
                    $uid = $acl->CurrentUser->Id;
                    $router = Router::GetInstance();
                    $userAdmin = new UserAdmin();
                    $userAdmin->FindById($uid);
                    $usercomp = null;
                    $usercab = null;
                    $oke = false;
                    if ($userAdmin != null) {
                        // periksa status user aktif atau tidak
                        if ($userAdmin->IsAktif == 1) {
                            // update table sys_users dengan info login
                            // cabang2 apa saja yg boleh diakses
                            $usercabakses = $userAdmin->ACabangId;
                            $usercabakses = explode(",", $usercabakses);
                            if (in_array($usercabang, $usercabakses)) {
                                $oke = true;
                            }
                            if (!$oke && $userAdmin->CabangId != $usercabang && $userAdmin->UserLvl < 4) {
                                $this->Set("error", "Maaf, Anda tidak boleh mengakses cabang ini!"); //tampilkan pesan error
                                $log = $userAdmin->LoginActivityWriter($usercabang, $username, 'Akses cabang ditolak');
                            } else {
                                // update table sys_users dengan info login
                                $userAdmin->Status = "6";
                                $userAdmin->LoginTime = date('Y-m-d H:i:s');
                                $userAdmin->LoginFrom = $router->IpAddress;
                                $userAdmin->SessionId = $this->persistence->GetPersistenceId();
                                $userAdmin->LoginRecord($userAdmin->UserUid);
                                // ambil data entity dan project user yang login simpan ke session
                                $usercab = new Cabang($usercabang);
                                if ($usercab == null) {
                                    $this->persistence->SaveState("entity_id", $userAdmin->EntityId);
                                    $this->persistence->SaveState("entity_cd", $userAdmin->EntityCd);
                                    $this->persistence->SaveState("entity_name", $userAdmin->CompanyName);
                                    $this->persistence->SaveState("cabang_id", $userAdmin->CabangId);
                                    $this->persistence->SaveState("cabang_kode", $userAdmin->CabangKode);
                                    $this->persistence->SaveState("cabang_name", $userAdmin->CabangName);
                                    $this->persistence->SaveState("area_id", $userAdmin->AreaId);
                                } else {
                                    /** @var $usercab Cabang */
                                    $this->persistence->SaveState("entity_id", $usercab->EntityId);
                                    $this->persistence->SaveState("entity_cd", $usercab->EntityCd);
                                    $this->persistence->SaveState("entity_name", $usercab->CompanyName);
                                    $this->persistence->SaveState("cabang_id", $usercabang);
                                    $this->persistence->SaveState("cabang_kode", $usercab->Kode);
                                    $this->persistence->SaveState("cabang_name", $usercab->NamaCabang);
                                    $this->persistence->SaveState("area_id", $usercab->AreaId);
                                }
                                $this->persistence->SaveState("user_lvl", $userAdmin->UserLvl);
                                $this->persistence->SaveState("sys_start_date", $userAdmin->SysStartDate);
                                $this->persistence->SaveState("empdept_id", $userAdmin->EmpDepId);
                                // Simpan data untuk lock tanggal periode ad / edit voucher
                                $this->persistence->SaveState("force_periode", $userAdmin->IsForceAccountingPeriod);
                                $this->persistence->SaveState("acc_year", $year);
                                $this->persistence->SaveState("acc_month", $month);
                                $log = $userAdmin->LoginActivityWriter($usercabang, $username, 'Login success');
                                $log = $userAdmin->UserActivityWriter($usercabang, 'home.login', 'LogIn to System', '', 'Success');
                                if ($userAdmin->IsForceAccountingPeriod) {
                                    redirect_url("main/set_periode");
                                } else {
                                    redirect_url("main");
                                }
                            }
                        }else{
                            $log = $userAdmin->LoginActivityWriter($usercabang,$username,'User ID tidak aktif');
                            $this->Set("error", "Nama Pemakai terdaftar tapi tidak di-aktif-kan!"); //tampilkan pesan error
                        }
                    }else{
                        $log = $userAdmin->LoginActivityWriter($usercabang,$username,'User ID belum terdaftar');
                        $this->Set("error", "Nama Pemakai belum terdaftar!"); //tampilkan pesan error
                    }
                } else {
                    $userAdmin = new UserAdmin();
                    $log = $userAdmin->LoginActivityWriter($usercabang,$username,'User ID atau Password salah');
                    $this->Set("error", "Nama atau kata sandi yang dimasukkan salah!"); //tampilkan pesan error
                }
            }else{
                $userAdmin = new UserAdmin();
                $log = $userAdmin->LoginActivityWriter($usercabang,$username,'Nilai Captha salah');
                $this->Set("error", "Nilai Captcha yang dimasukkan salah !"); //tampilkan pesan error
                //Dispatcher::RedirectUrl("home/login");
            }
		} else {
			$acl = AclManager::GetInstance();
			// Kita cek apakah user sudah login atau belum
			if ($acl->GetIsUserAuthenticated()) {
				// User sudah login ke system maka tidak perlu login lagi
				Dispatcher::RedirectUrl("main");
			}
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
        $loader = new Cabang();
        $cablists = $loader->LoadByType(0,2,"<>");
        $this->Set("year", $year);
        $this->Set("month", $month);
        $this->Set("cablists", $cablists);
	}

	//proses validasi data login
	private function doLogin($username, $password, $cabid = 0) {
		$acl = AclManager::GetInstance();
		$success = $acl->Authenticate($username, $password, $cabid);
		if ($success) {
			$acl->SerializeUser();
		}
		return $success;
	}


	public function logout() {
		require_once(MODEL . "master/user_admin.php");
		$acl = AclManager::GetInstance();
		$uid = $acl->CurrentUser->Id;
        $userAdmin = new UserAdmin();
		$userAdmin->Status = "7";
		$userAdmin->LoginTime = date('Y-m-d H:i:s');
		$userAdmin->LoginFrom = trim(getenv("REMOTE_ADDR"));
		$userAdmin->SessionId = null;
		$userAdmin->LoginRecord($uid);
        $log = $userAdmin->UserActivityWriter($this->persistence->LoadState("cabang_id"),'home.login','LogOut From System','','Success');
		$acl->SignOut(); // Logout User yang aktif
		$acl->SerializeUser(); // hapus semua session data
		//$this->persistence->DestroyPersistence;

		Dispatcher::RedirectUrl("home/login");
	}

    public function capgambar(){
        //session_start();
        $this->persistence->SaveState("user_captcha", "");
        $text = substr(md5(microtime()),mt_rand(0,26),5);
        //$_SESSION["ttcapt"] = $text;
        $this->persistence->SaveState("user_captcha", $text);
        $height = 35;
        $width = 54;
        $tt_image = imagecreate($width, $height);
        $blue = imagecolorallocate($tt_image, 0, 0, 255);
        $white = imagecolorallocate($tt_image, 255, 255, 255);
        $font_size = 14;
        imagestring($tt_image, $font_size, 5, 8, $text, $white);
        /* Avoid Caching */
        header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header( "Content-type: image/png" );
        imagepng($tt_image);
        imagedestroy($tt_image );
    }

    public function capgambar1(){
        header("Content-type: image/png");

        // beri nama session dengan nama Captcha
        //$_SESSION["Captcha"]="";
        $this->persistence->SaveState("user_captcha", "");
        //tentukan ukuran gambar
        $gbr = imagecreate(165, 30);
        //warna background gambar
        imagecolorallocate($gbr, 167, 218, 239);
        $grey = imagecolorallocate($gbr, 128, 128, 128);
        $black = imagecolorallocate($gbr, 0, 0,0);
        // tentukan font
        $font = "apps/library/font/monaco.ttf";
        $nomox = null;
        // membuat nomor acak dan ditampilkan pada gambar
        for($i=0;$i<=5;$i++) {
            // jumlah karakter
            $nomor=rand(0, 9);
            $nomox.=$nomor;
            $sudut= rand(-25, 25);
            imagettftext($gbr, 20, $sudut, 8+15*$i, 25, $black, $font, $nomor);
            // efek shadow
            imagettftext ($gbr, 20, $sudut, 9+15*$i, 26, $grey, $font, $nomor);
        }
        $this->persistence->SaveState("user_captcha", $nomox);
        //untuk membuat gambar
        imagepng($gbr);
        imagedestroy($gbr);
    }
}
