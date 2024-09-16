<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
		public function __construct(){
			parent::__construct();     
			$this->load->model('AdminModel','am',TRUE); 
			$this->load->model('CommonModel','cm',TRUE);
			$this->load->library('session');
			$this->load->helper('url');
			$this->load->helper("jwt_helper");
			$this->load->library('Common');
		} 
		/* =======================Common Methods====================== */
		public function index() {
		echo "Hi version 1 in index method";
		}
		
		public function encryption($payload) {
			return $encryptedId = JWT::encode($payload,pkey);
		}
		public function decryption($cipher) {
			return $encryptedId = JWT::decode($cipher,pkey);
		} 
	
		public function common_data() {
			$this->am->getrecordsoftrue(); 
			date_default_timezone_set('Asia/Kolkata');
			$data["date"]=date('Y-m-d');
			$data["time"]=date("h:i:sa");
			$data['date_india']=date('d-m-y');
			
			return $data;
		}
		
		
		public function access_control() {
			header("access-control-allow-credentials:true");
			header("access-control-allow-headers:Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token");
			header("access-control-allow-methods:POST, GET, OPTIONS");
			//header("access-control-allow-origin:".$_SERVER['HTTP_ORIGIN']);
			header("access-control-expose-headers:AMP-Redirect-To,AMP-Access-Control-Allow-Source-Origin");
			// header("amp-access-control-allow-source-origin:".$_SERVER['HTTP_ORIGIN']);
			header("Content-Type: application/json");
			header("AMP-Same-Origin: true");
		
			header("Access-Control-Max-Age: 600");    // cache for 10 minutes
		
			if(isset($_SERVER["HTTP_ORIGIN"]))
			{
				// You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
				header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			}
			else
			{
				//No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
				header("Access-Control-Allow-Origin: *");
			}
		
			if($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
				if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
					header("access-control-allow-methods:POST, GET, OPTIONS"); //Make sure you remove those you do not want to support
		
				if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
		
					header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		
				//Just exit with 200 OK with the above headers for OPTIONS method
				exit(0);
			}
		}
	

	

	/************************Signin********************/
	public function signin() {
		$this->access_control();
		$commonData=$this->common_data();
		$today = $commonData['date'];
		$access_token = false;
		$row=$this->input->request_headers();
		if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken']; }
		$data=array();$ret=array();
		if($access_token){
			try {
				if($access_token==globalAccessToken) {
					$params = array();
					$email = null;
					$password = null;$user_type="";
					$user_role=$user_status=$userid=$user_status=$name=$photo='';
					$params = json_decode(@file_get_contents('php://input'),TRUE);
					if(isset($params)) {
						if(isset($params['email'])) { $email = $params['email'];}
						if(isset($params['password'])) { $password = $params['password'];}
						// if(isset($params['user_type'])) { $user_type = $params['user_type'];}
						if($email!=null && $password!=null) {
							try {
								$where=array('email'=>$email,'user_password'=>md5($password),'user_role_col'=>'admin');
								$isExits = $this->cm->getRecords($where,'mst_users');
								if($isExits!=false) {
									foreach($isExits as $inf)
									{
										$name=$inf->full_name;
										if($inf->user_pic!=null){
											$photo=$inf->user_pic;
										}
										$profile_pic=$photo;
										$email=$inf->email;
										$userid=$inf->sk_user_id;
									}
									$response = array(
											'name'=>$name,
											'profile_pic'=>$profile_pic,
											'email'=>$email,
											'userid'=>$userid,
											'Accesstoken'=>JWT::encode($userid,pkey)
									);
									$update_data=array("lastlogin"=>date("Y-m-d H:i:s"));
									$this->cm->Update('mst_users','sk_user_id',$userid,$update_data);								$ret=$this->common->response(200,true,'Success',$response);
								
							}
								else {
									
									$ret=$this->common->response(400,false,'Inavlid username/password',array());
								}
							}
							catch(Exception $e) {
	
								$msg = "";
								$eMessage = $e->getMessage();
								$eMessage = explode('/',$eMessage);
								$eMessage = explode(':',$eMessage[0]);
								if($eMessage[1]==1062) {
									$msg = "Duplicate Entry";
								}
								else if($eMessage[1]==1452) {
									$msg = "Foreign key constraint fails";
								}
								else  {
									$msg = "Database error";
								}
	
								$ret=$this->common->response(400,false,$msg,array());
							}
						}
						else {
							
							$ret=$this->common->response(400,false,'please check the input key and value',array());
						}	
					}
					else {
						
						$ret=$this->common->response(400,false,'please check the input ',array());
					}
				}
				else {
					
					$ret=$this->common->response(400,false,'Invalid Access Token',array());
				}
			}
			catch (Exception $e) {
				
				$ret=$this->common->response(400,false,'Invalid Access Token',array());
			}
		}
		else {
			header('HTTP/1.1 400 Failure', false, 400);
			$ret = array(
					'status' => false,
					'message' => 'Invalid Access Token - please check access token both key and value',
					'data' => $data
	
			);
			$ret=$this->common->response(400,false,'Invalid Access Token - please check access token both key and value',array());
		}
		echo json_encode($ret);
	}
	
