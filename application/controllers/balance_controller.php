<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Balance_controller extends CI_Controller {

	/*
	**********************************************
	*
	* table - user_balance
	*
	* TRANS_STATUS = 0 = Balance Return
	* TRANS_STATUS = 1 = Balance Transfer
	* TRANS_STATUS = 2 = Reffer Comission
	* TRANS_STATUS = 3 = Generation Income
	* TRANS_STATUS = 4 = Meture Share Balance
	* TRANS_STATUS = 5 = Buy Share
	* TRANS_STATUS = 6 = Withdraw Request
	* TRANS_STATUS = 7 = Cancelled Withdraw Request
	*
	**********************************************
	*/

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
	}

	public function send_money() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'send_money');
		$this->webspice->permission_verify('send_money');
		$data = array();
		$errors = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('public_id', 'public id', 'required|trim|xss_clean');
		$this->form_validation->set_rules('amount', 'amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('user_note', 'user note', 'trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/balance/send_money', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('send_id');
		// dd($input);

		# verify public id
		if(!$this->webspice->verify_public_id_and_data($input->public_id)) {
			$errors[] = "Wrong public id";
		}
		
		$user_id = $this->webspice->verify_public_id_and_data($input->public_id);
		if(!$user_id) {
			$errors[] = "Invalid user";
		}
		else {
			$user_id = $user_id->USER_ID;
		}
		

		# verify numeric value
		if(!is_numeric($input->amount)) {
			$errors[] = "Amount must be in numeric value";
		}

		# verify minimum amount from settings
		if($this->webspice->verify_first_balance($user_id)) {
			$minimum_investment = $this->webspice->settings_data()->MINIMUM_INVESTMENT;
			if($input->amount < $minimum_investment) {
				$errors[] = "This is first balance for this user. You must provide $". $minimum_investment ." or upper amount";
			}
		}

		# check user balance
		if(!$this->webspice->admin_verify()) {
			$user_balance = $this->webspice->user_balance($this->webspice->get_user_id());
			if($user_balance < $input->amount) {
				$errors[] = "Insufficient funds, plesae contact with admin to load balance";
			}
		}


		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view("admin_new/balance/send_money", $data);
			return false;
		}

		// variable initialize
		$user_id = $user_id;
		$amount = $input->amount;
		$note = $input->user_note;
		$public_id = $input->public_id;

		$my_str = $public_id . "|" . $user_id . "|" . $amount . "|" . $note;
		$my_str = $this->webspice->enc($my_str, 'encrypt');
		// dd($this->webspice->enc($my_str, 'decrypt'));

		$this->webspice->message_board('Please insert your pin to confirm action');
		$this->webspice->force_redirect($url_prefix.'confirm_send_money/'.$my_str);
	}

	public function confirm_send_money() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'send_money');
		$this->webspice->permission_verify('send_money');
		$data = array();
		$errors = array();
		$key = $this->uri->segment(2);
		$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
		// dd($id);
		$id = explode("|", $id);
		$data['public_id'] = $id[0];
		$data['user_id'] = $id[1];
		$data['amount'] = $id[2];
		$data['user_note'] = $id[3];
		$reg_data = $this->webspice->verify_public_id_and_data($id[0]);
		$data['first_name'] = $reg_data->FIRST_NAME;
		$data['last_name'] = $reg_data->LAST_NAME;
		$data['email'] = $reg_data->EMAIL;
		$data['mobile'] = $reg_data->MOBILE;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('pin_number', 'pin number', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/balance/confirm_send_money', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('flexi_trans_id');
		$pin_number = $input->pin_number;

		// dd($pin_number);

		// verify pin number
		if(!$this->webspice->user_pin_verify($this->webspice->get_user_id(), $pin_number)) {
			$errors[] = "Your pin didn't match.";
		}

		if(count($errors)) {
			// dd($errors);
			$data['errors'] = $errors;
			$this->load->view("admin_new/balance/confirm_send_money", $data);
			return false;
		}

		# init data setup
		$user_id = $data['user_id'];
		$balance_type = "GET";
		$amount = $data['amount'];
		$reason = "Balance Transfer From " . $this->webspice->admin_name($this->webspice->get_user_id());
		$user_note = $data['user_note'];
		$trans_date = $this->webspice->now();
		$provider_id = $this->webspice->get_user_id();
		$trans_status = 1;
		$created_date = $this->webspice->now();

		$sql = "
		INSERT INTO user_balance
		(USER_ID, BALANCE_TYPE, AMOUNT, REASON, USER_NOTE, TRANS_DATE, PROVIDER_ID, TRANS_STATUS, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql, array(
			$user_id,
			$balance_type,
			$amount,
			$reason,
			$user_note,
			$trans_date,
			$provider_id,
			$trans_status,
			$created_date
		));
	
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		// update user as premium user if this the first balance transfer
		if($this->webspice->verify_non_premium($data['public_id'])) {
			$this->db->query("UPDATE user_registration SET ACC_STATUS=1 WHERE MY_ID='".$data['public_id']."'");
		}

		// if user transfer the balance
		if(!$this->webspice->admin_verify()) {
			# data setup
			$user_id = $this->webspice->get_user_id();
			$balance_type = "POST";
			$reason = "Balance transfer to " . $this->webspice->admin_name($data['user_id']);
			$trans_status = 1;
			$sql2 = "
			INSERT INTO user_balance
			(USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
			VALUES
			( ?, ?, ?, ?, ?, ?, ? )";
			$this->db->query($sql2, array(
				$user_id,
				$balance_type,
				$amount,
				$reason,
				$trans_date,
				$trans_status,
				$created_date
			));
		}

		$this->webspice->message_board('Balance added successfully!');
		$this->webspice->force_redirect($url_prefix.'send_money');

	}

	public function return_money() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'return_money');
		$this->webspice->permission_verify('return_money');
		$data = array();
		$errors = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('public_id', 'public id', 'required|trim|xss_clean');
		$this->form_validation->set_rules('amount', 'amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('user_note', 'user note', 'trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/balance/return_money', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('return_id');
		// dd($input);

		# verify public id
		if(!$this->webspice->verify_public_id_and_data($input->public_id)) {
			$errors[] = "Wrong public id";
		}
		else {
			$user_id = $this->webspice->verify_public_id_and_data($input->public_id)->USER_ID;
		}

		# verify numeric value
		if(!is_numeric($input->amount)) {
			$errors[] = "Amount must be in numeric value";
		}

		# verify minimum amount from settings
		/*if($this->webspice->verify_first_balance($user_id)) {
			$minimum_investment = $this->webspice->settings_data()->MINIMUM_INVESTMENT;
			if($input->amount < $minimum_investment) {
				$errors[] = "This is first balance for this user. You must provide $". $minimum_investment ." or upper amount";
			}
		}*/

		# check user balance
		// .............
		// .............
		// .............


		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view("admin_new/balance/return_money", $data);
			return false;
		}

		// variable initialize
		$user_id = $user_id;
		$amount = $input->amount;
		$note = $input->user_note;
		$public_id = $input->public_id;

		$my_str = $public_id . "|" . $user_id . "|" . $amount . "|" . $note;
		$my_str = $this->webspice->enc($my_str, 'encrypt');
		// dd($this->webspice->enc($my_str, 'decrypt'));

		$this->webspice->message_board('Please insert your pin to confirm action');
		$this->webspice->force_redirect($url_prefix.'confirm_return_money/'.$my_str);
	}

	public function confirm_return_money() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'return_money');
		$this->webspice->permission_verify('return_money');
		$data = array();
		$errors = array();
		$key = $this->uri->segment(2);
		$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
		// dd($id);
		$id = explode("|", $id);
		$data['public_id'] = $id[0];
		$data['user_id'] = $id[1];
		$data['amount'] = $id[2];
		$data['user_note'] = $id[3];
		$reg_data = $this->webspice->verify_public_id_and_data($id[0]);
		$data['first_name'] = $reg_data->FIRST_NAME;
		$data['last_name'] = $reg_data->LAST_NAME;
		$data['email'] = $reg_data->EMAIL;
		$data['mobile'] = $reg_data->MOBILE;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('pin_number', 'pin number', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/balance/confirm_return_money', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('flexi_trans_id');
		$pin_number = $input->pin_number;

		// dd($pin_number);

		// verify pin number
		if(!$this->webspice->user_pin_verify($this->webspice->get_user_id(), $pin_number)) {
			$errors[] = "Your pin didn't match.";
		}

		if(count($errors)) {
			// dd($errors);
			$data['errors'] = $errors;
			$this->load->view("admin_new/balance/confirm_return_money", $data);
			return false;
		}

		# init data setup
		$user_id = $data['user_id'];
		$balance_type = "POST";
		$amount = $data['amount'];
		$reason = "Balance Return By " . $this->webspice->admin_name($this->webspice->get_user_id());
		$user_note = $data['user_note'];
		$trans_date = $this->webspice->now();
		$provider_id = $this->webspice->get_user_id();
		$trans_status = 0;
		$created_date = $this->webspice->now();

		$sql = "
		INSERT INTO user_balance
		(USER_ID, BALANCE_TYPE, AMOUNT, REASON, USER_NOTE, TRANS_DATE, PROVIDER_ID, TRANS_STATUS, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql, array(
			$user_id,
			$balance_type,
			$amount,
			$reason,
			$user_note,
			$trans_date,
			$provider_id,
			$trans_status,
			$created_date
		));
	
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		// update user as premium user if this the first balance transfer
		/*if($this->webspice->verify_non_premium($data['public_id'])) {
			$this->db->query("UPDATE user_registration SET ACC_STATUS=1 WHERE MY_ID='".$data['public_id']."'");
		}*/

		$this->webspice->message_board('Balance deducted successfully!');
		$this->webspice->force_redirect($url_prefix.'return_money');

	}

	public function transfer_balance_history() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'transfer_balance_history');
		$this->webspice->permission_verify('transfer_balance_history');
		$data = array();
		$errors = array();
		$user_id = $this->webspice->get_user_id();

		// $data['get_record'] = $this->db->query("SELECT * FROM user_balance WHERE PROVIDER_ID='".$user_id."' AND TRANS_STATUS=1")->result();
		if($this->webspice->admin_verify()) {
			$data['get_record'] = $this->db->query("SELECT * FROM user_balance WHERE PROVIDER_ID='".$user_id."' AND TRANS_STATUS=1")->result();
		}
		else {
			$data['get_record'] = $this->db->query("SELECT * FROM user_balance WHERE (USER_ID='".$user_id."') AND TRANS_STATUS IN (0, 1)")->result();
		}

		$this->load->view("admin_new/balance/transfer_balance_history", $data);
	}

	public function return_balance_history() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'return_balance_history');
		$this->webspice->permission_verify('return_balance_history');
		$data = array();
		$errors = array();
		$user_id = $this->webspice->get_user_id();

		$data['get_record'] = $this->db->query("SELECT * FROM user_balance WHERE PROVIDER_ID='".$user_id."' AND TRANS_STATUS=0")->result();
		$this->load->view("admin_new/balance/return_balance_history", $data);
	}

	public function reffer_income() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'reffer_income');
		$this->webspice->permission_verify('reffer_income');
		$data = array();
		$errors = array();
		$user_id = $this->webspice->get_user_id();

		$data['get_record'] = $this->db->query("SELECT * FROM refer_wallet WHERE USER_ID='".$user_id."' AND BALANCE_TYPE='GET' AND TRANS_STATUS=2")->result();
		$this->load->view("admin_new/balance/reffer_income", $data);
	}

	public function generation_income() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'generation_income');
		$this->webspice->permission_verify('generation_income');
		$data = array();
		$errors = array();
		$user_id = $this->webspice->get_user_id();

		$data['get_record'] = $this->db->query("SELECT * FROM refer_wallet WHERE USER_ID='".$user_id."' AND BALANCE_TYPE='GET' AND TRANS_STATUS=3")->result();
		$this->load->view("admin_new/balance/generation_income", $data);
	}

	public function mature_share_income() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'mature_share_income');
		$this->webspice->permission_verify('mature_share_income');
		$data = array();
		$errors = array();
		$user_id = $this->webspice->get_user_id();

		$data['get_record'] = $this->db->query("SELECT * FROM user_balance WHERE USER_ID='".$user_id."' AND BALANCE_TYPE='GET' AND TRANS_STATUS=4")->result();
		$this->load->view("admin_new/balance/mature_share_income", $data);
	}

	public function buy_share() {
		
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'buy_share');
		$this->webspice->permission_verify('buy_share');
		$data = array();
		$errors = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('share_amount', 'shopping package', 'required|trim|xss_clean');
		// $this->form_validation->set_rules('no_of_share', 'no of share', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/share/buy_share', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('share_id');
		// dd($input);

		# verify numeric value
		/*if(!is_numeric($input->share_amount)) {
			$errors[] = "Share amount must be in numeric value";
		}*/

		# share can buy for once
		/*$chk_buy_share = $this->db->query("SELECT * FROM share_balance WHERE USER_ID='".$this->webspice->get_user_id()."'")->row();
		if(count($chk_buy_share)) {
			$errors[] = "You already purchased share you can not purchase it again";
		}

		# verify minimum amount from settings
		$total_amount = $input->share_amount * $input->no_of_share;
		$user_balance = $this->webspice->user_balance($this->webspice->get_user_id());
		$minimum_investment = $this->webspice->settings_data()->MINIMUM_INVESTMENT;

		if($input->share_amount < $minimum_investment) {
			$errors[] = "You must buy minimum $". $minimum_investment ." or upper amount";
		}*/

		$shopping_balance = $this->webspice->shopping_balance($this->webspice->get_user_id());

		# check user balance
		if($shopping_balance < $input->share_amount) {
			$errors[] = "Insufficient funds, plesae contact with admin to load balance";
		}

		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view("admin_new/share/buy_share", $data);
			return false;
		}

		# inser data to share_balance table
		$user_id = $this->webspice->get_user_id();
		$per_share_amount = $input->share_amount;
		$total_share = 1;
		$total_amount = $input->share_amount;
		$share_expire = date("Y-m-d", strtotime("+365 days"));
		$buy_datetime = $this->webspice->now();
		$created_date = $this->webspice->now();

		$sql = "
		INSERT INTO share_balance
		(USER_ID, PER_SHARE_AMOUNT, TOTAL_SHARE, TOTAL_AMOUNT, SHARE_EXPIRED, BUY_DATETIME, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql, array(
			$user_id,
			$per_share_amount,
			$total_share,
			$total_amount,
			$share_expire,
			$buy_datetime,
			$created_date
		));
	
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		# init data setup
		$balance_type = "POST";
		$reason = "Shopping package purched by " . $this->webspice->admin_name($this->webspice->get_user_id());
		$trans_date = $this->webspice->now();
		$trans_status = 5;
		$created_date = $this->webspice->now();

		$sql2 = "
		INSERT INTO shopping_wallet
		(USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql2, array(
			$user_id,
			$balance_type,
			$total_amount,
			$reason,
			$trans_date,
			$trans_status,
			$created_date
		));
	
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Shopping package purched successfully');
		$this->webspice->force_redirect($url_prefix.'manage_share');

	}

	public function confirm_buy_share() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'buy_share');
		$this->webspice->permission_verify('buy_share');
		$data = array();
		$errors = array();
		$key = $this->uri->segment(2);
		$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
		// dd($id);
		$id = explode("|", $id);
		$data['share_amount'] = $id[0];
		$data['no_of_share'] = $id[1];
		$data['total_amount'] = $id[2];
		$data['share_expire'] = $id[3];

		$this->load->library('form_validation');
		$this->form_validation->set_rules('pin_number', 'pin number', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/share/confirm_buy_share', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('share_id');
		$pin_number = $input->pin_number;

		// dd($pin_number);

		// verify pin number
		if(!$this->webspice->user_pin_verify($this->webspice->get_user_id(), $pin_number)) {
			$errors[] = "Your pin didn't match.";
		}

		if(count($errors)) {
			// dd($errors);
			$data['errors'] = $errors;
			$this->load->view("admin_new/share/confirm_buy_share", $data);
			return false;
		}

		# inser data to share_balance table
		$user_id = $this->webspice->get_user_id();
		$per_share_amount = $data['share_amount'];
		$total_share = $data['no_of_share'];
		$total_amount = $data['total_amount'];
		$share_expire = $data['share_expire'];
		$buy_datetime = $this->webspice->now();
		$created_date = $this->webspice->now();

		$sql = "
		INSERT INTO share_balance
		(USER_ID, PER_SHARE_AMOUNT, TOTAL_SHARE, TOTAL_AMOUNT, SHARE_EXPIRED, BUY_DATETIME, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql, array(
			$user_id,
			$per_share_amount,
			$total_share,
			$total_amount,
			$share_expire,
			$buy_datetime,
			$created_date
		));
	
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		# init data setup
		$balance_type = "POST";
		$reason = "Share Purched by " . $this->webspice->admin_name($this->webspice->get_user_id());
		$trans_date = $this->webspice->now();
		$trans_status = 5;
		$created_date = $this->webspice->now();

		$sql2 = "
		INSERT INTO user_balance
		(USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql2, array(
			$user_id,
			$balance_type,
			$total_amount,
			$reason,
			$trans_date,
			$trans_status,
			$created_date
		));
	
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		# pay reffre income
		$my_refferer = $this->webspice->reffer_id_via_user_id($user_id);
		if($my_refferer != 0) {
			$reffre_user_id = $this->webspice->user_id_via_reffer_id($my_refferer);

			# init data setup
			$balance_type = "GET";
			$reason = "Got reffre bonus for joining " . $this->webspice->admin_name($this->webspice->get_user_id());
			$trans_date = $this->webspice->now();
			$trans_status = 2;
			$created_date = $this->webspice->now();
			$reffre_comission = $this->webspice->settings_data()->REFFER_INCOME;
			$reffre_amount = $total_amount * ($reffre_comission/100);

			$sql3 = "
			INSERT INTO user_balance
			(USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, PROVIDER_ID, TRANS_STATUS, CREATED_DATE)
			VALUES
			( ?, ?, ?, ?, ?, ?, ?, ? )";
			$this->db->query($sql3, array(
				$reffre_user_id,
				$balance_type,
				$reffre_amount,
				$reason,
				$trans_date,
				$user_id,
				$trans_status,
				$created_date
			));
		}

		// update REFFER_PAY_STATUS = 1 to the user_registration table
		$my_id = $this->webspice->my_id_via_user_id($user_id);
		$this->db->query("UPDATE user_registration SET REFFER_PAY_STATUS=1 WHERE MY_ID='".$my_id."'");

		$this->webspice->message_board('Share purched successfully');
		$this->webspice->force_redirect($url_prefix.'manage_share');

	}

	public function manage_share() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_share');
		$this->webspice->permission_verify('manage_share');
		$data = array();
		$errors = array();
		$user_id = $this->webspice->get_user_id();

		if($this->webspice->admin_verify()) {
			$data['get_record'] = $this->db->query("SELECT * FROM share_balance")->result();
		}
		else {
			$data['get_record'] = $this->db->query("SELECT * FROM share_balance WHERE USER_ID='".$user_id."'")->result();
		}
		$this->load->view("admin_new/share/manage_share", $data);
	}


	/***********************************************************************
	******************* add click & earn functionalities *******************
	***********************************************************************/
	public function ptc_earn() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'ptc_earn');
		$user_id = $this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt');
		$this->webspice->permission_verify('ptc_earn');
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		$data = array();

		# action area
		if($criteria) {
			switch ($criteria) {
				case 'delete_package':
					$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
					
					$sql = $this->db->query("DELETE FROM package_mgmt WHERE PACK_ID='".$id."' LIMIT 1");
					if($sql) {
						$this->webspice->message_board('Package deleted successfully');
						$this->webspice->force_redirect($url_prefix.'ptc_earn');
					}
					return false;
				break;

				case 'add_click':
					$key_data = $this->webspice->enc($key, 'decrypt');
					$key_data = explode("|", $key_data);
					$data['add_id'] = $key_data[0];
					$data['package_id'] = $key_data[1];
					$data['add_name'] = $key_data[2];
					$data['url'] = $key_data[3];
					$data['price'] = $key_data[4];
					$data['add_duration'] = $key_data[5];
					$cur_date = date("Y-m-d");

					// re-click check
					$re_click = $this->db->query("SELECT * FROM ptc_click WHERE ADD_ID='{$data['add_id']}' AND USER_ID='{$this->webspice->get_user_id()}' AND CLICK_DATE='{$cur_date}'")->result();

					if(!count($re_click)) {
						$add_data = $this->db->query("SELECT * FROM add_setup WHERE ADD_ID='{$data['add_id']}'")->row();
						$add_value = $add_data->PRICE;
						$click_date = $this->webspice->now();
						$click_date_time = $this->webspice->now();
						$click_validation = 0;
						
						$sql = "
						INSERT INTO ptc_click
						(ADD_ID, USER_ID, ADD_VALUE, CLICK_DATE, CLICK_DATE_TIME, CLICK_VALIDATION)
						VALUES
						( ?, ?, ?, ?, ?, ? )";
						$this->db->query($sql, array(
							$data['add_id'],
							$this->webspice->get_user_id(),
							$add_value,
							$click_date,
							$click_date_time,
							$click_validation
						));

						if( !$this->db->insert_id() ){
							$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
							$this->webspice->force_redirect($url_prefix . 'admin');
							return false;
						}
					}

					$this->load->view('admin_new/click/ptc_add_page', $data);
					return false;
				break;

				case 'confirm_click':
					$id = $this->webspice->enc($key, 'decrypt');
					$cur_date = date("Y-m-d");

					// validation check
					$valid_click = $this->db->query("SELECT * FROM ptc_click WHERE ADD_ID='{$id}' AND USER_ID='{$this->webspice->get_user_id()}' AND CLICK_DATE='{$cur_date}' AND CLICK_VALIDATION=0")->row();

					if(count($valid_click)) {

						$this->db->query("UPDATE ptc_click SET CLICK_VALIDATION=1 WHERE ADD_ID='{$id}'");

						// insert balance to adds_wallet
						$balance_type = "GET";
						$amount = $valid_click->ADD_VALUE*.7;
						$reason = "PTC income by add click job";
						$trans_date = $this->webspice->now();
						$trans_status = 1;
						$created_date = $trans_date;
						$sql3 = "
						INSERT INTO adds_wallet
						(ADD_ID, USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
						VALUES
						( ?, ?, ?, ?, ?, ?, ?, ? )";

						$this->db->query($sql3, array(
							$id,
							$this->webspice->get_user_id(),
							$balance_type,
							$amount,
							$reason,
							$trans_date,
							$trans_status,
							$created_date
						));

						// insert balance to joining_wallet
						$balance_type = "GET";
						$amount = $valid_click->ADD_VALUE*.15;
						$reason = "PTC income by add click job";
						$trans_date = $this->webspice->now();
						$trans_status = 1;
						$created_date = $trans_date;
						$sql4 = "
						INSERT INTO joining_wallet
						(ADD_ID, USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
						VALUES
						( ?, ?, ?, ?, ?, ?, ?, ? )";

						$this->db->query($sql4, array(
							$id,
							$this->webspice->get_user_id(),
							$balance_type,
							$amount,
							$reason,
							$trans_date,
							$trans_status,
							$created_date
						));

						// insert balance to shopping_wallet
						$balance_type = "GET";
						$amount = $valid_click->ADD_VALUE * .15;
						$reason = "PTC income by add click job";
						$trans_date = $this->webspice->now();
						$trans_status = 1;
						$created_date = $trans_date;
						$sql5 = "
						INSERT INTO shopping_wallet
						(ADD_ID, USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
						VALUES
						( ?, ?, ?, ?, ?, ?, ?, ? )";

						$this->db->query($sql5, array(
							$id,
							$this->webspice->get_user_id(),
							$balance_type,
							$amount,
							$reason,
							$trans_date,
							$trans_status,
							$created_date
						));
					}

					echo '<script>window.top.close();</script>';
				break;
			}
		}

		$package_data = $this->webspice->user_package_data();
		$ptc_limit = $package_data->PTC_LINK;
		$package_id = $package_data->PACKAGE_ID;
		$add_type = 'ptc';
		$cur_date = date("Y-m-d");

		$todays_click = $this->db->query("SELECT * FROM ptc_click WHERE USER_ID='{$user_id}' AND CLICK_DATE='{$cur_date}'")->result();
		$data['click_data'] = $todays_click;
		// dd($todays_click);

		if(count($todays_click)) {
			$tot_clicked = array();
			foreach($todays_click as $t_val) {
				$tot_clicked[] = $t_val->ADD_ID;
			}
			// dd($tot_clicked);
			$ptc_limit = $ptc_limit - count($todays_click);
			$not_in = implode(",", $tot_clicked);
			// dd($not_in);
			//$data['get_record'] = $this->db->query("SELECT * FROM add_setup WHERE PACKAGE_ID='{$package_id}' AND ADD_TYPE='{$add_type}' AND ADD_ID NOT IN('".$not_in."') ORDER BY ADD_ID DESC LIMIT ".$ptc_limit)->result();
			$data['get_record'] = $this->db->query("SELECT * FROM add_setup WHERE PACKAGE_ID='{$package_id}' AND STATUS=7 AND ADD_TYPE='{$add_type}' AND ADD_ID NOT IN(SELECT ADD_ID FROM ptc_click WHERE  USER_ID='{$user_id}' AND CLICK_DATE='{$cur_date}') ORDER BY RAND() LIMIT ".$ptc_limit)->result();
			//dd($data['get_record']);
			//dd($tot_clicked);
			//dd($this->db->last_query());
		}
		else {
			$data['get_record'] = $this->db->query("SELECT * FROM add_setup WHERE PACKAGE_ID='{$package_id}' AND STATUS=7 AND ADD_TYPE='{$add_type}' ORDER BY RAND() LIMIT ".$ptc_limit)->result();
		}
		// dd($data['get_record']);


		$this->load->view('admin_new/click/ptc_earn', $data);

	}

}