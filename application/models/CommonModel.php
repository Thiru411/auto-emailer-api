<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class CommonModel extends CI_Model{
	
    public function __construct() 
	{
		parent::__construct();
		$this->load->library("session");
		$this->load->library('form_validation');	
	}
	 
	 /*********************** Mail Integration *********************************/

	 public function sendEmailOne($tomail,$subject,$body){
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'ssl://smtp.gmail.com';
		$config['smtp_user'] = 'thirucshh411@gmail.com';
		$config['smtp_pass'] = 'xmgusnumvwjlptvh';
		$config['smtp_port'] = 465;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
	 
		$this->email->set_newline("\r\n");
		$this->email->from('thirucshh411@gmail.com', 'Auto-emailer');
		$this->email->to($tomail);
		$this->email->subject($subject);
		$this->email->message($body);
		$result = $this->email->send();
		if($result){
			return $result;	
		}else{
		  return $this->email->print_debugger();
		}
		
	}
	function callAPI($method, $url, $data, $headers = false) {
        
        $curl = curl_init();
        
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); }
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
    
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        if(!$headers){
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'APIKEY: 111111111111111111111',
            'Content-Type: application/json',
            ));
        }else{
            curl_setopt($curl, CURLOPT_HTTPHEADER,$headers );
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        
        /* curl_setopt($curl, CURLOPT_PROXY, "192.168.201.77");
        curl_setopt($curl, CURLOPT_PROXYPORT, "8080"); */
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    
        // EXECUTE:
        $result = curl_exec($curl);
		
		 
        if(!$result){die("Connection Failure");}
		
        curl_close($curl);
        //print_r("++++".$data);
        return $result;
    }	
/*********************** Mail Integration *********************************/
     /*********************** Crud Opertaions *********************************/
	function Save($data,$table)
	{
		$this->db->insert($table,$data);
		return $this->db->insert_id();
	}

	function Update($table,$cond,$email,$data)
	{
		$this->db->where($cond,$email);
		$this->db->update($table,$data);
	}
	function Update2($table,$cond,$email,$cond1,$email1,$data)
	{
		$this->db->where($cond,$email);
		$this->db->where($cond1,$email1);
		$this->db->update($table,$data);
	}
	function Delete($table,$idcolumn,$idvalue)
	{
		$query=$this->db->query("delete from $table where $idcolumn='$idvalue'");
	}
	public function getAllRecords($where,$table,$order_column,$order_type) {
		$this->db->select("*");
		$this->db->order_by($order_column, $order_type);
		$query = $this->db->get_where($table, $where);
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		else {
			return false;
		}
	}
	public function getRecords($where,$table) {
		$this->db->select("*");
		$query = $this->db->get_where($table, $where);
		
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		else {
			return false;
		}
	}
	public function getSelectedRecords($where,$table,$selected) {
		$this->db->select($selected);
		$query = $this->db->get_where($table, $where);
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		else {
			return false;
		}
	}
	function updateRecords($data,$where,$table) {
		$this->db->where($where);
		$this->db->update($table,$data);
		if($this->db->affected_rows()>0) {
			return true;
		}
		else {
			return false;
		}
	}
	function getMax($table,$id) {
		$maxID=0;
		$sql = "SELECT max($id) as $id FROM $table ";
		$binds = array();
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			$max=$query->result();
			foreach ($max as $info){
				$maxID=$info->$id;
			}
			return $maxID++;
		} else {
			return $maxID;
		}
	}
	
	function GetTableData($table,$idcolumn,$idvalue,$statuscolumn,$statusvalue)
	{
		if($idcolumn!="" && $statuscolumn!="")
		{
			$query=$this->db->query("select * from $table where $idcolumn='$idvalue' and $statuscolumn='$statusvalue'");
			$result = $query->result();
			return $result;
		}
		else if($idcolumn=="" && $statusvalue!="")
		{
			$query=$this->db->query("select * from $table where $statuscolumn='$statusvalue' ");
			$result = $query->result();
			return $result;
		}
		else
		{
			$query=$this->db->query("select * from $table order by $statuscolumn DESC");
			$result = $query->result();
			return $result;
		}	
	}
 
	
	function GetJoinTableData($from,$join,$where)
	{
		    $query=$this->db->query("$from $join $where");
			$result = $query->result();
			return $result;
	}
	function login($email, $password)
	{
		$query=$this->db->query("select * from mst_employee where email='$email' and password='$password' and employee_status='1'");
		$result = $query->result();
		return $result;
	}
	
	public function DataUpdate($data,$idcolumn,$idvalue,$table)
	{
		$this -> db -> select('*');
		$this -> db -> from($table);
		$this -> db -> where($idcolumn,$idvalue);
		$query = $this -> db -> get();
		if($query -> num_rows() > 0)
		{
			$this->db->where($idcolumn,$idvalue);
			$this->db->update($table,$data);
		}
		else
		{
			$id=$this->db->insert($table, $data);
			return $this->db->insert_id();
		}
	}
	
	function GetTotalCount($table,$idcolumn,$idvalue,$idcolumn1,$idvalue1,$statuscolumn,$statusvalue)
	{
		$query=$this->db->query("select * from $table where $idcolumn='$idvalue' and $idcolumn1='$idvalue1' and $statuscolumn='$statusvalue'");
			$result = $query->result();
			return $result;
	}
	
	function getRecordsQuery($sql,$binds){
		$query = $this->db->query($sql, $binds);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	function getRecordsCountQuery($sql,$binds){
		$query = $this->db->query($sql, $binds);
		if ($query->num_rows() > 0) {
			return $query->num_rows();
		} else {
			return false;
		}
	}
	function getRecordsRow($sql,$binds){
		$query = $this->db->query($sql, $binds);
		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			return false;
		}
	} 
