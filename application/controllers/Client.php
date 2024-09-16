<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Controller {
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



        public function alert_details() {
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
                    $user_type=$user_id=$user_status='';
                    $plain_userid=JWT::decode($access_token,pkey);				 
                    $where=array('sk_user_id'=>$plain_userid);
                    $userExists=$this->cm->getRecords($where,'mst_users');
                    if($userExists){
                        if ($this->input->server('REQUEST_METHOD') === 'GET')
                        {
                           
                           
                            if(isset($row['alert_id']))
                            {
                                if($row['alert_id']=="All"){ $user_id ="All";}
                                else{$user_id = $row['alert_id'];}
                            }
                            $sql='';
                            if($user_id!='All'){
                                 $sql=$sql."sk_alert_id=$user_id";
                            }
                           
                           
                            $binds=array();
                            if($user_id=='All' ){
                                $sql='select * from mst_alert order by sk_alert_id ASC';
                            }else{
                                $sql="select * from mst_alert where $sql order by sk_alert_id ASC";
                            }
                            $temp2=array();
                            $userDetails=$this->cm->getRecordsQuery($sql,$binds);//getRecords($where,'mst_user');
                            if($userDetails)
                            {
                                foreach($userDetails as $info1)
                                {  
                                    $temp['to']=$info1->to;
                                    $temp['cc']=$info1->cc;
                                     $temp['sk_alert_d']=$info1->sk_alert_id;
                                    $temp['subject']=$info1->subject;
                                    $temp['customer']=$info1->customer;
                                    $temp['project']=$info1->project;
                                    $temp['project_name']='';
                                    if($info1->project){
                                        $projects=$this->cm->getRecords(array("sk_project_id"=>$info1->project),'mst_projects');
                                        if($projects){
                                            $temp['project_name']=$projects[0]->project_name;
                                        }
                                    }
                                    $temp['frequency']=$info1->frequency;
                                    $temp['start_date']=$info1->start_date;
                                    $temp['end_date']=$info1->end_date;
                                    $temp['alert_message']=$info1->alert_message;
                                    $temp['pdf_file']=$info1->pdf_file;
                                    $temp['created_at']=$info1->created_at;
                                    if($info1->customer==null){
                                        $temp['email']='';
                                        $temp['customer_name']='';
                                        $temp['phonenumner']='';
                                        $temp['address']='';
                                        $temp['email']='';  
                                        $temp['country_name']="";                                  
                                        }else{
                                        $country=$this->cm->getRecords(array("sk_customer_id"=>$info1->customer),'mst_customer');
                                        if($country){
                                        $temp['email']=$country[0]->email;
                                        $temp['customer_name']=$country[0]->full_name;
                                        $temp['phonenumner']=$country[0]->phonenumner;
                                        $temp['address']=$country[0]->address_1.' '.$country[0]->address_2;
                                        $temp['email']=$country[0]->email;
                                        if($country[0]->country==null){
                                            $temp['country_name']="";
                                        }else{
                                            $language=$this->cm->getRecords(array("sk_country_id"=>$country[0]->country),'mst_country');
                                            if($language){
                                                $temp['country_name']=$language[0]->country_name;

                                            }else{
                                                $temp['country_name']='';

                                            }
                                        }
                                    }else{
                                        $temp['email']='';
                                        $temp['customer_name']='';
                                        $temp['phonenumner']='';
                                        $temp['address']='';
                                        $temp['email']='';  
                                        $temp['country_name']="";
                                    }
                                    }                                    
                                   
                                    // $temp['address2']=$info1->address2;
                                    // $temp['description']=$info1->description;
                                    // $temp['town']=$info1->town;
                                    // $temp['state']=$info1->state;
                                    // $temp['post_code']=$info1->post_code;
                                    // $temp['last_login']=$info1->lastlogin;
                                    // $user_roles=$this->cm->getRecords(array('sk_role_id'=>$info1->user_role),'mst_role');
                                    // if($user_roles!=false){
                                    //     $temp['user_role']=$user_roles[0]->role_name;
                                    // }else{
                                    //     $temp['user_role']='';
                                    // }
                                    $temp['created_at']=$info1->created_at;
                                    // $org_details=$this->cm->getRecords(array('sk_org_id'=>$info1->org_id),'mst_org');
                                    // if($org_details!=false){
                                    //     $temp['organization']=$org_details[0]->orn_name;
                                    // }else{
                                    //     $temp['organization']='';
                                    // }                                    
                                    $temp1[]=$temp;
                                }
                                $temp2['alert_details']=$temp1;
                                $ret=$this->common->response(200,true,'user Details',$temp2);
                            }
                            else{
                                $ret=$this->common->response(200,false,'No Data Available',array());
                            }
                            }                            
                        elseif ($this->input->server('REQUEST_METHOD') === 'POST')
                        {

                            $params = array();
                            $client=$to=$cc=$subject=$customer=$projects=$frequency=$start_date=$end_date=$alert_message=$pdfattach='';
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['to'])) { $to = $params['to'];} 
                                if(isset($params['cc'])) { $cc = $params['cc'];}
                                if(isset($params['customer'])) { $customer = $params['customer'];} 
                                if(isset($params['projects'])) { $projects = $params['projects'];} 
                                if(isset($params['frequency'])) { $frequency = $params['frequency'];} 
                                if(isset($params['start_date'])) { $start_date = $params['start_date'];} 
                                if(isset($params['end_date'])) { $end_date = $params['end_date'];} 
                                if(isset($params['alert_message'])) { $alert_message = $params['alert_message'];} 
                                if(isset($params['pdfattach'])) { $pdfattach = $params['pdfattach'];}      
                                $saveData = array(
                                    'to'=>$to,
                                    'cc'=>$cc,
                                    'subject'=>$subject,
                                    'customer'=>$customer,
                                    'project'=>$projects,
                                    'frequency'=>$frequency,
                                    'start_date'=>$start_date,
                                    'end_date'=>$end_date,
                                    'alert_message'=>$alert_message,
                                    'pdf_file'=>$pdfattach,
                                    'alert_status'=>1,
                                    'created_at'=>date('Y-m-d H:i:s')
                            );
                                    try {
                                        $user_id = $this->cm->Save($saveData,'mst_alert'); 
                                        if($user_id>0) {
                                            $mailtriggerdate = date('Y-m-d', strtotime($start_date . ' +1 day'));
                                            $this->cm->save(array('alert_for_days'=>'INITIAL EMAIL AT TIME OF PROJECT COMPLETION','for_how_many_days'=>'1st Day','alert_id'=>$user_id,'datestart'=>$mailtriggerdate),'mst_alert_list');
                                            $mailtriggerdate = date('Y-m-d', strtotime($start_date . ' +15 days'));

                                            $this->cm->save(array('alert_for_days'=>'15 DAYS FROM DATE OF INITIAL DELIVERY','for_how_many_days'=>'15 Days','alert_id'=>$user_id,'datestart'=>$mailtriggerdate),'mst_alert_list');
                                            $mailtriggerdate = date('Y-m-d', strtotime($start_date . ' +30 days'));

                                            $this->cm->save(array('alert_for_days'=>'30 DAYS FROM DATE OF INITIAL DELIVERY','for_how_many_days'=>'30 Days','alert_id'=>$user_id,'datestart'=>$mailtriggerdate),'mst_alert_list');
                                            $mailtriggerdate = date('Y-m-d', strtotime($start_date . ' +45 days'));

                                            $this->cm->save(array('alert_for_days'=>'45 DAYS FROM DATE OF INITIAL DELIVERY','for_how_many_days'=>'45 Days','alert_id'=>$user_id,'datestart'=>$mailtriggerdate),'mst_alert_list');
                                            $mailtriggerdate = date('Y-m-d', strtotime($start_date . ' +52 days'));

                                            $this->cm->save(array('alert_for_days'=>'52 DAYS FROM DATE-OF INITIAL DELIVERY (7 days later from 45day notice)','for_how_many_days'=>'52 Days','alert_id'=>$user_id,'datestart'=>$mailtriggerdate),'mst_alert_list');
                                            $mailtriggerdate = date('Y-m-d', strtotime($start_date . ' +59 days'));

                                            $this->cm->save(array('alert_for_days'=>'59 DAYS FROM DATE OF INITIAL DELIVERY (7 days later from 52day notice)','for_how_many_days	'=>'59 Days','alert_id'=>$user_id,'datestart'=>$mailtriggerdate),'mst_alert_list');
                                            $mailtriggerdate = date('Y-m-d', strtotime($start_date . ' +60 days'));

                                            $this->cm->save(array('alert_for_days'=>'60 DAYS FROM DATE OF INITIAL DELIVERY (1 day later from 59day notice)','for_how_many_days	'=>'60 Days','alert_id'=>$user_id,'datestart'=>$mailtriggerdate),'mst_alert_list');

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








        public function alert_list() {
            $this->access_control();
            $commonData=$this->common_data();
            $access_token = false;$alert_status='';
            $row=$this->input->request_headers();
            if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken']; }
            $data=array();$ret=array();
            $temp=array();
            $temp2['user_details']=array();
            if($access_token!=globalAccessToken){
                try {
                    $user_type=$user_id=$user_status='';
                    $plain_userid=JWT::decode($access_token,pkey);				 
                    $where=array('sk_user_id'=>$plain_userid);
                    $userExists=$this->cm->getRecords($where,'mst_users');
                    if($userExists){
                        if ($this->input->server('REQUEST_METHOD') === 'GET')
                        {
                           
                           
                            if(isset($row['alert_id']))
                            {
                                if($row['alert_id']=="All"){ $user_id ="All";}
                                else{$user_id = $row['alert_id'];}
                            }
                           
                            $sql='';
                            if($user_id!='All'){
                                 $sql=$sql."alert_id=$user_id";
                            }
                           
                           
                            $binds=array();
                            if($user_id=='All' ){
                                $sql="select * from mst_alert_list join mst_alert on alert_id = sk_alert_id order by sk_alert_list_id ASC";
                            }else{
                                $sql="select * from mst_alert_list join mst_alert on alert_id = sk_alert_id where $sql order by sk_alert_list_id ASC";
                            }
                            $temp2=array();
                            $userDetails=$this->cm->getRecordsQuery($sql,$binds);//getRecords($where,'mst_user');
                            if($userDetails)
                            {
                                foreach($userDetails as $info1)
                                {  
                                    $temp['sk_alert_list_id']=$info1->sk_alert_list_id;
                                    $temp['alert_for_days']=$info1->alert_for_days;
                                    $temp['alertstopdate']=$info1->alertstopdate;
                                    $temp['for_how_many_days']=$info1->for_how_many_days; 
                                    $temp['alert_id']=$info1->alert_id;
                                    $temp['alert_list_date']=$info1->date;
                                    $projectName='';$project_id='';
                                    if($info1->alert_id){
                                        $alertRecords=$this->cm->getRecords(array('sk_alert_id'=>$info1->alert_id),'mst_alert');
                                        if($alertRecords){
                                            $project_id=$alertRecords[0]->project;
                                            if($project_id){
                                                $projectRecords=$this->cm->getRecords(array('sk_project_id'=>$project_id),'mst_projects'); 
                                                if($projectRecords){
                                                    $projectName=$projectRecords[0]->project_name;
                                                }
                                            }
                                        }
                                        $temp['projectName']=$projectName;
                                        $temp['project_id']=$project_id;
                                    }
                                    $temp1[]=$temp;
                                }
                                $temp2['alert_details_list']=$temp1;
                                $ret=$this->common->response(200,true,'user Details',$temp2);
                            }
                            else{
                                $ret=$this->common->response(200,false,'No Data Available',array());
                            }
                            }                            
                        elseif ($this->input->server('REQUEST_METHOD') == 'PUT')
                        {
                            $params = array();
                            $alert_list_id='';
                            $alert_id='';$alert_status='';
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['alert_list_id'])) { $alert_list_id = $params['alert_list_id'];} 
                                if(isset($params['alert_status'])) { $alert_status = $params['alert_status'];} 
                                if($alert_list_id){
                                    $alertListRecords=$this->cm->getRecords(array('sk_alert_list_id'=>$alert_list_id),'mst_alert_list');
                                   if($alertListRecords){
                                     $alert_id=$alertListRecords[0]->alert_id;
                                   }
                                  if($alert_id){
                                
                                    $update_data = array(
                                            'alert_status'=>$alert_status
                                    );
                                    
                                    $this->cm->Update('mst_alert','sk_alert_id',$alert_id,$update_data);
                                    if($alert_status==0){
                                    $update_data = array(
                                        'alertstopdate'=>date('Y-m-d')
                                    );
                                }else{
                                    $update_data = array(
                                        'alertstopdate'=>null
                                    );
                                }
                                    try {
                                        $this->cm->Update('mst_alert_list','sk_alert_list_id',$alert_list_id,$update_data);
                                        if($plain_userid>0) {
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
                                }else{
                                    $ret=$this->common->response(400,false,'Something Went Wrong1',$data);
                                }
                                 }
                                else {
                                    $ret=$this->common->response(400,false,'Something Went Wrong2',$data);
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




        public function alert_list_view() {
            $this->access_control();
            $commonData=$this->common_data();
            $access_token = false;$alert_status='';
            $row=$this->input->request_headers();
            if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken']; }
            $data=array();$ret=array();
            $temp=array();
            $temp2['user_details']=array();
            if($access_token!=globalAccessToken){
                try {
                    $user_type=$user_id=$user_status='';
                    $plain_userid=JWT::decode($access_token,pkey);				 
                    $where=array('sk_user_id'=>$plain_userid);
                    $userExists=$this->cm->getRecords($where,'mst_users');
                    if($userExists){
                        if ($this->input->server('REQUEST_METHOD') === 'GET')
                        {
                           
                           
                            if(isset($row['alert_id']))
                            {
                                if($row['alert_id']=="All"){ $user_id ="All";}
                                else{$user_id = $row['alert_id'];}
                            }
                           
                            $sql='';
                            if($user_id!='All'){
                                 $sql=$sql."sk_alert_id=$user_id";
                            }
                           
                           
                            $binds=array();
                            if($user_id=='All' ){
                                $sql="select * from mst_alert_list join mst_alert on alert_id = sk_alert_id where order by sk_alert_list_id ASC";
                            }else{
                                $sql="select * from mst_alert_list join mst_alert on alert_id = sk_alert_id where $sql order by sk_alert_list_id ASC";
                            }
                            $temp2=array();
                            $userDetails=$this->cm->getRecordsQuery($sql,$binds);//getRecords($where,'mst_user');
                            if($userDetails)
                            {
                                foreach($userDetails as $info1)
                                {  
                                    $temp['sk_alert_list_id']=$info1->sk_alert_list_id;
                                    $temp['alert_for_days']=$info1->alert_for_days;
                                    $temp['alertstopdate']=$info1->alertstopdate;
                                    $temp['datestart']=$info1->datestart;
                                    $temp['for_how_many_days']=$info1->for_how_many_days; 
                                    $temp['alert_id']=$info1->alert_id;
                                    $temp['alert_list_date']=$info1->date;
                                    $projectName='';$project_id='';
                                    if($info1->alert_id){
                                        $alertRecords=$this->cm->getRecords(array('sk_alert_id'=>$info1->alert_id),'mst_alert');
                                        if($alertRecords){
                                            $project_id=$alertRecords[0]->project;
                                            if($project_id){
                                                $projectRecords=$this->cm->getRecords(array('sk_project_id'=>$project_id),'mst_projects'); 
                                                if($projectRecords){
                                                    $projectName=$projectRecords[0]->project_name;
                                                }
                                            }
                                        }
                                        $temp['projectName']=$projectName;
                                        $temp['project_id']=$project_id;
                                    }
                                    $temp1[]=$temp;
                                }
                                $temp2['alert_details_list']=$temp1;
                                $ret=$this->common->response(200,true,'user Details',$temp2);
                            }
                            else{
                                $ret=$this->common->response(200,false,'No Data Available',array());
                            }
                            }                            
                        elseif ($this->input->server('REQUEST_METHOD') == 'PUT')
                        {
                            $params = array();
                            $alert_list_id='';
                            $alert_id='';
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['alert_list_id'])) { $alert_list_id = $params['alert_list_id'];} 
                                if($alert_list_id){
                                    $alertListRecords=$this->cm->getRecords(array('sk_alert_list_id'=>$alert_list_id),'mst_alert_list');
                                   if($alertListRecords){
                                     $alert_id=$alertListRecords[0]->alert_id;
                                   }
                                  if($alert_id){
                                    $update_data = array(
                                            'alert_status'=>'0'
                                    );
                                    
                                    $this->cm->Update('mst_alert','sk_alert_id',$alert_id,$update_data);
                                    $update_data = array(
                                        'alertstopdate'=>date('Y-m-d')
                                    );
                                    try {
                                        $this->cm->Update('mst_alert_list','sk_alert_list_id',$alert_list_id,$update_data);
                                        if($plain_userid>0) {
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
                                }else{
                                    $ret=$this->common->response(400,false,'Something Went Wrong1',$data);
                                }
                                 }
                                else {
                                    $ret=$this->common->response(400,false,'Something Went Wrong2',$data);
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


        public function alert_list_by_date_list() {
            $this->access_control();
            $commonData=$this->common_data();
            $access_token = false;$alert_status='';$from='';$to='';
            $row=$this->input->request_headers();
            if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken']; }
            $data=array();$ret=array();
            $temp=array();
            $temp2['user_details']=array();
            if($access_token!=globalAccessToken){
                try {
                    $user_type=$user_id=$user_status='';
                    $plain_userid=JWT::decode($access_token,pkey);				 
                    $where=array('sk_user_id'=>$plain_userid);
                    $userExists=$this->cm->getRecords($where,'mst_users');
                    if($userExists){
                        if ($this->input->server('REQUEST_METHOD') === 'GET')
                        {
                           
                           
                            if(isset($row['from']))
                            {
                                if($row['from']=="All"){ $from ="All";}
                                else{$from = $row['from'];}
                            }
                            if(isset($row['to']))
                            {
                                if($row['to']=="All"){ $to ="All";}
                                else{ $to = $row['to'];}
                            }
                           
                           
                           $binds=array();
                            
                                 $sql="select distinct sk_alert_id,sk_alert_list_id,alert_for_days,alertstopdate,customer,datestart,for_how_many_days,alert_id,date from mst_alert join mst_alert_list on sk_alert_id = alert_id where (datestart BETWEEN '$from' and '$to')  order by sk_alert_list_id ASC";
                            $temp2=array();
                            $userDetails=$this->cm->getRecordsQuery($sql,$binds);//getRecords($where,'mst_user');
                            if($userDetails)
                            {
                                foreach($userDetails as $info1)
                                {  
                                    $temp['sk_alert_list_id']=$info1->sk_alert_list_id;
                                    $temp['datestart']=$info1->datestart;
                                    $temp['alert_for_days']=$info1->alert_for_days;
                                    $temp['alertstopdate']=$info1->alertstopdate;
                                    $temp['for_how_many_days']=$info1->for_how_many_days; 
                                    $temp['alert_id']=$info1->alert_id;
                                    $temp['alert_list_date']=$info1->date;
                                    $projectName='';$project_id=$fullname='';
                                    if($info1->alert_id){
                                        $alertRecords=$this->cm->getRecords(array('sk_alert_id'=>$info1->alert_id),'mst_alert');
                                        if($alertRecords){
                                            $project_id=$alertRecords[0]->project;
                                            if($project_id){
                                                $projectRecords=$this->cm->getRecords(array('sk_project_id'=>$project_id),'mst_projects'); 
                                                if($projectRecords){
                                                    $projectName=$projectRecords[0]->project_name;
                                                }
                                            }
                                        }
                                        if($info1->customer!=''){
                                            $customerRecords=$this->cm->getRecords(array('sk_customer_id'=>$info1->customer),'mst_customer');
                                            if($customerRecords){
                                                foreach($customerRecords as $info22){
                                                    $fullname=$info22->full_name;

                                                }
                                            }
                                        }
                                        $temp['client_name']=$fullname;
                                        $temp['projectName']=$projectName;
                                        $temp['project_id']=$project_id;
                                    }
                                    $temp1[]=$temp;
                                }
                                $temp2['alert_details_list']=$temp1;
                                $ret=$this->common->response(200,true,'user Details',$temp2);
                            }
                            else{
                                $ret=$this->common->response(200,false,'No Data Available',array());
                            }
                            }                            
                        elseif ($this->input->server('REQUEST_METHOD') == 'PUT')
                        {
                            $params = array();
                            $alert_list_id='';
                            $alert_id='';
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['alert_list_id'])) { $alert_list_id = $params['alert_list_id'];} 
                                if($alert_list_id){
                                    $alertListRecords=$this->cm->getRecords(array('sk_alert_list_id'=>$alert_list_id),'mst_alert_list');
                                   if($alertListRecords){
                                     $alert_id=$alertListRecords[0]->alert_id;
                                   }
                                  if($alert_id){
                                    $update_data = array(
                                            'alert_status'=>'0'
                                    );
                                    
                                    $this->cm->Update('mst_alert','sk_alert_id',$alert_id,$update_data);
                                    $update_data = array(
                                        'alertstopdate'=>date('Y-m-d')
                                    );
                                    try {
                                        $this->cm->Update('mst_alert_list','sk_alert_list_id',$alert_list_id,$update_data);
                                        if($plain_userid>0) {
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
                                }else{
                                    $ret=$this->common->response(400,false,'Something Went Wrong1',$data);
                                }
                                 }
                                else {
                                    $ret=$this->common->response(400,false,'Something Went Wrong2',$data);
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






        public function alert_list_by_date_list_v1() {
            $this->access_control();
            $commonData=$this->common_data();
            $access_token = false;$alert_status='';$from='';$to='';
            $row=$this->input->request_headers();
            if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken']; }
            $data=array();$ret=array();
            $temp=array();
            $temp2['user_details']=array();
            if($access_token!=globalAccessToken){
                try {
                    $user_type=$user_id=$user_status='';
                    $plain_userid=JWT::decode($access_token,pkey);				 
                    $where=array('sk_user_id'=>$plain_userid);
                    $userExists=$this->cm->getRecords($where,'mst_users');
                    if($userExists){
                        if ($this->input->server('REQUEST_METHOD') === 'GET')
                        {
                           
                           
                            if(isset($row['from']))
                            {
                                if($row['from']=="All"){ $from ="All";}
                                else{$from = $row['from'];}
                            }
                            if(isset($row['to']))
                            {
                                if($row['to']=="All"){ $to ="All";}
                                else{ $to = $row['to'];}
                            }
                           
                           
                           $binds=array();
                            
                                  $sql="select * from mst_alert  where (created_at BETWEEN '$from' and '$to')  order by sk_alert_id ASC";
                            $temp2=array();
                            $userDetails=$this->cm->getRecordsQuery($sql,$binds);//getRecords($where,'mst_user');
                            if($userDetails)
                            {
                                foreach($userDetails as $info1)
                                {  
                                    $alertRecords1=$this->cm->getRecords(array('alert_id'=>$info1->sk_alert_id),'mst_alert_list');
                                    $temp['sk_alert_list_id']=$alertRecords1[0]->sk_alert_list_id;
                                    $temp['alert_for_days']=$alertRecords1[0]->alert_for_days;
                                    $temp['alertstopdate']=$alertRecords1[0]->alertstopdate;
                                    $temp['for_how_many_days']=$alertRecords1[0]->for_how_many_days; 
                                    $temp['alert_id']=$alertRecords1[0]->alert_id;
                                    $temp['alert_list_date']=$alertRecords1[0]->date;
                                    $projectName='';$project_id='';$fullname='';
                                    if($info1->sk_alert_id){
                                        $alertRecords=$this->cm->getRecords(array('sk_alert_id'=>$info1->sk_alert_id),'mst_alert');
                                        if($alertRecords){
                                            $project_id=$alertRecords[0]->project;
                                            if($project_id){
                                                $projectRecords=$this->cm->getRecords(array('sk_project_id'=>$project_id),'mst_projects'); 
                                                if($projectRecords){
                                                    $projectName=$projectRecords[0]->project_name;
                                                }
                                            }
                                        }
                                        if($info1->customer!=''){
                                            $customerRecords=$this->cm->getRecords(array('sk_customer_id'=>$info1->customer),'mst_customer');
                                            if($customerRecords){
                                                foreach($customerRecords as $info22){
                                                    $fullname=$info22->full_name;

                                                }
                                            }
                                        }
                                        $temp['start_date']=$info1->start_date;
                                        $temp['client_name']=$fullname;
                                        $temp['projectName']=$projectName;
                                        $temp['project_id']=$project_id;
                                    }
                                    $temp1[]=$temp;
                                }
                                $temp2['alert_details_list']=$temp1;
                                $ret=$this->common->response(200,true,'user Details',$temp2);
                            }
                            else{
                                $ret=$this->common->response(200,false,'No Data Available',array());
                            }
                            }                            
                        elseif ($this->input->server('REQUEST_METHOD') == 'PUT')
                        {
                            $params = array();
                            $alert_list_id='';
                            $alert_id='';
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['alert_list_id'])) { $alert_list_id = $params['alert_list_id'];} 
                                if($alert_list_id){
                                    $alertListRecords=$this->cm->getRecords(array('sk_alert_list_id'=>$alert_list_id),'mst_alert_list');
                                   if($alertListRecords){
                                     $alert_id=$alertListRecords[0]->alert_id;
                                   }
                                  if($alert_id){
                                    $update_data = array(
                                            'alert_status'=>'0'
                                    );
                                    
                                    $this->cm->Update('mst_alert','sk_alert_id',$alert_id,$update_data);
                                    $update_data = array(
                                        'alertstopdate'=>date('Y-m-d')
                                    );
                                    try {
                                        $this->cm->Update('mst_alert_list','sk_alert_list_id',$alert_list_id,$update_data);
                                        if($plain_userid>0) {
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
                                }else{
                                    $ret=$this->common->response(400,false,'Something Went Wrong1',$data);
                                }
                                 }
                                else {
                                    $ret=$this->common->response(400,false,'Something Went Wrong2',$data);
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