/*********************Signin**********************/





/******************forget password*************************************/


public function password_operations(){
	$this->access_control();
	$commonData=$this->common_data();
	$access_token = false;
	$city_id="";
	$city_status="";
	$row=$this->input->request_headers();
	if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken'];}
	$data=array();$ret=array();
	if($access_token){
		try {
			if($access_token==globalAccessToken){				
				if ($this->input->server('REQUEST_METHOD') === 'GET')
				{
					$params = array();
					$email="";
					$params = json_decode(@file_get_contents('php://input'),TRUE);
					if(isset($params)) {
						if(isset($params['email'])) { $email = $params['email'];} 
						if($email!=null) {
							try {
								$where=array('email'=>$email);
								$isExits = $this->cm->getRecords($where,'mst_users');
								if($isExits!=false) {

									foreach($isExits as $inf)
									{
										$name=$inf->full_name; 
										$userid=$inf->sk_user_id;
									}
									$response = array(
										'email'=>$email,
										'Accesstoken'=>JWT::encode($userid,pkey)
								);
									$resetlink='<p><a href='.reset_pass_link.'?mail='.$email.'>Click Here</a></p>';
								//$message="Hi $name Click the link $reset_link to complete the reset and your OTP to reset password is $reset_otp";
									$message="Hi $name, The reset password link is here $resetlink";
								 	$email_status=$this->cm->sendEmailOne($email,'Reset Password Link',$message); 
									$ret=$this->common->response(200,true,'Success',$response);										
								}
								else {
									$ret=$this->common->response(200,false,'Invalid Email Id',array());
								}
							}
								
							catch(Exception $e) {
	
								$msg = "";
								$eMessage = $e->getMessage();
								$eMessage = explode('/',$eMessage);
								$eMessage = explode(':',$eMessage[0]);
								if($eMessage[1]==1062) {
									$msg = "Duplicate Entry";
								}
								else if($eMessage[1]==1452) {
									$msg = "Foreign key constraint fails";
								}
								else  {
									$msg = "Database error";
								}
	
								$ret=$this->common->response(400,false,$msg,array());
							}
						}
						
						else {
							
							$ret=$this->common->response(400,false,'please check the input key and value',array());
						}	
					}
					else {
						$ret=$this->common->response(400,false,'Please check the input',$data);
					}
				}elseif ($this->input->server('REQUEST_METHOD') === 'PUT'){
					$params = array();

					$email=$new_password=$old_password="";
					$params = json_decode(@file_get_contents('php://input'),TRUE);
					if(isset($params)) {
						if(isset($params['email'])) { $email = $params['email'];} 
						if(isset($params['new_password'])) { $new_password = $params['new_password'];}  
						if(isset($params['old_password'])) { $old_password = $params['old_password'];}  
						if($old_password!='' && $new_password!='' && $email!='') {
							try {
								if($old_password==$new_password){
								$where=array('email'=>$email);
								$isExits = $this->cm->getRecords($where,'mst_users');
								if($isExits!=false) {
									foreach($isExits as $inf)
									{
										$name=$inf->full_name; 
										$userid=$inf->sk_user_id;
									}
									$update_data=array("user_password"=>md5($new_password));
									$this->cm->Update('mst_users','sk_user_id',$userid,$update_data);
									$response = array(
										'email'=>$email,
										'Accesstoken'=>JWT::encode($userid,pkey)
								);
								$ret=$this->common->response(200,true,'Password Updated Successfully',$response);										
								}
								else {
									$ret=$this->common->response(200,false,'User not Exists',array());
								}
							}else{
								$ret=$this->common->response(400,false,'Repeat Password not Matched',array());
							}
							}
							catch(Exception $e) {
								$msg = "";
								$eMessage = $e->getMessage();
								$eMessage = explode('/',$eMessage);
								$eMessage = explode(':',$eMessage[0]);
								if($eMessage[1]==1062) {
									$msg = "Duplicate Entry";
								}
								else if($eMessage[1]==1452) {
									$msg = "Foreign key constraint fails";
								}
								else  {
									$msg = "Database error";
								}
	
								$ret=$this->common->response(400,false,$msg,array());
							}
						}
						
						else {
							
							$ret=$this->common->response(400,false,'please check the input key and value',array());
						}	
					}
					else {
						$ret=$this->common->response(400,false,'Please check the input',$data);
					}				
				}else{
					$ret=$this->common->response(400,false,'Please Check Method',$data);
				}
			}
			else {
				$ret=$this->common->response(400,false,'Invalid Access Token',$data);
			}
		}
		catch (Exception $e) {
			$ret=$this->common->response(400,false,'Invalid Access Token',$data);
		}
	}
	else {
		$ret=$this->common->response(400,false,'Invalid Access Token - please check access token both key and value',$data);  
	}
	echo json_encode($ret);

} 
/******************forget password*************************************/