function get_continet_details($sk_continent_id){
if($sk_continent_id=='ALL'){
	$query=$this->db->query("select * from  mst_continent");
	$result = $query->result();
	return $result;

}
else{
	$query=$this->db->query("select * from  mst_continent where sk_continent_id='$sk_continent_id'");
	$result = $query->result();
	return $result;

}
}
public function geInventory($where,$table,$view_type) {
	if($view_type=='Inventory'){
	$this->db->select("*");
	$this->db->join('mst_category_type', 'sk_category_id = category_id', 'left');
	$this->db->join('mst_location', 'sk_location_id = location_id', 'left');
	$this->db->order_by('title','asc');

	$query=$this->db->get_where($table, $where);
	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return false;
	}
	}
	elseif($view_type=='CategoryType'){
		$this->db->select("*");
		$query=$this->db->get_where($table, $where);

	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return false;
	}
	}
	else{
		$query=$this->db->query("select availability, category_type,quantity_status,location_name,sk_inventory_id,matuare_size,inventory,title,inventory_date,zone,location_id,category_id,description,inventory_date,inventory_status,photo_video from mst_inventory join mst_category_type on mst_inventory.category_id=mst_category_type.sk_category_id join mst_location on mst_inventory.location_id=mst_location.sk_location_id where mst_inventory.category_id='$where'");
	    return $query->result();
	}
}
public function getNotifications($where,$table){
	$query=$this->db->query("select * from $table where notifications_status='1' order by created_date DESC");
	    return $query->result();
		// 	$this->db->select("*");
		// 	$this->db->order_by("created_at","DESC");
		// $query = $this->db->get_where($table, $where);
		// if ($query->num_rows() > 0) {
		// 	return $query->result();
		// }
		// else {
		// 	return false;
		// }

}
public function getonsite($where,$table,$view_type) {
	$query=$this->db->query("select availability, category_type,quantity_status,location_name,sk_inventory_id,matuare_size,inventory,title,inventory_date,zone,location_id,category_id,description,inventory_date,inventory_status,photo_video from mst_inventory join mst_category_type on mst_inventory.category_id=mst_category_type.sk_category_id join mst_location on mst_inventory.location_id=mst_location.sk_location_id where mst_inventory.category_id='$where' and availability='$view_type'");
	return $query->result();
}
	public function tagRecordsUpdate($data,$where,$table,$update_type){
		if($update_type=='Delete'){
			$this->db->where($where);
			$query=$this->db->delete($table);
			if($this->db->affected_rows()>0) {
				return true;
			}
			else {
				return false;

		}
	}
		elseif($update_type=='Edit'){
			$this->db->where($where);
				$this->db->update($table,$data);
				if($this->db->affected_rows()>0) {
					return true;
				}
				else {
					return false;
		}
		}
		else{
			$this->db->where($where);
				$this->db->update($table,$data);
				if($this->db->affected_rows()>0) {
					return true;
				}
				else {
					return false;
		}
		}
	}


	
public function getLibrary($where,$table,$view_type) {
	if($view_type=='Saved'){
		$this->db->select("*");
		$this->db->group_by('inventory_id'); 
		$this->db->order_by('title','ASC'); 
	$query=$this->db->get_where($table, $where);
	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return false;
	}
	}
	else if($view_type=='Tagged'){
		$this->db->select("*");
		$this->db->group_by('inventory_id'); 

		 $query=$this->db->get_where($table,$where);

	if ($query->num_rows() > 0) {
		 return $query->result();
	}
	else {
		return false;
	}
	}
	else if($view_type=='TaggInventory'){
		$this->db->select("*");
		 $query=$this->db->get_where($table,$where);

	if ($query->num_rows() > 0) {
		 return $query->result();
	}
	else {
		return false;
	}
	}
	else if($view_type=='Project'){
		$this->db->select("*");
		$query=$this->db->get_where($table, $where);

	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return false;
	}
	}
}
public function getpartners($where,$table){
	$this->db->select("*");
		$query=$this->db->get_where($table, $where);
		$this->db->order_by('partner_name','ASC'); 

	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return false;
	}
}
public function deleteRecords($where,$table){
	$this -> db -> where($where);
    $query=$this -> db -> delete($table);
	return $query;
}
public function updateDeviceId($where,$table){
	$this -> db -> where($where);
    $query=$this -> db -> delete($table);
	return $query;
}


public function getDepositeUsers()
{
    $query=$this->db->query("SELECT * FROM `mst_deposits` a,mst_user b WHERE a.deposite_status='1' and a.user_id=b.sk_user_id and b.user_type!='admin' order by('ASC')");
    $result = $query->result();
    return $result;
}


public function inventorybycategory($category_id,$table)
{
    $query=$this->db->query("select * from mst_inventory where category_id like '%$category_id%' and inventory_status='1'");
    $result = $query->result();
    return $result;
}


}
?>



