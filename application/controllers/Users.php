<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
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

        /*******************users***********************/



        public function users_details() {
            $this->access_control();
            $commonData=$this->common_data();
            $access_token = false;
            $row=$this->input->request_headers();
            if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken']; }
            $data=array();$ret=array();
            $temp=array();
            if($access_token!=globalAccessToken){
                try {
                    $user_type=$user_id=$user_status='';
                    $plain_userid=JWT::decode($access_token,pkey);				 
                    $where=array('sk_user_id'=>$plain_userid);
                    $userExists=$this->cm->getRecords($where,'mst_users');
                    if($userExists){
                        if ($this->input->server('REQUEST_METHOD') === 'GET')
                        {
                           
                            if(isset($row['user_status']))
                            {
                                if($row['user_status']=="All"){ $user_status ="All";}
                                else{$user_status = $row['user_status'];}
                            }
                            if(isset($row['user_id']))
                            {
                                if($row['user_id']=="All"){ $user_id ="All";}
                                else{$user_id = $row['user_id'];}
                            }
                            $sql='';
                            if($user_id!='All'){
                                 $sql=$sql."sk_user_id=$user_id";
                            }
                            if($user_status!='All'){
                                $sql=$sql." and user_status=$user_status";
                            }
                            $binds=array();
                            if($user_status=='All' && $user_id=='All' ){
                                 $sql='select * from mst_users  order by user_status desc';
                            }else{
                                $sql="select * from mst_users where $sql order by user_status desc";
                            }
                            $temp2=array();
                            $userDetails=$this->cm->getRecordsQuery($sql,$binds);//getRecords($where,'mst_user');
                            if($userDetails)
                            {
                                foreach($userDetails as $info1)
                                {  
                                    $temp['name']=$info1->full_name;
                                    $temp['photo']=image_url.'users/'.$info1->user_pic;
                                    $temp['email']=$info1->email;
                                    $temp['user_status']=$info1->user_status;
                                    $temp['userid']=$info1->sk_user_id;
                                    $temp['address']=$info1->address;
                                    if($info1->country==null){
                                        $temp['country']="";
                                    }else{
                                        $country=$this->cm->getRecords(array("sk_country_id"=>$info1->country),'mst_country');
                                        if($country){
                                            $temp['country']=$country[0]->country_name;
                                        }else{
                                            $temp['country']='';
                                        }
                                    }                                    
                                    if($info1->language==null){
                                        $temp['language']="";
                                    }else{
                                        $language=$this->cm->getRecords(array("sk_language_id"=>$info1->language),'mst_language');
                                        if($language){
                                        $temp['language']=$language[0]->language;
                                        }else{
                                            $temp['language']='';
 
                                        }
                                    }
                                    $temp['address2']=$info1->address2;
                                    $temp['description']=$info1->description;
                                    $temp['town']=$info1->town;
                                    $temp['state']=$info1->state;
                                    $temp['post_code']=$info1->post_code;
                                    $temp['last_login']=$info1->lastlogin;
                                    $user_roles=$this->cm->getRecords(array('sk_role_id'=>$info1->user_role),'mst_role');
                                    if($user_roles!=false){
                                        $temp['user_role']=$user_roles[0]->role_name;
                                    }else{
                                        $temp['user_role']='';
                                    }
                                    $temp['created_at']=$info1->create_date;
                                    $org_details=$this->cm->getRecords(array('sk_org_id'=>$info1->org_id),'mst_org');
                                    if($org_details!=false){
                                        $temp['organization']=$org_details[0]->orn_name;
                                    }else{
                                        $temp['organization']='';
                                    }                                    
                                    $temp1[]=$temp;
                                }
                                $temp2['user_details']=$temp1;
                                $ret=$this->common->response(200,true,'user Details',$temp2);
                            }
                            else{
                                $ret=$this->common->response(200,false,'No Data Available',array());
                            }
                            }                            
                        elseif ($this->input->server('REQUEST_METHOD') === 'POST')
                        {

                            $params = array();
                            $avatar=$fullname=$email=$address=$phonenumber=$organization=$country=$role="";
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['avatar'])) { $avatar = $params['avatar'];} 
                                if(isset($params['fullname'])) { $fullname = $params['fullname'];}
                                if(isset($params['email'])) { $email = $params['email'];} 
                                if(isset($params['phonenumber'])) { $phonenumber = $params['phonenumber'];} 
                                if(isset($params['address'])) { $address = $params['address'];} 
                                if(isset($params['country'])) { $country = $params['country'];} 
                                if(isset($params['role'])) { $role = $params['role'];} 
                                $where=array('email'=>$email);
                                $user_details=$this->cm->getRecords($where,'mst_users');
                                if($user_details==false){
                                if($avatar!=''){
                                    if (!file_exists("uploads/users/")) {
                                        mkdir("uploads/users/", 0777, true);
                                    }           
                                    // $upload_folder = "uploads/gallery/";
                                $upload_folder = "uploads/users/";
                                $uniquesavename=array();        
                                $extensions = explode(';',$avatar);
                                $extension = explode('/',$extensions[0]);
                                $FileExtension = $extension[1];
        
                            //	echo $FileExtension; exit;
                                $detail_title="";
                                if($FileExtension=="png" || $FileExtension=="jpg" || $FileExtension=="jpeg" || $FileExtension=="webp"){
                                    $detail_title="image";
                                }
                                // to remove the mimetype
                                $img = preg_replace('#data:'.$detail_title.'/[^;]+;base64,#', '', $avatar);
                                $img = str_replace(' ', '+', $img);
                                    
                                // image name
                                $uniquesavename=time().uniqid(rand());
                                $decreasenum=substr(str_shuffle($uniquesavename), 0, 8);
                                $avatar=$decreasenum.'.'.$FileExtension;
                                $path = "$upload_folder"."$avatar";
                                file_put_contents($path, base64_decode($img));
                            }
                                    $saveData = array(
                                            'user_pic'=>$avatar, 
                                            'full_name'=>$fullname,
                                            'email'=>$email,
                                            'user_role'=>'1',
                                            'user_role_col'=>$role,
                                            'user_status'=>1,
                                            'country'=>$country,
                                            'address'=>$address,
                                            'phonenumber'=>$phonenumber,
                                            'create_date'=>date('y-m-d')
                                    );
                                    try {
                                        $user_id = $this->cm->Save($saveData,'mst_users'); 
                                        if($user_id>0) {
                                            $ret=$this->common->response(200,true,'user added Successfully',$saveData);
                                        }
                                        else {
                                            $ret=$this->common->response(200,false,'user add Failure',array());
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
                                    $ret=$this->common->response(400,false,'User Already Exist',$data);
                                } 
                            }
                                else {
                                    $ret=$this->common->response(400,false,'Please check the input like key and value',$data);
                                }	
                            }
                           
                        elseif ($this->input->server('REQUEST_METHOD') == 'PUT')
                        {
                            $params = array();
                           $role=$post_code=$state=$town=$description=$language=$address2=$avatar=$fullname=$email=$address=$phonenumber=$organization=$country=$role=$user_id="";
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['avatar'])) { $avatar = $params['avatar'];} 
                                if(isset($params['user_id'])) { $user_id = $params['user_id'];} 
                                if(isset($params['fullname'])) { $fullname = $params['fullname'];}
                                if(isset($params['email'])) { $email = $params['email'];} 
                                if(isset($params['address'])) { $address = $params['address'];} 
                                if(isset($params['country'])) { $country = $params['country'];} 
                                if(isset($params['address2'])) {  $address2 = $params['address2'];} 
                                if(isset($params['language'])) { $language = $params['language'];} 
                                if(isset($params['description'])) { $description = $params['description'];}
                                if(isset($params['town'])) { $town = $params['town'];} 
                                if(isset($params['state'])) { $state = $params['state'];} 
                                if(isset($params['post_code'])) { $post_code = $params['post_code'];} 
                                if($user_id!=false){
                                if($avatar!=''){
                                    if (!file_exists("uploads/users/")) {
                                        mkdir("uploads/users/", 0777, true);
                                    }           
                                    // $upload_folder = "uploads/gallery/";
                                $upload_folder = "uploads/users/";
                                $uniquesavename=array();        
                                $extensions = explode(';',$avatar);
                                $extension = explode('/',$extensions[0]);
                                $FileExtension = $extension[1];
        
                            //	echo $FileExtension; exit;
                                $detail_title="";
                                if($FileExtension=="png" || $FileExtension=="jpg" || $FileExtension=="jpeg" || $FileExtension=="webp"){
                                    $detail_title="image";
                                }
                                // to remove the mimetype
                                $img = preg_replace('#data:'.$detail_title.'/[^;]+;base64,#', '', $avatar);
                                $img = str_replace(' ', '+', $img);
                                    
                                // image name
                                $uniquesavename=time().uniqid(rand());
                                $decreasenum=substr(str_shuffle($uniquesavename), 0, 8);
                                $avatar=$decreasenum.'.'.$FileExtension;
                                $path = "$upload_folder"."$avatar";
                                file_put_contents($path, base64_decode($img));
                            }
                                    $update_data = array(
                                            'user_pic'=>$avatar, 
                                            'full_name'=>$fullname,
                                            'email'=>$email,
                                            'country'=>$country,
                                            'address'=>$address,
                                            'post_code'=>$post_code,
                                            'state'=>$state,
                                            'town'=>$town,
                                            'description'=>$description,
                                            'language'=>$language,
                                            'address2'=>$address2,
                                            'create_date'=>date('y-m-d')
                                    );
                                    try {
                                        $this->cm->Update('mst_users','sk_user_id',$user_id,$update_data);
                                        if($user_id>0) {
                                            $ret=$this->common->response(200,true,'user updated Successfully',$update_data);
                                        }
                                        else {
                                            $ret=$this->common->response(200,false,'user update Failure',array());
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
                                    $ret=$this->common->response(400,false,'User Already Exist',$data);
                                } 
                            }
                                else {
                                    $ret=$this->common->response(400,false,'Please check the input like key and value',$data);
                                } 
                        }
                    else {
                        $ret=$this->common->response(400,false,'Invalid Access Token1',$data);
                    }
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


        public function user_details_update(){
            $this->access_control();
            $commonData=$this->common_data();
            $access_token = false;
            $row=$this->input->request_headers();
            if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken']; }
            $data=array();$ret=array();
            $temp=array();
            if($access_token!=globalAccessToken){
                try {
                    $user_type=$user_id=$user_status='';
                    $plain_userid=JWT::decode($access_token,pkey);				 
                    $where=array('sk_user_id'=>$plain_userid);
                    $userExists=$this->cm->getRecords($where,'mst_users');
                    if($userExists){
                    if ($this->input->server('REQUEST_METHOD') == 'PUT')
                        {
                            $params = array();
                           $id=$status=$table=$description=$language=$address2=$avatar=$fullname=$email=$address=$phonenumber=$organization=$country=$role=$user_id="";
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['id'])) { $id = $params['id'];} 
                                if(isset($params['status'])) { $status = $params['status'];} 
                                if(isset($params['table'])) {    $table = $params['table'];}
                                if($table=='mst_users'){
                                    $update_data = array(
                                            'user_status'=>$status
                                    );
                                     $this->cm->Update($table,'sk_user_id',$id,$update_data);
                                }else if($table=='mst_customer'){
                                    $update_data = array(
                                    'customer_status'=>$status
                                    );
                                     $this->cm->Update($table,'sk_customer_id',$id,$update_data);
                                }else{
                                    $update_data = array(
                                        'project_status'=>$status
                                        );
                                         $this->cm->Update($table,'sk_project_id',$id,$update_data);
                                }

                                    try {
                                        if($user_id>0) {
                                            $ret=$this->common->response(200,true,'user updated Successfully',$update_data);
                                        }
                                        else {
                                            $ret=$this->common->response(200,false,'user update Failure',array());
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
                                    $ret=$this->common->response(400,false,'User Already Exist',$data);
                                } 
                            }
                                else {
                                    $ret=$this->common->response(400,false,'Please check the input like key and value',$data);
                                } 
                        }
                    else {
                        $ret=$this->common->response(400,false,'Invalid Access Token1',$data);
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
    }