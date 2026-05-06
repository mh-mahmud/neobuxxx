<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_controller extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}


	public function index() {
		$data = array();
		$this->load->view("web/home", $data);
	}

	public function about() {
		$data = array();
		$this->load->view("web/about", $data);
	}

	public function packages() {
		$data = array();
		$this->load->view("web/packages", $data);
	}

	public function policy() {
		$data = array();
		$this->load->view("web/policy", $data);
	}

	public function proof() {
		$data = array();
		$this->load->view("web/proof", $data);
	}

	public function works() {
		$data = array();
		$this->load->view("web/works", $data);
	}

	public function contact() {
		$data = array();
		$this->load->view("web/contact", $data);
	}

	public function faq() {
		$data = array();
		$this->load->view("web/faq", $data);
	}

	public function features() {
		$data = array();
		$this->load->view("web/features", $data);
	}

	public function trading_tools() {
		$data = array();
		$this->load->view("web/projects", $data);
	}

}