<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Flexi_controller extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
	}

	public function flexi_service_setup($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'flexi_service_setup');
		$this->webspice->permission_verify('flexi_service_setup');
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'SERVICE_ID'=>null,
				'SERVICE_TYPE'=>null,
				'SERVICE_NAME'=>null,
				'SERVICE_CODE'=>null,
				'PREFIX'=>null,
				'MIN_AMOUNT'=>null,
				'MAX_AMOUNT'=>null,
				'LOGO'=>null,
				'BULK_LIMIT'=>null
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('service_name', 'service name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('service_code', 'service code', 'required|trim|xss_clean');
		$this->form_validation->set_rules('prefix', 'prefix', 'required|trim|xss_clean');
		$this->form_validation->set_rules('min_amount', 'min amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('max_amount', 'max amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('bulk_limit', 'bulk limit', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin/flexi/flexi_service_setup', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('service_id');
		$input->service_type = "flexi_service";
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM service_settings WHERE SERVICE_NAME=? AND SERVICE_TYPE=?", array($input->service_name, $input->service_type), 'You are not allowed to enter duplicate service', 'SERVICE_ID', $input->service_id, $data, 'admin/flexi/flexi_service_setup');
		
		# remove cache
		$this->webspice->remove_cache('flexi');

		# verify file type
		if( $_FILES['image']['tmp_name'] ){
			$this->webspice->check_file_type(array('jpg','jpeg', 'png', 'gif'), 'image', $data, 'admin/flexi/flexi_service_setup');
		}

		# update process
		if( $input->service_id ){
			if($_FILES['image']['name']) {
				$sql = "
				UPDATE service_settings SET SERVICE_TYPE=?, SERVICE_NAME=?, SERVICE_CODE=?, PREFIX=?, MIN_AMOUNT=?, MAX_AMOUNT=?, LOGO=?, BULK_LIMIT=?, UPDATED_BY=?,UPDATED_DATE=?
				WHERE SERVICE_ID=?";

				$this->db->query($sql, array($input->service_type, $input->service_name, $input->service_code, $input->prefix, $input->min_amount, $input->max_amount, $_FILES['image']['name'], $input->bulk_limit, $this->webspice->get_user_id(), $this->webspice->now(), $input->service_id));

				// image processing
				$this->webspice->process_image_single('image', $_FILES['image']['name'], 'service_full');
			}
			else {
				$sql = "
				UPDATE service_settings SET SERVICE_TYPE=?, SERVICE_NAME=?, SERVICE_CODE=?, PREFIX=?, MIN_AMOUNT=?, MAX_AMOUNT=?, BULK_LIMIT=?, UPDATED_BY=?,UPDATED_DATE=?
				WHERE SERVICE_ID=?";

				$this->db->query($sql, array($input->service_type, $input->service_name, $input->service_code, $input->prefix, $input->min_amount, $input->max_amount, $input->bulk_limit, $this->webspice->get_user_id(), $this->webspice->now(), $input->service_id));
			}
			$this->webspice->message_board('Record has been updated!');
			$this->webspice->log_me('product_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_flexi_service_setup');
			return false;
		}
		
		#insert data
		if($_FILES['image']['name']) {
			$sql = "
			INSERT INTO service_settings
			(SERVICE_TYPE, SERVICE_NAME, SERVICE_CODE, PREFIX, MIN_AMOUNT, MAX_AMOUNT, LOGO, BULK_LIMIT, CREATED_BY, CREATED_DATE, STATUS)
			VALUES
			( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
			$this->db->query($sql, array($input->service_type, $input->service_name, $input->service_code, $input->prefix, $input->min_amount, $input->max_amount, $_FILES['image']['name'], $input->bulk_limit, $this->webspice->get_user_id(), $this->webspice->now()));
			$this->webspice->process_image_single('image', $_FILES['image']['name'], 'service_full');
		}
		else {
			$sql = "
			INSERT INTO service_settings
			(SERVICE_TYPE, SERVICE_NAME, SERVICE_CODE, PREFIX, MIN_AMOUNT, MAX_AMOUNT, BULK_LIMIT, CREATED_BY, CREATED_DATE, STATUS)
			VALUES
			( ?, ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
			$this->db->query($sql, array($input->service_type, $input->service_name, $input->service_code, $input->prefix, $input->min_amount, $input->max_amount, $input->bulk_limit, $this->webspice->get_user_id(), $this->webspice->now()));
		}
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Service data inserted successfully!');
		if($this->webspice->permission_verify('manage_flexi_service_setup',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_flexi_service_setup');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'flexi_service_setup');

	}

	public function manage_flexi_service_setup() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_flexi_service_setup');
		$this->webspice->permission_verify('manage_flexi_service_setup');
		$this->load->database();
		$orderby = 'ORDER BY service_settings.SERVICE_ID DESC';
		$groupby = null;
		$where = ' WHERE service_settings.SERVICE_TYPE="flexi_service" ';
		$page_index = 0;
		$no_of_record = 20000000;
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
		SELECT  * FROM service_settings ";


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
				$this->webspice->action_executer($TableName='service_settings', $KeyField='SERVICE_ID', $key, $RedirectURL='manage_flexi_service_setup', $PermissionName='manage_flexi_service_setup', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='flexi', $Log='inactive_flexi_service');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='service_settings', $KeyField='SERVICE_ID', $key, $RedirectURL='manage_flexi_service_setup', $PermissionName='manage_flexi_service_setup', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='flexi', $Log='active_flexi_service');
				return false;
				break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$image_name = $this->db->query("SELECT LOGO FROM service_settings WHERE SERVICE_ID=".$id)->row()->LOGO;
				// dd($image_name);
				$sql = $this->db->query("DELETE FROM service_settings WHERE SERVICE_ID='".$id."' LIMIT 1");
				if(!unlink($this->webspice->get_path('service_full').$image_name)) {
					die($this->webspice->get_path('service_full').$image_name);
				}
				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_flexi_service_setup');
				}
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_flexi_service_setup/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin/flexi/manage_flexi_service_setup', $data);

	}

	public function flexi_send_money($data=null) {

		$data['confirm_page'] = false;
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'flexi_send_money');
		$this->webspice->permission_verify('flexi_send_money');

		/*
		 * Remove the edit portion
		*/

		$this->load->library('form_validation');
		$this->form_validation->set_rules('number', 'mobile number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('amount', 'amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('service_name', 'service name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('flexi_type', 'flexi type', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/flexi/flexi_send_money', $data);
			return FALSE;
		}

		# get input post
		$my_str = null;
		$errors = array();
		$input = $this->webspice->get_input('flexi_trans_id');
		// dd($input);
		$number_chk = substr($input->number, 0, 1);
		if($number_chk == "+") {
			$errors[] = "+88 do not support, please provide just phone number";
		}
		$prefix_chk = substr($input->number, 0, 3);
		if(!$this->webspice->service_check($prefix_chk, $input->service_name)) {
			$errors[] = "Service code didn't match.";
		}

		if(!is_numeric($input->number) || !is_numeric($input->amount)) {
			$errors[] = "Number & Amount field must be numeric";
		}
		
		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view('admin_new/flexi/flexi_send_money', $data);
			return false;
		}
		$number = $input->number;
		$amount = $input->amount;
		$service_id = $input->service_name;
		$flexi_type = $input->flexi_type;

		$my_str = $number . "|" . $amount . "|" . $service_id . "|" . $flexi_type;
		$my_str = $this->webspice->encrypt_decrypt($my_str, 'encrypt');

		$this->webspice->message_board('Please insert your pin to confirm action');
		if($this->webspice->permission_verify('view_flexi_send_money',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'view_flexi_send_money/confirm/'.$my_str);
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'confirm_send_money/confirm/'.$my_str);
	}

	public function view_flexi_send_money() {

		$user_sess_id = $this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt');
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'view_flexi_send_money');
		$this->webspice->permission_verify('view_flexi_send_money');
		$this->load->database();
		$orderby = 'ORDER BY address_book_new.SRV_ID DESC';
		$groupby = null;
		$where = null;
		if(!$this->webspice->admin_verify()) {
			$where = ' WHERE USR_NM="'.$user_sess_id.'" ';
		}
		$page_index = 0;
		$no_of_record = 200000000;
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
		SELECT  * FROM address_book_new ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'flexi_trans',
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

			case 'confirm':

				/*
				 * permission check
				 * service permission check
				 * balance check
				 * rate mod check & collect data
				*/

				// permission check
				$this->webspice->permission_verify('flexi_send_money');

				// init variable
				$errors = array();
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$id = explode("|", $id);
				$data['number'] = $id[0];
				$data['amount'] = $id[1];
				$data['service_id'] = $id[2];
				$service_data = $this->db->query("SELECT SERVICE_NAME, SERVICE_CODE FROM service_settings WHERE SERVICE_ID='".$data['service_id']."'")->row();
				$data['service_name'] = $service_data->SERVICE_NAME;
				$data['service_code'] = $service_data->SERVICE_CODE;
				$data['type'] = $id[3];
				$cost = null;

				// service permisison check
				$service = strtolower(str_replace(" ", "_", $data['service_name']));
				$user_id = $this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt');
				$this->webspice->service_permisison_check($service, $user_id);

				// balance check
				$reseller_id = $this->webspice->get_reseller_id($user_id);
				$balance = $this->webspice->get_reseller_cur_balance($reseller_id);
				// dd($balance);
				if(!$this->webspice->admin_verify() && $data['amount'] > $balance) {
					$this->webspice->message_board('Insufficient balance, contact with your reseller');
					$this->webspice->force_redirect($url_prefix . 'flexi_send_money');
					return false;
				}

				// rate mod check & collect data
				// dd($reseller_id);
				if($reseller_id) {
					$rate_data = $this->webspice->rate_mod_data($data['service_id'], $reseller_id);
					if(count($rate_data)) {
						$comission = ($rate_data->COMISSION * $data['amount'])/100;
						$charge = ($rate_data->CHARGE * $data['amount'])/100;

						$comission_amt = $comission - $charge;
						$cost = $data['amount'] - $comission_amt;
					}
					else {
						$cost = $data['amount'];
					}
				}
				else if ($this->webspice->admin_verify()) {
					$cost = $data['amount'];
				}
				else {
					$cost = $data['amount'];
				}


				// dd($service);
				
				$this->load->library('form_validation');
				$this->form_validation->set_rules('pin_number', 'pin number', 'required|trim|xss_clean');
				
				if( !$this->form_validation->run() ){
					$this->load->view('admin_new/flexi/confirm_send_money', $data);
					return FALSE;
				}

				# get input post
				$input = $this->webspice->get_input('flexi_trans_id');
				$pin_number = $input->pin_number;

				// verify pin number
				if(!$this->webspice->pin_verification($data['service_id'], $reseller_id, $pin_number)) {
					$errors[] = "Your pin didn't match.";
				}

				if(count($errors)) {
					// dd($errors);
					$data['errors'] = $errors;
					$this->load->view("admin_new/flexi/confirm_send_money", $data);
					return false;
				}

				// dd("Hello");

				// additional var setup
				if($this->webspice->admin_verify()) {
					$parent_id = 1;
				}
				else {
					$parent_id = $this->webspice->get_perent_id($reseller_id);
				}
				$ip = $this->webspice->get_ip_address();
				$last_balance = $this->webspice->get_reseller_cur_balance($reseller_id);
				$updated_balance = $last_balance - $cost;
				$user_email = $this->webspice->enc($_SESSION['user']['USER_EMAIL'], 'decrypt');
				$trans_type = "flexi_service";

				$sql = "
				INSERT INTO address_book_new
				(SRV_ID, TU_DI, SLD, CST, TRX_TY, TRX_STS, USR_NM, RSL_NM, PRN_NM, IPR, LST_SLD, PB_CD, STAMP, USR_TXT)
				VALUES
				( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
				$this->db->query($sql, array(
					$data['service_id'],
					$data['number'],
					$data['amount'],
					$cost,
					$data['type'],
					0,
					$user_id,
					$reseller_id,
					$parent_id,
					$ip,
					$last_balance,
					$data['service_code'],
					$this->webspice->now(),
					$user_email
				));
			
				if( !$this->db->insert_id() ){
					$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
					$this->webspice->force_redirect($url_prefix . 'admin');
					return false;
				}

				// update balance data
				if(!$this->webspice->admin_verify()) {
					$sql2 = "
					INSERT INTO reseller_trans
					(USER_ID, RESELLER_ID, AMOUNT, BALANCE, TRANS_TYPE, SEND_DATE)
					VALUES
					( ?, ?, ?, ?, ?, ?)";
					$this->db->query($sql2, array($user_id, $reseller_id, $cost, $updated_balance, $trans_type, $this->webspice->now()));
				}

				$this->webspice->message_board('Money send successfully!');
				if($this->webspice->permission_verify('view_flexi_send_money',TRUE)){
					$this->webspice->force_redirect($url_prefix . 'view_flexi_send_money');
					return FALSE;
				}
				$this->webspice->force_redirect($url_prefix.'flexi_send_money');

				return false;

			break;


			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$image_name = $this->db->query("SELECT LOGO FROM service_settings WHERE SERVICE_ID=".$id)->row()->LOGO;
				// dd($image_name);
				$sql = $this->db->query("DELETE FROM service_settings WHERE SERVICE_ID='".$id."' LIMIT 1");
				if(!unlink($this->webspice->get_path('service_full').$image_name)) {
					die($this->webspice->get_path('service_full').$image_name);
				}
				if($sql) {
					$this->webspice->force_redirect($url_prefix.'view_flexi_send_money');
				}
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'view_flexi_send_money/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/flexi/view_flexi_send_money', $data);

	}

	public function flexi_bulk_upload() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'flexi_bulk_upload');
		$this->webspice->permission_verify('flexi_bulk_upload');
		$data = array();
		$errors = array();


		if( !$_FILES || !$_FILES['upload_data_file']['tmp_name'] ){
			$this->load->view('admin_new/flexi/flexi_bulk_upload', $data);
			return FALSE;
		}

		if(empty($this->input->post('pin_number'))) {
			$errors[] = "PIN is required. Provide pin to get transaction";
		}
		$pin = $this->input->post('pin_number');

		if(!$this->webspice->reseller_pin_verify($this->webspice->enc($_SESSION['user']['RESELLER_ID'], 'decrypt'), $pin)) {
			$errors[] = "Pin didn't match. Plesae enter correct pin to transaction";
		}

		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view("admin_new/flexi/flexi_bulk_upload", $data);
			return false;
		}

		# get input post
		$input = $this->webspice->get_input('bulk_id');

		if( $_FILES['upload_data_file'] ) {

			$file_name = $_FILES['upload_data_file']['name'];
			$chk_ext = explode(".", $file_name);
			if( (strtolower(end($chk_ext)) !== "csv") ) {
				/*$this->webspice->message_board("Your file type must be in csv format");
				$this->load->view('admin_new/flexi/flexi_bulk_upload', $data);
				return false;*/
				$errors[] = "Your file type must be in csv format";
				$data['errors'] = $errors;
				$this->load->view("admin_new/flexi/flexi_bulk_upload", $data);
				return false;
				
			}
			
			$fname = $_FILES['upload_data_file']['tmp_name'];
			$handle = fopen($fname, "r");
			$my_data = array();
			while ( ($up_data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
				if(strtolower($up_data[4]) == "ok") {
					$my_data[] = $up_data;
				}
			}

			$user_id = $this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt');
			$reseller_id = $this->webspice->get_reseller_id($user_id);
			$amount_val = array();
			$cost_val = array();
			for($i=0; $i<count($my_data); $i++) {
				$amount_val[] = $my_data[$i][1];
				
				// rate mod check & collect data
				if($reseller_id) {
					$rate_data = $this->webspice->rate_mod_data($this->webspice->get_service_id($my_data[$i][2]), $reseller_id);
					if(count($rate_data)) {
						$comission = ($rate_data->COMISSION * $my_data[$i][1])/100;
						$charge = ($rate_data->CHARGE * $my_data[$i][1])/100;

						$comission_amt = $comission - $charge;
						$cost_val[] = $my_data[$i][1] - $comission_amt;
					}
					else {
						$cost_val[] = $my_data[$i][1];
					}
				}
				else if ($this->webspice->admin_verify()) {
					$cost_val[] = $my_data[$i][1];
				}
				else {
					$cost_val[] = $my_data[$i][1];
				}
			}
			/*dd(array_sum($cost_val), true);
			dd(array_sum($amount_val), true);
			dd("Hallo");*/
			fclose($handle);
			$cost_val = array_sum($cost_val);
			$amount_val = array_sum($amount_val);
			$balance = $this->webspice->get_reseller_cur_balance($reseller_id);
			$ip = $this->webspice->get_ip_address();
			if($this->webspice->admin_verify()) {
				$parent_id = 1;

			}
			else {
				$parent_id = $this->webspice->get_perent_id($reseller_id);
			}

			if(!$this->webspice->admin_verify() && $amount_val > $balance) {
				$errors[] = "Insufficient balance, contact with your reseller";
				$data['errors'] = $errors;
				$this->load->view("admin_new/flexi/flexi_bulk_upload", $data);
				return false;
			}
			$last_balance = $balance;
			$updated_balance = $last_balance - $cost_val;
			$user_email = $this->webspice->enc($_SESSION['user']['USER_EMAIL'], 'decrypt');
			$trans_type = "flexi_service";

			#insert csv data
			$sql = "
			INSERT INTO address_book_new
			(SRV_ID, TU_DI, SLD, CST, TRX_TY, TRX_STS, USR_NM, RSL_NM, PRN_NM, IPR, LST_SLD, PB_CD, STAMP, USR_TXT)
			VALUES
			( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
			for($j=0; $j<count($my_data); $j++) {
				// dd($this->webspice->servive_wise_cost($reseller_id, $my_data[$j][1], $this->webspice->get_service_id($my_data[$j][2])));
				// dd($last_balance);
				$number = 0 . $my_data[$j][0];

				$this->db->query($sql, array(
					$this->webspice->get_service_id($my_data[$j][2]),
					$number,
					$my_data[$j][1],
					$this->webspice->servive_wise_cost($reseller_id, $my_data[$j][1], $this->webspice->get_service_id($my_data[$j][2])),
					$my_data[$j][3],
					0,
					$user_id,
					$reseller_id,
					$parent_id,
					$ip,
					$last_balance,
					$my_data[$j][2],
					$this->webspice->now(),
					$user_email
				));
			}
		
			if( !$this->db->insert_id() ){
				$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
				$this->webspice->force_redirect($url_prefix . 'admin');
				return false;
			}

			// update balance data
			if(!$this->webspice->admin_verify()) {
				$sql2 = "
				INSERT INTO reseller_trans
				(USER_ID, RESELLER_ID, AMOUNT, BALANCE, TRANS_TYPE, SEND_DATE)
				VALUES
				( ?, ?, ?, ?, ?, ?)";
				$this->db->query($sql2, array($user_id, $reseller_id, $cost_val, $updated_balance, $trans_type, $this->webspice->now()));
			}

			$this->webspice->message_board('Money send successfully!');
			if($this->webspice->permission_verify('view_flexi_send_money',TRUE)){
				$this->webspice->force_redirect($url_prefix . 'view_flexi_send_money');
				return FALSE;
			}
			$this->webspice->force_redirect($url_prefix.'flexi_send_money');

			return false;


		}
		


	}


}