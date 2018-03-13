 <?php
 defined('BASEPATH') OR exit('No direct script access allowed');
 class User_model extends CI_Model{
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');

	}
	public function sign_up($data,$type){
		if ($type == 1) {
			if (!empty($data['email'])) {
				$checkemail=$this->db->query("SELECT * from ms_users where email='".$data['email']."' ")->row();
				if (empty($checkemail)) {
					$checkphone=$this->db->query("SELECT * from ms_users where  phone='".$data['phone']."' ")->row();
					if (empty($checkphone)) {
						$signup=$this->User_model->insert_data('ms_unverifiedusers',$data);
						return $signupdata=$this->db->query("SELECT id,name,email,country_code,phone,profile_pic,is_verified from ms_unverifiedusers where id='".$signup."'")->row();
					}
					else{
						return 2;
					}
				}
				else{
					return 0;
				}
			}
			else{
					$checkphone=$this->db->query("SELECT * from ms_users where  phone='".$data['phone']."' ")->row();
					if (empty($checkphone)) {
						$signup=$this->User_model->insert_data('ms_unverifiedusers',$data);
						return $signupdata=$this->db->query("SELECT id,name,email,country_code,phone,profile_pic,is_verified from ms_unverifiedusers where id='".$signup."'")->row();
					}
					else{
						return 2;
					}
			}
		}
		die;
	}
	public function login($data,$loginParams,$type){
		if ($type == 1) {
			$checkemail=$this->db->query("SELECT * from ms_users where (email='".$data['email']."' OR phone='".$data['email']."') ")->row();
			if (!empty($checkemail)) {
				$checkemailpass=$this->db->query("SELECT * from ms_users where (email='".$data['email']."' OR phone='".$data['email']."') and password='".$data['password']."' ")->result();
				if (!empty($checkemailpass)) {

					if ($checkemailpass[0]->is_suspend==0) {
						$checklogin=$this->User_model->select_data('*','ms_login',array('user_id'=>$checkemailpass[0]->id,'status'=>1));
						if (!empty($checklogin)) {
	        				$var= $this->User_model->update_data('ms_login',array('status'=>0),array('user_id'=>$checkemailpass[0]->id));
	        				foreach ($checklogin as $key => $value) {

								$pushData['action']="1";
								$pushData['title']="Session expired";
								$pushData['bodymessage']="Your session has been expired as you login from another device.";
								$pushData['token'] = $value->token_id;
								if($value->device_id == 1){
									$this->User_model->iosPush($pushData);
								}
								else if($value->device_id == 0){
									$this->User_model->androidPush($pushData);
								}
	        				}	
						}
						$loginParams['user_id']=$checkemailpass[0]->id;
						$login=$this->User_model->insert_data('ms_login',$loginParams);
						return $signupdata=$this->db->query("SELECT id,name,email,country_code,phone,profile_pic,is_verified from ms_users where id='".$loginParams['user_id']."'")->row();
					}
					else{
						return 7;
					}
				}
				else{
					return 0;
				}
			}
			else{
				return 0;
			}
		}
	}
	public function log_out($unique_device_id,$user_id){
		$getRes=$this->db->select('*')
		->from('ms_login')
		->where('user_id',$user_id)
		->get()->result();
		if (!empty($getRes)) {
			$data = array('status' => 0);
			$this->db->where('unique_device_id',$unique_device_id);
			$this->db->where('user_id',$user_id);
			$result= $this->db->update('ms_login',$data);
			return $result;
		}
		else{
			return 0;
		}
	}
	public function get_posts($data,$user_id){
		$type1=explode(',',$data['type']);
		foreach ($type1 as $key => $value) {
			if ($value==1) {
				$type[0]=1;
			}
			if ($value==2) {
				$type[1]=2;
			}
			if ($value==3) {
				$type[2]=3;
			}
			if ($value==4) {
				$type[3]=4;
			}
		}
		// print_r($type1);die;
		$radius = 500;
		$result="SELECT *,ROUND(6371 * acos(cos(radians('".$data['lat']."')) * cos(radians(lat)) * cos(radians(lng) - radians('".$data['long']."')) + sin(radians('".$data['lat']."')) * sin(radians(lat)))) as distance from ms_post WHERE  status=1 ";
		if (!empty($user_id)) {
			$result .=' and user_id !='.$user_id."";
		}
		if(!empty($data['radius'])){
			$result .=" HAVING `distance` <= ".$data['radius']." ";
		}
		else{
			$result .=" HAVING `distance` <= ".$radius." ";
		}
		if(!empty($data['price'])){
			$result .=" and ((daily between 1 and  ".$data['price'].") or (weekly between 1 and ".$data['price'].") or (monthly between 1 and ".$data['price'].") )";
		}
		if(!empty($type[0]) && $type[0]==1){
			$result .=' and  daily <> "" ';
		}
		if(!empty($type[1]) && $type[1]==2){
			$result .=' and  weekly <> ""';
		}
		if(!empty($type[2]) && $type[2]==3){
			$result .=' and  monthly <> ""';
		}
		if(!empty($type[3]) && $type[3]==4){
			$result .=' and  fixed_price <> ""';
		}
		if(!empty($data['cat_id'])){
			$result .=' and cat_id IN ('.$data['cat_id'].")";
		}
		$result .=" ORDER BY date_created desc";
		$selectposts = $this->db->query($result)->result();
		// print_r($this->db->last_query());die;
		$postdata=array();
		// print_r($data);die;
		foreach ($selectposts as $key => $value) {

			if (!empty($user_id)) {
					$sharedata=$this->db->query("SELECT * from ms_share_info where post_id='".$value->id."' and ((post_creator_id='".$value->user_id."' and  user_id='".$user_id."') or ( post_creator_id='".$user_id."' and  user_id='".$value->user_id."'))  ")->row();
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
				}
				else{
					$selectposts[$key]->call_share_owner=2;
					$selectposts[$key]->call_share_customer=2;
					$selectposts[$key]->loc_status=2;
				}
			// }
			// else{
			// 	$selectposts[$key]->call_status=2;
			// 	$selectposts[$key]->loc_status=2;
			// }
	
			if ($value->monthly=="") {
				$selectposts[$key]->monthly="";
			}
			if ($value->weekly=="") {
				$selectposts[$key]->weekly="";
			}
			if ($value->daily=="") {
				$selectposts[$key]->daily="";
			}
			$abc=$this->db->query("SELECT name,profile_pic,phone from ms_users where id='".$value->user_id."'")->row();
			$selectposts[$key]->name=$abc->name;
			$selectposts[$key]->profile_pic=$abc->profile_pic;
			$selectposts[$key]->phone=$abc->phone;

			$postdata= unserialize($value->images);
			$postdata=array_filter($postdata);
			$postdata=array_values($postdata);
			if (!empty($postdata)) {
				$selectposts[$key]->itemimages=$postdata;
			}
			else{
				$selectposts[$key]->itemimages=array();
			}
		}
		$result=array();
		foreach ($selectposts as $key => $value) {
			$count=$this->db->query("SELECT count(id) as count from ms_post_spam where post_id='".$value->id."'")->row();
			$blockdata=$this->db->query("SELECT *  from ms_block_users where ((user_id='".$user_id."' and block_users_id='".$value->user_id."') or (user_id='".$value->user_id."' and block_users_id='".$user_id."')) and status = 1 ")->row();
			if ($count->count<=10 && empty($blockdata)) {
				$result[]=$value;
			}
		}

		// print_r($result);die;
		$result->abc=count($result);
		return $result;
	}
	public function insert_data($tbl_name,$data)                                         /* Data insert */
	{
		$this->db->insert($tbl_name, $data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}
	public function update_data($tbl_name,$data,$where){                                 /* Update data */

		$this->db->where($where);
		$this->db->update($tbl_name,$data);
		return($this->db->affected_rows())?1:0;
	}

	public function select_data($selection,$tbl_name,$where=null,$order=null)                   /* Select data with condition*/
		{
			if (empty($where)&&empty($order)) {
			$data_response = $this->db->select($selection)
			->from($tbl_name)
			->get()->result();
		}
		elseif(empty($order)){
			$data_response =
			$this->db->select($selection)
			->from($tbl_name)
			->where($where)
			->get()->result();

		}else{
			$data_response =
			$this->db->select($selection)
			->from($tbl_name)
			->where($where)
			->order_by($order)
			->get()->result();
		}
		return $data_response;
	}
	public function delete_data($postid,$table){
		$this->db->where('id', $postid);
		$this->db->delete($table);
		return true;
	}

// Function to delete posts, spam posts & chat
	public function delete_posts($postid,$table){
		$this->db->where('id', $postid);
		$this->db->delete($table);

		$row = $this->db->affected_rows();
		if ($this->db->affected_rows() > 0){
			$this->db->from("ms_post_spam");
			$this->db->where("ms_post_spam.post_id", $postid);
			$this->db->delete("ms_post_spam");
		
			$this->db->from("ms_chat");
			$this->db->where("ms_chat.post_id", $postid);
			$this->db->delete("ms_chat");
		}

		return true;
	}
	/*push notification for android common function*/
    public function androidPush($pushData=null){
		$mytime = date("Y-m-d H:i:s");
		$api_key = "AIzaSyAj9FthudE13E06WKvQI8PYD56JbJqwC2M";  //for user app
		$fcm_url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array(
				'registration_ids' => array(
				$pushData['token']
			),
			'data' => array(
				"message" =>$pushData['message'],
				"spMessage" =>$pushData['message1'],
				"action" => $pushData['action'],
				"postDetail" => $pushData['postDetail'],
				"fromname" => $pushData['fromname'],
				"postname" => $pushData['postName'],
				"fromphone"=> $pushData['fromphone'],
				"frompic"=>$pushData['frompic'],
				"body"=>$pushData['bodymessage'],	
				"badge"=>$pushData['count'],
				"call_share_owner" =>$pushData['call_share_owner'] ,
				"call_share_customer" =>$pushData['call_share_customer'] ,
				"loc_status" =>$pushData['loc_status'] ,
				"time" => $mytime
			) ,
		);
		$headers = array(
			'Authorization: key=' . $api_key,
			'Content-Type: application/json'
		);
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($curl_handle, CURLOPT_URL, $fcm_url);
		curl_setopt($curl_handle, CURLOPT_POST, true);
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($fields));
		$response = curl_exec($curl_handle);
		// print_r($response);
		curl_close($curl_handle);
  	}
	/*push notification for ios common function*/
    public function iosPush($pushData=null) {
	    $deviceToken = $pushData['token'];
	   	$mytime = date("Y-m-d H:i:s");
	    $passphrase = '123456789';
	    $ctx = stream_context_create();

    	stream_context_set_option($ctx, 'ssl', 'local_cert', './certs/mstooPush.pem');
    	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

	    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
		$body['aps'] = array(
			"from_name"=>$pushData['fromname'],
			'alert' => array(
			'title' => $pushData['title'],//post name
			'body' =>$pushData['bodymessage'],//zohaib: message
			),
			"action"=>$pushData['action'],
			"postDetail"=>$pushData['postDetail'],
			"message" =>$pushData['message'] ,
			"call_share_owner" =>$pushData['call_share_owner'] ,
			"call_share_customer" =>$pushData['call_share_customer'] ,
			"loc_status" =>$pushData['loc_status'] ,
			"badge"=>$pushData['count'],
			'sound' => 'default'
		);
	    $payload = json_encode($body);
	    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
	    $result = fwrite($fp, $msg, strlen($msg));
	    fclose($fp);
	}
	public function forgotpassword($email){
		$select_user = $this->db->select('*')->from('ms_users')->where('email', $email)->get()->row();
		if (empty($select_user->id))
		{
			return 0;
		}
		else
		{
			$static_key = "afvsdsdjkldfoiuy4uiskahkhsajbjksasdasdgf43gdsddsf";
			$id = $select_user->id . "_" . $static_key;
			$result['b_id'] = base64_encode($id);
			$result['user_id'] = $select_user->id;
			$result['name'] = $select_user->name;
			$time=date('Y-m-d H:i:s');
			$getforgot = $this->db->select('*')->from('ms_forgotPassword')->where('user_id', $select_user->id)->get()->result();
			if (empty($getforgot)) {
				$addtransArray = array(
					'user_id'=>$select_user->id,
					'time'=>date('Y-m-d H:i:s'),
					'status' => 1
				);
				$addtrans = $this->insert_data('ms_forgotPassword',$addtransArray);
			}
			else{
				$uptBal = $this->update_data('ms_forgotPassword',array('status'=>1,' time'=>date('Y-m-d H:i:s')),array('user_id'=>$select_user->id));
			}
		}
		return $result;
	}
	public function updateNewpassword($message){
		$getforgot = $this->db->select('*')->from('ms_forgotPassword')->where('user_id', $message['id'])->get()->result();
		$sendtime=$getforgot[0]->time;
		$time=date('Y-m-d H:i:s');
		$det= date('Y-m-d H:i:s', strtotime("$sendtime  +30 minutes"));
		/*checking that user can update password only in 30 minute*/
		if ($time <= $det && $getforgot[0]->status==1) {
			$update_pwd = $this->db->where('id', $message['id']);
			$this->db->update("ms_users", array(
			'password' => md5($message['password']))
			);
			$update_pwd2 = $this->db->where('user_id', $message['id']);
			$this->db->update("ms_forgotPassword", array(
			'status' => 0)
			);
			if ($update_pwd)
				$this->session->set_flashdata('msg', '<span style="color:green">Password Changed Successfully</span>');
				redirect("api/User/newpassword?id=" . $message['base64id']);
			}
			else{
			$this->session->set_flashdata('msg', '<span style="color:red">Your Reset Password Link has been Expired</span>');
			redirect("api/User/newpassword?id=" . $message['base64id']);
			}
	}




}
