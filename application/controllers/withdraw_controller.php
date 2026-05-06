<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Withdraw_controller extends CI_Controller {

	/*
	**********************************************
	*
	* table - payment
	*
	* TRANS_STATUS = 0 = Pending
	* TRANS_STATUS = 1 = On Precess
	* TRANS_STATUS = 2 = Success
	* TRANS_STATUS = 3 = Cancelled
	*
	**********************************************
	*/

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
	}

	public function balance_withdraw() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'balance_withdraw');
		$this->webspice->permission_verify('balance_withdraw');
		$data = array();
		$errors = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('payment_method', 'payment withdraw from', 'required|trim|xss_clean');
		$this->form_validation->set_rules('user_note', 'user note', 'trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/withdraw/balance_withdraw', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('send_id');
		// dd($input);

		# verify payment method
		if($input->payment_method==3 ||$input->payment_method==4) {
			$errors[] = "Right now we can not accept Neteller & Solid Trust";
		}
		

		# verify numeric value
		if(!is_numeric($input->amount)) {
			$errors[] = "Amount must be in numeric value";
		}

		# verify minimum amount $10 transaction
		if($input->amount < 20) {
			$errors[] = "You can not request withdraw less then $20";
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
			$this->load->view("admin_new/withdraw/balance_withdraw", $data);
			return false;
		}

		// variable initialize
		$payment_method = $input->payment_method;
		$amount = $input->amount;
		$note = $input->user_note;

		$my_str = $payment_method . "|" . $amount . "|" . $note;
		$my_str = $this->webspice->enc($my_str, 'encrypt');
		// dd($this->webspice->enc($my_str, 'decrypt'));

		$this->webspice->message_board('Please insert your pin to confirm action');
		$this->webspice->force_redirect($url_prefix.'confirm_withdraw_request/'.$my_str);
	}

	public function confirm_withdraw_request() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'balance_withdraw');
		$this->webspice->permission_verify('balance_withdraw');
		$data = array();
		$errors = array();
		$key = $this->uri->segment(2);
		$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
		// dd($id);
		$id = explode("|", $id);
		$data['payment_method'] = $id[0];
		$data['amount'] = $id[1];
		$data['user_note'] = $id[2];
		$my_id = $this->webspice->my_id_via_user_id($this->webspice->get_user_id());
		$reg_data = $this->webspice->verify_public_id_and_data($my_id);
		// dd($reg_data);
		$data['first_name'] = $reg_data->FIRST_NAME;
		$data['last_name'] = $reg_data->LAST_NAME;
		$data['email'] = $reg_data->EMAIL;
		$data['mobile'] = $reg_data->MOBILE;


		$this->load->library('form_validation');
		$this->form_validation->set_rules('pin_number', 'pin number', 'required|trim|xss_clean');
		if($data['payment_method'] == 2) {
			$this->form_validation->set_rules('pm_number', 'perfect money account number', 'required|trim|xss_clean');
		}
		else if($data['payment_method'] == 1) {
			$this->form_validation->set_rules('bkash_number', 'bKash number', 'required|trim|xss_clean');
		}
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/withdraw/confirm_withdraw_request', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('flexi_trans_id');
		$pin_number = $input->pin_number;
		if($data['payment_method'] == 1) {
			$acc_number = $input->bkash_number;
			if(!is_numeric($acc_number)) {
				$errors[] = "Your bKash number must be numeric";
			}
		}
		else if($data['payment_method'] == 2) {
			$acc_number = $input->pm_number;
			if(!is_numeric($acc_number)) {
				$errors[] = "Your perfect money account number must be numeric";
			}
		}

		// dd($pin_number);

		// verify pin number
		if(!$this->webspice->user_pin_verify($this->webspice->get_user_id(), $pin_number)) {
			$errors[] = "Your pin didn't match.";
		}

		# payment number numeric check

		if(count($errors)) {
			// dd($errors);
			$data['errors'] = $errors;
			$this->load->view("admin_new/withdraw/confirm_withdraw_request", $data);
			return false;
		}

		# init data setup for Table: payment
		$user_id = $this->webspice->get_user_id();
		$payment_method = $data['payment_method'];
		$acc_number = $acc_number;
		$amount = $data['amount'];
		$trans_status = 0;
		if($payment_method == 1) {
			$payment_process = "bKash";
		}
		else if($payment_method == 2) {
			$payment_process = "Perfect Money";
		}
		$trans_note = "Withdraw request to admin & payment process is" . $payment_process;
		$req_date = $this->webspice->now();

		# init data setup for user balance
		// -- user_id
		$balance_type = "POST";
		// -- amount
		$reason = $trans_note;
		$trans_status_blnc = 6;
		// -- created_date


		# insert data to payment
		$sql = "
		INSERT INTO payment
		(USER_ID, PAYMENT_TYPE, ACC_NUMBER, AMOUNT, TRANS_STATUS, TRANS_NOTE, REQ_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql, array(
			$user_id,
			$payment_method,
			$acc_number,
			$amount,
			$trans_status,
			$trans_note,
			$req_date
		));

		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		# insert data to user_balance
		$sql = "
		INSERT INTO user_balance
		(USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql, array(
			$user_id,
			$balance_type,
			$amount,
			$reason,
			$req_date,
			$trans_status_blnc,
			$req_date
		));
	
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Withdraw request completed successfully. Please wait for admin approval');
		$this->webspice->force_redirect($url_prefix.'manage_withdraw');

	}

	/*public function manage_withdraw() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_withdraw');
		$this->webspice->permission_verify('manage_withdraw');
		$data = array();
		$errors = array();
		$user_id = $this->webspice->get_user_id();

		if($this->webspice->admin_verify()) {
			$data['get_record'] = $this->db->query("SELECT * FROM payment")->result();
		}
		else {
			$data['get_record'] = $this->db->query("SELECT * FROM payment WHERE USER_ID='".$user_id."'")->result();
		}
		$this->load->view("admin_new/withdraw/manage_withdraw", $data);
	}*/

	public function manage_withdraw() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_withdraw');
		$this->webspice->permission_verify('manage_withdraw');
		$this->load->database();
		$orderby = 'ORDER BY REQ_DATE DESC';
		$groupby = null;
		$user_id = $this->webspice->get_user_id();
		$where = '';
		if(!$this->webspice->admin_verify()) {
			$where = ' WHERE USER_ID="'.$user_id.'" ';
		}
		$page_index = 0;
		$no_of_record = 2000000;
		$limit = ' LIMIT '.$no_of_record;
		$filter_by = 'Last Created';
		$data['pager'] = null;
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		if ($criteria == 'page') {
			$page_index = (int)$key;
			$page_index < 0 ? $page_index=0 : $page_index=$page_index;
		}

		$initialSQL = "
		SELECT  * FROM payment ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'service_settings',
				$InputField = array(),
				$Keyword = array('SERVICE_NAME'),
				$AdditionalWhere = null,
				$DateBetween = null
			);

			$result['where'] ? $where = $result['where'] : $where=$where;
			$result['filter'] ? $filter_by = $result['filter'] : $filter_by=$filter_by;
		}

		# action area
		switch ($criteria) {
			case 'print':
			case 'csv':
				if( !isset($_SESSION['sql']) || !$_SESSION['sql'] ){
					$_SESSION['sql'] = $initialSQL . $where . $orderby;
					$_SESSION['filter_by'] = $filter_by;
				}

				$record = $this->db->query( substr($_SESSION['sql'], 0, stripos($_SESSION['sql'],'LIMIT')) );
				$data['get_record'] = $record->result();
				$data['filter_by'] = $_SESSION['filter_by'];

				$this->load->view('admin/flexi/print_flexi_service',$data);
				return false;
				break;

			case 'update':
				$id = $this->uri->segment(3);
				$id2 = $this->uri->segment(4);
				$id3 = $this->uri->segment(5);
				$data = $this->db->query($id . " " . $id2 . " " . $id3);
				if($data) { echo "Just for test purpose";}
				return false;
				break;
			case 'inactive':
				$this->webspice->action_executer($TableName='card_service', $KeyField='CARD_SERVICE_ID', $key, $RedirectURL='manage_card_service', $PermissionName='manage_card_service', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='card_service', $Log='inactive_card_service');
				return false;
				break;

			case 'approve':
				$data = array();
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$payment_data = $this->db->query("SELECT * FROM payment WHERE PAYMENT_ID='".$id."'")->row();
				$user_data = $this->db->query("SELECT * FROM user WHERE USER_ID='".$payment_data->USER_ID."'")->row();
				if($payment_data->PAYMENT_TYPE == 1) {
					$charge_data = $this->webspice->settings_data()->WITHDRAW_CHARGE_BK;
				}
				else if($payment_data->PAYMENT_TYPE == 2) {
					$charge_data = $this->webspice->settings_data()->WITHDRAW_CHARGE_PM;
				}
				$data['name'] = $user_data->USER_NAME;
				$data['email'] = $user_data->USER_EMAIL;
				$data['payment_type'] = $payment_data->PAYMENT_TYPE;
				$data['acc_number'] = $payment_data->ACC_NUMBER;
				$data['amount'] = $payment_data->AMOUNT;
				$data['charge'] = $charge_data;
				$data['charge_amt'] = $data['amount']*($charge_data/100);
				$data['withdraw_amount'] = $data['amount'] - $data['charge_amt'];

				// dd($data);


				$this->load->library('form_validation');
				$this->form_validation->set_rules('pin_number', 'pin number', 'required|trim|xss_clean');
				$this->form_validation->set_rules('transaction_id', 'transaction id', 'required|trim|xss_clean');
				$this->form_validation->set_rules('approval_note', 'approval note', 'trim|xss_clean');
				
				if( !$this->form_validation->run() ){
					$this->load->view('admin_new/withdraw/confirm_approve', $data);
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

				# payment number numeric check

				if(count($errors)) {
					// dd($errors);
					$data['errors'] = $errors;
					$this->load->view("admin_new/withdraw/confirm_withdraw_request", $data);
					return false;
				}



				// update payment field status
				$sql = "
				UPDATE payment SET TRANSACTION_ID=?, CHARGE=?, NET_AMOUNT=?, TRANS_STATUS=?, APPROVAL_NOTE=?, PAYMENT_BY=?,PAYMENT_DATE=?
				WHERE PAYMENT_ID=?";

				$this->db->query($sql, array($input->transaction_id, $data['charge_amt'], $data['withdraw_amount'], 2, $input->approval_note, $this->webspice->get_user_id(), $this->webspice->now(), $id));

				// redirect to manage_withdraw panel
				$this->webspice->message_board("Withdraw request paid successfully");
				$this->webspice->force_redirect($url_prefix.'manage_withdraw');
				return false;
			break;

			case 'onprogress':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$this->db->query("UPDATE payment SET TRANS_STATUS=1 WHERE PAYMENT_ID='".$id."'");
				$this->webspice->message_board("Withdraw request dump to on progress");
				$this->webspice->force_redirect($url_prefix.'manage_withdraw');

				return false;
			break;

			case 'cancelled':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$payment_data = $this->db->query("SELECT * FROM payment WHERE PAYMENT_ID='".$id."'")->row();
				$user_id = $payment_data->USER_ID;
				$amount = $payment_data->AMOUNT;
				$balance_type = "GET";
				$reason = "Withdraw request cancelled, refund balance $".$amount." to user";
				$trans_status = 7;
				$req_date = $this->webspice->now();
				// dd($payment_data);

				$this->db->query("UPDATE payment SET TRANS_STATUS=3 WHERE PAYMENT_ID='".$id."'");

				# insert data to user_balance
				$sql = "
				INSERT INTO user_balance
				(USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
				VALUES
				( ?, ?, ?, ?, ?, ?, ? )";
				$this->db->query($sql, array(
					$user_id,
					$balance_type,
					$amount,
					$reason,
					$req_date,
					$trans_status,
					$req_date
				));
			
				if( !$this->db->insert_id() ){
					$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
					$this->webspice->force_redirect($url_prefix . 'admin');
					return false;
				}
				$this->webspice->message_board("Withdraw request cancelled");
				$this->webspice->force_redirect($url_prefix.'manage_withdraw');

				return false;
			break;
		}

		# default
		$sql = $initialSQL . $where . $groupby . $orderby . $limit;

		# only for pager
		if( $criteria == 'page' ){
			if( !isset($_SESSION['sql']) || !$_SESSION['sql'] ){
				$sql = $sql;
			}
			$limit = sprintf("LIMIT %d, %d", $page_index, $no_of_record);		# this is to avoid SQL Injection
			$sql = substr($_SESSION['sql'], 0, strpos($_SESSION['sql'],'LIMIT'));
			$sql = $sql . $limit;
		}

		# load all records
		if( !$this->input->post('filter') ){
			$count_data = $this->db->query( substr($sql,0,strpos($sql,'LIMIT')) );
			$count_data = $count_data->result();
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_withdraw/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/withdraw/manage_withdraw', $data);

	}

	public function wallet_conversion() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'wallet_conversion');
		$this->webspice->permission_verify('wallet_conversion');
		$data = array();
		$errors = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('amount', 'amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('transfer_from', 'transfer from', 'required|trim|xss_clean');
		$this->form_validation->set_rules('transfer_to', 'transfer to', 'trim|xss_clean');
		$this->form_validation->set_rules('user_note', 'user note', 'trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/withdraw/wallet_conversion', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('wallet_id');

		# verify numeric value
		if(!is_numeric($input->amount)) {
			$errors[] = "Amount must be in numeric value";
		}

		# verify minimum amount $10 transaction
		/*if($input->amount < 20) {
			$errors[] = "You can not request withdraw less then $20";
		}*/

		# check user balance
		if(!$this->webspice->admin_verify()) {
			$user_balance = $this->webspice->wallet_balance($this->webspice->get_user_id(), $input->transfer_from);
			if($user_balance < ($input->amount + 1)) {
				$errors[] = "Insufficient funds, plesae contact with admin to load balance";
			}
		}

		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view("admin_new/withdraw/wallet_conversion", $data);
			return false;
		}

		// data setup
		$user_id = $this->webspice->get_user_id();
		$amount = $input->amount;
		$req_date = $this->webspice->now();

		# init data setup for user balance
		// -- user_id
		$balance_type = "POST";
		// -- amount
		$reason = "Wallet balance transfer from '".$input->transfer_from."' to Main Wallet";
		$trans_status_blnc = 1;
		// -- created_date

		# insert data to selected wallet
		$sql = "
		INSERT INTO $input->transfer_from
		(USER_ID, BALANCE_TYPE, AMOUNT, REASON, USER_NOTE, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql, array(
			$user_id,
			"POST",
			$amount,
			$reason,
			$input->user_note,
			$req_date,
			$trans_status_blnc,
			$req_date
		));

		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		# insert data to user_balance
		$sql = "
		INSERT INTO user_balance
		(USER_ID, BALANCE_TYPE, AMOUNT, REASON, USER_NOTE, TRANS_DATE, TRANS_STATUS, CONVERSION_WALLET, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql, array(
			$user_id,
			"GET",
			$amount,
			$reason,
			$input->user_note,
			$req_date,
			$trans_status_blnc,
			$input->transfer_from,
			$req_date
		));
	
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Balance transfered successfully, Thanks you.');
		$this->webspice->force_redirect($url_prefix.'wallet_conversion_history');
	}

	public function wallet_conversion_history() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'wallet_conversion_history');
		$this->webspice->permission_verify('wallet_conversion_history');
		$this->load->database();
		$orderby = 'ORDER BY BALANCE_ID DESC';
		$groupby = null;
		$user_id = $this->webspice->get_user_id();
		$where = ' WHERE CONVERSION_WALLET IS NOT NULL ';
		if(!$this->webspice->admin_verify()) {
			$where = ' WHERE USER_ID="'.$user_id.'" AND CONVERSION_WALLET IS NOT NULL ';
		}
		$page_index = 0;
		$no_of_record = 2000000;
		$limit = ' LIMIT '.$no_of_record;
		$filter_by = 'Last Created';
		$data['pager'] = null;
		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);
		if ($criteria == 'page') {
			$page_index = (int)$key;
			$page_index < 0 ? $page_index=0 : $page_index=$page_index;
		}

		$initialSQL = "
		SELECT  * FROM user_balance ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'service_settings',
				$InputField = array(),
				$Keyword = array('SERVICE_NAME'),
				$AdditionalWhere = null,
				$DateBetween = null
			);

			$result['where'] ? $where = $result['where'] : $where=$where;
			$result['filter'] ? $filter_by = $result['filter'] : $filter_by=$filter_by;
		}

		# action area
		switch ($criteria) {
			case 'print':
			case 'csv':
				if( !isset($_SESSION['sql']) || !$_SESSION['sql'] ){
					$_SESSION['sql'] = $initialSQL . $where . $orderby;
					$_SESSION['filter_by'] = $filter_by;
				}

				$record = $this->db->query( substr($_SESSION['sql'], 0, stripos($_SESSION['sql'],'LIMIT')) );
				$data['get_record'] = $record->result();
				$data['filter_by'] = $_SESSION['filter_by'];

				$this->load->view('admin/flexi/print_flexi_service',$data);
				return false;
				break;
		}

		# default
		$sql = $initialSQL . $where . $groupby . $orderby . $limit;

		# only for pager
		if( $criteria == 'page' ){
			if( !isset($_SESSION['sql']) || !$_SESSION['sql'] ){
				$sql = $sql;
			}
			$limit = sprintf("LIMIT %d, %d", $page_index, $no_of_record);		# this is to avoid SQL Injection
			$sql = substr($_SESSION['sql'], 0, strpos($_SESSION['sql'],'LIMIT'));
			$sql = $sql . $limit;
		}

		# load all records
		if( !$this->input->post('filter') ){
			$count_data = $this->db->query( substr($sql,0,strpos($sql,'LIMIT')) );
			$count_data = $count_data->result();
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'wallet_conversion_history/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/withdraw/wallet_conversion_history', $data);

	}

}