/*****************************************language***************/
/*****************************************language***************/

/*****************************************country***************/

public function common_details(){
	$this->access_control();
	$commonData=$this->common_data();
	$access_token = false;
	$country_id="";
	$country_status=$table="";
	$country['country_details']=array();
	$row=$this->input->request_headers();
	if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken'];}
	$data=array();$ret=array();
	if($access_token){
		try {
			$plain_userid=JWT::decode($access_token,pkey);				 
			$where=array('sk_user_id'=>$plain_userid);
			$userExists=$this->cm->getRecords($where,'mst_users');
			//var_dump($userExists);
			if($userExists){
				
				
				if ($this->input->server('REQUEST_METHOD') === 'GET')
				{
					
					if(isset($row['table'])) {  $table = $row['table'];}

					 $common_details=$this->cm->getRecords(array('status'=>1),$table);
				 
					 
					if($common_details)
					{
						foreach($common_details as $info1)
						{  
							if($table=='mst_country'){
								$country_info['sk_id']=$info1->sk_country_id;
								$country_info['sk_name']=$info1->country_name;
							}else{
								$country_info['sk_id']=$info1->sk_language_id;
								$country_info['sk_name']=$info1->language;
							}
							$temp[]=$country_info;
						}
						$country['common_details']=$temp;
						$ret=$this->common->response(200,true,'common Details',$country);   
					}
					else{
						$ret=$this->common->response(200,false,'No Data Available',array()); 
					 }
				}
			
			else {
				$ret=$this->common->response(400,false,'Invalid Access Token',$data);
			}
		}else{
			$ret=$this->common->response(400,false,'User not existed',$data);
		}
	}
		catch (Exception $e) {
			$ret=$this->common->response(400,false,'Invalid Access Token',$data);
		}
	}
	else {
		$ret=$this->common->response(400,false,'Invalid Access Token - please check access token both key and value',$data);  
	}
	echo json_encode($ret);

}
/*****************************************country***************/

