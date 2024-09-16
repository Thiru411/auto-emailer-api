<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class AdminModel extends CI_Model{
	public function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		$this->load->library('form_validation');
	}
	 
	function getCategory($category_id)
	{
		$query=$this->db->query("select * from mst_sub_category where sk_sub_category_id in ($category_id) ");
		$result = $query->result();
		return $result;
	}

	function getEventDetails($user_id,$event_id,$event_status,$event_type,$event_view,$event_date)
	{
		if($event_view=="EVENT_VIEW"){
			if($event_id=="All"){
				if($event_status=="All" && $user_id=="All")
				{
					$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id");
				}
				if($event_status=="All" && $user_id!="All")
				{
					$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id where mst_event.user_id=$user_id");
				}
				else if($event_status!="All" && $user_id=="All") {
					$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id WHERE mst_event.event_status='$event_status'");
				}
				else if($event_status!="All" && $user_id!="All") {
					$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id WHERE mst_event.event_status='$event_status' and mst_event.user_id=$user_id");
				}
			}
			else {
				if($event_status=="All" && $user_id=="All")
				{
					$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id where sk_event_id=$event_id");
				}
				else if($event_status=="All" && $user_id!="All")
				{
					$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id where sk_event_id=$event_id and mst_event.user_id=$user_id");
				}
				else if($event_status!="All" && $user_id=="All"){
					$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id WHERE mst_event.event_status='$event_status' and sk_event_id=$event_id");
				}
				else if($event_status!="All" && $user_id!="All"){
					$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id WHERE mst_event.event_status='$event_status' and sk_event_id=$event_id and mst_event.user_id=$user_id");
				}
			}
		}
	else if($event_view=="Location"){
		
		if($event_type=="Past Events")
		{
			if($event_id!='All'){
				$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id WHERE   mst_event.location_id=$event_id and mst_event.event_date < '$event_date'");
			}
			else{
				$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id WHERE  mst_event.event_date < '$event_date'");
			}
			
		}
		else if($event_type=="Current Events"){
			if($event_id!='All'){
				$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id WHERE  mst_event.location_id=$event_id and mst_event.event_date >='$event_date'");
			}
			else{
				$query=$this->db->query("SELECT mst_event.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name,mst_location.location_name,mst_sub_category.sub_category_name FROM `mst_event` LEFT JOIN mst_continent ON mst_continent.sk_continent_id=mst_event.continent_id  LEFT JOIN mst_country ON mst_country.sk_country_id=mst_event.country_id   LEFT JOIN mst_state ON mst_state.sk_state_id=mst_event.state_id LEFT JOIN mst_city on mst_city.sk_city_id=mst_event.city_id LEFT JOIN mst_location on mst_location.sk_location_id=mst_event.location_id LEFT JOIN mst_sub_category on mst_sub_category.sk_sub_category_id=mst_event.category_id WHERE  mst_event.event_date >= '$event_date'");
			}
		}
	}

		$result = $query->result();
		return $result;

	}
	 function getCountryDetails($country_status,$country_id,$view_type)
	 { 
		if($view_type=="Continent"){
			
			$continent_id=$country_id;$sql="SELECT mst_country.*,mst_continent.continent_name FROM mst_country,mst_continent where mst_country.continent_id=mst_continent.sk_continent_id and mst_country.continent_id=$continent_id and mst_country.country_status=$country_status order by mst_country.country_status desc"; 
		}
		else if($view_type=="Country"){  
		if($country_id=="All")
			{
				if($country_status=="All"){
					$sql="SELECT mst_country.*,mst_continent.continent_name FROM mst_country,mst_continent where mst_country.continent_id=mst_continent.sk_continent_id order by mst_country.country_status desc";
				}else{ 
					$sql="SELECT mst_country.*,mst_continent.continent_name FROM mst_country,mst_continent where mst_country.continent_id=mst_continent.sk_continent_id and mst_country.country_status=$country_status order by mst_country.country_status desc"; 
				}				
			}
			else
			{
				if($country_status=="All"){
					$sql="SELECT mst_country.*,mst_continent.continent_name FROM mst_country,mst_continent where mst_country.continent_id=mst_continent.sk_continent_id  and mst_country.sk_country_id=$country_id order by mst_country.country_status desc";
				}else{
					$sql="SELECT mst_country.*,mst_continent.continent_name FROM mst_country,mst_continent where mst_country.continent_id=mst_continent.sk_continent_id and mst_country.country_status=$country_status and mst_country.sk_country_id=$country_id order by mst_country.country_status desc";
				}
			}
		}
			$query=$this->db->query($sql);
			$result = $query->result();
			return $result;
	 }
	 function getStateDetails($state_status,$state_id,$view_type)
	 {
		if($view_type=="Country"){
			
			$country_id=$state_id;$sql="SELECT mst_state.*,mst_continent.continent_name,mst_country.country_name FROM mst_state,mst_country,mst_continent where mst_state.continent_id=mst_continent.sk_continent_id  and mst_state.country_id=mst_country.sk_country_id and mst_state.country_id=$country_id and mst_state.state_status=$state_status order by mst_state.state_status desc"; 
		}
		else if($view_type=="States"){ 
		if($state_id=="All")
		{
			if($state_status=="All"){
				$sql="SELECT mst_state.*,mst_continent.continent_name,mst_country.country_name FROM mst_state,mst_country,mst_continent where mst_state.continent_id=mst_continent.sk_continent_id  and mst_state.country_id=mst_country.sk_country_id order by mst_state.state_status desc";
			}else{ 
				$sql="SELECT mst_state.*,mst_continent.continent_name,mst_country.country_name FROM mst_state,mst_country,mst_continent where mst_state.continent_id=mst_continent.sk_continent_id  and mst_state.country_id=mst_country.sk_country_id and mst_state.state_status=$state_status order by mst_state.state_status desc"; 
			}				
		}
		else
		{
			if($state_status=="All"){
				$sql="SELECT mst_state.*,mst_continent.continent_name,mst_country.country_name FROM mst_state,mst_country,mst_continent where mst_state.continent_id=mst_continent.sk_continent_id  and mst_state.country_id=mst_country.sk_country_id and mst_state.sk_state_id=$state_id order by mst_state.state_status desc";
			}else{
				$sql="SELECT mst_state.*,mst_continent.continent_name,mst_country.country_name FROM mst_state,mst_country,mst_continent where mst_state.continent_id=mst_continent.sk_continent_id  and mst_state.country_id=mst_country.sk_country_id and mst_state.state_status=$state_status and mst_state.sk_state_id=$state_id order by mst_state.state_status desc";
			}
		}
	}
		$query=$this->db->query($sql);
		$result = $query->result();
		return $result;
	 }
	 function getCityDetails($city_status,$city_id,$view_type)
	 {
		if($view_type=="States"){
			
			$state_id=$city_id;
			$sql="SELECT mst_city.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name FROM mst_city,mst_state,mst_country,mst_continent where mst_city.state_id=mst_state.sk_state_id and mst_city.continent_id=mst_continent.sk_continent_id and mst_city.country_id=mst_country.sk_country_id and mst_city.state_id=$state_id and mst_city.city_status=$city_status order by mst_city.city_status desc"; 
		}
		else if($view_type=="City"){ 
		if($city_id=="All")
		{
			if($city_status=="All"){
				$sql="SELECT mst_city.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name FROM mst_city,mst_state,mst_country,mst_continent where mst_city.state_id=mst_state.sk_state_id and mst_city.continent_id=mst_continent.sk_continent_id and mst_city.country_id=mst_country.sk_country_id order by mst_city.city_status desc";
			}else{ 
				$sql="SELECT mst_city.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name FROM mst_city,mst_state,mst_country,mst_continent where mst_city.state_id=mst_state.sk_state_id and mst_city.continent_id=mst_continent.sk_continent_id and mst_city.country_id=mst_country.sk_country_id and mst_city.city_status=$city_status order by mst_city.city_status desc"; 
			}				
		}
		else
		{
			if($city_status=="All"){
				$sql="SELECT mst_city.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name FROM mst_city,mst_state,mst_country,mst_continent where mst_city.state_id=mst_state.sk_state_id and mst_city.continent_id=mst_continent.sk_continent_id and mst_city.country_id=mst_country.sk_country_id  and mst_state.country_id=mst_country.sk_country_id and mst_city.sk_city_id=$city_id order by mst_city.city_status desc";
			}else{
				$sql="SELECT mst_city.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name FROM mst_city,mst_state,mst_country,mst_continent where mst_city.state_id=mst_state.sk_state_id and mst_city.continent_id=mst_continent.sk_continent_id and mst_city.country_id=mst_country.sk_country_id and mst_city.city_status=$city_status and mst_city.sk_city_id=$city_id order by mst_city.city_status desc";
			}
		}
	}
		$query=$this->db->query($sql);
		$result = $query->result();
		return $result;
	 }
