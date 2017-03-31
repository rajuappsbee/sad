<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Auth extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        /*$this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key*/
        $this->load->model('Account','',TRUE);
    }

    public function signup_post()
    {
        $data['fname'] = $this->input->post('fname');
        $data['lname'] = $this->input->post('lname');
        $data['email'] = $this->input->post('email');
        $data['password'] = $this->input->post('password');
        $is_social = $this->input->post('is_social');
        $data['asocial'] = $this->input->post('signup_source');
        $data['asocial_id'] = $this->input->post('social_id');


        if($is_social == 1){
            $accountData = array(
                                array(
                                    'key' => 'aemail',
                                    'value' => $data['email'],
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'asocial',
                                    'value' => $data['asocial'],
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'asocial_id',
                                    'value' => $data['asocial_id'],
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'afirstname',
                                    'value' => $data['fname'],
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'alastname',
                                    'value' => $data['lname'],
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'aspeed',
                                    'value' => '25',
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'apasscode',
                                    'value' => rand(1111,9999),
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'acreated',
                                    'value' => date('Y-m-d H:i:s'),
                                    'escape' => true
                                )
                            );
            if($this->Account->emailExists($data['email'])){
                // Set the response and exit
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'Email already exists!',
                    'response' => new stdClass()
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }else{

                $account_id = $this->Account->accountExist($data['asocial_id']);
                if(!$account_id){
                    $account_id = $this->Account->addAccount($accountData);

                    $sosData = array();
                    $sosData['snumber'] = '911';
                    $sosData['account_id'] = $account_id;
                    $sosData['screated'] = date('Y-m-d H:i:s');

                    $sos_id = $this->Account->addSos($sosData);
                }
                
                if($account_id) {
                    $accData = $this->Account->getAccount($account_id);
                    $response = [
                        'status' => TRUE,
                        'message' => 'Successful!',
                        'response' => $accData
                    ];

                    $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
                }else{
                    // Set the response and exit
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No users were found',
                        'response' => new stdClass()
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }else{
            $accountData = array(
                                array(
                                    'key' => 'aemail',
                                    'value' => $data['email'],
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'apassword',
                                    'value' => md5($data['password']),
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'afirstname',
                                    'value' => $data['fname'],
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'alastname',
                                    'value' => $data['lname'],
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'aspeed',
                                    'value' => '25',
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'apasscode',
                                    'value' => rand(1111,9999),
                                    'escape' => true
                                ),
                                array(
                                    'key' => 'acreated',
                                    'value' => date('Y-m-d H:i:s'),
                                    'escape' => true
                                )
                            );

            if($this->Account->emailExists($data['email'])){
                // Set the response and exit
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'Email already exists!',
                    'response' => new stdClass()
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }else{

                $account_id = $this->Account->addAccount($accountData);

                $sosData = array();
                $sosData['snumber'] = '911';
                $sosData['account_id'] = $account_id;
                $sosData['screated'] = date('Y-m-d H:i:s');

                $sos_id = $this->Account->addSos($sosData);

                if($account_id) {
                    $accData = $this->Account->getAccount($account_id);
                    $response = [
                        'status' => TRUE,
                        'message' => 'Successful!',
                        'response' => $accData
                    ];

                    $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
                }else{
                    // Set the response and exit
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No users were found',
                        'response' => new stdClass()
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }
    }

    public function login_post(){
        $data['email'] = $this->input->post('email');
        $data['password'] = $this->input->post('password');

        if(!$this->Account->emailExists($data['email'])){
            // Set the response and exit
            $this->set_response([
                'status' => FALSE,
                'message' => 'No users were found with this Email id!',
                'response' => new stdClass()
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }else{

            $accountData = $this->Account->accountLogin($data['email'],$data['password']);

            if($accountData) {
                $response = [
                    'status' => TRUE,
                    'message' => 'Successful!',
                    'response' => $accountData
                ];

                $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
            }else{
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Password not correct!',
                    'response' => new stdClass()
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }

    public function getProfile_post(){
        $data['id'] = $this->input->post('accountId');
        $accountData = $this->Account->getAccount($data['id']);

        if(!$accountData){
            // Set the response and exit
            $this->set_response([
                'status' => FALSE,
                'message' => 'No users were found with this id!',
                'response' => new stdClass()
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }else{
            $response = [
                'status' => TRUE,
                'message' => 'Successful!',
                'response' => $accountData
            ];

            $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        }
    }

    public function changePassword_post(){
        $data['id'] = $this->input->post('accountId');
        $data['old_password'] = $this->input->post('old_password');
        $uData['apassword'] = $this->input->post('new_password');

        $accountData = $this->Account->getAccountAuthInformation($data['id']);

        if(!$accountData){
            // Set the response and exit
            $this->set_response([
                'status' => FALSE,
                'message' => 'No users were found with this id!',
                'response' => new stdClass()
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }else{
            if(($accountData->apassword == "") && ($accountData->asocial != 'E')){
                $logType = ($accountData->asocial == 'F')?'Facebook':'Twitter';
                // Set the response and exit
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'You cannot change password as you have logged in with'.$logType.'.',
                    'response' => new stdClass()
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }else{
                if( !empty($data['old_password']) && (md5($data['old_password']) == $accountData->apassword) ){
                    if($this->Account->updateAccount($uData,$accountData->id)){
                        $response = [
                            'status' => TRUE,
                            'message' => 'Password reset successful!',
                            'response' => $accountData
                        ];

                        $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
                    }else{
                        // Set the response and exit
                        $this->set_response([
                            'status' => FALSE,
                            'message' => 'Password reset unsuccessful! Please try again.',
                            'response' => new stdClass()
                        ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                    }
                }else{
                    // Set the response and exit
                    $this->set_response([
                        'status' => FALSE,
                        'message' => 'Old password does not match!',
                        'response' => new stdClass()
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        }
    }

    public function updateProfile_post(){

        $data['id'] = $this->input->post('accountId');
        $uData['afirstname'] = $this->input->post('fname');
        $uData['alastname'] = $this->input->post('lname');
        $uData['alocation'] = $this->input->post('location');

        if (!empty($_FILES['photo']['name'])) {
            $photoUploadPath = 'uploads/';
            if (!file_exists($photoUploadPath))     
                mkdir($photoUploadPath, 0777, true);

            $this->load->library('upload');
            $config = array();
            $config['upload_path'] = $photoUploadPath;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '1024000'; //10240
            $config['max_width']  = '0';
            $config['max_height']  = '0';
            $config['file_name'] = bin2hex( mcrypt_create_iv( 32, MCRYPT_DEV_URANDOM ) );
            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload('photo',$config) ){
                // Set the response and exit
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'Profile image does not updated! Please try again.',
                    'response' => $this->upload->display_errors()
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }else{
                $uphdata = array('upload_data' => $this->upload->data());
                $uData['aprofile_photo'] = $uphdata['upload_data']['file_name'];
            }
        }

        $accountData = $this->Account->updateAccount($uData,$data['id']);

        if($accountData){
            $response = [
                'status' => TRUE,
                'message' => 'Profile update successfully!',
                'response' => new stdClass()
            ];

            $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        }else{
            // Set the response and exit
            $this->set_response([
                'status' => FALSE,
                'message' => 'Profile does not updated! Please try again.',
                'response' => new stdClass()
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function setSpeed_post(){

        $data['id'] = $this->input->post('accountId');
        $uData['aspeed'] = $this->input->post('speed');
        $accountData = $this->Account->updateAccount($uData,$data['id']);

        if($accountData){
            $response = [
                'status' => TRUE,
                'message' => 'Profile update successfully!',
                'response' => new stdClass()
            ];

            $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        }else{
            // Set the response and exit
            $this->set_response([
                'status' => FALSE,
                'message' => 'Profile does not updated! Please try again.',
                'response' => new stdClass()
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function getSpeed_post(){

        $data['id'] = $this->input->post('accountId');
        $accountData = $this->Account->getAccountField($data['id'],'aspeed');

        if(!empty($accountData)){
            $response = [
                'status' => TRUE,
                'message' => 'Profile speed fetched successfully!',
                'response' => $accountData
            ];

            $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        }else{
            // Set the response and exit
            $this->set_response([
                'status' => FALSE,
                'message' => 'Profile speed does not found! Please try again.',
                'response' => new stdClass()
            ], REST_Controller::HTTP_OK);
        }
    }

    public function setSos_post(){

        $sosData = array();
        $sosData['account_id'] = $this->input->post('accountId');
        $sosData['sname'] = $this->input->post('sname');
        $sosData['snumber'] = $this->input->post('snumber');
        $data['id'] = $this->input->post('s_id');

        $apasscodeData = $this->Account->getAccountField($sosData['account_id'],'apasscode');

        if($data['id'] > 0){
            $updStatus = $this->Account->updateSos($sosData,$data['id']);
            $sos_id = $data['id'];
            $msg = 'SOS number updated successfully.';
        }else{
            $sosData['screated'] = date('Y-m-d H:i:s');
            $sos_id = $this->Account->addSos($sosData);
            $sosData['id'] = $sos_id;
            $msg = 'SOS number added successfully.';
        }


        if($sos_id > 0){
            $result = new stdClass();
            $result->apasscode = $apasscodeData->apasscode;
            $result->sosData = $sosData;
            $response = [
                'status' => TRUE,
                'message' => $msg,
                'response' => $result
            ];

            $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        }else{
            // Set the response and exit
            $this->set_response([
                'status' => FALSE,
                'message' => 'SOS number does not updated! Please try again.',
                'response' => new stdClass()
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function getSos_post(){

        $data['id'] = $this->input->post('accountId');
        $apasscodeData = $this->Account->getAccountField($data['id'],'apasscode');
        $sosData = $this->Account->getSos($data['id']);

        if(!empty($sosData)){
            $result = new stdClass();
            $result->apasscode = $apasscodeData->apasscode;
            $result->sosData = $sosData;
            $response = [
                'status' => TRUE,
                'message' => 'SOS numbers fetched successfully!',
                'response' => $result
            ];

            $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        }else{
            // Set the response and exit
            $this->set_response([
                'status' => FALSE,
                'message' => 'SOS numbers are not found! Please try again.',
                'response' => new stdClass()
            ], REST_Controller::HTTP_OK);
        }
    }

    public function forgotPassword_post() { 
        $from_email = "no-reply@sad.com"; 
        $account_email = $this->input->post('email');

        $accountData = $this->Account->emailExists($account_email);

        if(!empty($accountData)){
            $to_email = $accountData;
            $uData['apassword'] = rand('1111,9999').rand('a','z');
            $updPassword = $this->Account->updatePassword($uData,$to_email);
            //Load email library 
            $this->load->library('email'); 

            $this->email->from($from_email, 'SAD'); 
            $this->email->to($to_email);
            $this->email->subject('Recover Password'); 
            $this->email->message('Your new password is "'.$uData['apassword'].'". Please use it to login.'); 

            //Send mail 
            if($this->email->send()){
                //$this->session->set_flashdata("email_sent","Email sent successfully."); 
                $response = [
                    'status' => TRUE,
                    'message' => 'Email sent successfully.',
                    'response' => new stdClass()
                ];

                $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
            } else {
                //$this->session->set_flashdata("email_sent","Error in sending Email.");
                // Set the response and exit
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'Error in sending Email. Please try again!',
                    'response' => new stdClass()
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }else{
            // Set the response and exit
            $this->set_response([
                'status' => FALSE,
                'message' => 'No users were found with this email!',
                'response' => new stdClass()
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

}
