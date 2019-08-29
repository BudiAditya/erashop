<?php

class NotificationGroup {
	public $Name;
	/** @var Notification[] */
	public $UserNotifications;

	public function __construct($name) {
		$this->Name = $name;
	}
}

class Notification {
	public  $Text;
	public  $Url;
	/** @var NotificationGroup */
	private $group;

	private function  __construct(NotificationGroup $group) {
		$this->group = $group;
	}

	/**
	 * @return NotificationGroup
	 */
	public function GetGroup() {
		return $this->group;
	}

	/**
	 * @return NotificationGroup[]
	 */
	public static function GetCurrentUserNotifications() {
		$notifications = array();

		$acl = AclManager::GetInstance();
		$sbu = PersistenceManager::GetInstance()->LoadState("entity_id");
		$isCorporate = ($sbu == 7 || $sbu == null);
		$connector = ConnectorManager::GetDefaultConnector();

		// Group : Rekonsil
		$group = new NotificationGroup("Rekonsil");
		// Notifikasi Rekonsil masih dalam proses
		if ($acl->CheckUserAccess("trx.rekonsil", "index")) {
			$query = "SELECT COUNT(a.id) as jum_rekonsil, b.progress_name
FROM t_tx_rekonsil a Left Join m_rekonsil_progress b on a.progress_repair = b.seq_index
WHERE a.is_deleted = 0 AND a.progress_repair < 12 Group By b.progress_name Order By b.seq_index;";
			$connector->CommandText = $query;
			$rs = $connector->ExecuteQuery();
			if ($rs != null) {
                while ($row = $rs->FetchAssoc()) {
                    $notification = new Notification($group);
                    $notification->Text = sprintf("Ada %d data Rekonsil yang masih dalam proses", $row["jum_rekonsil"]);
                    $notification->Status = sprintf("-%s-", $row["progress_name"]);
                    $notification->Url = "trx.rekonsil";
                    $group->UserNotifications[] = $notification;
                }
			}
		}

		// OK jika ada notifnya baru kita add...
		if (count($group->UserNotifications) > 0) {
			$notifications[] = $group;
		}

		// Group : AR Invoice
		$group = new NotificationGroup("AR Invoice");
		// Notifikasi AR Invoice belum approval
		if ($acl->CheckUserAccess("ar.invoice", "approve")) {
			$query = "SELECT COUNT(a.id) FROM t_ar_invoice_master AS a WHERE a.is_deleted = 0 AND a.invoice_status = 0";
			$connector->CommandText = $query;
			$rs = $connector->ExecuteScalar();
			if ($rs > 0) {
				$notification = new Notification($group);
				$notification->Text = sprintf("Ada %d data A/R Invoice yang belum di approve.", $rs);
                $notification->Status = sprintf("-%s-","DRAFT");
				$notification->Url = "ar.invoice";
				$group->UserNotifications[] = $notification;
			}
		}
        // Notifikasi AR Invoice jatuh tempo dan belum terbayar
        if ($acl->CheckUserAccess("ar.receipt", "index")) {
            $query = "SELECT COUNT(a.id) FROM t_ar_invoice_master AS a WHERE a.is_deleted = 0 AND a.invoice_status = 1 AND DATEDIFF(date_add(a.invoice_date, Interval a.credit_terms Day), CURRENT_DATE) <= 30";
            $connector->CommandText = $query;
            $rs = $connector->ExecuteScalar();
            if ($rs > 0) {
                $notification = new Notification($group);
                $notification->Text = sprintf("Ada %d A/R Invoice yang sudah/akan jatuh tempo dalam 30 hari kedepan", $rs);
                $notification->Status = sprintf("-%s-","UNPAID");
                $notification->Url = "ar.receipt";
                $group->UserNotifications[] = $notification;
            }
        }
		// OK jika ada notifnya baru kita add...
		if (count($group->UserNotifications) > 0) {
			$notifications[] = $group;
		}

        // Group : AR Receipt
        $group = new NotificationGroup("AR Receipt");
        // Notifikasi AR Receipt belum approval
        if ($acl->CheckUserAccess("ar.receipt", "approve")) {
            $query = "SELECT COUNT(a.id) FROM t_ar_receipt_master AS a WHERE a.is_deleted = 0 AND a.receipt_status = 0";
            $connector->CommandText = $query;
            $rs = $connector->ExecuteScalar();
            if ($rs > 0) {
                $notification = new Notification($group);
                $notification->Text = sprintf("Ada %d data A/R Receipt yang belum di approve.", $rs);
                $notification->Status = sprintf("-%s-","DRAFT");
                $notification->Url = "ar.receipt";
                $group->UserNotifications[] = $notification;
            }
        }
        // OK jika ada notifnya baru kita add...
        if (count($group->UserNotifications) > 0) {
            $notifications[] = $group;
        }

        // Group : AP Invoice
        $group = new NotificationGroup("AP Invoice");
        // Notifikasi AR Invoice belum approval
        if ($acl->CheckUserAccess("ap.invoice", "approve")) {
            $query = "SELECT COUNT(a.id) FROM t_ap_invoice_master AS a WHERE a.is_deleted = 0 AND a.invoice_status = 0";
            $connector->CommandText = $query;
            $rs = $connector->ExecuteScalar();
            if ($rs > 0) {
                $notification = new Notification($group);
                $notification->Text = sprintf("Ada %d data A/P Invoice yang belum di approve.", $rs);
                $notification->Status = sprintf("-%s-","DRAFT");
                $notification->Url = "ap.invoice";
                $group->UserNotifications[] = $notification;
            }
        }
        // Notifikasi AP Invoice jatuh tempo dan belum dibayar
        if ($acl->CheckUserAccess("ap.payment", "index")) {
            $query = "SELECT COUNT(a.id) FROM t_ap_invoice_master AS a WHERE a.is_deleted = 0 AND a.invoice_status = 1 AND DATEDIFF(date_add(a.invoice_date, Interval a.credit_terms Day), CURRENT_DATE) <= 30";
            $connector->CommandText = $query;
            $rs = $connector->ExecuteScalar();
            if ($rs > 0) {
                $notification = new Notification($group);
                $notification->Text = sprintf("Ada %d A/P Invoice yang sudah/akan jatuh tempo dalam 30 hari kedepan", $rs);
                $notification->Status = sprintf("-%s-","UNPAID");
                $notification->Url = "ap.payment";
                $group->UserNotifications[] = $notification;
            }
        }
        // OK jika ada notifnya baru kita add...
        if (count($group->UserNotifications) > 0) {
            $notifications[] = $group;
        }

		return $notifications;
	}
}

// EoF: user_notification.php