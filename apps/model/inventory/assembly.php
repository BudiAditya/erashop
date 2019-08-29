<?php

require_once("assembly_detail.php");

class Assembly extends EntityBase {
	private $editableDocId = array(1, 2, 3, 4);

	public static $AssemblyStatusCodes = array(
		0 => "DRAFT",
		1 => "POSTED",
        2 => "CLOSED",
		3 => "VOID"
	);
   
	public $Id;
    public $IsDeleted = false;
    public $EntityId;
    public $AreaId;
    public $EntityCode;
    public $CompanyName;
    public $CabangId;
    public $CabangCode;
	public $AssemblyNo;
	public $AssemblyDate;
    public $AssemblyDescs;
    public $ItemId;
    public $ItemCode;
    public $ItemName;
    public $Qty;
    public $Price;
    public $AssemblyStatus;
	public $CreatebyId;
	public $CreateTime;
	public $UpdatebyId;
	public $UpdateTime;
    public $ItemSatuan;
    public $AdminName;

	/** @var AssemblyDetail[] */
	public $Details = array();

	public function __construct($id = null) {
		parent::__construct();
		if (is_numeric($id)) {
			$this->LoadById($id);
		}
	}

	public function FillProperties(array $row) {
        $this->Id = $row["id"];
        $this->IsDeleted = $row["is_deleted"] == 1;
        $this->EntityCode = $row["entity_cd"];
        $this->EntityId = $row["entity_id"];
        $this->CompanyName = $row["company_name"];
        $this->CabangId = $row["cabang_id"];
        $this->CabangCode = $row["cabang_code"];
        $this->AssemblyNo = $row["assembly_no"];
        $this->AssemblyDate = strtotime($row["assembly_date"]);
        $this->ItemId = $row["item_id"];
        $this->ItemCode = $row["item_code"];
        $this->ItemName = $row["item_name"];
        $this->ItemSatuan = $row["bsatbesar"];
        $this->Qty = $row["qty"];
        $this->Price = $row["price"];
        $this->AssemblyDescs = $row["assembly_descs"];
        $this->AssemblyStatus = $row["assembly_status"];
        $this->CreatebyId = $row["createby_id"];
        $this->CreateTime = $row["create_time"];
        $this->UpdatebyId = $row["updateby_id"];
        $this->UpdateTime = $row["update_time"];
        $this->AdminName = $row["admin_name"];
	}

	public function FormatAssemblyDate($format = HUMAN_DATE) {
		return is_int($this->AssemblyDate) ? date($format, $this->AssemblyDate) : date($format, strtotime(date('Y-m-d')));
	}

	/**
	 * @return AssemblyDetail[]
	 */
	public function LoadDetails() {
		if ($this->Id == null) {
			return $this->Details;
		}
		$detail = new AssemblyDetail();
		$this->Details = $detail->LoadByAssemblyId($this->Id);
		return $this->Details;
	}