function getLocationDetails($location_status,$location_id,$view_type){
	  
	if($view_type=="city"){
			
		$state_id=$location_id;
		$sql="SELECT mst_location.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name FROM mst_location,mst_city,mst_state,mst_country,mst_continent where mst_location.city_id=mst_city.sk_city_id and mst_location.continent_id=mst_continent.sk_continent_id and mst_location.country_id=mst_country.sk_country_id and mst_location.city_id=$state_id and mst_location.location_status=$location_status order by mst_location.location_status desc"; 
	}
	else if($view_type=="location"){ 
	if($location_id=="All")
	{
		if($location_status=="All"){
			$sql="SELECT mst_location.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name FROM  mst_location,mst_city,mst_state,mst_country,mst_continent  where mst_location.continent_id=mst_continent.sk_continent_id and mst_location.country_id=mst_country.sk_country_id and mst_location.state_id=mst_state.sk_state_id and mst_location.city_id=mst_city.sk_city_id ORDER BY mst_location.location_status DESC";
		}else{ 
			$sql="SELECT mst_location.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name FROM  mst_location,mst_city,mst_state,mst_country,mst_continent  where mst_location.continent_id=mst_continent.sk_continent_id and mst_location.country_id=mst_country.sk_country_id and mst_location.state_id=mst_state.sk_state_id and mst_location.city_id=mst_city.sk_city_id and mst_location.location_status=$location_status ORDER BY mst_location.location_status DESC"; 
		}				
	}
	else
	{
		if($location_status=="All"){
			$sql="SELECT mst_location.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name FROM  mst_location,mst_city,mst_state,mst_country,mst_continent  where mst_location.continent_id=mst_continent.sk_continent_id and mst_location.country_id=mst_country.sk_country_id and mst_location.state_id=mst_state.sk_state_id and mst_location.city_id=mst_city.sk_city_id and mst_location.sk_location_id=$location_id ORDER BY mst_location.location_status DESC"; 
		}else{
			$sql="SELECT mst_location.*,mst_continent.continent_name,mst_country.country_name,mst_state.state_name,mst_city.city_name FROM  mst_location,mst_city,mst_state,mst_country,mst_continent  where mst_location.continent_id=mst_continent.sk_continent_id and mst_location.country_id=mst_country.sk_country_id and mst_location.state_id=mst_state.sk_state_id and mst_location.city_id=mst_city.sk_city_id and mst_location.location_status=$location_status and mst_location.sk_location_id=$location_id ORDER BY mst_location.location_status DESC";
		}
	}
}
	$query=$this->db->query($sql);
	$result = $query->result();
	return $result;
 }