public function commonMethod(){
	$this->access_control();
	$commonData=$this->common_data();
	$access_token = false;
	$country_id="";
	$country_status=$table=$user_status=$fieldName='';
	$country['commonDetails']=array();
	$row=$this->input->request_headers();
	if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken'];}
	$data=array();$ret=array();
	if($access_token){
		try {
			$plain_userid=JWT::decode($access_token,pkey);				 
			$where=array('sk_user_id'=>$plain_userid);
			$userExists=$this->cm->getRecords($where,'mst_users');
			//var_dump($userExists);
			if($userExists){
				
				
				if ($this->input->server('REQUEST_METHOD') === 'GET')
				{
					if(isset($row['table'])) {  $table = $row['table'];}
					if(isset($row['user_status'])) {  $user_status = $row['user_status'];}
					if(isset($row['field_name'])) {  $Fieldname = $row['field_name'];}
					 $common_details=$this->cm->getRecords(array($Fieldname=>$user_status),$table);
					if($common_details)
					{
						foreach($common_details as $info1)
						{  
							if($table=='mst_users'){
								$temp['name']=$info1->full_name;
								$temp['photo']=image_url.'users/'.$info1->user_pic;
								$temp['email']=$info1->email;
								$temp['user_status']=$info1->user_status;
								$temp['userid']=$info1->sk_user_id;
								$temp['address']=$info1->address;
								$temp['address2']=$info1->address2;
								$temp['description']=$info1->description;
								$temp['town']=$info1->town;
								$temp['state']=$info1->state;
								$temp['post_code']=$info1->post_code;
								$temp['last_login']=$info1->lastlogin;
								$user_roles=$this->cm->getRecords(array('sk_role_id'=>$info1->user_role),'mst_role');
								$temp['created_at']=$info1->create_date;                                  
								$temp1[]=$temp;
							}
							else if($table=='mst_customers'){
								$temp['name']=$info1->full_name;
								$temp['email']=$info1->email;
								$temp['userid']=$info1->sk_customer_id;
								$temp['userid']=$info1->sk_customer_id;
								$temp['phonenumber']=$info1->pocnumber;
								$temp['cust_country']=$info1->cust_country;
								$temp['created_at']=$info1->create_date;
								$temp['customer_status']=$info1->customer_status;
								$temp1[]=$temp;
							}else{

								$temp['sk_project_id']=$info1->sk_project_id;
								$temp['email']=$info1->email;
								$temp['project_status']=$info1->project_status;
								$temp['country_name']=$info1->country_name;
								if($info1->country_name==null){
									$temp['country_name']="";
								}else{
									$country=$this->cm->getRecords(array("sk_country_id"=>$info1->country_name),'mst_country');
									$temp['country_name']=$country[0]->country_name;
								} 
								if($info1->client==null){
									$temp['client']="";
								}else{
									$customer=$this->cm->getRecords(array("sk_customer_id"=>$info1->client),'mst_customer');
									if($customer){
									$temp['client']=$customer[0]->full_name;
									}else{
										$temp['client']="";   
									}
								}   
								$temp['code']=$info1->code;
								$temp['project_name']=$info1->project_name;
								$temp['created_at']=$info1->created_date;     
								$temp['selected_format']=$info1->selected_format;                                                               
								$temp1[]=$temp;
							}
						}
						$temp2['common_details']=$temp1;
						$ret=$this->common->response(200,true,'common Details',$temp2);   
					}
					else{
						$ret=$this->common->response(200,false,'No Data Available',array()); 
					 }
				}
			
			else {
				$ret=$this->common->response(400,false,'Invalid Access Token',$data);
			}
		}else{
			$ret=$this->common->response(400,false,'User not existed',$data);
		}
	}
		catch (Exception $e) {
			$ret=$this->common->response(400,false,'Invalid Access Token1',$data);
		}
	}
	else {
		$ret=$this->common->response(400,false,'Invalid Access Token - please check access token both key and value',$data);  
	}
	echo json_encode($ret);

}

}