	/**
	 * @param int $id
	 * @return Assembly
	 */
	public function LoadById($id) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ic_assembly_master AS a WHERE a.id = ?id";
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function FindById($id) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_assembly_master AS a WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        $rs = $this->connector->ExecuteQuery();
        if ($rs == null || $rs->GetNumRows() == 0) {
            return null;
        }
        $this->FillProperties($rs->FetchAssoc());
        return $this;
    }

	public function LoadByAssemblyNo($assemblyNo,$cabangId) {
		$this->connector->CommandText = "SELECT a.* FROM vw_ic_assembly_master AS a WHERE a.assembly_no = ?assemblyNo And a.cabang_id = ?cabangId";
		$this->connector->AddParameter("?assemblyNo", $assemblyNo);
        $this->connector->AddParameter("?cabangId", $cabangId);
		$rs = $this->connector->ExecuteQuery();
		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$this->FillProperties($rs->FetchAssoc());
		return $this;
	}

    public function LoadByEntityId($entityId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_assembly_master AS a WHERE a.entity_id = ?entityId";
        $this->connector->AddParameter("?entityId", $entityId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Assembly();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function LoadByCabangId($cabangId) {
        $this->connector->CommandText = "SELECT a.* FROM vw_ic_assembly_master AS a.cabang_id = ?cabangId";
        $this->connector->AddParameter("?cabangId", $cabangId);
        $rs = $this->connector->ExecuteQuery();
        $result = array();
        if ($rs != null) {
            while ($row = $rs->FetchAssoc()) {
                $temp = new Assembly();
                $temp->FillProperties($row);
                $result[] = $temp;
            }
        }
        return $result;
    }

	public function Insert() {
        $sql = "INSERT INTO t_ic_assembly_master (cabang_id, assembly_no, assembly_date, item_id, item_code, qty, price, assembly_descs, assembly_status, createby_id, create_time)";
        $sql.= "VALUES(?cabang_id, ?assembly_no, ?assembly_date, ?item_id, ?item_code, ?qty, ?price, ?assembly_descs, ?assembly_status, ?createby_id, now())";
		$this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
		$this->connector->AddParameter("?assembly_no", $this->AssemblyNo, "char");
		$this->connector->AddParameter("?assembly_date", $this->AssemblyDate);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?price", $this->Price);
		$this->connector->AddParameter("?assembly_descs", $this->AssemblyDescs);
        $this->connector->AddParameter("?assembly_status", $this->AssemblyStatus == null ? 0 : $this->AssemblyStatus);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
		$rs = $this->connector->ExecuteNonQuery();
		if ($rs == 1) {
			$this->connector->CommandText = "SELECT LAST_INSERT_ID();";
			$this->Id = (int)$this->connector->ExecuteScalar();
            $mid = $this->Id;
            //tambah stock
            $this->connector->CommandText = "SELECT fc_ic_assemblymaster_post($mid) As valresult;";
            $rsx = $this->connector->ExecuteQuery();
		}
		return $rs;
	}

	public function Update($id) {
		$this->connector->CommandText =
"UPDATE t_ic_assembly_master SET
	cabang_id = ?cabang_id
	, assembly_no = ?assembly_no
	, assembly_date = ?assembly_date
	, item_id = ?item_id
	, item_code = ?item_code
	, qty = ?qty
	, price = ?price
	, assembly_descs = ?assembly_descs
	, assembly_status = ?assembly_status
	, updateby_id = ?updateby_id
	, update_time = NOW()
WHERE id = ?id";
        $this->connector->AddParameter("?cabang_id", $this->CabangId);
        $this->connector->AddParameter("?assembly_no", $this->AssemblyNo, "char");
        $this->connector->AddParameter("?assembly_date", $this->AssemblyDate);
        $this->connector->AddParameter("?item_id", $this->ItemId);
        $this->connector->AddParameter("?item_code", $this->ItemCode);
        $this->connector->AddParameter("?qty", $this->Qty);
        $this->connector->AddParameter("?price", $this->Price);
        $this->connector->AddParameter("?assembly_descs", $this->AssemblyDescs);
        $this->connector->AddParameter("?assembly_status", $this->AssemblyStatus);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?id", $id);
		$rs = $this->connector->ExecuteNonQuery();
        return $rs;
	}

	public function Delete($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ic_assemblymaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //hapus data assembly_
        $this->connector->CommandText = "Delete From t_ic_assembly_master WHERE id = ?id";
		$this->connector->AddParameter("?id", $id);
		return $this->connector->ExecuteNonQuery();
	}

    public function Void($id) {
        //unpost stock dulu
        $rsx = null;
        $this->connector->CommandText = "SELECT fc_ic_assemblymaster_unpost($id) As valresult;";
        $rsx = $this->connector->ExecuteQuery();
        //hapus data assembly_
        $this->connector->CommandText = "Update t_ic_assembly_master a Set a.assembly_status = 3 WHERE a.id = ?id";
        $this->connector->AddParameter("?id", $id);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetAssemblyDocNo(){
        $sql = 'Select fc_sys_getdocno(?cbi,?txc,?txd) As valout;';
        $txc = 'ASY';
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?cbi", $this->CabangId);
        $this->connector->AddParameter("?txc", $txc);
        $this->connector->AddParameter("?txd", $this->AssemblyDate);
        $rs = $this->connector->ExecuteQuery();
        $val = null;
        if($rs){
            $row = $rs->FetchAssoc();
            $val = $row["valout"];
        }
        return $val;
    }

    public function LoadProduksi4Reports($entityId,$cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.* FROM vw_ic_assembly_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.assembly_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Order By a.assembly_date,a.assembly_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadRekapProduksi4Reports($entityId,$cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.item_code,a.item_name,a.bsatkecil as satuan, sum(a.qty) as sum_qty,sum(a.qty*a.price) as sum_total FROM vw_ic_assembly_master AS a";
        $sql.= " WHERE a.is_deleted = 0 and a.assembly_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Group By a.item_code,a.item_name,a.bsatkecil Order By a.item_name,a.item_code";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadMaterial4Reports($entityId,$cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT a.id,a.cabang_code,a.assembly_date,a.assembly_no,b.item_code,c.bnama as item_name,b.qty,b.price,c.bsatkecil as satuan";
        $sql.= " FROM vw_ic_assembly_master AS a Join t_ic_assembly_detail AS b on a.assembly_no = b.assembly_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.assembly_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Order By a.assembly_date,a.assembly_no,a.id";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }

    public function LoadRekapMaterial4Reports($entityId,$cabangId = 0, $startDate = null, $endDate = null) {
        $sql = "SELECT b.item_code,c.bnama as item_name,c.bsatkecil as satuan, sum(b.qty) as sum_qty,sum(b.qty*b.price) as sum_total";
        $sql.= " FROM vw_ic_assembly_master AS a Join t_ic_assembly_detail AS b on a.assembly_no = b.assembly_no Left Join m_barang AS c On b.item_code = c.bkode";
        $sql.= " WHERE a.is_deleted = 0 and a.assembly_date BETWEEN ?startdate and ?enddate";
        if ($cabangId > 0){
            $sql.= " and a.cabang_id = ".$cabangId;
        }else{
            $sql.= " and a.entity_id = ".$entityId;
        }
        $sql.= " Group By b.item_code,c.bnama,c.bsatkecil Order By c.bnama,b.item_code";
        $this->connector->CommandText = $sql;
        $this->connector->AddParameter("?startdate", date('Y-m-d', $startDate));
        $this->connector->AddParameter("?enddate", date('Y-m-d', $endDate));
        $rs = $this->connector->ExecuteQuery();
        return $rs;
    }
}


// End of File: estimasi.php
