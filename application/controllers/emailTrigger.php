<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class emailTrigger extends CI_Controller {

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


    public function mailTriggerAction(){
        $this->access_control();
        $commonData=$this->common_data();
        $projectDetails=$this->cm->getRecords(array(),'mst_projects');
        $emailtemplate='';
        if(strtotime(date('H:i'))==strtotime('21:10')){
            echo $emailtemplate="<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>



            <html xmlns='http://www.w3.org/1999/xhtml' lang='en' xml:lang='en'>
            <head>
                <style>
                    body {
                        background-color: #fff;
                        font-family: 'Manrope', sans-serif !important;
                        margin: 0 !important;
                    }
                </style>
            </head>
            
            <body>
            
                <table class='body' width='100%' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td class='center' align='center' valign='middle'>
                            <table class='body main '
                                style='background-color:#FFFFFF; width: 600px;box-shadow: 0px 4px 40px rgba(87, 87, 87, 0.1);'>
            
            
            
            
                                <tr>
                                    <td style='padding: 60px 40px 60px;'>
                                        <p style=' font-weight: 400;
                                                    font-size: 16px;
                                                    line-height: 24px;
                                                    color: #30333F;
                                                    padding-bottom: 20px;
                                                    margin: 0 !important;
                                                    font-family: 'Manrope' !important;'>
                                            [Date: ]
                                        </p>
                                        <p style=' font-weight: 400;
                                                    font-size: 16px;
                                                    line-height: 24px;
                                                    color: #30333F;
                                                    padding-bottom: 20px;
                                                    margin: 0 !important;
                                                    font-family: 'Manrope' !important;'>
                                            Dear Thirumala:,
                                        </p>
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 24px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            Terralogic Document Systems has completed digitization of your records. These files have
                                            been
                                            uploaded via FTP and sent via external hard drive(s).</p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            We are attaching a Project Summary Report (aka PSR) of the documents that were digitized
                                            & imported
                                            as well as screenshots of the requested index file configurations. Please review your
                                            images along with
                                            the PSR data and contact us as soon as possible if there are any concerns, questions or
                                            discrepancies.</p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            Otherwise, if all is satisfactory, we kindly request the PSR document be signed and
                                            returned to
                                            <a href='#'>monica.dell@terralogic.com</a> or myself as soon as conveniently possible, but no later than
                                            60 calendar
                                            days from today’s date.</p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 27px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            Per the Project Summary Report memorandum of understanding, TDS only retains project
                                            data/images
                                            on our servers for 60 calendar days from completion of the project. Thereafter, all
                                            data/images are
                                            purged from our system and unrecoverable. <b>The data purge/deletion date will take place
                                            on [[Date –
                                            60 days from initial date of delivery]].</b></p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            Please don’t hesitate to contact us with any questions.</p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            We appreciate your business and support of Terralogic Document Systems.</p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            Thank you,</p>
            
                                        <p style=' font-weight: 400;
                                            font-size: 16px;
                                            line-height: 20px;
                                            color: #30333F;
                                            margin: 0 !important;
                                            font-family: 'Manrope' !important;'>
                                                <b>Tech Title</b><br>
                                                <b>Tech Name</b><br>
                                                Terralogic Document Systems Inc.<br>
                                                El Paso, Albuquerque, Midland, Colorado Springs<br>
                                                Office: <a href='#' style='color: #30333F;text-decoration-line: none;'>915-593-3100</a><br>
                                                Toll Free:  <a href='#' style='color: #30333F; text-decoration-line: none;'>800-644-7112</a><br>
                                                <a href='#'>www.pdswest.com</a>
                                        </p>
            
                                            <a href='#'><img src='".base_url()."upload/emailimages/psrlogo.png' alt='Image' width='250' height='120' style='padding-top: 24px;'></a>
                                    </td>
                                    
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>";

            $emailInfo=$this->sendEmailOne('thirumala.b@terralogic.com','Mail from Psr',$emailtemplate);
        }else if(strtotime(date('H:i'))==strtotime('21:11')){
           $emailtemplate="<head>

           <meta charset='utf-8'>
           <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
       
           <title>
               15 DAYS FROM DATE OF INITIAL DELIVERY
           </title>
       
           <style>
               body {
                   background-color: #fff;
                   font-family: 'Manrope', sans-serif !important;
                   margin: 0 !important;
               }
           </style>
       </head>
       
       <body>
       
           <table class='body' width='100%' cellspacing='0' cellpadding='0'>
               <tr>
                   <td class='center' align='center' valign='middle'>
                       <table class='body main '
                           style='background-color:#FFFFFF; width: 600px;box-shadow: 0px 4px 40px rgba(87, 87, 87, 0.1);'>
       
       
       
       
                           <tr>
                               <td style='padding: 60px 40px 60px;'>
                                   <p style=' font-weight: 400;
                                               font-size: 16px;
                                               line-height: 24px;
                                               color: #30333F;
                                               padding-bottom: 20px;
                                               margin: 0 !important;
                                               font-family: 'Manrope' !important;'>
                                       [Date: ]
                                   </p>
                                   <p style=' font-weight: 400;
                                               font-size: 16px;
                                               line-height: 24px;
                                               color: #30333F;
                                               padding-bottom: 20px;
                                               margin: 0 !important;
                                               font-family: 'Manrope' !important;'>
                                       Dear Thirumala:,
                                   </p>
                                   <p style=' font-weight: 400;
                                   font-size: 16px;
                                   line-height: 24px;
                                   color: #30333F;
                                   padding-bottom: 24px;
                                   margin: 0 !important;
                                   font-family: 'Manrope' !important;'>
                                       Terralogic Document Systems completed digitization of your records on {{Initial Date}}.
                                       These files were uploaded via FTP and sent via external hard drive(s). We sent a Project
                                       Summary Report for review (see previous email) and are pending the return of the signed
                                       and accepted PSR.</p>
       
                                   <p style=' font-weight: 400;
                                   font-size: 16px;
                                   line-height: 24px;
                                   color: #30333F;
                                   padding-bottom: 20px;
                                   margin: 0 !important;
                                   font-family: 'Manrope' !important;'>
                                       Please review your images along with the PSR data and contact us as soon as possible if
                                       there are any concerns, questions or discrepancies.</p>
       
                                   <p style=' font-weight: 400;
                                   font-size: 16px;
                                   line-height: 24px;
                                   color: #30333F;
                                   padding-bottom: 20px;
                                   margin: 0 !important;
                                   font-family: 'Manrope' !important;'>
                                       We kindly request the PSR document be signed and returned as soon as conveniently
                                       possible, but no later than [[Date – 60 days from initial date of delivery]].
                                   </p>
       
                                   <p style=' font-weight: 400;
                                   font-size: 16px;
                                   line-height: 27px;
                                   color: #30333F;
                                   padding-bottom: 20px;
                                   margin: 0 !important;
                                   font-family: 'Manrope' !important;'>
                                       We appreciate your business and support of Terralogic Document Systems.</p>
       
                                   <p style=' font-weight: 400;
                                   font-size: 16px;
                                   line-height: 24px;
                                   color: #30333F;
                                   padding-bottom: 20px;
                                   margin: 0 !important;
                                   font-family: 'Manrope' !important;'>
                                       Thank you,</p>
       
                                   
       
                                   <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 20px;
                                       color: #30333F;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                       <b>Monica Dell</b><br>
                                       <b>Projects Coordinator</b><br>
                                       Terralogic Document Systems Inc.<br>
                                       El Paso, Albuquerque, Midland, Colorado Springs<br>
                                       Office: <a href='#'
                                           style='color: #30333F;text-decoration-line: none;'>915-593-3100</a><br>
                                       Toll Free: <a href='#'
                                           style='color: #30333F; text-decoration-line: none;'>800-644-7112</a><br>
                                       <a href='#'>www.pdswest.com</a>
                                   </p>
                                   <a href='#'><img src='".base_url()."uploads/emailimages/psrlogo.png' alt='Image' width='250' height='120'
                                           style='padding-top: 24px;'></a>
                               </td>
                           </tr>
                       </table>
                   </td>
               </tr>
           </table>
       </body>" ;
       $emailInfo=$this->sendEmailOne('thirumala.b@terralogic.com','Mail from Psr',$emailtemplate);
        }else if(strtotime(date('H:i'))==strtotime('21:12')){
        $emailtemplate="<head>
        
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
        
            <title>
                30 DAYS FROM DATE OF INITIAL DELIVERY
            </title>
        
            <style>
                body {
                    background-color: #fff;
                    font-family: 'Manrope', sans-serif !important;
                    margin: 0 !important;
                }
            </style>
        </head>
        
        <body>
        
            <table class='body' width='100%' cellspacing='0' cellpadding='0'>
                <tr>
                    <td class='center' align='center' valign='middle'>
                        <table class='body main '
                            style='background-color:#FFFFFF; width: 600px;box-shadow: 0px 4px 40px rgba(87, 87, 87, 0.1);'>
        
        
        
        
                            <tr>
                                <td style='padding: 60px 40px 60px;'>
                                    <p style=' font-weight: 400;
                                                font-size: 16px;
                                                line-height: 24px;
                                                color: #30333F;
                                                padding-bottom: 20px;
                                                margin: 0 !important;
                                                font-family: 'Manrope' !important;'>
                                        [Date: ]
                                    </p>
                                    <p style=' font-weight: 400;
                                                font-size: 16px;
                                                line-height: 24px;
                                                color: #30333F;
                                                padding-bottom: 20px;
                                                margin: 0 !important;
                                                font-family: 'Manrope' !important;'>
                                        Dear Thirumala:,
                                    </p>
                                    <p style=' font-weight: 400;
                                    font-size: 16px;
                                    line-height: 24px;
                                    color: #30333F;
                                    padding-bottom: 24px;
                                    margin: 0 !important;
                                    font-family: 'Manrope' !important;'>
                                        Terralogic Document Systems completed digitization of your records on {{Initial Date}}.
                                        These files were uploaded via FTP and sent via external hard drive(s). We sent a Project
                                        Summary Report for review (see previous emails) and are pending the return of the signed
                                        and accepted PSR.</p>
        
                                    <p style=' font-weight: 400;
                                    font-size: 16px;
                                    line-height: 24px;
                                    color: #30333F;
                                    padding-bottom: 20px;
                                    margin: 0 !important;
                                    font-family: 'Manrope' !important;'>
                                        Please review your images along with the PSR data and contact us as soon as possible if
                                        there are any concerns, questions or discrepancies.</p>
        
                                    <p style=' font-weight: 400;
                                    font-size: 16px;
                                    line-height: 24px;
                                    color: #30333F;
                                    padding-bottom: 20px;
                                    margin: 0 !important;
                                    font-family: 'Manrope' !important;'>
                                        We kindly request the PSR document be signed and returned as soon as conveniently
                                        possible, but no later than [[Date – 60 days from initial date of delivery]].
                                    </p>
        
                                    <p style=' font-weight: 400;
                                    font-size: 16px;
                                    line-height: 27px;
                                    color: #30333F;
                                    padding-bottom: 20px;
                                    margin: 0 !important;
                                    font-family: 'Manrope' !important;'>
                                       We appreciate your business and support of Terralogic Document Systems.</p>
        
                                    <p style=' font-weight: 400;
                                    font-size: 16px;
                                    line-height: 24px;
                                    color: #30333F;
                                    padding-bottom: 20px;
                                    margin: 0 !important;
                                    font-family: 'Manrope' !important;'>
                                        Thank you,</p>
        
        
        
                                    <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 20px;
                                        color: #30333F;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                        <b>Monica Dell</b><br>
                                        <b>Projects Coordinator</b><br>
                                        Terralogic Document Systems Inc.<br>
                                        El Paso, Albuquerque, Midland, Colorado Springs<br>
                                        Office: <a href='#'
                                            style='color: #30333F;text-decoration-line: none;'>915-593-3100</a><br>
                                        Toll Free: <a href='#'
                                            style='color: #30333F; text-decoration-line: none;'>800-644-7112</a><br>
                                        <a href='#'>www.pdswest.com</a>
                                    </p>
        
                                    <a href='#'><img src='".base_url()."uploads/emailimages/psrlogo.png' alt='Image' width='250' height='120'
                                            style='padding-top: 24px;'></a>
                                </td>
        
                            </tr>
        
        
        
        
                        </table>
                    </td>
                </tr>
            </table>
        </body>";
        $emailInfo=$this->sendEmailOne('thirumala.b@terralogic.com','Mail from Psr',$emailtemplate);
        }else if(strtotime(date('H:i'))==strtotime('21:13')){
            $emailtemplate="
            
            
            <head>
            
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
            
                <title>
                    45 DAYS FROM DATE OF INITIAL DELIVERY
                </title>
            
                <style>
                    body {
                        background-color: #fff;
                        font-family: 'Manrope', sans-serif !important;
                        margin: 0 !important;
                    }
                </style>
            </head>
            
            <body>
            
                <table class='body' width='100%' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td class='center' align='center' valign='middle'>
                            <table class='body main '
                                style='background-color:#FFFFFF; width: 600px;box-shadow: 0px 4px 40px rgba(87, 87, 87, 0.1);'>
            
            
            
            
                                <tr>
                                    <td style='padding: 60px 40px 60px;'>
                                        <p style=' font-weight: 400;
                                                    font-size: 16px;
                                                    line-height: 24px;
                                                    color: #30333F;
                                                    padding-bottom: 20px;
                                                    margin: 0 !important;
                                                    font-family: 'Manrope' !important;'>
                                            [Date: ]
                                        </p>
                                        <p style=' font-weight: 400;
                                                    font-size: 16px;
                                                    line-height: 24px;
                                                    color: #30333F;
                                                    padding-bottom: 20px;
                                                    margin: 0 !important;
                                                    font-family: 'Manrope' !important;'>
                                            Dear Thirumala:,
                                        </p>
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 24px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            Terralogic Document Systems completed digitization of your records on {{Initial Date}}.
                                            These files were uploaded via FTP and sent via external hard drive(s). We sent a Project
                                            Summary Report for review (see previous emails) and are pending the return of the signed
                                            & accepted PSR document.</p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            Per the Project Summary Report memorandum of understanding, TDS only retains project
                                            data/images on our servers for 60 calendar days from completion of the project.
                                            Thereafter, all data/images are purged from our system and unrecoverable.</p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            Please review your images along with the PSR data and contact us as soon as possible if
                                            there are any concerns, questions or discrepancies. We kindly request the PSR document
                                            be signed and returned as soon as conveniently possible, but no later than [[Date – 60
                                            days from initial date of delivery]].
                                        </p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 27px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            We appreciate your business and support of Terralogic Document Systems.</p>
            
                                        <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                            Thank you,</p>
            
            
            
                                        <p style=' font-weight: 400;
                                            font-size: 16px;
                                            line-height: 20px;
                                            color: #30333F;
                                            margin: 0 !important;
                                            font-family: 'Manrope' !important;'>
                                            <b>Monica Dell</b><br>
                                            <b>Projects Coordinator</b><br>
                                            Terralogic Document Systems Inc.<br>
                                            El Paso, Albuquerque, Midland, Colorado Springs<br>
                                            Office: <a href='#'
                                                style='color: #30333F;text-decoration-line: none;'>915-593-3100</a><br>
                                            Toll Free: <a href='#'
                                                style='color: #30333F; text-decoration-line: none;'>800-644-7112</a><br>
                                            <a href='#'>www.pdswest.com</a>
                                        </p>
            
                                        <a href='#'><img src='".base_url()."uploads/emailimages/psrlogo.png' alt='Image' width='250' height='120'
                                                style='padding-top: 24px;'></a>
                                    </td>
            
                                </tr>
            
            
            
            
                            </table>
                        </td>
                    </tr>
                </table>
            </body>";
            $emailInfo=$this->sendEmailOne('thirumala.b@terralogic.com','Mail from Psr',$emailtemplate); 
        }else if(strtotime(date('H:i'))==strtotime('21:14')){
           $emailtemplate="
           
           <head>
           
               <meta charset='utf-8'>
               <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
           
               <title>
                   52 DAYS FROM DATE OF INITIAL DELIVERY (7 days later from 45 day notice)
               </title>
           
               <style>
                   body {
                       background-color: #fff;
                       font-family: 'Manrope', sans-serif !important;
                       margin: 0 !important;
                   }
               </style>
           </head>
           
           <body>
           
               <table class='body' width='100%' cellspacing='0' cellpadding='0'>
                   <tr>
                       <td class='center' align='center' valign='middle'>
                           <table class='body main '
                               style='background-color:#FFFFFF; width: 600px;box-shadow: 0px 4px 40px rgba(87, 87, 87, 0.1);'>
           
           
           
           
                               <tr>
                                   <td style='padding: 60px 40px 60px;'>
                                       <p style=' font-weight: 400;
                                                   font-size: 16px;
                                                   line-height: 24px;
                                                   color: #30333F;
                                                   padding-bottom: 20px;
                                                   margin: 0 !important;
                                                   font-family: 'Manrope' !important;'>
                                           [Date: ]
                                       </p>
                                       <p style=' font-weight: 400;
                                                   font-size: 16px;
                                                   line-height: 24px;
                                                   color: #30333F;
                                                   padding-bottom: 20px;
                                                   margin: 0 !important;
                                                   font-family: 'Manrope' !important;'>
                                           Dear Thirumala:,
                                       </p>
                                       <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 24px;
                                       color: #30333F;
                                       padding-bottom: 24px;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                           Terralogic Document Systems completed digitization of your records on {{Initial Date}}.
                                           These files were uploaded via FTP and sent via external hard drive(s). We sent a Project
                                           Summary Report for review (see previous emails) and are pending the return of the signed
                                           & accepted PSR document.</p>
           
                                       <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 24px;
                                       color: #30333F;
                                       padding-bottom: 20px;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                           Per the Project Summary Report memorandum of understanding, TDS only retains project
                                           data/images on our servers for 60 calendar days from completion of the project.
                                           Thereafter, all data/images are purged from our system and unrecoverable. <b>The data
                                               purge/deletion date will take place on [[Date – 60 days from initial date of
                                               delivery]].</b>
                                       </p>
           
                                       <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 24px;
                                       color: #30333F;
                                       padding-bottom: 20px;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                           Please review your images along with the PSR data and contact us as soon as possible if
                                           there are any concerns, questions or discrepancies. We kindly request the PSR document
                                           be signed and returned as soon as conveniently possible, but no later than [[Date – 60
                                           days from initial date of delivery]].
                                       </p>
           
                                       <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 27px;
                                       color: #30333F;
                                       padding-bottom: 20px;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                           We appreciate your business and support of Terralogic Document Systems.</p>
           
                                       <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 24px;
                                       color: #30333F;
                                       padding-bottom: 20px;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                           Thank you,</p>
           
           
           
                                       <p style=' font-weight: 400;
                                           font-size: 16px;
                                           line-height: 20px;
                                           color: #30333F;
                                           margin: 0 !important;
                                           font-family: 'Manrope' !important;'>
                                           <b>Monica Dell</b><br>
                                           <b>Projects Coordinator</b><br>
                                           Terralogic Document Systems Inc.<br>
                                           El Paso, Albuquerque, Midland, Colorado Springs<br>
                                           Office: <a href='#'
                                               style='color: #30333F;text-decoration-line: none;'>915-593-3100</a><br>
                                           Toll Free: <a href='#'
                                               style='color: #30333F; text-decoration-line: none;'>800-644-7112</a><br>
                                           <a href='#'>www.pdswest.com</a>
                                       </p>
           
                                       <a href='#'><img src='".base_url()."uploads/emailimages/psrlogo.png' alt='Image' width='250' height='120'
                                               style='padding-top: 24px;'></a>
                                   </td>
           
                               </tr>
           
           
           
           
                           </table>
                       </td>
                   </tr>
               </table>
           </body>";
           $emailInfo=$this->sendEmailOne('thirumala.b@terralogic.com','Mail from Psr',$emailtemplate);
        }else if(strtotime(date('H:i'))==strtotime('21:15')){
            

    $emailtemplate="<head>

    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <title>
        59 DAYS FROM DATE OF INITIAL DELIVERY (7 days later from 52 day notice)
    </title>

    <style>
        body {
            background-color: #fff;
            font-family: 'Manrope', sans-serif !important;
            margin: 0 !important;
        }
    </style>
</head>

<body>

    <table class='body' width='100%' cellspacing='0' cellpadding='0'>
        <tr>
            <td class='center' align='center' valign='middle'>
                <table class='body main '
                    style='background-color:#FFFFFF; width: 600px;box-shadow: 0px 4px 40px rgba(87, 87, 87, 0.1);'>




                    <tr>
                        <td style='padding: 60px 40px 60px;'>
                            <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                [Date: ]
                            </p>
                            <p style=' font-weight: 400;
                                        font-size: 16px;
                                        line-height: 24px;
                                        color: #30333F;
                                        padding-bottom: 20px;
                                        margin: 0 !important;
                                        font-family: 'Manrope' !important;'>
                                Dear Thirumala:,
                            </p>
                            <p style=' font-weight: 400;
                            font-size: 16px;
                            line-height: 24px;
                            color: #30333F;
                            padding-bottom: 24px;
                            margin: 0 !important;
                            font-family: 'Manrope' !important;'>
                                Terralogic Document Systems completed digitization of your records on {{Initial Date}}.
                                These files were uploaded via FTP and sent via external hard drive(s). We sent a Project
                                Summary Report for review (see previous emails) and are pending the return of the signed
                                & accepted PSR document.</p>

                            <p style=' font-weight: 400;
                            font-size: 16px;
                            line-height: 24px;
                            color: #30333F;
                            padding-bottom: 20px;
                            margin: 0 !important;
                            font-family: 'Manrope' !important;'>
                                Per the Project Summary Report memorandum of understanding, TDS only retains project
                                data/images on our servers for 60 calendar days from completion of the project. <b>ALL
                                    PROJECT will be purged/deleted from our server TOMORROW, [[Date – 60 days from
                                    initial
                                    date of delivery]] and unrecoverable.</b>
                            </p>

                            <p style=' font-weight: 400;
                            font-size: 16px;
                            line-height: 24px;
                            color: #30333F;
                            padding-bottom: 20px;
                            margin: 0 !important;
                            font-family: 'Manrope' !important;'>
                                Please review your images along with the PSR data and contact us as soon as possible if
                                there are any concerns, questions or discrepancies. We kindly request the PSR document
                                be signed and returned no later than close of business day today. If the PSR document is
                                not returned, TDS assumes that the client has fully reviewed the images & data and the
                                job was performed satisfactorily.
                            </p>

                            <p style=' font-weight: 400;
                            font-size: 16px;
                            line-height: 27px;
                            color: #30333F;
                            padding-bottom: 20px;
                            margin: 0 !important;
                            font-family: 'Manrope' !important;'>
                                We appreciate your business and support of Terralogic Document Systems.</p>

                            <p style=' font-weight: 400;
                            font-size: 16px;
                            line-height: 24px;
                            color: #30333F;
                            padding-bottom: 20px;
                            margin: 0 !important;
                            font-family: 'Manrope' !important;'>
                                Thank you,</p>



                            <p style=' font-weight: 400;
                                font-size: 16px;
                                line-height: 20px;
                                color: #30333F;
                                margin: 0 !important;
                                font-family: 'Manrope' !important;'>
                                <b>Monica Dell</b><br>
                                <b>Projects Coordinator</b><br>
                                Terralogic Document Systems Inc.<br>
                                El Paso, Albuquerque, Midland, Colorado Springs<br>
                                Office: <a href='#'
                                    style='color: #30333F;text-decoration-line: none;'>915-593-3100</a><br>
                                Toll Free: <a href='#'
                                    style='color: #30333F; text-decoration-line: none;'>800-644-7112</a><br>
                                <a href='#'>www.pdswest.com</a>
                            </p>

                            <a href='#'><img src='".base_url()."uploads/emailimages/psrlogo.png' alt='Image' width='250' height='120'
                                    style='padding-top: 24px;'></a>
                        </td>

                    </tr>




                </table>
            </td>
        </tr>
        </table>
    </body>";
    $emailInfo=$this->sendEmailOne('thirumala.b@terralogic.com','Mail from Psr',$emailtemplate);
        }else if(strtotime(date('H:i'))==strtotime('21:16')){
           $emailtemplate="
           
           
           <head>
           
               <meta charset='utf-8'>
               <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
           
               <title>
                   60 DAYS FROM DATE OF INITIAL DELIVERY (1 days later from 59 day notice)
               </title>
           
               <style>
                   body {
                       background-color: #fff;
                       font-family: 'Manrope', sans-serif !important;
                       margin: 0 !important;
                   }
               </style>
           </head>
           
           <body>
           
               <table class='body' width='100%' cellspacing='0' cellpadding='0'>
                   <tr>
                       <td class='center' align='center' valign='middle'>
                           <table class='body main '
                               style='background-color:#FFFFFF; width: 600px;box-shadow: 0px 4px 40px rgba(87, 87, 87, 0.1);'>
           
           
           
           
                               <tr>
                                   <td style='padding: 60px 40px 60px;'>
                                       <p style=' font-weight: 400;
                                                   font-size: 16px;
                                                   line-height: 24px;
                                                   color: #30333F;
                                                   padding-bottom: 20px;
                                                   margin: 0 !important;
                                                   font-family: 'Manrope' !important;'>
                                           [Date: ]
                                       </p>
                                       <p style=' font-weight: 400;
                                                   font-size: 16px;
                                                   line-height: 24px;
                                                   color: #30333F;
                                                   padding-bottom: 20px;
                                                   margin: 0 !important;
                                                   font-family: 'Manrope' !important;'>
                                           Dear Thirumala:,
                                       </p>
                                       <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 24px;
                                       color: #30333F;
                                       padding-bottom: 24px;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                           Terralogic Document Systems completed digitization of your records on {{Initial Date}}.
                                           These files were uploaded via FTP and sent via external hard drive(s). We have yet to
                                           receive the signed Project Summary Report from your organization. Per the Project
                                           Summary Report memorandum of understanding that was sent 6 times within the preceding 60
                                           days, TDS only retains project data/images on our servers for 60 calendar days from
                                           completion of the project. <b>ALL PROJECT will be purged/deleted from our server TODAY
                                               at
                                               close of business day, 5:00PM M.S.T.</b></p>
           
                                       <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 24px;
                                       color: #30333F;
                                       padding-bottom: 20px;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                           As there has been no response from your organization, TDS assumes that the client has
                                           fully reviewed the images & data and the job was performed satisfactorily.
                                       </p>
           
           
                                       <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 27px;
                                       color: #30333F;
                                       padding-bottom: 20px;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                           We appreciate your business and support of Terralogic Document Systems.</p>
           
                                       <p style=' font-weight: 400;
                                       font-size: 16px;
                                       line-height: 24px;
                                       color: #30333F;
                                       padding-bottom: 20px;
                                       margin: 0 !important;
                                       font-family: 'Manrope' !important;'>
                                           Thank you,</p>
           
           
           
                                       <p style=' font-weight: 400;
                                           font-size: 16px;
                                           line-height: 20px;
                                           color: #30333F;
                                           margin: 0 !important;
                                           font-family: 'Manrope' !important;'>
                                           <b>Monica Dell</b><br>
                                           <b>Projects Coordinator</b><br>
                                           Terralogic Document Systems Inc.<br>
                                           El Paso, Albuquerque, Midland, Colorado Springs<br>
                                           Office: <a href='#'
                                               style='color: #30333F;text-decoration-line: none;'>915-593-3100</a><br>
                                           Toll Free: <a href='#'
                                               style='color: #30333F; text-decoration-line: none;'>800-644-7112</a><br>
                                           <a href='#'>www.pdswest.com</a>
                                       </p>
           
                                       <a href='#'><img src='src='".base_url()."uploads/emailimages/psrlogo.png' alt='Image' width='250' height='120'
                                               style='padding-top: 24px;'></a>
                                   </td>
           
                               </tr>
           
           
           
           
                           </table>
                       </td>
                   </tr>
               </table>
           </body>";
           $emailInfo=$this->sendEmailOne('thirumala.b@terralogic.com','Mail from Psr',$emailtemplate);
        }
     

    }


    public function sendEmailOne($tomail,$subject,$body){
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.gmail.com';
        $config['smtp_user'] = 'thirucshh411@gmail.com';
        $config['smtp_pass'] = 'xmgusnumvwjlptvh';
        $config['smtp_port'] = 465;
        $config['mailtype'] = "html";
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->from('thirucshh411@gmail.com', 'PSR');
        $this->email->to($tomail);
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->cc('udaykumar.bobbili@terralogic.com');
        $result = $this->email->send();
        if($result){
            return $result;	
        }else{
          return $this->email->print_debugger();
        }
        
    }
    
    
}
