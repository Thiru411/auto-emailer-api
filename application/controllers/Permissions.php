<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions extends CI_Controller {
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



        public function permission_details() {
            $this->access_control();
            $commonData=$this->common_data();
            $access_token = false;
            $row=$this->input->request_headers();
            if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken']; }
            $data=array();$ret=array();
            $temp=array();
            $temp2['user_details']=array();
            if($access_token!=globalAccessToken){
                try {
                   
                    $plain_userid=JWT::decode($access_token,pkey);				 
                    $where=array('sk_user_id'=>$plain_userid);
                    $userExists=$this->cm->getRecords($where,'mst_users');
                    if($userExists){
                        if ($this->input->server('REQUEST_METHOD') === 'GET')
                        {
                           
                           
                            if(isset($row['permission_id']))
                            {
                                if($row['permission_id']=="All"){ $user_id ="All";}
                                else{$user_id = $row['permission_id'];}
                            }
                            $sql='';
                            if($user_id!='All'){
                                 $sql=$sql."permission_id=$user_id";
                            }
                           
                           
                            $binds=array();
                            if($user_id=='All' ){
                                $sql='select * from mst_permission_projetcs  order by sk_permission_id ASC';
                            }else{
                                $sql="select * from mst_permission_projetcs where $sql order by sk_permission_id ASC";
                            }
                            $temp2=array();
                            $userDetails=$this->cm->getRecordsQuery($sql,$binds);//getRecords($where,'mst_user');
                            if($userDetails)
                            {
                                foreach($userDetails as $info1)
                                {  
                                  
                                    $country=$this->cm->getRecords(array("sk_user_id"=>$info1->user_id),'mst_users');
                                    $temp['full_name']=$country[0]->full_name;
                                    $temp['client']=$info1->customers;
                                    $temp['projects']=$info1->projects;
                                    $clients=explode(',',$info1->customers);
                                    $output1=$output2='';
                                    if(count($clients)>0){  
                                        for($i=0;$i<count($clients);$i++) {
                                            $country=$this->cm->getRecords(array("sk_customer_id"=>$clients[$i]),'mst_customer');
                                            if($country){
                                                foreach($country as $info10){
                                                     $output1=$output1.' '.$info10->full_name;
                                                }
                                            }
                                        }                              
                                    }

                                    $projects=explode(',',$info1->projects);

                                    if(count($projects)>0){  
                                        for($i=0;$i<count($projects);$i++) {
                                            $country=$this->cm->getRecords(array("sk_project_id"=>$projects[$i]),'mst_projects');
                                            if($country){
                                                foreach($country as $info10){
                                                    $output2=$output2.' '.$info10->project_name;
                                                }
                                            }
                                        }                              
                                    }
                                    $temp['customer_name']=$output1;
                                    $temp['project_name']=$output2; 
                                        
                                    $temp1[]=$temp;
                                }
                                $temp2['permision_details']=$temp1;
                                $ret=$this->common->response(200,true,'user Details',$temp2);
                            }
                            else{
                                $ret=$this->common->response(200,false,'No Data Available',array());
                            }
                            }                            
                        elseif ($this->input->server('REQUEST_METHOD') === 'POST')
                        {

                            $params = array();
                            $user_id=$clients=$projects='';
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['user_id'])) { $user_id = $params['user_id'];} 
                                if(isset($params['clients'])) { $clients = $params['clients'];} 
                                if(isset($params['projects'])) { $projects = $params['projects'];} 
                                    $saveData = array(
                                        'user_id'=>$user_id,
                                        'customers'=>$clients,
                                        'projects'=>$projects
                                );
                                    try {
                                        $user_id = $this->cm->Save($saveData,'mst_permission_projetcs'); 
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
                                    $ret=$this->common->response(400,false,'Please check the input like key and value',$data);
                                }	
                            }
                           
                        elseif ($this->input->server('REQUEST_METHOD') == 'PUT')
                        {
                            $params = array();
                           $post_code=$state=$town=$description=$language=$address2=$avatar=$fullname=$email=$address=$phonenumber=$organization=$country=$role=$user_id="";
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
    }