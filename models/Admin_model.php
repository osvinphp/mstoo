<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_model extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();

	}

	 // Function for admin login
	public function admin_login($data)
	{
		$result = $this->db->select('*')
						->from('ms_users')
						->where('email',$data['email'])
						->where('password',md5($data['password']))
						->where('user_type','1')
						->get()->row();

		return $result;
	}

        //Function to insert login data
	public function insert_login($user_id,$login_via,$unique_device_id,$token_id) {
		$update = $this->db->or_where('unique_device_id=', $unique_device_id);
		$this->db->or_where('token_id =', $token_id);
		$this->db->update('ms_login', array(
			'status' => 0
			));

		$insiData = array(
			'user_id' => $user_id,
			'login_via' => $login_via,
			'unique_device_id' => $unique_device_id,
			'token_id' => $token_id,
			'status' => 1
			);

		$insert = $this->db->insert("ms_login", $insiData);
	}

  

       // function for login
	function login($myarray)
	{
		if ($myarray['login_type'] == "email")
		{
    // case: login facebook
			$response = $this->db->select('*')
			->from('ms_users')
			->where('email', $myarray['email'])
			->where('password', md5($myarray['email']."-".$myarray['password']))
			->where('active_status =', 0 )
			->where('signup_level =', 5 )
			->get()->row();

			if(empty($response))
			{
				$user = $this->db->select('*')
				->from('ms_users')
				->where('email', $myarray['email'])
				->where('password', md5($myarray['email']."-".$myarray['password']))
				->where('active_status =', 0 )
				->get()->row();
				if($user)
				{
             // $array=array("signup_level"=>$user->signup_level);
             // return $this->errorResponse("Your Account is not completed yet",$array);
					return $this->errorResponse("User not registered");
				}else
				{
					return $this->errorResponse("Invalid email or password.");
				}
			}
			else
			{
				$UserDetails=$this->getUserDetails($response->id);
			}

		}
		elseif ($myarray['login_type'] == "facebook")
		{
     // case: login facebook
			$response = $this->db->select('*')
			->from('ms_users')
			->where('email', $myarray['email'])
			->where('fb_id', $myarray['fb_id'])
			->where('fb_id!=', '')
			->where('fb_id!=', 0)
			->where('active_status =', 0 )
			->where('signup_level =', 5 )
			->get()->row();

			if(empty($response))
			{

				$user = $this->db->select('*')
				->from('ms_users')
				->where('email', $myarray['email'])
				->where('fb_id', $myarray['fb_id'])
				->where('fb_id!=', '')
				->where('fb_id!=', 0)
				->where('active_status =', 0 )
				->get()->row();

				if($user)
				{
					$array=array("signup_level"=>$user->signup_level);
					return $this->errorResponse("User not registered.");
				}else
				{
					return $this->errorResponse("Invalid email.");
				}

			}else
			{
				$UserDetails=$this->getUserDetails($response->id);
			}

		}
		/*push end*/

		if(!empty($response))
		{
      //$update = $this->db->or_where('user_id', $response->id);
			$update = $this->db->or_where('unique_device_id=', $myarray['unique_device_id']);
			$this->db->or_where('token_id =', $myarray['token_id']);
			$this->db->update('ms_login', array(
				'status' => 0
				));

			$insiData = array(
				'user_id' => $response->id,
				'login_via' => $myarray['login_via'],
				'unique_device_id' => $myarray['unique_device_id'],
				'token_id' => $myarray['token_id'],
				'status' => 1
				);
			$insert = $this->db->insert("ms_login", $insiData);
			return $this->successResponse("Login Successfully",$UserDetails);
		}
	}

}