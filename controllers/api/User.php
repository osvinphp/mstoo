    <?php
    error_reporting(0);
    ini_set("display_errors",0);
    defined('BASEPATH') OR exit('No direct script access allowed');
    // This can be removed if you use __autoload() in config.php OR use Modular Extensions
    require APPPATH . '/libraries/REST_Controller.php';
    require APPPATH. '/libraries/Twilio/autoload.php';
    require APPPATH. '/third_party/vendor/autoload.php';
    // require __DIR__ . '/vendor/autoload.php';
    use sendotp\sendotp;
    /**
    * This is an example of a few basic user interaction methods you could use
    * all done with a hardcoded array
    *
    * @package         CodeIgniter
    * @subpackage      Rest Server
    * @category        Controller
    * @author          Ravinder Sharma,Osvin web solution.
    * @license         MIT
    * @link            https://github.com/chriskacerguis/codeigniter-restserver
    */
    class User extends REST_Controller {
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('User_model');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        $this->load->library('email');
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }
    public function signup_post(){

        $data = array(
            'name'=>$this->input->post('name'),
            'email'=>$this->input->post('email'),
            'password'=>md5($this->input->post('password')),
            'country_code'=>$this->input->post('country_code'),
            'phone'=>$this->input->post('phone'),
            'date_created' =>date('Y-m-d H:i:s')
        );
        $num=$data['country_code'].$data['phone'];
        $type=$this->input->post('type');
        /*profile pic of user start*/
        $image='profile_pic';
        $upload_path='public/profile_img';
        $imagename=$this->file_upload($upload_path,$image);
        $data['profile_pic']=$imagename;
        $signUpData=$this->User_model->sign_up($data,$type);

        if (!empty($signUpData) && $signUpData != 0 && $signUpData != 2){
            $otp = new sendotp('189865AFgPmFtnr5j5a424853','Message  : Your otp is {{otp}}. Please do not share with anyone.');
            $sendotp=($otp->send($num, 'MSGIND'));
            $result = array(
                "controller" => "User",
                "action" => "signup",
                "ResponseCode" => true,
                "MessageWhatHappen" =>"sucessfully registered.",
                "signUpResponse" => $signUpData
            );
        }
        else if($signUpData == 0){
            $result = array(
            "controller" => "User",
            "action" => "signup",
            "ResponseCode" => false,
            "MessageWhatHappen" => "User already exists.",
            );
        }
        else if($signUpData == 2){
            $result = array(
            "controller" => "User",
            "action" => "signup",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Mobile number already exists.",
            );
        }
        else {
            $result = array(
            "controller" => "User",
            "action" => "signup",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Something went wrong.",
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function login_post(){
        $data = array(
            'email'=>$this->input->post('email'),
            'password'=>md5($this->input->post('password')),
        );
        $loginParams =  array(
            'device_id'=>$this->input->post('device_id'),
            'unique_device_id '=>$this->input->post('unique_device_id'),
            'token_id'=>$this->input->post('token_id'),
            'date_created' =>date('Y-m-d H:i:s')
        );
        $type=$this->input->post('login_type');
        $var = $this->User_model->login($data,$loginParams,$type);

        if(!empty($var) && $var != 0 && $var != 7){
            $result = array(
            "controller" => "User",
            "action" => "login",
            "ResponseCode" => true,
            "MessageWhatHappen" => "Sucessfully Login.",
            "loginResponse" => $var
            );
        }
        elseif($var== 0){
            $result = array(
            "controller" => "User",
            "action" => "login",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Login credential is incorrect."
            );
        }
        elseif($var== 7){
            $result = array(
            "controller" => "User",
            "action" => "login",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Due to some reason, your account has been suspended by admin. Please contact the admin to resolve this issue."
            );
        }
        $this->set_response($result, REST_Controller::HTTP_OK);
    }
    public function verify_post(){
        $user_id=$this->input->post('user_id');
        $is_verified=$this->input->post('is_verified');
        $var= $this->User_model->update_data('ms_users',array('is_verified'=>$is_verified),array('id'=>$user_id));
        if($var){
            $result = array(
            "controller" => "User",
            "action" => "verify",
            "ResponseCode" => true,
            "MessageWhatHappen" => "Your account has been sucessfully verified.",
            "verifyresponse" => $var
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "verify",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Something went wrong."
            );
        }
        $this->set_response($result, REST_Controller::HTTP_OK);
    }
    public function logout_post(){
        $user_id=$this->input->post('user_id');
        $unique_device_id=$this->input->post('unique_device_id');
        $log_out= $this->User_model->log_out($unique_device_id,$user_id);
        if (!empty($log_out) && $log_out != 0){
            $result = array(
            "controller" => "User",
            "action" => "logout",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Sucessfully logged out.",
            "logoutResponse" => $log_out
            );
        }
        else if($log_out == 0){
            $result = array(
            "controller" => "User",
            "action" => "logout",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Something went wrong."
            );
        }
        $this->set_response($result, REST_Controller::HTTP_OK);
    }
    public function getprofile_post(){
        $id= $this->input->post('user_id');
        $data=$this->db->query("SELECT id,name,email,country_code,phone,profile_pic from ms_users where id='".$id."'")->row();
        if (!empty($data)){
            $result = array(
            "controller" => "User",
            "action" => "getprofile",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your profile data shows sucessfully.",
            "Response" => $data
            );
        }
        else {
            $result = array(
            "controller" => "User",
            "action" => "getprofile",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong.",
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function forgotpassword_post(){
    	$phone=$this->input->post('phone');
    	$country_code=$this->input->post('country_code');
    	$data=$this->db->query("SELECT * from ms_users where phone='".$phone."' ")->row();
    	if (!empty($data)) {    		
	    	$num=$country_code.$phone;
			$otp = new sendotp('189865AFgPmFtnr5j5a424853','Message  : Your otp is {{otp}}. Please do not share with anyone.');
			$sendotp=$otp->send($num, 'MSGIND');
			if ($sendotp['type']=='success') {
				$result = array(
				"controller" => "User",
				"action" => "forgotpassword",
				"ResponseCode" => true,
				"otp" =>$sendotp['otp'],
				);
			}
			else{
				$result = array(
				"controller" => "User",
				"action" => "forgotpassword",
				"ResponseCode" => false,
				"forgotpassword" =>$sendotp['message'],
				"forgotpasswordresponse"=>$sendotp
				);
			}
    	}
    	else{
				$result = array(
				"controller" => "User",
				"action" => "forgotpassword",
				"ResponseCode" => false,
				"MessageWhatHappen" =>"Phone number doesn't exists.",
			);

    	}
    	$this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function mypost_post(){
        $user_id= $this->input->post('user_id');
        $page=$this->input->post('offset');
        
        $data=$this->db->query("SELECT * from ms_post where user_id='".$user_id."' ORDER BY date_created desc ")->result();
        $postdata=array();
        foreach ($data as $key => $value) {
        	$messagedata=$this->db->query("SELECT  ms_chat.*  from ms_chat join ms_post on ms_post.id=ms_chat.post_id where post_id='".$value->id."' GROUP BY IF ('".$user_id."' = from_id,to_id,from_id) ")->result();

        	$data[$key]->replies=count($messagedata);
            if ($value->monthly=="") {
                $data[$key]->monthly="";
            }
            if ($value->weekly=="") {
                $data[$key]->weekly="";
            }
            if ($value->daily=="") {
                $data[$key]->daily="";
            }


            $postdata= unserialize($value->images);
            $postdata=array_filter($postdata);
            $postdata=array_values($postdata);
            if (!empty($postdata)) {
                $data[$key]->itemimages=$postdata;
            }
            else{
                $data[$key]->itemimages=array();
            }
        }
        $count=count($data);
        $data=array_slice($data, $page, 10 );
        if (!empty($data)){
            $result = array(
            "controller" => "User",
            "action" => "mypost",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your post data shows sucessfully.",
            "myPostList" => $data,
            'count'=>$count
            );
        }
        elseif(empty($data)) {
            $result = array(
            "controller" => "User",
            "action" => "mypost",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Data does not exist in table.",
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "mypost",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function postDetail_post(){
        $post_id= $this->input->post('post_id');
        $data=$this->db->query("SELECT * from ms_post where id='".$post_id."' ORDER BY date_created desc ")->result();
        $postdata=array();
        foreach ($data as $key => $value) {
            if ($value->monthly=="") {
                $data[$key]->monthly="";
            }
            if ($value->weekly=="") {
                $data[$key]->weekly="";
            }
            if ($value->daily=="") {
                $data[$key]->daily="";
            }
            $postdata= unserialize($value->images);
            $postdata=array_filter($postdata);
            $postdata=array_values($postdata);
            if (!empty($postdata)) {
                $data[$key]->itemimages=$postdata;
            }
            else{
                $data[$key]->itemimages=array();
            }
        }
        if (!empty($data)){
            $result = array(
            "controller" => "User",
            "action" => "postDetail",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your data shows sucessfully.",
            "myPostList" => $data,
            );
        }
        elseif(empty($data)) {
            $result = array(
            "controller" => "User",
            "action" => "postDetail",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Data doesnot exist in table.",
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "postDetail",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }

    public function updateLocation_post(){ /* api for updating the location of the user */
        $data = array(
            'location_name'=>$this->input->post('location_name'),
            'latitude'=>$this->input->post('latitude'),
            'longitude'=>$this->input->post('longitude')
        );
        $user_id = $this->input->post('user_id');
        $getUser = $this->User_model->select_data('*','ms_users','id = '.$user_id.'');
        if(!empty($getUser)){
        $this->db->where('id',$user_id);
        $this->db->update('ms_users',$data);
        $result = array(
            "controller" => "User",
            "action" => "updateLocation",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"User location updated Successfully."
            );
        }else{
             $result = array(
            "controller" => "User",
            "action" => "updateLocation",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"User not found."
            );
        }
   
        $this->set_response($result,REST_Controller::HTTP_OK);

    } 


      public function getCategories_get(){
       //$resArray = array();
       $getCat = $this->db->select('*')
                           ->from('ms_categories')
                           ->get()->result();
           foreach ($getCat as $key => $value) {
              $value->icon = base_url().'public/assets/images/'.$value->icon;
              $getSubCats = $this->db->select('id,sub_category_name,icon')
                                      ->from('ms_sub_categories')
                                      ->where('category_id',$value->id)
                                      ->get()->result();
                                      // print_r($this->db->last_query()); die;
                                      foreach ($getSubCats as  $conc) {
                                        $conc->icon = base_url().'public/assets/images/'.$conc->icon;
                                      }
               $value->subCat_data = $getSubCats;
             
           }
           if(empty($getCat)) {
            $result = array(
            "controller" => "User",
            "action" => "getCategories",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"No categories found",
            "getCategoriesResponse" => ""
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "getCategories",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Categories Data",
            "getCategoriesResponse" => $getCat
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);

      }

    
    public function editprofile_post(){
        $arra = array(
        'name'=>$this->input->post('name'),
        );
        $user_id=$this->input->post('user_id');
        $email=$this->input->post('email');
        if(!empty($email)){
            $userdata=$this->db->query("SELECT * from ms_users where email='".$email."' and id != '".$user_id."' ")->row();
            if ($userdata) {
                $result = array(
                "controller" => "User",
                "action" => "editprofile",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"Email already exists."
                );
                print_r(json_encode($result));die;
            }
        }
        $arra['email']=$email;
        /*updation of profile pic start*/
        if (isset($_FILES['profile_pic'])) {
            $image='profile_pic';
            $upload_path='public/profile_img';
            $imagename=$this->file_upload($upload_path,$image);
            $arra['profile_pic']=$imagename;
        }
        $data=array_filter($arra);
        $var= $this->User_model->update_data('ms_users',$data,array('id'=>$user_id));
        $userdata=$this->db->query("SELECT id,name,email,country_code,phone,profile_pic from ms_users where id='".$user_id."'")->row();
        if ($userdata) {
            $result = array(
            "controller" => "User",
            "action" => "editprofile",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Profile updated.",
            "editProfileResponse"=>$userdata
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "editprofile",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function changepassword_post(){
        $user_id=$this->input->post('user_id');
        $new_password=md5($this->input->post('newpassword'));
        $old_password=md5($this->input->post('oldpassword'));
        $passwordchk = $this->User_model->select_data('*','ms_users',array('id'=>$user_id,'password'=>$old_password));
        /*checking of old password start*/
        if($passwordchk){
            $data['password']=$new_password;
            $var= $this->User_model->update_data('ms_users',$data,array('id'=>$user_id));
            $getRes=$this->db->query("SELECT id,name,email,country_code,phone,profile_pic from ms_users where id='".$user_id."'")->row();
            if ($var) {
                $result = array(
                "controller" => "User",
                "action" => "changepassword",
                "ResponseCode" => true,
                "MessageWhatHappen" =>"Password changed sucessfully.",
                "changepasswordresponse"=>$getRes
                );
            }
            else{
                $result = array(
                "controller" => "User",
                "action" => "changepassword",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"Something went wrong."
                );
            }
        }
        else {
            $result = array(
            "controller" => "User",
            "action" => "changepassword",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Current password doesn't match."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function blockuser_post(){
        $data = array(
        'user_id'=>$this->input->post('user_id'),
        'block_users_id'=>$this->input->post('block_users_id'),
        'date_created'=>date('Y-m-d H:i:s'),
        'status'=>$this->input->post('status')
        );
        $res = $this->User_model->select_data('*','ms_block_users',array('user_id'=>$data['user_id'],'block_users_id'=>$data['block_users_id']));
        if (empty($res)) {
            $var=$this->User_model->insert_data('ms_block_users',$data);
            if ($data['status']==1 ) {
                $result = array(
                "controller" => "User",
                "action" => "blockuser",
                "ResponseCode" => true,
                "MessageWhatHappen" =>"User blocked sucessfully."
                );
            }
            elseif ($data['status']==2 ) {
                $result = array(
                "controller" => "User",
                "action" => "blockuser",
                "ResponseCode" => true,
                "MessageWhatHappen" =>"User unblocked sucessfully."
                );
            }
            else{
                $result = array(
                "controller" => "User",
                "action" => "blockuser",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"Something went wrong."
                );
            }
        }
        else{

            if ($data['status']==1) {
                $var= $this->User_model->update_data('ms_block_users',array('status'=>$data['status']),array('id'=>$res[0]->id));
                if ( $data['status']==1 ) {
                    $result = array(
                    "controller" => "User",
                    "action" => "blockuser",
                    "ResponseCode" => true,
                    "MessageWhatHappen" =>"User blocked sucessfully."
                    );
                }
                elseif ( $data['status']==2 ) {
                    $result = array(
                    "controller" => "User",
                    "action" => "blockuser",
                    "ResponseCode" => true,
                    "MessageWhatHappen" =>"User unblocked sucessfully."
                    );
                }
                else{
                    $result = array(
                    "controller" => "User",
                    "action" => "blockuser",
                    "ResponseCode" => false,
                    "MessageWhatHappen" =>"Something went wrong."
                    );
                }
            }
            elseif($data['status']==2){
                if ($res[0]->user_id==$data['user_id']) {
                    $var= $this->User_model->update_data('ms_block_users',array('status'=>$data['status']),array('id'=>$res[0]->id));
                    if ($var) {
                        $result = array(
                        "controller" => "User",
                        "action" => "blockuser",
                        "ResponseCode" => true,
                        "MessageWhatHappen" =>"User unblocked sucessfully."
                        );
                    }
                    else{
                        $result = array(
                        "controller" => "User",
                        "action" => "blockuser",
                        "ResponseCode" => false,
                        "MessageWhatHappen" =>"Something went wrong."
                        );
                    }
                } 
                else{
                    $result = array(
                        "controller" => "User",
                        "action" => "blockuser",
                        "ResponseCode" => false,
                        "MessageWhatHappen" =>"You can't unblock."
                    );

                }  
            }
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function postspam_post(){
        $data=array(
            'user_id'=>$this->input->post('user_id'),
            'post_id'=>$this->input->post('post_id'),
            'date_created'=>date('Y-m-d H:i:s')
        );
        $var=$this->User_model->insert_data('ms_post_spam',$data);
        if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "postspam",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your request has been submitted."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "postspam",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    /*used delete method*/
    public function postdelete_delete(){
        $id=$_REQUEST['postid'];
        $table='ms_post';
        $var=$this->User_model->delete_posts($id,$table);
        if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "postdelete",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Post deleted sucessfully."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "postdelete",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function mymessages_post(){
        $user_id=$this->input->post("user_id");
        $res=$this->User_model->select_data('*','ms_chat',array('form_id'=>$user_id));
        if ($res) {
            $result = array(
            "controller" => "User",
            "action" => "mymessages",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your messages shows sucessfully.",
            "response" =>$res
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "mymessages",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function updateShare_post(){
        $data=array(
            'post_id'=>$this->input->post('post_id'),
            'post_creator_id'=>$this->input->post('post_creator_id'),
            'user_id'=>$this->input->post('user_id'),
            'call_share_owner'=>$this->input->post('call_share_owner'),
            'call_share_customer'=>$this->input->post('call_share_customer'),
            'loc_status'=>$this->input->post('loc_status')
        );
        $type=$this->input->post('type');

        // if (empty($data['call_share_customer'])) {
        // }


        $res=$this->User_model->select_data('*','ms_share_info',array('post_id'=>$data['post_id'],'post_creator_id'=>$data['post_creator_id'],'user_id'=>$data['user_id']));
        if ($type==1) {
            if (empty($res)) {
                if (empty($data['call_share_owner'])) {
                    $data['call_share_owner']=2;
                }
                if (empty($data['call_share_customer'])) {
                    $data['call_share_customer']=2;
                }
                if (empty($data['loc_status'])) {
                    $data['loc_status']=2;
                }
                $var=$this->User_model->insert_data('ms_share_info',$data);
            }
            else{
                if (empty($data['call_share_owner'])) {
                    $data['call_share_owner']=$res[0]->call_share_owner;
                }
                if (empty($data['call_share_customer'])) {
                    $data['call_share_customer']=$res[0]->call_share_customer;
                }
                if (empty($data['loc_status'])) {
                    $data['loc_status']=$res[0]->loc_status;
                }
                $var=$this->User_model->update_data('ms_share_info',$data,array('post_id'=>$data['post_id']));
            } 
            $checklogin=$this->User_model->select_data('*','ms_login',array('user_id'=>$data['user_id'],'status'=>1));
            $fromdata=$this->db->query("SELECT name,email,phone,profile_pic from ms_users where id='".$data['user_id']."'")->row();  
        }
        elseif ($type==2) {
            if (empty($res)) {
                if (empty($data['call_share_owner'])) {
                    $data['call_share_owner']=2;
                }
                if (empty($data['call_share_customer'])) {
                    $data['call_share_customer']=2;
                }
                if (empty($data['loc_status'])) {
                    $data['loc_status']=2;
                }
                $var=$this->User_model->insert_data('ms_share_info',$data);
            }
            else{
                if (empty($data['call_share_owner'])) {
                    $data['call_share_owner']=$res[0]->call_share_owner;
                }
                if (empty($data['call_share_customer'])) {
                    $data['call_share_customer']=$res[0]->call_share_customer;
                }
                if (empty($data['loc_status'])) {
                    $data['loc_status']=$res[0]->loc_status;
                }
                $var=$this->User_model->update_data('ms_share_info',$data,array('post_id'=>$data['post_id']));
            } 
            $checklogin=$this->User_model->select_data('*','ms_login',array('user_id'=>$data['user_id'],'status'=>1));
            $fromdata=$this->db->query("SELECT name,email,phone,profile_pic from ms_users where id='".$data['user_id']."'")->row();

        }
        elseif ($type==3) {
            if (empty($res)) {

                if (empty($data['call_share_owner'])) {
                    $data['call_share_owner']=2;
                }
                if (empty($data['call_share_customer'])) {
                    $data['call_share_customer']=2;
                }
                if (empty($data['loc_status'])) {
                    $data['loc_status']=2;
                }
                $var=$this->User_model->insert_data('ms_share_info',$data);
            }
            else{
                if (empty($data['call_share_owner'])) {
                    $data['call_share_owner']=$res[0]->call_share_owner;
                }
                if (empty($data['call_share_customer'])) {
                    $data['call_share_customer']=$res[0]->call_share_customer;
                }
                if (empty($data['loc_status'])) {
                    $data['loc_status']=$res[0]->loc_status;
                }
                $var=$this->User_model->update_data('ms_share_info',$data,array('post_id'=>$data['post_id']));
            } 
            $checklogin=$this->User_model->select_data('*','ms_login',array('user_id'=>$data['post_creator_id'],'status'=>1));
            $fromdata=$this->db->query("SELECT name,email,phone,profile_pic from ms_users where id='".$data['post_creator_id']."'")->row();
         
        }
        foreach ($checklogin as $key => $value) {
            $postDetail=$this->db->query("SELECT * from ms_post where id='".$data['post_id']."'")->row();
            $pushData['title']=$postDetail->title;
            $pushData['message'] = $postDetail;
            $pushData['action']="4";
            $pushData['call_share_owner']=(int)$data['call_share_owner'];
            $pushData['call_share_customer']=(int)$data['call_share_customer'];
            $pushData['loc_status']=(int)$data['loc_status'];
            $pushData['token'] = $value->token_id;
            $pushData['bodymessage']=$fromdata->name." have share status with you.";
            if($value->device_id == 1){
                $this->User_model->iosPush($pushData);
            }
            else if($value->device_id == 0){
                $this->User_model->androidPush($pushData);
            }
        }
        if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "updateShare",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your status updated sucessfully."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "updateShare",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }

    public function updateSharestatus_post(){
		$data=array(
			'post_id'=>$this->input->post('post_id'),
            'post_creator_id'=>$this->input->post('post_creator_id'),
			'user_id'=>$this->input->post('user_id'),
			'call_share_owner'=>$this->input->post('call_share_owner'),
            'call_share_customer'=>$this->input->post('call_share_customer'),
			'loc_status'=>$this->input->post('loc_status'),
		);
        $type=$this->input->post('type');
		$data=array_filter($data);
        $res=$this->User_model->select_data('*','ms_share_info',array('post_id'=>$data['post_id'],'post_creator_id'=>$data['post_creator_id'],'user_id'=>$data['user_id']));
        if (empty($res)) {
            $var=$this->User_model->insert_data('ms_share_info',$data);
        }
        else{
    	   $var=$this->User_model->update_data('ms_share_info',$data,array('post_id'=>$data['post_id']));
        }



        if (!empty($data['call_share_owner'])) {            
    		$checklogin=$this->User_model->select_data('*','ms_login',array('user_id'=>$data['user_id'],'status'=>1));
    		$fromdata=$this->db->query("SELECT name,email,phone,profile_pic from ms_users where id='".$data['user_id']."'")->row();
        }
        elseif (!empty($data['call_share_customer'])){
            $checklogin=$this->User_model->select_data('*','ms_login',array('user_id'=>$data['post_creator_id'],'status'=>1));
            $fromdata=$this->db->query("SELECT name,email,phone,profile_pic from ms_users where id='".$data['post_creator_id']."'")->row();
        }
        // if (!empty($data['loc_status'])) {
        //     $checklogin=$this->User_model->select_data('*','ms_login',array('user_id'=>$data['user_id'],'status'=>1));
        //     $fromdata=$this->db->query("SELECT name,email,phone,profile_pic from ms_users where id='".$data['user_id']."'")->row();
        // }
        foreach ($checklogin as $key => $value) {


			$postDetail=$this->db->query("SELECT * from ms_post where id='".$data['post_id']."'")->row();
			$pushData['title']=$postDetail->title;
			$pushData['message'] = $postDetail;
			$pushData['action']="4";
            $pushData['call_share_owner']=(int)$data['call_share_owner'];
            $pushData['call_share_customer']=(int)$data['call_share_customer'];
            $pushData['loc_status']=(int)$data['loc_status'];
			$pushData['token'] = $value->token_id;
			$pushData['bodymessage']=$fromdata->name." have share status with you.";
			if($value->device_id == 1){
				$this->User_model->iosPush($pushData);
			}
			else if($value->device_id == 0){
				$this->User_model->androidPush($pushData);
			}
		}
    	if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "updateSharestatus",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your status updated sucessfully."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "updateSharestatus",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
	}
    public function viewpost_post(){
        $data=array(
            'radius'=>$this->input->post('radius'),
            'price'=>$this->input->post('price'),
            'lat'=>$this->input->post('lat'),
            'long'=>$this->input->post('long'),
            'type'=>$this->input->post('type'),
            'cat_id'=>$this->input->post('cat_id'),
        );
        // print_r($data);die;
        // print($data);die;
        $page=$this->input->post('offset');
        $user_id=$this->input->post('user_id');
        $findposts = $this->User_model->get_posts($data,$user_id);
        $count=count($findposts);
        $findposts=array_slice( $findposts, $page, 10 );
        if ($findposts) {
            $result = array(
            "controller" => "User",
            "action" => "viewpost",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your posts shows sucessfully.",
            "postList" =>$findposts,
            "count"=>$count
            );
        }
        elseif (empty($findposts)) {
            $result = array(
            "controller" => "User",
            "action" => "viewpost",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Data doesnot exist in table."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "viewpost",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function updatepushStatus_post(){
        $data=array(
            'from_id'=>$this->input->post('from_id'),
            'post_id'=>$this->input->post('post_id'),
            'to_id'=>$this->input->post('to_id'),
            'last_id'=>$this->input->post('last_id')
        );
        $var=$this->User_model->update_data('ms_chat',array('status'=>2),array('post_id'=>$data['post_id'],'from_id'=>$data['from_id'],'to_id'=>$data['to_id']));
        if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "updatepushStatus",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your status updated sucessfully.",
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "updatepushStatus",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }



    public function chat_post(){
        $data=array(
            'from_id'=>$this->input->post('from_id'),
            'post_id'=>$this->input->post('post_id'),
            'to_id'=>$this->input->post('to_id'),
            'message'=>$this->input->post('message'),
            'date_created'=>date('Y-m-d H:i:s')
        );
        $blockdata=$this->db->query("SELECT * from ms_block_users  where ((user_id ='".$data['from_id']."' and block_users_id='".$data['to_id']."' ) or(user_id ='".$data['to_id']."' and block_users_id='".$data['from_id']."')) and status='1' ")->result();
        if (empty($blockdata)) {
            $var=$this->User_model->insert_data('ms_chat',$data);
            $checklogin=$this->User_model->select_data('*','ms_login',array('user_id'=>$data['to_id'],'status'=>1));
          
            $sharedata=$this->db->query("SELECT * from ms_share_info where post_id='".$data['post_id']."' and ((post_creator_id='".$data['to_id']."' and  user_id='".$data['from_id']."') or (post_creator_id='".$data['from_id']."' and  user_id='".$data['to_id']."'))  ")->row();
   
            // if ($data['from_id']!=$postDetail->user_id) {            
            //     if (!empty($sharedata)) {
            //         $selectposts[$key]->call_status=$sharedata->call_status;
            //         $selectposts[$key]->loc_status=$sharedata->loc_status;
            //     }
            //     else{
            //         $selectposts[$key]->call_status=2;
            //         $selectposts[$key]->loc_status=2;
            //     }
            // }
            if (!empty($sharedata)) {
                $selectposts[$key]->call_share_owner=$sharedata->call_share_owner;
                $selectposts[$key]->call_share_customer=$sharedata->call_share_customer;

                $selectposts[$key]->loc_status=$sharedata->loc_status;
            }
            else{
                $selectposts[$key]->call_share_owner=2;
                $selectposts[$key]->call_share_customer=2;

                $selectposts[$key]->loc_status=2;
            }
            $res1=$this->db->query("SELECT * from ms_chat where id='".$var."' ")->row();
            $count=$this->db->query("SELECT *  from ms_chat where to_id='".$data['to_id']."'   and status='1'  GROUP BY IF ('".$data['to_id']."' = from_id,to_id,from_id) ")->result();

            if (!empty($count)) {
                $countdata=count($count);
            }
            else{
                $countdata=0;
            }
                $postDetail=$this->db->query("SELECT * from ms_post where id='".$data['post_id']."'")->row();
                // print_r($postDetail);die;
            foreach ($checklogin as $key => $value) {
                // $count=$this->db->query()->row();
                $fromdata=$this->db->query("SELECT name,email,phone,profile_pic from ms_users where id='".$data['from_id']."'")->row();
                $pushData['title']=$postDetail->title;
                $pushData['message'] = $res1;
                $pushData['message1'] = $data['message'];
                $pushData['fromname']=$fromdata->name;
                $pushData['fromphone']=$fromdata->phone;
                $pushData['frompic']=$fromdata->profile_pic;
                $pushData['action']="2";
                $pushData['postDetail']=$postDetail;
                $pushData['token'] = $value->token_id;
                $pushData['count'] =(int)$countdata;
                $pushData['bodymessage']=$pushData['fromname'].': '.$pushData['message1'];

                // print_r($pushData);
                if($value->device_id == 1){
                    $this->User_model->iosPush($pushData);
                }
                else if($value->device_id == 0){
                    $this->User_model->androidPush($pushData);
                }
            }
            // die;
            $res1=$this->db->query("SELECT * from ms_chat where id='".$var."' ")->row();
            if ($res1) {
                $result = array(
                "controller" => "User",
                "action" => "chat",
                "ResponseCode" => true,
                'chatResponse'=>$res1,
                "MessageWhatHappen" =>"Message send sucessfully."
                );
            }
            else{
                $result = array(
                "controller" => "User",
                "action" => "chat",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"Something went wrong."
                );
            }
        }
        else{
            $result = array(
                "controller" => "User",
                "action" => "chat",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"One of you have blocked the other to send messages.."
            );
        }

        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function particularChatList_post(){
        $from_id=$this->input->post('from_id');
        $to_id=$this->input->post('to_id');
        $post_id=$this->input->post('post_id');
        $date=date('Y-m-d H:i:s');

        $blockdata=$this->db->query("SELECT * from ms_block_users  where ((user_id ='".$from_id."' and block_users_id='".$to_id."' ) or(user_id ='".$to_id."' and block_users_id='".$from_id."')) and status='1' ")->result();
        if (empty($blockdata)) {

            $postdata=$this->db->query("SELECT * from ms_post where id='".$post_id."' ")->row();
            // if ($postdata->user_id!=$from_id) {            
                $sharedata=$this->db->query("SELECT * from ms_share_info where post_id='".$post_id."' and ((post_creator_id='".$from_id."' and  user_id='".$to_id."')or(post_creator_id='".$to_id."' and  user_id='".$from_id."'))  ")->row();
                if (!empty($sharedata)) {
                    $call_share_owner=(int)$sharedata->call_share_owner;
                    $call_share_customer=(int)$sharedata->call_share_customer;
                    $loc_status=(int)$sharedata->loc_status;
                }
                else{
                    $call_share_owner=(int)2;
                    $call_share_customer=(int)2;
                    $loc_status=(int)2;
                }
            // }
            // else{
            //     $call_status=2;
            //     $loc_status=2;
            // }
            $res1=$this->db->query("SELECT * from ms_chat where post_id='".$post_id."' and (from_id ='".$from_id."' or from_id ='".$to_id."' ) and (to_id ='".$from_id."' or to_id ='".$to_id."' ) order by date_created asc ")->result();

            foreach ($res1 as $key => $value) {
                $this->User_model->update_data('ms_chat',array('status'=>'2'),array('id'=>$value->id));
            }
            if ($res1) {
                $result = array(
                "controller" => "User",
                "action" => "particularChatList",
                "ResponseCode" => true,
                "MessageWhatHappen" =>"Your data shows sucessfully.",
                'particularChatResponse'=>$res1,
                'dateTime'=>$date,
                'call_share_owner'=>$call_share_owner,
                'call_share_customer'=>$call_share_customer,
                'loc_status'=>$loc_status
                );
            }
            elseif (empty($res1)){
                $result = array(
                "controller" => "User",
                "action" => "particularChatList",
                "ResponseCode" => true,
                'call_share_owner'=>$call_share_owner,
                'call_share_customer'=>$call_share_customer,
                'loc_status'=>$loc_status,
                "MessageWhatHappen" =>"No data exist in table.",
                );
            }
            else{
                $result = array(
                "controller" => "User",
                "action" => "particularChatList",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"Something went wrong."
                );
            }
        }
        else{
            $result = array(
                "controller" => "User",
                "action" => "particularChatList",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"One of you have blocked the other to send messages.."
                );
        }


        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    /*whats app like inbox api*/      
    public function chatlisting_post(){
        $user_one=$this->input->post('user_id');
        $data=$this->db->query("SELECT * FROM ms_chat WHERE from_id IN (     SELECT MAX(from_id) AS last_msg_id      FROM ms_chat WHERE to_id = 80 OR from_id = 80   GROUP BY to_id )")->result();
        print_r($data);die;




 //        $abc=$this->db->query("select T1.user2_id, ms_users.name, max(date_created) maxDate from  (select ms_chat.to_id user2_id, max(date_created) cdate    from ms_chat     where ms_chat.from_id=80    group by ms_chat.to_id    union distinct    (select  ms_chat.from_id user2_id, max(date_created) cdate    from ms_chat  where ms_chat.to_id = 80    group by ms_chat.from_id)) T1   inner join ms_users on (ms_users.id = T1.user2_id)    group by T1.user2_id    order by maxDate desc")->result();
        

 // SELECT  ms_chat.*,ms_post.images  from ms_chat join ms_post on ms_post.id=ms_chat.post_id  GROUP BY IF (80 = from_id,to_id,from_id) order by date_created desc.*,ms_post.images  from ms_chat join ms_post on ms_post.id=ms_chat.post_id  GROUP BY IF (80 = from_id,to_id,from_id) order by date_created desc")->result();
        


 //       // $abc=$this->db->query("SELECT T1.id ,ms_users.name, ms_users.email, max(date_created) maxDate from    (SELECT ms_chat.from_id,to_id, max(date_created) as cdate    from ms_chat     where ms_chat.to_id=80    group by ms_chat.from_id    union distinct    (SELECT  ms_chat.to_id user2_id, max(date_created) as cdate    from ms_chat  where ms_chat.from_id = 80    group by ms_chat.to_id)) as  T1  inner join ms_users on (ms_users.id = T1.to_id)  group by ms_chat.to_id    order by ms_chat.date_created desc")->result();
 //       print_r($abc);die;
       

        $abc=$this->db->query("SELECT ms_chat.* FROM
        (SELECT MAX(date_created) AS date_created
        FROM ms_chat
        WHERE '".$user_one."' IN (from_id,to_id)
        GROUP BY IF ('".$user_one."' = from_id,to_id,from_id)) AS latest
        LEFT JOIN ms_chat ON latest.date_created = ms_chat.date_created AND '".$user_one."' IN (ms_chat.from_id, ms_chat.to_id)
        GROUP BY IF ('".$user_one."' = ms_chat.from_id,ms_chat.to_id,ms_chat.from_id) order by date_created desc ")->result();




        foreach ($abc as $key => $value) {
            $count=$this->db->query("SELECT count(id) as count  from ms_chat where ((to_id='".$value->from_id."' and from_id='".$value->to_id."')or(to_id='".$value->to_id."' and from_id='".$value->from_id."')) and status='1'")->row();
            if (!empty($count)) {
                $abc[$key]->count=$count->count;
            }
            else{
                $abc[$key]->count=0;
            }
        }
        if ($abc) {
            $result = array(
            "controller" => "User",
            "action" => "chatlist",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your chat shows sucessfully",
            "listChatResponse" =>$abc
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "chatlist",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"something went wrong"
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }

    public function sellingpostMessages_post(){
        function custom_sort($a,$b) {
            return strtotime($b->date_created)>strtotime($a->date_created);
        }
        $post_id=$this->input->post('post_id');
        $user_id=$this->input->post('user_id');
        $page=$this->input->post('offset');
        if (($page=='')) {
            $result = array(
                "controller" => "User",
                "action" => "sellingpostMessages",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"please select offset."
            );
            print_r(json_encode($result));die;
        }
        $data=$this->db->query("SELECT  *,ms_post.images,ms_post.loc_name,ms_post.lat,ms_post.lng,ms_post.user_id  from ms_chat join ms_post on ms_post.id=ms_chat.post_id where post_id='".$post_id."' GROUP BY IF ('".$user_id."' = from_id,to_id,from_id)")->result();
        foreach ($data as $key => $value) {
            $blockdata=$this->db->query("SELECT * from ms_block_users where (user_id='".$value->from_id."' and block_users_id='".$value->to_id."') or  (user_id='".$value->to_id."' and block_users_id='".$value->from_id."') ")->row();
            if (empty($blockdata)) {
                $data[$key]->blockdata='2';
            }
            else{
                $data[$key]->blockdata=$blockdata->status;
            }
            // $count=$this->db->query("SELECT count(id) as count  from ms_chat where ((to_id='".$value->to_id."' and from_id='".$value->from_id."') or (to_id='".$value->from_id."' and from_id='".$value->to_id."')) and status=1 and post_id='".$value->post_id."' and status= 1 ")->row();

            $number=$this->db->query("SELECT  *  from ms_chat where  to_id='".$user_id."' and ((to_id='".$value->to_id."' and from_id='".$value->from_id."') or (to_id='".$value->from_id."' and from_id='".$value->to_id."')) and post_id='".$post_id."' and status= 1  ")->result();

            if (!empty($number)) {
                $data[$key]->count=count($number);
            }
            else{
                $data[$key]->count=0;
            }
            $message=$this->db->query("SELECT message,date_created  from ms_chat where ((to_id='".$value->to_id."' and from_id='".$value->from_id."') or (to_id='".$value->from_id."' and from_id='".$value->to_id."')) and post_id='".$post_id."' order by date_created desc ")->row();
            $data[$key]->message=$message->message;
            $data[$key]->date_created=$message->date_created;
            // if (!empty($count)) {
            //     $data[$key]->count=$count->count;
            // }
            // else{
            //     $data['buying'][$key]->count=0;
            // }
            if ($value->from_id !=$user_id) {
                $data[$key]->to_id=$value->from_id;    
                $data[$key]->from_id=$user_id ;  
            }
            $todata=$this->db->query("SELECT name,profile_pic,phone from ms_users where id='".$value->to_id."'")->row();
            $data[$key]->toname=$todata->name;
            $data[$key]->topic=$todata->profile_pic;
            $data[$key]->tophone=$todata->phone;
            $postdata= unserialize($value->images);
            $postdata=array_filter($postdata);
            $postdata=array_values($postdata);
            if (!empty($postdata)) {
                $data[$key]->itemimages=$postdata;
            }
            else{
                $data[$key]->itemimages=array();
            }
        }
        usort($data, "custom_sort");
        $count=count($data);
        $data=array_slice($data, $page, 10 );
        if ($data) {
            $result = array(
            "controller" => "User",
            "action" => "sellingpostMessages",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your chat shows sucessfully.",
            "sellingpostMessages" =>$data,
            'count'=>$count
            );
        }
        elseif (empty($data)) {
            $result = array(
            "controller" => "User",
            "action" => "sellingpostMessages",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"No data exist in table."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "sellingpostMessages",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);

    }
    /*for buying and selling case chat listing*/
    public function listingchat_post(){
        function custom_sort($a,$b) {
            return strtotime($b->date_created)>strtotime($a->date_created);
        }
        $user_id=$this->input->post('user_id');
        $type=$this->input->post('type');
        $page=$this->input->post('offset');
        if (empty($type) || ($page=='')) {
            $result = array(
                "controller" => "User",
                "action" => "listingchat",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"Please select type and offset"
            );
            print_r(json_encode($result));die;
        }
        if ($type==1) {
        /*selling case start*/  
        $data=$this->db->query("SELECT ms_chat.*,ms_post.title,ms_post.description,ms_post.images from ms_chat join ms_post on ms_post.id=ms_chat.post_id where (from_id='".$user_id."' or to_id='".$user_id."')  and ms_post.user_id='".$user_id."' group by post_id order by ms_chat.date_created desc")->result();
            $postdata=array();
            foreach ($data as $key => $value) {
                $blockdata=$this->db->query("SELECT * from ms_block_users where (user_id='".$value->from_id."' and block_users_id='".$value->to_id."') or  (user_id='".$value->to_id."' and block_users_id='".$value->from_id."') ")->row();
                if (empty($blockdata)) {
                    $data[$key]->blockdata='2';
                }
                else{
                    $data[$key]->blockdata=$blockdata->status;
                }
                if ($value->from_id !=$user_id) {
                    $data[$key]->to_id=$value->from_id;    
                    $data[$key]->from_id=$user_id ;  
                }
                $todata=$this->db->query("SELECT name,profile_pic,phone from ms_users where id='".$value->to_id."'")->row();
                $data[$key]->toname=$todata->name;
                $data[$key]->topic=$todata->profile_pic;
                $data[$key]->tophone=$todata->phone;


                $number=$this->db->query("SELECT  *  from ms_chat where post_id='".$value->post_id."' and to_id='".$user_id."' and status= 1   GROUP BY IF ('".$user_id."' = from_id,to_id,from_id) ")->result();
                if (!empty($number)) {
                    $data[$key]->count=count($number);
                }
                else{
                    $data[$key]->count=0;
                }


                $number=$this->db->query("SELECT  *  from ms_chat where post_id='".$value->post_id."' and to_id='".$user_id."'  GROUP BY IF ('".$user_id."' = from_id,to_id,from_id) ")->result();
                if (!empty($number)) {
                $data[$key]->noofpeople=count($number);
                }
                else{
                $data[$key]->noofpeople=0;
                }





                $postdata= unserialize($value->images);
                $postdata=array_filter($postdata);
                $postdata=array_values($postdata);
                if (!empty($postdata)) {
                    $data[$key]->itemimages=$postdata;
                }
                else{
                    $data[$key]->itemimages=array();
                }
            }
            usort($data, "custom_sort");
            $count=count($data);
            $data=array_slice($data, $page, 10 );
        }
        // print_r($data);die;
        /*selling case end*/

        if ($type==2) {
        /*buying case start*/
        $data=$this->db->query("SELECT ms_chat.id as chatid,ms_chat.*,ms_post.id,ms_post.title,ms_post.description,ms_post.images,ms_post.loc_name,ms_post.lat,ms_post.lng,ms_post.user_id from ms_chat join ms_post on ms_post.id=ms_chat.post_id where ms_post.user_id NOT IN ('".$user_id."') and (ms_chat.from_id='".$user_id."' or ms_chat.to_id='".$user_id."'  ) group by ms_chat.post_id  order by ms_chat.date_created desc")->result();

            foreach ($data as $key => $value) {
                $count=$this->db->query("SELECT count(id) as count  from ms_chat where ((to_id='".$value->to_id."' and from_id='".$value->from_id."') or (to_id='".$value->from_id."' and from_id='".$value->to_id."')) and to_id='".$user_id."' and status=1 and post_id='".$value->post_id."' and status= 1  ")->row();

                 // $number=$this->db->query("SELECT  *  from ms_chat where post_id='".$value->post_id."' and to_id='".$user_id."' and status= 1   GROUP BY IF ('".$user_id."' = from_id,to_id,from_id) ")->result();

                $blockdata=$this->db->query("SELECT * from ms_block_users where (user_id='".$value->from_id."' and block_users_id='".$value->to_id."') or  (user_id='".$value->to_id."' and block_users_id='".$value->from_id."') ")->row();
                if (empty($blockdata)) {
                    $data[$key]->blockdata='2';
                }
                else{
                    $data[$key]->blockdata=$blockdata->status;
                }

                if (!empty($count)) {
                    $data[$key]->count=(int)$count->count;
                }
                else{
                    $data[$key]->count=(int)0;
                }
                if ($value->from_id !=$user_id) {
                    $data[$key]->to_id=$value->from_id;    
                    $data[$key]->from_id=$user_id ;  
                }
                $message=$this->db->query("SELECT *  from ms_chat where ((to_id='".$value->to_id."' and from_id='".$value->from_id."') or (to_id='".$value->from_id."' and from_id='".$value->to_id."'))  and post_id='".$value->post_id."' order by ms_chat.date_created desc")->row();
                $data[$key]->message=$message->message;
                $data[$key]->date_created=$message->date_created;
                // $fromdata=$this->db->query("SELECT name,profile_pic,phone from ms_users where id='".$value->from_id."'")->row();
                // $data[$key]->fromname=$fromdata->name;
                // $data[$key]->frompic=$fromdata->profile_pic;
                // $data[$key]->fromphone=$fromdata->phone;
                $todata=$this->db->query("SELECT name,profile_pic,phone from ms_users where id='".$value->to_id."'")->row();
                $data[$key]->toname=$todata->name;
                $data[$key]->topic=$todata->profile_pic;
                $data[$key]->tophone=$todata->phone;


                $postdata= unserialize($value->images);
                $postdata=array_filter($postdata);
                $postdata=array_values($postdata);
                if (!empty($postdata)) {
                    $data[$key]->itemimages=$postdata;
                }
                else{
                    $data[$key]->itemimages=array();
                }
            }
            usort($data, "custom_sort");
            $count=count($data);
            $data=array_slice($data, $page, 10 );

        }    
        /*buying case end*/
        if($data){
            $result = array(
            "controller" => "User",
            "action" => "listingchat",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your chat shows sucessfully.",
            "listingChatResponse" =>$data,
            'count'=>$count
            );
        }
        elseif (empty($data)) {
            $result = array(
            "controller" => "User",
            "action" => "listingchat",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"No data exists in table."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "listingchat",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function addpost_post(){
        $data=array(
            'user_id'=>$this->input->post('user_id'),
            'title'=>$this->input->post('title'),
            'description'=>$this->input->post('description'),
            'cat_id'=>$this->input->post('cat_id'),
            'loc_name'=>$this->input->post('loc_name'),
            'lat'=>$this->input->post('lat'),
            'lng'=>$this->input->post('lng'),
            'daily'=>$this->input->post('daily'),
            'weekly'=>$this->input->post('weekly'),
            'monthly'=>$this->input->post('monthly'),
            'security_amount'=>$this->input->post('security_amount'),
                    'fixed_price'=>$this->input->post('fixed_price'),
            'date_created'=>date('Y-m-d H:i:s')
        );
        if (empty($_FILES['image1']) && empty($_FILES['image2']) && empty($_FILES['image3']) && empty($_FILES['image4']) && empty($_FILES['image5'])) {
            $result = array(
            "controller" => "User",
            "action" => "addpost",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"At least one image is required."
            );
            print_r(json_encode($result));die;
        }
        /*item images start*/
        if (isset($_FILES['image1'])) {
            $image='image1';
            $upload_path='public/post_images';
            $imagename1 = $this->file_upload($upload_path,$image);
            $item1=$imagename1;
        }
        if (isset($_FILES['image2'])) {
            $image='image2';
            $upload_path='public/post_images';
            $imagename2=$this->file_upload($upload_path,$image);
            $item2=$imagename2;
        }
        if (isset($_FILES['image3'])) {
            $image='image3';
            $upload_path='public/post_images';
            $imagename3=$this->file_upload($upload_path,$image);
            $item3=$imagename3;
        }
        if (isset($_FILES['image4'])) {
            $image='image4';
            $upload_path='public/post_images';
            $imagename4=$this->file_upload($upload_path,$image);
            $item4=$imagename4;
        }
        if (isset($_FILES['image5'])) {
            $image='image5';
            $upload_path='public/post_images';
            $imagename5=$this->file_upload($upload_path,$image);
            $item5=$imagename5;
        }
        /*item images end*/
        /*item images serilize start*/
        $seru=array($item1,$item2,$item3,$item4,$item5);
        $data['images']=serialize($seru);
        /*item images serialize end*/
        $var=$this->User_model->insert_data('ms_post',$data);
        if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "addpost",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Post added sucessfully."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "addpost",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }


    public function editPost_post(){
        $data=array(
            'user_id'=>$this->input->post('user_id'),
            'title'=>$this->input->post('title'),
            'description'=>$this->input->post('description'),
            'cat_id'=>$this->input->post('cat_id'),
            'loc_name'=>$this->input->post('loc_name'),
            'lat'=>$this->input->post('lat'),
            'lng'=>$this->input->post('lng'),
            'daily'=>$this->input->post('daily'),
            'weekly'=>$this->input->post('weekly'),
            'monthly'=>$this->input->post('monthly'),
            'security_amount'=>$this->input->post('security_amount'),
            'fixed_price'=>$this->input->post('fixed_price'),
            'date_created'=>date('Y-m-d H:i:s')
        );
        $post_id=$this->input->post('post_id');
        if (empty($_FILES['image1']) && empty($_FILES['image2']) && empty($_FILES['image3']) && empty($_FILES['image4']) && empty($_FILES['image5'])) {
            $result = array(
            "controller" => "User",
            "action" => "editPost",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"At least one image is required."
            );
            print_r(json_encode($result));die;
        }
        /*item images start*/
        if(isset($_FILES['image1'])) {
            $image='image1';
            $upload_path='public/post_images';
            $imagename1 = $this->file_upload($upload_path,$image);
            $item1=$imagename1;
        }
        if (isset($_FILES['image2'])) {
            $image='image2';
            $upload_path='public/post_images';
            $imagename2=$this->file_upload($upload_path,$image);
            $item2=$imagename2;
        }
        if (isset($_FILES['image3'])) {
            $image='image3';
            $upload_path='public/post_images';
            $imagename3=$this->file_upload($upload_path,$image);
            $item3=$imagename3;
        }
        if (isset($_FILES['image4'])) {
            $image='image4';
            $upload_path='public/post_images';
            $imagename4=$this->file_upload($upload_path,$image);
            $item4=$imagename4;
        }
        if (isset($_FILES['image5'])) {
            $image='image5';
            $upload_path='public/post_images';
            $imagename5=$this->file_upload($upload_path,$image);
            $item5=$imagename5;
        }
        /*item images end*/
        /*item images serilize start*/
        $seru=array($item1,$item2,$item3,$item4,$item5);
        $data['images']=serialize($seru);
        /*item images serialize end*/
        // $data=array_filter($data);
        $var=$this->User_model->update_data('ms_post',$data,array('id'=>$post_id));
        if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "editPost",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Post edited sucessfully."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "editPost",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    function forgotpassword1_post() {
        $email = $this->post('email');
        $id = $this->User_model->forgotpassword($email);
        if ($id == 0){
           $result = array(
             "controller" => "User",
             "action" => "forgotpassword",
             "ResponseCode" => false,
             "MessageWhatHappen" => "Email does not exist in our database."
             );
        }
        else {
            $body = "<!DOCTYPE html>
            <head>
            <meta content=text/html; charset=utf-8 http-equiv=Content-Type />
            <title>Feedback</title>
            <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>
            </head>
            <body>
            <table style='background:rgba(28, 182, 140, 0.8) none repeat scroll 0 0; border: 3px solid #1cb68c;' width=60% border=0 bgcolor=#53CBE6 style=margin:0 auto; float:none;font-family: 'Open Sans', sans-serif; padding:0 0 10px 0; >

            <tr>
            <td width=20px></td>
            <td bgcolor=#fff style=border-radius:10px;padding:20px;>
            <table width=100%;>
            <tr>
            <th style=font-size:20px; font-weight:bolder; text-align:right;padding-bottom:10px;border-bottom:solid 1px #ddd;> Hello " . $id['name'] . "</th>
            </tr>

            <tr>
            <td style=font-size:16px;>
            <p> You have requested a password retrieval for your user account at mstoo.To complete the process, click the link below.</p>
            <p> This is valid for 30 Minutes.</p>
            <p><a href=" . site_url('api/User/newpassword?id=' . $id['b_id']) . ">Change Password</a></p>
            </td>
            </tr>
            <tr>
            <td style=text-align:center; padding:20px;>
            <h2 style=margin-top:50px; font-size:29px;>Best Regards,</h2>
            <h3 style=margin:0; font-weight:100;>Customer Support</h3>

            </td>
            </tr>
            </table>
            </td>
            <td width=20px></td>
            </tr>
            <tr>
            <td width=20px></td>
            <td style='text-align:center; color:#fff; padding:10px;'> Copyright © mstoo All Rights Reserved</td>
            <td width=20px></td>
            </tr>
            </table>
            </body>";

            $this->load->library('email');
            $this->email->set_newline("\r\n");
            $this->email->to($email);
            $this->email->from('Mstoo@gmail.com', 'Mstoo');
            $this->email->subject('Forgot Password');
            $this->email->message($body);
            $mail = $this->email->send();
            $result = array(
            "controller" => "User",
            "action" => "forgotpassword",
            "ResponseCode" => true,
            "MessageWhatHappen" => "Mail Sent Successfully."
            );
        }
          $this->set_response($result, REST_Controller::HTTP_OK);
    }

    function newpassword_get($user_id=null){
        if ($user_id!="") {
           $user_id = base64_decode($user_id);
        }
        else{
           $user_id = base64_decode($this->get('id'));
        }
        $useridArr = explode("_", $user_id);
        $user_id = $useridArr[0];
        $data['id'] = $user_id;
        $data['title'] = "new Password";
        $this->load->view('templete2/header');
        $this->load->view('templete2/newpassword', $data);
    }
    function updateNewpassword_post(){
        $uid = $this->input->post('id');
        $static_key = "afvsdsdjkldfoiuy4uiskahkhsajbjksasdasdgf43gdsddsf";
        $id = $uid . "_" . $static_key;
        $id = base64_encode($id);
        $message = ['id' => $this->input->post('id') , 'password' => $this->input->post('password') , 'base64id' => $id];
        $this->load->library('form_validation');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|md5');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]|md5');
            if ($this->form_validation->run() == FALSE)
            {
                $this->session->set_flashdata('msg', '<span style="color:red">Please Enter Same Password</span>');
                redirect("api/User/newpassword?id=" . $message['base64id']);
            }
            else
            {
                $this->User_model->updateNewpassword($message);
            }
    }
    public function retryOtp_post(){
        $data = array(
            'country_code'=>$this->input->post('country_code'),
            'phone'=>$this->input->post('phone')
        );
        $num=$data['country_code'].$data['phone'];
        $otp = new sendotp('189865AFgPmFtnr5j5a424853','Message  : Your otp is {{otp}}. Please do not share with anyone.');
        $sendotp=($otp->send($num, 'MSGIND'));
        if ($sendotp['type']=='success') {
            $result = array(
            "controller" => "User",
            "action" => "retryOtp",
            "ResponseCode" => true,
            "retryOtp" =>$sendotp['message'],
            "retryreponse"=>$sendotp['type']
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "retryOtp",
            "ResponseCode" => false,
            "retryOtp" =>$sendotp['message'],
            "retryreponse"=>$sendotp['type']
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    Public function verifyotp_post(){
        $data = array(
            'country_code'=>$this->input->post('country_code'),
            'phone'=>$this->input->post('phone')
        );
        $loginParams =  array(
            'device_id'=>$this->input->post('device_id'),
            'unique_device_id '=>$this->input->post('unique_device_id'),
            'token_id'=>$this->input->post('token_id'),
            'date_created'=>date('Y-m-d H:i:s')
        );
        $id=$this->input->post('id');
        $otp1=$this->input->post('otp');
        $num=$data['country_code'].$data['phone'];
        $otp = new sendotp('189865AFgPmFtnr5j5a424853','Message  : Your otp is {{otp}}. Please do not share with anyone.');
        $res=($otp->verify($num, $otp1));
        if ($res['type']=='success') {
            $res1=$this->User_model->select_data('*','ms_unverifiedusers',array('id'=>$id));
            $data=array('name'=>$res1[0]->name,
                'email'=>$res1[0]->email,
                'password'=>$res1[0]->password,
                'country_code'=>$res1[0]->country_code,
                'phone'=>$res1[0]->phone,
                'profile_pic'=>$res1[0]->profile_pic,
                'is_verified'=>'1',
                'date_created'=>date('Y-m-d H:i:s')
            );
            $var= $this->User_model->insert_data('ms_users',$data);
            $loginParams['user_id']=$var;
            $login=$this->User_model->insert_data('ms_login',$loginParams);
            $table='ms_unverifiedusers';
            $var1=$this->User_model->delete_data($id,$table);
            $returnres=$this->db->query("SELECT id,name,email,country_code,phone,profile_pic,is_verified from ms_users where id='".$var."'")->row();
            $result = array(
            "controller" => "User",
            "action" => "verifyotp",
            "ResponseCode" => true,
            "verifymessage" =>$res['message'],
            "userdata"=>$returnres
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "verifyotp",
            "ResponseCode" => false,
            "verifymessage" =>$res['message']
            );
        }
          $this->set_response($result,REST_Controller::HTTP_OK);
    }
    /*used patch method for updating single key */
    public function rentstatus_PATCH(){
        $user_id=$_REQUEST['user_id'];
        $post_id=$_REQUEST['post_id'];
        $status=$_REQUEST['status'];
        $var=$this->User_model->update_data('ms_post',array('status'=>$status),array('id'=>$post_id,'user_id'=>$user_id ));
        if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "rentstatus",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your status updated sucessfully."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "rentstatus",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong, please try again later."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function blockUserslisting_post(){
        $user_id=$this->input->post('user_id');
        $var=$this->User_model->select_data('*','ms_block_users',array('user_id'=>$user_id,'status'=>1));

        foreach ($var as $key => $value) {
            $blockuserDetail=$this->db->query("SELECT name,profile_pic from ms_users where id='".$value->block_users_id."'")->row();
            $var[$key]->block_users_name=$blockuserDetail->name;
            $var[$key]->block_users_pic=$blockuserDetail->profile_pic;
        }
        if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "blockUserslisting",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your data shows sucessfully.",
            'blockUserslisting'=>$var,
            );
        }
        elseif (empty($var)) {
            $result = array(
            "controller" => "User",
            "action" => "blockUserslisting",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"No data exist"
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "blockUserslisting",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong"
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);


    }
    public function reportUser_post(){
        $user_id=$this->input->post('user_id');
        $report_user_id=$this->input->post('report_user_id');
        $var=$this->User_model->select_data('*','ms_report_users',array('user_id'=>$user_id,'report_user_id'=> $report_user_id));
        if (empty($var)) {
            $res= $this->User_model->insert_data('ms_report_users',array('user_id'=>$user_id,'report_user_id'=> $report_user_id,'date_created'=>date('Y-m-d H:i:s')));
            $result = array(
                "controller" => "User",
                "action" => "reportUser",
                "ResponseCode" => true,
                "MessageWhatHappen" =>"Your request has been submitted."
            );
        }
        else{
            $result = array(
                "controller" => "User",
                "action" => "reportUser",
                "ResponseCode" => false,
                "MessageWhatHappen" =>"You have already reported this user."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function resetPassword_post(){
    	$phone=$this->input->post('phone');
        $password=$this->input->post('password');
        $var=$this->User_model->update_data('ms_users',array('password'=>md5($password)),array('phone'=>$phone));
        if ($var) {
            $result = array(
            "controller" => "User",
            "action" => "resetPassword",
            "ResponseCode" => true,
            "MessageWhatHappen" =>"Your password reset sucessfully.",
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "resetPassword",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
        }
        $this->set_response($result,REST_Controller::HTTP_OK);
    }
    public function file_upload($upload_path, $image) {
        $baseurl = base_url();
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = '*';
        $config['max_size'] = '0';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
        $config['overwrite'] = FALSE;

        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($image))
        {
            // $error = array(
            // 'error' => $this->upload->display_errors()
            // );
            $result = array(
            "controller" => "User",
            "action" => "fileupload",
            "ResponseCode" => false,
            "MessageWhatHappen" =>"Something went wrong."
            );
            print_r(json_encode($result));die;
        }
        else
        {
            $detail = $this->upload->data();
            return $imagename = $baseurl . $upload_path .'/'. $detail['file_name'];
        }
    }

}
