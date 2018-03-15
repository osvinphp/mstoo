<?php
error_reporting(0);
ini_set('display_errors',1);
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {


	function __construct() {
		parent::__construct();
        // error_reporting(E_ALL); ini_set('display_errors', 1);
		$this->load->library('session');
		$haveAccess = $this->session->userdata('logged_in');
		if (!$haveAccess) {
			redirect('Login');
		}
		$this->load->model('Common_model');
		// $this->load->library('email');
		// $this->load->library('form_validation');
		$this->load->helper('common_helper');
		$this->load->helper('url');

	}

	 // Common function to upload images
	public function file_upload($upload_path, $image) {
		$baseurl = base_url();
		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size'] = '5000';
		$config['max_width'] = '5024';
		$config['max_height'] = '5068';
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload($image))
		{
			$error = array(
				'error' => $this->upload->display_errors()
				);
			//print_r($error);die;
			return $imagename = "";
		}
		else
		{
			$detail = $this->upload->data();
			return $imagename = $baseurl.$upload_path .'/'. $detail['file_name'];
		}
	}

	public function logout()
	{
		$this->session->unset_userdata('logged_in');
		$this->session->sess_destroy();
		redirect('Login');
	}

//Common function to include all view files
	public function template($view,$data)
	{
		$this->load->view('templates/header.php');
		$this->load->view('templates/subheader.php');
		$this->load->view('templates/sidebar.php');
		$this->load->view('templates/footer.php');
		$this->load->view($view,$data);
	}

	public function index()
	{
		// $this->load->view('Dashboard');
		$data['allusers'] = $this->Common_model->count_user('ms_users');
		$data['allposts'] = $this->Common_model->count('ms_post');
		$data['spamposts'] = $this->Common_model->count('ms_post_spam');
		$data['blockusers'] = $this->Common_model->count('ms_block_users');
		// echo "<pre>";print_r($data['allusers']); die;
		$this->template('Dashboard.php',$data);
	}

    // Function to get all reported users
	public function users()
	{
		$data['allusers']=$this->Common_model->select_where('*','ms_users',array('user_type' => '0'));
		$this->template('User.php',$data);
	}

	// function to display  all posts
	public function posts()
	{
		$data['allposts']=$this->Common_model->select('*','ms_post');
		$this->template('posts.php',$data);
	}


    // Function to get all reported post
	public function reported_user()
	{
		$data['ReportedUsers']=$this->Common_model->select('*','ms_block_users');
		$this->template('reported_user.php',$data);
	}

	// Function to get all users
	public function reported_post()
	{
		$data['ReportedPosts']=$this->Common_model->ReportedPosts();
		// echo "<pre>"; print_r($data['ReportedPosts']); die;
		$this->template('reported_post.php',$data);
	}


	// function to suspend a user
	public function suspend_user()
	{
		$UserID=$_POST['UserID'];
		$status=$_POST['status'];
		
		if($status == 1){
			$is_suspend ='0';
			$data=$this->Common_model->update('ms_users',array('is_suspend'=>$is_suspend), array('id'=>$UserID));
		}else{
			$is_suspend ='1';
			$data=$this->Common_model->update('ms_users',array('is_suspend'=>$is_suspend), array('id'=>$UserID));
			$checklogin=$this->db->query("SELECT * from ms_login where user_id='".$UserID."' and status=1 ")->result();
			foreach ($checklogin as $key => $value) {
				$pushData['title']="suspend";
				$pushData['action']="3";
				$pushData['bodymessage']="Your account has been suspended. Please contact admin for more info.";
				$pushData['token'] = $value->token_id;
				if($value->device_id == 1){
					$this->Common_model->iosPush($pushData);
				}
				else if($value->device_id == 0){
					$this->Common_model->androidPush($pushData);
				}
			}
		}
		echo "Success";die;
	}



