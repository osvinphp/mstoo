<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
     * @package  CodeIgniter
     * @author   Saurabh
     * @category Controller
     *
	 */
	function __construct() {
        // Construct the parent class
        parent::__construct();
        // Configure limits on our controller methods
        // error_reporting(E_ALL); ini_set('display_errors', 1);
        $this->load->library('session');
        $this->load->model('Admin_model');
        $this->load->library('email');
        $this->load->library('form_validation');
        // $this->load->helper('common_helper');

    }

	public function index()
	{
        if (isset($_POST['login'])) {
            $get_data = $this->Admin_model->admin_login($_POST);
            if (!empty($get_data)) {
                $this->session->set_userdata('logged_in',$get_data);
                redirect('Dashboard');
            }else{
                redirect('Login');
                // $this->session->set_flashdata('msg', 'Invalid Credentials');
            }
        }
        $data='haha';
        $this->load->view('templates/header.php');
        $this->load->view('templates/footer.php');
        $this->load->view('login_view.php',$data);
	}
}

