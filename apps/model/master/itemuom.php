<?php
class ItemUom extends EntityBase {
	public $Sid;
	public $IsDeleted = false;
	public $Skode;
	public $Snama;
    public $CreatebyId;
    public $UpdatebyId;

	public function __construct($sid = null) {
		parent::__construct();
		if (is_numeric($sid)) {
			$this->FindById($sid);
		}
	}

	public function FillProperties(array $row) {
		$this->Sid = $row["sid"];
		$this->IsDeleted = $row["is_deleted"] == 1;
		$this->Skode = $row["skode"];
		$this->Snama = $row["snama"];
        $this->CreatebyId = $row["createby_id"];
        $this->UpdatebyId = $row["updateby_id"];
	}

	/**
	 * @param string $orderBy
	 * @param bool $includeDeleted
	 * @return Location[]
	 */
	public function LoadAll($orderBy = "a.skode", $includeDeleted = false) {
		if ($includeDeleted) {
			$this->connector->CommandText = "SELECT a.* FROM m_satuan AS a ORDER BY $orderBy";
		} else {
			$this->connector->CommandText = "SELECT a.* FROM m_satuan AS a WHERE a.is_deleted = 0 ORDER BY $orderBy";
		}
		$rs = $this->connector->ExecuteQuery();
		$result = array();
		if ($rs != null) {
			while ($row = $rs->FetchAssoc()) {
				$temp = new ItemUom();
				$temp->FillProperties($row);
				$result[] = $temp;
			}
		}
		return $result;
	}

	/**
	 * @param int $sid
	 * @return Location
	 */
	public function FindById($sid) {
		$this->connector->CommandText = "SELECT a.* FROM m_satuan AS a WHERE a.sid = ?sid";
		$this->connector->AddParameter("?sid", $sid);
		$rs = $this->connector->ExecuteQuery();

		if ($rs == null || $rs->GetNumRows() == 0) {
			return null;
		}
		$row = $rs->FetchAssoc();
		$this->FillProperties($row);
		return $this;
	}

	/**
	 * @param int $sid
	 * @return Location
	 */
	public function LoadById($sid) {
		return $this->FindById($sid);
	}

	public function Insert() {
		$this->connector->CommandText = 'INSERT INTO m_satuan(skode,snama,createby_id,create_time) VALUES(?skode,?snama,?createby_id,now())';
		$this->connector->AddParameter("?skode", $this->Skode);
        $this->connector->AddParameter("?snama", $this->Snama);
        $this->connector->AddParameter("?createby_id", $this->CreatebyId);
        $rs = $this->connector->ExecuteNonQuery();
        if ($rs == 1) {
            $this->connector->CommandText = "SELECT LAST_INSERT_ID();";
            $this->Sid = (int)$this->connector->ExecuteScalar();
            $rs = $this->Sid;
        }
		return $rs;
	}

	public function Update($sid) {
		$this->connector->CommandText = 'UPDATE m_satuan SET skode = ?skode, snama = ?snama, updateby_id = ?updateby_id, update_time = now() WHERE sid = ?sid';
		$this->connector->AddParameter("?skode", $this->Skode);
        $this->connector->AddParameter("?snama", $this->Snama);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		$this->connector->AddParameter("?sid", $sid);
		return $this->connector->ExecuteNonQuery();
	}

	public function Delete($sid) {
		$this->connector->CommandText = 'UPDATE m_satuan SET is_deleted = 1,updateby_id = ?updateby_id, update_time = now() WHERE sid = ?sid';
		$this->connector->AddParameter("?sid", $sid);
        $this->connector->AddParameter("?updateby_id", $this->UpdatebyId);
		return $this->connector->ExecuteNonQuery();
	}

    public function Hapus($sid) {
        $this->connector->CommandText = 'Delete From m_satuan Where sid = ?sid';
        $this->connector->AddParameter("?sid", $sid);
        return $this->connector->ExecuteNonQuery();
    }

    public function GetData($offset,$limit,$field,$search='',$sort = 'a.skode',$order = 'ASC') {
        $sql = "SELECT a.* FROM m_satuan as a Where a.is_deleted = 0 ";
        if ($search !='' && $field !=''){
            $sql.= "And $field Like '%{$search}%' ";
        }
        $this->connector->CommandText = $sql;
        $data['count'] = $this->connector->ExecuteQuery()->GetNumRows();
        $sql.= "Order By $sort $order Limit {$offset},{$limit}";
        $this->connector->CommandText = $sql;
        $rs = $this->connector->ExecuteQuery();
        $rows = array();
        if ($rs != null) {
            $i = 0;
            while ($row = $rs->FetchAssoc()) {
                $rows[$i]['sid'] = $row['sid'];
                $rows[$i]['skode'] = $row['skode'];
                $rows[$i]['snama'] = $row['snama'];
                $i++;
            }
        }
        //data hasil query yang dikirim kembali dalam format json
        $result = array('total'=>$data['count'],'rows'=>$rows);
        return $result;
    }

}