// function to delete a spam posts
	public function delete(){
		$PostID = $this->uri->segment(3);
		$data = $this->Common_model->DeleteSpamPosts($PostID);
		// print_r($row); echo "Hello";die;
		redirect('Dashboard/reported_post');
		// if ($row > 0){
		// }
	}

	// function to delete a post
	public function deletePost(){
		$PostID = $this->uri->segment(3);
		$data = $this->Common_model->DeletePosts($PostID);
		redirect('Dashboard/posts');
		// if ($row > 0){
		// }
	}


    //function to edit the users table
	public function EditUser(){
		if (isset($_POST['editUsers'])) {
			if(isset($_FILES['profile_pic']['name']))
			{
				$upload_path = 'public/assets/images';
				$image = 'profile_pic';
				$profile_pic = $this->file_upload($upload_path, $image);
			}else
			{
				$profile_pic="";
			}
			$userid       = $this->input->post('UserId');
			$name         = $this->input->post('UpdateName');
			$email        = $this->input->post('UpdateEmail');
			$country_code = $this->input->post('UpdateCode');
			$phone        = $this->input->post('UpdatePhone');
			$data = array(
				'name'  =>$name,
				'email' =>$email,
				'country_code'=>$country_code,
				'phone' =>$phone,
				'profile_pic'=>$profile_pic
				);
			$data = array_filter($data);
		// echo "<pre>"; print_r($data); die;
			$this->Common_model->update("ms_users",$data,array('id'=>$userid));
			// $this->session->set_flashdata('msg', 'Updated Successfully!');
		}

		redirect('Dashboard/User');

	}

	/******Function to add category ********/
	public function AddCategory(){
		$data['rest']='data';

		if(isset($_FILES['icon']['name']))
		{
			$upload_path = 'public/assets/images';
			$image = 'icon';
			$profile_pic = $this->file_upload($upload_path, $image);
		}else
		{
			$profile_pic="";
		}

		if(isset($_POST['submit'])){

			$icon_name=$_FILES['icon']['name'];
			$data=array(
				'category_name'=>$this->input->post('category'),
				'icon'=>$icon_name
				);
			$insert_id=$this->Common_model->insert('ms_categories',$data);
			if(!empty($insert_id)){
				$data['msg']=$insert_id;
			}

		}
		$this->template('add-category.php',$data);

	}


	/******Function to add Sub category ********/
	public function AddSubCategory(){
		$data['categories']=$this->Common_model->select('*','ms_categories');

		if(isset($_FILES['icon']['name']))
		{
			$upload_path = 'public/assets/images';
			$image = 'icon';
			$profile_pic = $this->file_upload($upload_path, $image);
		}else
		{
			$profile_pic="";
		}

		if(isset($_POST['submit'])){

			$icon_name=$_FILES['icon']['name'];
			$data=array(
				'category_id'=>$this->input->post('category'),
				'sub_category_name'=>$this->input->post('sub-category'),
				'icon'=>$icon_name
				);
			$data=array_filter($data);
			if(!empty($data)){
				$insert_id=$this->Common_model->insert('ms_sub_categories',$data);
				$data['msg']=$insert_id;
			}else{
				$data['msg']=0;
			}
		}
		$this->template('add-sub-category.php',$data);

	}

	/********** Function to get all categories ******************/

	public function GetAllCategories(){
		$data['AllCategories']=$this->Common_model->select('*','ms_categories');
		$this->template('all-categories.php',$data);


	}

	/********** Function to get all Sub-categories ******************/
	public function GetAllSubCategories(){
		$data['AllSubCategories']=$this->Common_model->GetAllCategory();

		// echo "<pre>"; print_r($data['AllSubCategories']); die;
		$this->template('all-sub-categories.php',$data);
		
	}

   /*******Functio to delete Category  *********/
   public function delete_category(){
   	$CatID = $this->uri->segment(3);
    $data = $this->Common_model->delete('ms_categories',$CatID);
   	redirect('Dashboard/GetAllCategories');

   }

    /*******Functio to delete Sub- Category *********/
   public function delete_sub_category(){
   	$CatID = $this->uri->segment(3);
    $data = $this->Common_model->delete('ms_sub_categories',$CatID);
   	redirect('Dashboard/GetAllSubCategories');

   }


   /******Function to Update category ********/
	public function UpdateCategory(){


		$data['rest']='data';

		if(isset($_FILES['update_icon']['name']))
		{
			$upload_path = 'public/assets/images';
			$image = 'update_icon';
			$profile_pic = $this->file_upload($upload_path, $image);
		}else
		{
			$profile_pic="";
		}

		if(isset($_POST['editCategory'])){

            $catId=$this->input->post('update_Id');
			$icon_name=$_FILES['update_icon']['name'];
			$data=array(
				'category_name'=>$this->input->post('update_category'),
				'icon'=>$icon_name
				);
			$data=array_filter($data);
			$this->Common_model->update('ms_categories',$data, array('id'=>$catId));

		}
		redirect('Dashboard/GetAllCategories');


	}

	   /******Function to Update Sub-category ********/
	public function UpdateSubCategory(){


		$data['rest']='data';

		if(isset($_FILES['update_icon']['name']))
		{
			$upload_path = 'public/assets/images';
			$image = 'update_icon';
			$profile_pic = $this->file_upload($upload_path, $image);
		}else
		{
			$profile_pic="";
		}

		if(isset($_POST['editSubCategory'])){

            $catId=$this->input->post('update_Id');
			$icon_name=$_FILES['update_icon']['name'];
			$data=array(
				'sub_category_name'=>$this->input->post('update_sub_category'),
				'icon'=>$icon_name
				);
			$data=array_filter($data);
			$this->Common_model->update('ms_sub_categories',$data, array('id'=>$catId));
			

		}
		redirect('Dashboard/GetAllSubCategories');


	}


}
