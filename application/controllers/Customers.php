<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends CI_Controller {
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



        public function customer_details() {
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
                           
                           
                            if(isset($row['customer_id']))
                            {
                                if($row['customer_id']=="All"){ $user_id ="All";}
                                else{$user_id = $row['customer_id'];}
                            }
                            $sql='';
                            if($user_id!='All'){
                                 $sql=$sql."sk_customer_id=$user_id";
                            }
                           
                           
                            $binds=array();
                            if($user_id=='All' ){
                                $sql='select * from mst_customer  order by customer_status desc';
                            }else{
                                $sql="select * from mst_customer where $sql order by customer_status desc";
                            }
                            $temp2=array();
                            $userDetails=$this->cm->getRecordsQuery($sql,$binds);//getRecords($where,'mst_user');
                            if($userDetails)
                            {
                                foreach($userDetails as $info1)
                                {  
                                    $temp['name']=$info1->full_name;
                                    $temp['email']=$info1->email;
                                    $temp['userid']=$info1->sk_customer_id;
                                    $temp['userid']=$info1->sk_customer_id;
                                    $temp['phonenumber']=$info1->pocnumber;
                                    $temp['cust_country']=$info1->cust_country;
                                    $temp['customer_status']=$info1->customer_status;
                                    //$temp['address']=$info1->address_ful;
                                    // if($info1->country==null){
                                    //     $temp['country']="";
                                    // }else{
                                    //     $country=$this->cm->getRecords(array("sk_country_id"=>$info1->country),'mst_country');
                                    //     $temp['country']=$country[0]->country_name;
                                    // }                                    
                                    // if($info1->language==null){
                                    //     $temp['language']="";
                                    // }else{
                                    //     $language=$this->cm->getRecords(array("sk_language_id"=>$info1->language),'mst_language');
                                    //     $temp['language']=$language[0]->language;
                                    // }
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
                                    $temp['created_at']=$info1->create_date;
                                    // $org_details=$this->cm->getRecords(array('sk_org_id'=>$info1->org_id),'mst_org');
                                    // if($org_details!=false){
                                    //     $temp['organization']=$org_details[0]->orn_name;
                                    // }else{
                                    //     $temp['organization']='';
                                    // }                                    
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
                            $owner=$phonenumner=$billing=$address_full=$cust_country=$postalcode=$state=$fullname=$poc=$email=$pocnumber=$address_1=$address_2=$city="";
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['postalcode'])) { $postalcode = $params['postalcode'];} 
                                if(isset($params['fullname'])) { $fullname = $params['fullname'];}
                                if(isset($params['poc'])) { $poc = $params['poc'];} 
                                if(isset($params['pocnumber'])) { $pocnumber = $params['pocnumber'];} 
                                if(isset($params['email'])) { $email = $params['email'];} 
                                if(isset($params['address_1'])) { $address_1 = $params['address_1'];} 
                                if(isset($params['address_2'])) { $address_2 = $params['address_2'];} 
                                if(isset($params['city'])) { $city = $params['city'];} 
                                if(isset($params['owner'])) { $owner = $params['owner'];} 
                                if(isset($params['state'])) { $state = $params['state'];} 
                                if(isset($params['country'])) { $country = $params['country'];}
                                if(isset($params['cust_country'])) { $cust_country = $params['cust_country'];} 
                                if(isset($params['address_full'])) { $address_full = $params['address_full'];} 
                                if(isset($params['billing'])) { $billing = $params['billing'];} 
                                if(isset($params['phonenumner'])) { $phonenumner = $params['phonenumner'];}                                 
                                    $saveData = array(
                                        'full_name'=>$fullname,
                                        'poc'=>$poc,
                                        'pocnumber'=>$pocnumber,
                                        'email'=>$email,
                                        'address_1'=>$address_1,
                                        'address_2'=>$address_2,
                                        'city'=>$city,
                                        'state'=>$state,
                                        'postalcode'=>$postalcode,
                                        'country'=>$country,
                                        'owner'=>$owner,
                                        'cust_country'=>$cust_country,
                                        'address_full'=>$address_full,
                                        'billing'=>$billing,
                                        'phonenumner'=>$phonenumner,
                                        'create_date'=>date('Y-m-d H:i:s'),
                                        'customer_status'=>1
                                );
                                    try {
                                        $user_id = $this->cm->Save($saveData,'mst_customer'); 
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






        public function mailTriggerAction(){
            $this->access_control();
            $commonData=$this->common_data();
            $projectDetails=$this->cm->getRecords(array('alert_status'=>1),'mst_alert');
            $emailtemplate='';
            if($projectDetails){
                foreach( $projectDetails as $project){
                    $customer_name= $project->customer;
                   $customers=$this->cm->getRecords(array('sk_customer_id'=>$customer_name),'mst_customer');
                   $cust_name='';
                   if($customers){
                    $cust_name=$customers[0]->full_name;
                   }
                   $to_email='';
                   if($project->to){
                
                    $to=explode(',',$project->to);
                
                   for($k=0;$k<count($to);$k++ ){
                    $users=$this->cm->getRecords(array('sk_user_id'=>$to[$k]),'mst_users');
                    $to_email=$to_email.','.$users[0]->email;
                }
                   
                   }
                   $cc_email='';
                   if($project->cc){
                
                    $cc=explode(',',$project->cc);
                
                   for($k=0;$k<count($cc);$k++ ){
                    $users=$this->cm->getRecords(array('sk_user_id'=>$cc[$k]),'mst_users');
                    $cc_email=$cc_email.','.$users[0]->email;
                }
                   
                   }
                $project_subject=$project->subject;
                $start_date=$project->start_date;
                $datestartdetails=$this->cm->getRecords(array('alert_id'=>$project->sk_alert_id),'mst_alert_list');
    if($datestartdetails)  {
        foreach($datestartdetails as $detail) {
        if(strtotime(date('H:i'))==strtotime(date('19:00'))){
            $emailtemplate=str_replace(array('%fullname%','%date%','%start_date%','%base_url%'), array($cust_name,date('d-m-Y'),$start_date,base_url()),  file_get_contents(base_url() . 'assets/email-template/initial.html'));
            $emailInfo=$this->sendEmailOne($to_email,'Digitization Project Complete; Project Summary Report Attached',$emailtemplate,$cc_email);
        }else if(strtotime(date('H:i'))==strtotime(date('20:00'))){
            $emailtemplate=str_replace(array('%fullname%','%date%','%start_date%','%base_url%'), array($cust_name,date('d-m-Y'),$start_date,base_url()),  file_get_contents(base_url() . 'assets/email-template/15days.html'));
            $emailInfo=$this->sendEmailOne($to_email,'REMINDER PSR Signature Requested',$emailtemplate,$cc_email);
        }else if(strtotime(date('H:i'))==strtotime(date('21:00'))){
            $emailtemplate=str_replace(array('%fullname%','%date%','%start_date%','%base_url%'), array($cust_name,date('d-m-Y'),$start_date,base_url()),  file_get_contents(base_url() . 'assets/email-template/30days.html'));
            $emailInfo=$this->sendEmailOne($to_email,'REMINDER PSR Signature Requested',$emailtemplate,$cc_email);
        }else if(strtotime(date('H:i'))==strtotime(date('22:00'))){
            $emailtemplate=str_replace(array('%fullname%','%date%','%start_date%','%base_url%'), array($cust_name,date('d-m-Y'),$start_date,base_url()),  file_get_contents(base_url() . 'assets/email-template/45days.html'));
            $emailInfo=$this->sendEmailOne($to_email,'REMINDER PSR Signature Requested – 15 DAYS Remaining to Automatic Digitization Project Data Purge/Deletion',$emailtemplate,$cc_email);
        }else if(strtotime(date('H:i'))==strtotime(date('23:00'))){
            $emailtemplate=str_replace(array('%fullname%','%date%','%start_date%','%base_url%'), array($cust_name,date('d-m-Y'),$start_date,base_url()),  file_get_contents(base_url() . 'assets/email-template/52days.html'));
            $emailInfo=$this->sendEmailOne($to_email,'REMINDER PSR Signature Requested – 7 DAYS Remaining to Automatic Digitization Project Data Purge/Deletion',$emailtemplate,$cc_email);
        }else if(strtotime(date('H:i'))==strtotime(date('00:00'))){
            $emailtemplate=str_replace(array('%fullname%','%date%','%start_date%','%base_url%'), array($cust_name,date('d-m-Y'),$start_date,base_url()),  file_get_contents(base_url() . 'assets/email-template/59days.html'));
            $emailInfo=$this->sendEmailOne($to_email,'REMINDER 1 Day Remaining to Automatic Digitization Project Data Purge/Deletion– PSR Signature Requested',$emailtemplate,$cc_email);
        }else if(strtotime(date('H:i'))==strtotime(date('01:00'))){
            $emailtemplate=str_replace(array('%fullname%','%date%','%start_date%','%base_url%'), array($cust_name,date('d-m-Y'),$start_date,base_url()),  file_get_contents(base_url() . 'assets/email-template/60days.html'));
            $emailInfo=$this->sendEmailOne($to_email,'Digitization Project Data Purge/Deletion Notice – PSR Not Received',$emailtemplate,$cc_email);
            }
        }
    } 
       
        }
        }
         

        }
    
    
        public function sendEmailOne($tomail,$subject,$body,$cc_email){
           
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'ssl://smtp.gmail.com';
            $config['smtp_user'] = 'psr.support@terralogic.com';
            $config['smtp_pass'] = 'Winter123!';
            $config['smtp_port'] = 465;
            $config['mailtype'] = "html";
            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $this->email->from('psr.support@terralogic.com', 'CLIENT PSR');
            $this->email->to($tomail);
            $this->email->subject($subject);
            $this->email->message($body);
            $this->email->cc($cc_email);
            $result = $this->email->send();
            if($result){
                return    $result;	
            }else{
              return   $this->email->print_debugger();
            }
            
        }
    }