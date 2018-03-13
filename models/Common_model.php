<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Common_model extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();

	}

	public function insert($table,$data){
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}

	public function select($data,$table){
		$getData = $this->db->select($data)
		->from($table)
		->get()->result_object();
		return $getData;

	}

	public function select_where($data,$table,$where){
		$getData = $this->db->select($data)
		->from($table)
		->where($where)
		->get()->result_object();
		return $getData;

	}

	public function update($table,$data,$where){
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete($table,$id){
		$this->db->where('id',$id );
		$this->db->delete($table);
		return $this->db->affected_rows();
	}

	public function ReportedPosts(){
		$getData = $this->db->select('*')
						->from('ms_post_spam')
						->join('ms_post', 'ms_post.id = ms_post_spam.post_id')
						->get()->result_object();
		return $getData;

	}

	public function DeleteSpamPosts($PostID){
		$this->db->from("ms_post");
		$this->db->where("ms_post.id", $PostID);
		$this->db->delete("ms_post");

		$row = $this->db->affected_rows();
		if ($row > 0){
			$this->db->from("ms_post_spam");
			$this->db->where("ms_post_spam.post_id", $PostID);
			$this->db->delete("ms_post_spam");
		
			$this->db->from("ms_chat");
			$this->db->where("ms_chat.post_id", $PostID);
			$this->db->delete("ms_chat");
		}

		return $row;

	}


	public function DeletePosts($PostID){
		$this->db->from("ms_post");
		$this->db->where("ms_post.id", $PostID);
		$this->db->delete("ms_post");

		$row = $this->db->affected_rows();
		if ($row > 0){
			$this->db->from("ms_post_spam");
			$this->db->where("ms_post_spam.post_id", $PostID);
			$this->db->delete("ms_post_spam");
		}
		if ($row > 0){
			$this->db->from("ms_chat");
			$this->db->where("ms_chat.post_id", $PostID);
			$this->db->delete("ms_chat");
		}
		
		return $row;


	}



	public function count($table){
		$result = $this->db->select('*')
		->from($table)
		->get()->num_rows();
		return $result;

	}
	public function count_user($table){
		$result = $this->db->select('*')
		->from($table)
		->where(array('user_type'=>'0'))
		->get()->num_rows();

		return $result;

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
			'sound' => 'default'
			);
		$payload = json_encode($body);
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		$result = fwrite($fp, $msg, strlen($msg));
		fclose($fp);
	}

}