function getCategoryDetails($sub_category_status,$sub_category_id,$view_type)
{
	 

	if($view_type=="Category"){
			
		$category_id=$sub_category_id;
		$sql="SELECT mst_sub_category.*,mst_category.category_name FROM mst_sub_category,mst_category WHERE mst_sub_category.category_id=mst_category.sk_category_id and mst_sub_category.category_id=$category_id order by mst_sub_category.sub_category_status desc"; 
	}
	else if($view_type=="subcategory"){  
		if($sub_category_id=="All")
		{
			if($sub_category_status=="All"){
				$sql="SELECT mst_sub_category.*,mst_category.category_name FROM mst_sub_category,mst_category WHERE mst_sub_category.category_id=mst_category.sk_category_id order by mst_sub_category.sub_category_status desc";
			}else{ 
				$sql="SELECT mst_sub_category.*,mst_category.category_name FROM mst_sub_category,mst_category WHERE mst_sub_category.category_id=mst_category.sk_category_id and mst_sub_category.sub_category_status=$sub_category_status order by mst_sub_category.sub_category_status desc"; 
			}				
		}
		else
		{
			if($sub_category_status=="All"){
				$sql="SELECT mst_sub_category.*,mst_category.category_name FROM mst_sub_category,mst_category WHERE mst_sub_category.category_id=mst_category.sk_category_id and mst_sub_category.sk_sub_category_id=$sub_category_id order by mst_sub_category.sub_category_status desc";
			}else{
				$sql="SELECT mst_sub_category.*,mst_category.category_name FROM mst_sub_category,mst_category WHERE mst_sub_category.category_id=mst_category.sk_category_id and mst_sub_category.sub_category_status=$sub_category_status and mst_sub_category.sk_sub_category_id=$sub_category_id order by mst_sub_category.sub_category_status desc";
			}
		}
}


	
	 
		$query=$this->db->query($sql);
		$result = $query->result();
		return $result; 
}

