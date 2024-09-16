<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends CI_Controller {
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



        public function project_details() {
            $this->access_control();
            $commonData=$this->common_data();
            $access_token = false;
            $row=$this->input->request_headers();
            if(isset($row['Accesstoken'])) { $access_token = $row['Accesstoken']; }
            $data=array();$ret=array();
            $temp=array();
            $temp2=array();
            if($access_token!=globalAccessToken){
                try {
                    $user_type=$project_id=$user_status='';
                    $plain_userid=JWT::decode($access_token,pkey);				 
                    $where=array('sk_user_id'=>$plain_userid);
                    $userExists=$this->cm->getRecords($where,'mst_users');
                    if($userExists){
                        if ($this->input->server('REQUEST_METHOD') === 'GET')
                        {
                           
                           
                            if(isset($row['project_id']))
                            {
                                if($row['project_id']=="All"){ $project_id ="All";}
                                else{$project_id = $row['project_id'];}
                            }
                            $sql='';
                            if($project_id!='All'){
                                 $sql=$sql."sk_project_id=$project_id";
                            }
                           
                           
                            $binds=array();
                            if($project_id=='All' ){
                                $sql='select * from mst_projects  order by project_status desc';
                            }else{
                                $sql="select * from mst_projects where $sql order by project_status desc";
                            }
                            $userDetails=$this->cm->getRecordsQuery($sql,$binds);//getRecords($where,'mst_user');
                            if($userDetails)
                            {
                                foreach($userDetails as $info1)
                                {  
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
                                $temp2['project_details']=$temp1;
                                $ret=$this->common->response(200,true,'Project_details Details',$temp2);
                            }
                            else{
                                $ret=$this->common->response(200,false,'No Data Available',array());
                            }
                            }                            
                        elseif ($this->input->server('REQUEST_METHOD') === 'POST')
                        {

                            $params = array();
                          $pname= $owner=$client= $project_name=$code=$project_email=$format=$country=$project_contact_num=$pocontact_num=$pocalternative=$project_contac_alternative='';
                            $params = json_decode(@file_get_contents('php://input'),TRUE);
                            if(isset($params)) { 	
                                if(isset($params['project_name'])) { $project_name = $params['project_name'];} 
                                if(isset($params['code'])) { $code = $params['code'];}
                                if(isset($params['project_email'])) { $project_email = $params['project_email'];} 
                                if(isset($params['project_contact_num'])) { $project_contact_num = $params['project_contact_num'];} 
                                if(isset($params['format'])) { $format = $params['format'];} 
                                if(isset($params['country'])) { $country = $params['country'];}   
                                if(isset($params['client'])) { $client = $params['client'];}       
                                if(isset($params['pocontact_num'])) { $pocontact_num = $params['pocontact_num'];} 
                                if(isset($params['pocalternative'])) { $pocalternative = $params['pocalternative'];} 
                                if(isset($params['owner'])) { $owner = $params['owner'];} 
                                if(isset($params['pname'])) { $pname = $params['pname'];} 
                                if(isset($params['project_contac_alternative'])) { $project_contac_alternative = $params['project_contac_alternative'];}                            
                                    $saveData = array(
                                        'project_name'=>$project_name,
                                        'code'=>$code,
                                        'email'=>$project_email,
                                        'project_contact_num'=>$project_contact_num,
                                        'selected_format'=>$format,
                                        'country_name'=>$country,
                                        'client'=>$client,
                                        'created_date'=>date('Y-m-d H:i:s'),
                                        'project_status'=>1,
                                        'project_contac_alternative'=>$project_contac_alternative,
                                        'pocalternative'=>$pocalternative,
                                        'pocontact_num'=>$pocontact_num,
                                        'owner'=>$owner,
                                        'pname'=>$pname
                                );
                                    try {
                                        $user_id = $this->cm->Save($saveData,'mst_projects'); 
                                        if($user_id>0) {
                                            $ret=$this->common->response(200,true,'project added Successfully',$saveData);
                                        }
                                        else {
                                            $ret=$this->common->response(200,false,'project add Failure',array());
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