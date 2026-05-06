<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reseller_controller extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
	}

	public function create_reseller($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_reseller');
		$this->webspice->permission_verify('create_reseller');
		
		/*
		 * Remove the edit portion
		*/

		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_name', 'user name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('user_email', 'user email', 'required|trim|xss_clean');
		$this->form_validation->set_rules('user_phone', 'user phone', 'required|trim|xss_clean');
		$this->form_validation->set_rules('user_type', 'user type', 'required|trim|xss_clean');
		$this->form_validation->set_rules('pin', 'pin', 'required|trim|xss_clean');
		$this->form_validation->set_rules('confirm_pin', 'confirm pin', 'required|trim|xss_clean');
		$this->form_validation->set_rules('service_permission', 'service permission', 'required|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/reseller/create_reseller', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('reseller_id');
		// dd($input);
		
		// data initialization & checking
		$errors = array();
		if( ($input->pin !== $input->confirm_pin) ) {
			$errors[] = "Your pin didn't match.";
		}

		if(count($errors)) {
			// dd($errors);
			$data['errors'] = $errors;
			$this->load->view("admin/reseller/create_reseller", $data);
			return false;
		}

		$service_permission = implode(",", $input->service_permission);
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM user WHERE USER_EMAIL=?", array($input->user_email), 'You are not allowed to enter duplicate reseller', 'RESELLER_ID', $input->user_id, $data, 'admin/reseller/create_reseller');
		// dd("Hello");
		
		# remove cache
		$this->webspice->remove_cache('reseller');

		# update process
		// if( $input->service_id ) {

			// no update process right now

			/*$sql = "
			UPDATE service_settings SET SERVICE_TYPE=?, SERVICE_NAME=?, SERVICE_CODE=?, PREFIX=?, MIN_AMOUNT=?, MAX_AMOUNT=?, BULK_LIMIT=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE SERVICE_ID=?";

			$this->db->query($sql, array($input->service_type, $input->service_name, $input->service_code, $input->prefix, $input->min_amount, $input->max_amount, $input->bulk_limit, $this->webspice->get_user_id(), $this->webspice->now(), $input->service_id));

			$this->webspice->message_board('Record has been updated!');
			$this->webspice->log_me('product_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_flexi_service_setup');
			return false;*/
		// }

		// at first data goes to user
		$role_id = $this->db->query("SELECT ROLE_ID FROM role WHERE ROLE_NAME='Reseller Permission'")->row()->ROLE_ID;
		$user_name = $input->user_name;
		$user_email = $input->user_email;
		$user_phone = $input->user_phone;
		$user_type = $this->webspice->encrypt_decrypt($input->user_type, 'encrypt');
		$user_password = $this->webspice->encrypt_decrypt("1234", 'encrypt');
		
		#insert data
		$sql = "
		INSERT INTO user
		(ROLE_ID, USER_NAME, USER_EMAIL, USER_PHONE, USER_TYPE, USER_PASSWORD, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($role_id, $user_name, $user_email, $user_phone, $user_type, $user_password, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$user_id = $this->db->insert_id();
		$parent_id = $_SESSION['user']['USER_ID'];
		$pin = $this->webspice->encrypt_decrypt($input->pin, 'encrypt');

		#insert reseller
		$sql2 = "
		INSERT INTO reseller
		(USER_ID, PARENT_ID, PIN, USER_TYPE, SERVICE_PERMISSION, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, 7 )";
		$this->db->query($sql2, array($user_id, $parent_id, $pin, $user_type, $service_permission, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		// $reseller_id = $this->webspice->encrypt_decrypt($this->db->insert_id(), 'encrypt');
		$reseller_id = $this->db->insert_id();
		$this->db->query("UPDATE user SET RESELLER_ID='".$reseller_id."' WHERE USER_ID='".$user_id."'");

		$this->webspice->message_board('Reseller data inserted successfully!');
		if($this->webspice->permission_verify('manage_reseller',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_reseller');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_reseller');

	}

	public function manage_reseller() {

		$user_sess_id = $_SESSION['user']['USER_ID'];
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_reseller');
		$this->webspice->permission_verify('manage_reseller');
		$this->load->database();
		$orderby = 'ORDER BY r.RESELLER_ID DESC';
		$groupby = null;
		$where = '';
		if(!$this->webspice->admin_verify()) {
			$where = ' WHERE r.PARENT_ID="'.$user_sess_id.'" ';
		}
		$page_index = 0;
		$no_of_record = 20;
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
		SELECT  r.*, u.* FROM reseller AS r INNER JOIN user AS u ON r.USER_ID=u.USER_ID ";


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

			case 'edit':
				$this->webspice->edit_generator($TableName='service_settings', $KeyField='SERVICE_ID', $key, $RedirectController='flexi_controller', $RedirectFunction='flexi_service_setup', $PermissionName='manage_flexi_service_setup', $StatusCheck=null, $Log='edit_flexi_service');
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
				$this->webspice->action_executer($TableName='reseller', $KeyField='RESELLER_ID', $key, $RedirectURL='manage_reseller', $PermissionName='manage_reseller', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='reseller', $Log='inactive_reseller');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='reseller', $KeyField='RESELLER_ID', $key, $RedirectURL='manage_reseller', $PermissionName='manage_reseller', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='reseller', $Log='active_reseller');
				return false;
				break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM reseller WHERE RESELLER_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_reseller');
				}
				return false;
			break;

			case 'payment':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');

				$this->load->library('form_validation');
				$this->form_validation->set_rules('amount', 'amount', 'required|trim|xss_clean');
				$this->form_validation->set_rules('trans_type', 'transaction type', 'required|trim|xss_clean');
				$this->form_validation->set_rules('note', 'user note', 'trim|xss_clean');
				$this->form_validation->set_rules('user_password', 'user password', 'required|trim|xss_clean');
				
				if( !$this->form_validation->run() ){
					$this->load->view('admin_new/reseller/reseller_payment', $data);
					return FALSE;
				}

				# get input post
				$input = $this->webspice->get_input('reseller_id');
				// dd($input);

				// variable initialize
				$user_pass = $this->db->query("SELECT USER_PASSWORD FROM user WHERE USER_ID='".$this->webspice->get_user_id()."'")->row()->USER_PASSWORD;
				$amount = $input->amount;
				$trans_type = $input->trans_type;
				$note = $input->note;
				$user_id = $this->webspice->get_reseller_user_id($id);
				$reseller_id = $id;
				$sender_id = $this->webspice->get_user_id();
				$balance = $this->webspice->reseller_load_balance_margin($id, $trans_type, $amount);

				// dd($balance);
				
				// data initialization & checking
				$errors = array();
				if( ($input->user_password !== $this->webspice->enc($user_pass, 'decrypt')) ) {
					$errors[] = "Invalid password number. Please enter correct password to transaction";
				}

				if(!is_numeric($amount)) {
					$errors[] = "Amount field must be integer. Text given";
				}

				if(count($errors)) {
					$data['errors'] = $errors;
					$this->load->view("admin_new/reseller/reseller_payment", $data);
					return false;
				}
				#insert data
				$sql = "
				INSERT INTO reseller_trans
				(USER_ID, RESELLER_ID, SENDER_ID, AMOUNT, BALANCE, NOTE, TRANS_TYPE, SEND_DATE)
				VALUES
				( ?, ?, ?, ?, ?, ?, ?, ? )";
				$this->db->query($sql, array($user_id, $reseller_id, $sender_id, $amount, $balance, $note, $trans_type, $this->webspice->now()));
				
				if( !$this->db->insert_id() ){
					$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
					$this->webspice->force_redirect($url_prefix . 'admin');
					return false;
				}

				$this->webspice->message_board('Transaction created successfully');
				if($this->webspice->permission_verify('manage_reseller',TRUE)){
					$this->webspice->force_redirect($url_prefix . 'manage_reseller');
					return FALSE;
				}
				$this->webspice->force_redirect($url_prefix.'create_reseller');
				
				return false;
			break;

			case 'payment_history':
				$data = array();
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');

				$data['get_record'] = $this->db->query("SELECT * FROM reseller_trans WHERE RESELLER_ID='".$id."'")->result();
				$this->load->view('admin_new/reseller/payment_history', $data);

				return false;
			break;

			case 'set_rates':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$data = array();
				$data['service_list'] = $this->db->query("SELECT SERVICE_ID, SERVICE_NAME, SERVICE_CODE, PREFIX, LOGO FROM service_settings WHERE STATUS=7")->result();
				// dd($data);

				$this->load->library('form_validation');
				$this->form_validation->set_rules('comission', 'comission', 'required|xss_clean');
				$this->form_validation->set_rules('charge', 'charge', 'required|xss_clean');
				
				if( !$this->form_validation->run() ){
					$this->load->view('admin_new/reseller/set_rates', $data);
					return FALSE;
				}

				# get input post
				$input = $this->webspice->get_input('rate_id');
				
				$errors = array();
				$comission_val = array();
				$charge_val = array();
				if(count($input->comission) == count($input->charge)) {
					$loop = count($input->comission);
				}
				foreach($input->charge as $k=>$v) {
					$charge_val[] = $v;
				}

				$comission_chk = array_values(array_filter($input->comission));
				$charge_chk = array_values(array_filter($input->charge));

				if(!count($comission_chk) && !count($charge_chk)) {
					$errors[] = "Value did not provide. Data can not be saved. To save at least one value have to provide";
				}

				if(count($errors)) {
					$data['errors'] = $errors;
					$this->load->view("admin_new/reseller/set_rates", $data);
					return false;
				}

				// variable initialize
				$reseller_id = $id;
				$parent_id = $this->webspice->get_user_id();
				$rates_id = array();


				
				#insert data
				$sql = "
				INSERT INTO rate_mod
				(SERVICE_ID, RESELLER_ID, PARENT_ID, COMISSION, CREATED_BY, CREATED_DATE)
				VALUES
				( ?, ?, ?, ?, ?, ? )";

				foreach($input->comission as $key=>$val) {
					$this->db->query($sql, array($key, $reseller_id, $parent_id, $val, $parent_id, $this->webspice->now()));
					$rates_id[] = $this->db->insert_id();
				}
				
				if( !$this->db->insert_id() ){
					$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
					$this->webspice->force_redirect($url_prefix . 'admin');
					return false;
				}
				
				$i=0;
				foreach($rates_id as $rate) {
					$this->db->query("UPDATE rate_mod SET CHARGE='".$charge_val[$i]."' WHERE RATE_ID='".$rate."'");
					$i++;
				}

				$this->webspice->message_board('Rate created successfully');
				if($this->webspice->permission_verify('manage_reseller',TRUE)){
					$this->webspice->force_redirect($url_prefix . 'manage_reseller');
					return FALSE;
				}
				$this->webspice->force_redirect($url_prefix.'create_reseller');
				
				return false;
			break;

			case 'view_rates':
				$data = array();
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');

				$data['get_record'] = $this->db->query("SELECT rm.*, ss.SERVICE_NAME, ss.SERVICE_CODE, ss.PREFIX, ss.LOGO FROM rate_mod AS rm INNER JOIN service_settings AS ss ON rm.SERVICE_ID=ss.SERVICE_ID WHERE RESELLER_ID='".$id."'")->result();
				
				$this->load->library('form_validation');
				$this->form_validation->set_rules('comission', 'comission', 'required|xss_clean');
				$this->form_validation->set_rules('charge', 'charge', 'required|xss_clean');
				
				if( !$this->form_validation->run() ){
					$this->load->view('admin_new/reseller/view_rates', $data);
					return FALSE;
				}

				# get input post
				$input = $this->webspice->get_input('rate_id');
				
				$errors = array();
				$comission_val = array();
				$charge_val = array();
				$rates_id = array();
				if(count($input->comission) == count($input->charge)) {
					$loop = count($input->comission);
				}
				foreach($input->charge as $k=>$v) {
					$charge_val[] = $v;
				}
				foreach($input->comission as $k1=>$v1) {
					$comission_val[] = $v1;
				}
				foreach($data['get_record'] as $rate_val) {
					$rates_id[] = $rate_val->RATE_ID;
				}

				$comission_chk = array_values(array_filter($input->comission));
				$charge_chk = array_values(array_filter($input->charge));

				if(!count($comission_chk) && !count($charge_chk)) {
					$errors[] = "Value did not provide. Data can not be saved. To save at least one value have to provide";
				}

				if(count($errors)) {
					$data['errors'] = $errors;
					$this->load->view("admin_new/reseller/set_rates", $data);
					return false;
				}

				// variable initialize
				$reseller_id = $id;
				$parent_id = $this->webspice->get_user_id();

				// update data
				$sql = "
				UPDATE rate_mod SET COMISSION=?, CHARGE=?, UPDATED_BY=?, UPDATED_DATE=?
				WHERE RATE_ID=?";

				$i=0;
				foreach($rates_id as $rate) {
					$this->db->query($sql, array($comission_val[$i], $charge_val[$i], $this->webspice->get_user_id(), $this->webspice->now(), $rate));
					$i++;
				}

				$this->webspice->message_board('Rate updated successfully');
				if($this->webspice->permission_verify('manage_reseller',TRUE)){
					$this->webspice->force_redirect($url_prefix . 'manage_reseller');
					return FALSE;
				}
				$this->webspice->force_redirect($url_prefix.'create_reseller');

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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_reseller/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/reseller/manage_reseller', $data);

	}

}