<?php

/**
 * Untuk membuat beberapa laporan accounting yang diminta.
 * Ini berisi laporan bantuan dari buku besar. Detail untuk buku besar. Beberapa caranya mirip dengan cashflow
 *
 * @see CashFlowController
 */
class WorksheetBalanceController extends AppController {
    private $userCompanyId;

    protected function Initialize() {
        $this->userCompanyId = $this->persistence->LoadState("entity_id");
    }

    public function recap() {
        require_once(MODEL . "master/company.php");
        require_once(MODEL . "master/coadetail.php");
        require_once(LIBRARY . "dot_net_tools.php");

        if (count($this->getData) > 0) {
            set_time_limit(300);
            $noOfDays = array(-1, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

            $rekapMethod = $this->GetGetValue("rekapMethod");
            $isIncOb = $this->GetGetValue("isIncOb");
            $month = $this->GetGetValue("month");
            $year = $this->GetGetValue("year");
            $noOfDay = $noOfDays[$month];
            if ($month == 2 && $year % 4 == 0) {
                $noOfDay = 29;	// Leap Year
            }
            $output = $this->GetGetValue("output", "web");
            $firstJanuary = mktime(0,0, 0, 1, 1, $year);
            if ($rekapMethod == 0){
                $startDate = $firstJanuary;
            }else{
                $startDate = mktime(0, 0, 0, $month, 1, $year);
            }
            $endDate = mktime(0,0, 0, $month, $noOfDay, $year);

            // Hmm gw tau klo ini bisa dalam bentuk string secara langsung tapi gw prefer cara ini agar 'strong type'
            // Setting global parameter (Jgn panggil ClearParameters() OK !)
            $this->connector->AddParameter("?start", date(SQL_DATETIME, $startDate));
            $this->connector->AddParameter("?end", date(SQL_DATETIME, $endDate));
            $this->connector->AddParameter("?firstJan", date(SQL_DATETIME, $firstJanuary));
            if ($month > 1) {
                $this->connector->AddParameter("?prev", date(SQL_DATETIME, $startDate - 1));
            }

            // OK dafuq ini... mari kita query multi step
            // #01: Filter account yang akan digunakan pada report (Hanya yang parent ID nya 3, 8 alias kas dan pendapatan)
            $this->connector->CommandText =
                "CREATE TEMPORARY TABLE acc_id AS
                SELECT a.id, a.kode as acc_no, a.perkiraan as acc_name, a.psaldo as posisi_saldo
                FROM vw_coa_detail AS a
                WHERE a.entity_id = ".$this->userCompanyId." Order By a.kode;";

            $this->connector->ExecuteNonQuery();

            // #02: Ambil sum semua debit pada periode yang diminta
            $this->connector->CommandText =
                "CREATE TEMPORARY TABLE sum_debit AS
                SELECT a.acdebet_no, SUM(a.jumlah) AS 'total_debit', SUM(IF(a.wbs_post = 'MTC', a.jumlah, 0)) AS 'mtc_debit', SUM(IF(a.wbs_post = 'ACR', a.jumlah, 0)) AS 'acr_debit', SUM(IF(a.wbs_post = 'ADJ', a.jumlah, 0)) AS 'adj_debit'
                FROM vw_ac_voucher_detail AS a
                WHERE a.doc_status = 2 AND a.is_deleted = 0 AND a.tgl_voucher BETWEEN ?start AND ?end
                    AND a.acdebet_no IN (SELECT acc_no FROM acc_id)
                GROUP BY a.acdebet_no;";
            $this->connector->ExecuteNonQuery();

            // #03: Ambil sum semua credit pada periode yang diminta
            $this->connector->CommandText =
                "CREATE TEMPORARY TABLE sum_credit AS
                SELECT a.ackredit_no, SUM(a.jumlah) AS 'total_credit', SUM(IF(a.wbs_post = 'MTC', a.jumlah, 0)) AS 'mtc_credit', SUM(IF(a.wbs_post = 'ACR', a.jumlah, 0)) AS 'acr_credit', SUM(IF(a.wbs_post = 'ADJ', a.jumlah, 0)) AS 'adj_credit'
                FROM vw_ac_voucher_detail AS a
                WHERE a.doc_status = 2 AND a.is_deleted = 0 AND a.tgl_voucher BETWEEN ?start AND ?end
                    AND a.ackredit_no IN (SELECT acc_no FROM acc_id)
                GROUP BY a.ackredit_no;";
            $this->connector->ExecuteNonQuery();

            if ($rekapMethod == 1 && $month > 1) {
                // kalau periode yang diminta bukan januari kita perlu data tambahan.... >_<
                // #04: Ambil data bulan-bulan sebelumnya (debet)
                $this->connector->CommandText =
                    "CREATE TEMPORARY TABLE sum_debit_prev AS
                    SELECT a.acdebet_no, SUM(a.jumlah) AS total_debit_prev
                    FROM vw_ac_voucher_detail AS a
                    WHERE a.doc_status = 2 AND a.is_deleted = 0 AND a.tgl_voucher BETWEEN ?firstJan AND ?prev AND a.acdebet_no IN (SELECT acc_no FROM acc_id)
                    GROUP BY a.acdebet_no;";
                $this->connector->ExecuteNonQuery();

                // #05: Ambil data bulan-bulan sebelumnya (kredit)
                $this->connector->CommandText =
                    "CREATE TEMPORARY TABLE sum_credit_prev AS
                    SELECT a.ackredit_no, SUM(a.jumlah) AS total_credit_prev
                    FROM vw_ac_voucher_detail AS a
                    WHERE a.doc_status = 2 AND a.is_deleted = 0 AND a.tgl_voucher BETWEEN ?firstJan AND ?prev AND a.ackredit_no IN (SELECT acc_no FROM acc_id)
                    GROUP BY a.ackredit_no;";
                $this->connector->ExecuteNonQuery();

                // #06: OK final query...
                $this->connector->CommandText =
                    "SELECT a.*, b.total_debit, b.mtc_debit,c.mtc_credit,b.acr_debit,c.acr_credit,b.adj_debit,c.adj_credit, d.total_debit_prev, e.total_credit_prev, f.debet as bal_debit_amt, f.kredit as bal_credit_amt
                    FROM acc_id AS a
                        LEFT JOIN sum_debit AS b ON a.acc_no = b.acdebet_no
                        LEFT JOIN sum_credit AS c ON a.acc_no = c.ackredit_no
                        LEFT JOIN sum_debit_prev AS d ON a.acc_no = d.acdebet_no
                        LEFT JOIN sum_credit_prev AS e ON a.acc_no = e.ackredit_no
                        LEFT JOIN t_gl_saldoawal_account AS f ON a.acc_no = f.acc_no AND f.op_date = ?firstJan And f.entity_id = ".$this->userCompanyId."
                    ORDER BY a.acc_no;";
            } else {
                // Bulan periode yang diminta adalah januari jadi bisa langsung query total debet dan kredit
                // Untuk data bulan-bulan sebelumnya selalu 0
                $this->connector->CommandText =
                    "SELECT a.*, b.total_debit, b.mtc_debit,c.mtc_credit,b.acr_debit,c.acr_credit,b.adj_debit,c.adj_credit, 0 AS total_debit_prev, 0 AS total_credit_prev, f.debet as bal_debit_amt, f.kredit as bal_credit_amt
                    FROM acc_id AS a
                        LEFT JOIN sum_debit AS b ON a.acc_no = b.acdebet_no
                        LEFT JOIN sum_credit AS c ON a.acc_no = c.ackredit_no
                        LEFT JOIN t_gl_saldoawal_account AS f ON a.acc_no = f.acc_no AND f.op_date = ?firstJan And f.entity_id = ".$this->userCompanyId."
                    ORDER BY a.acc_no;";
            }

            $report = $this->connector->ExecuteQuery();
        } else {
            $isIncOb = 1;
            $rekapMethod = 0;
            $month = (int)date("n");
            $year = (int)date("Y");
            $report = null;
            $output = "web";
        }

        $company = new Company();
        if ($this->userCompanyId == 1 || $this->userCompanyId == null) {
            $company = $company->LoadById(1);
        } else {
            $company = $company->LoadById($this->userCompanyId);
        }

        $account = new CoaDetail();

        $this->Set("isIncOb", $isIncOb);
        $this->Set("rekapMethod", $rekapMethod);
        $this->Set("month", $month);
        $this->Set("year", $year);
        $this->Set("report", $report);
        $this->Set("output", $output);

        $this->Set("company", $company);
        $this->Set("monthNames", array(1 => "Januari", "Febuari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"));
    }
}

// End of file: trialbalance_controller.php