function getDelete()
	{
		$query=$this->db->query("delete from mst_inventory_tag where tag_expire < now() - interval 10 DAY");
		
	}

	/*function getsearch($searchname)
	{
		$query=$this->db->query("SELECT * FROM `mst_category` LEFT JOIN mst_inventory on mst_inventory.category_id=mst_category.sk_category_id WHERE mst_inventory.title LIKE '%$searchname%' OR mst_category.category_name LIKE '%$searchname%'");
		$result = $query->result();
		return $result;
	} */

	function getsearch($searchname)
	{
		$query=$this->db->query("SELECT * FROM `mst_inventory` WHERE `title` LIKE '%$searchname%' and inventory_status=1 ");
		$result = $query->result();
		return $result;
	}


	function getTagged($user_id,$inv_id) 

	{
		$query=$this->db->query("SELECT * FROM `mst_inventory_tag` LEFT JOIN mst_inventory_price on mst_inventory_tag.inventory_price_id=mst_inventory_price.sk_inventory_price_id WHERE mst_inventory_tag.user_id=$user_id and mst_inventory_tag.inventory_tag_status='Tagged' AND mst_inventory_tag.inventory_id=$inv_id and mst_inventory_tag.no_of_tagged!='0'");
		$result = $query->result();
		return $result;
	}

	function getTaggedProject($inv_id) 

	{
		$query=$this->db->query("SELECT * FROM `mst_inventory` LEFT JOIN mst_inventory_tag on mst_inventory.sk_inventory_id=mst_inventory_tag.inventory_id LEFT JOIN mst_inventory_price ON mst_inventory_price.sk_inventory_price_id=mst_inventory_tag.inventory_price_id LEFT JOIN mst_category_type ON mst_category_type.sk_category_id=mst_inventory.category_id LEFT JOIN mst_location ON mst_location.sk_location_id=mst_inventory.location_id WHERE mst_inventory.sk_inventory_id=$inv_id and mst_inventory_tag.inventory_tag_status='Tagged' AND mst_inventory_tag.no_of_tagged!='0'");
		$result = $query->result();
		return $result;
	}

	function getBanner(){

		$query=$this->db->query("SELECT * FROM `mst_inventory` where sk_inventory_id limit 5");
		$result = $query->result();
		return $result;

	}



	function getSaved($user_id,$inv_id) 

	{
		$query=$this->db->query("SELECT mst_inventory_save.sk_save_id,mst_inventory_save.save_status,mst_inventory_save.inventory_id,mst_inventory_save.inventory_price_id,mst_inventory_price.sk_inventory_price_id,mst_inventory_price.callper, mst_inventory_price.price FROM `mst_inventory_save` LEFT JOIN mst_inventory_price on mst_inventory_save.inventory_price_id=mst_inventory_price.sk_inventory_price_id WHERE mst_inventory_save.user_id=$user_id and mst_inventory_save.inventory_id=$inv_id ");
		$result = $query->result();
		return $result;
	}

	public function getrecordsoftrue(){
		$this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		//$result = $query->result();
		return true;
	}


}


	