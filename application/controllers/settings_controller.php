<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings_controller extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
	}

	public function create_pin($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_pin');
		$this->webspice->permission_verify('create_pin');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'PIN_ID'=>null,
				'SERVICE_ID'=>null,
				'PIN'=>null,
				'PIN_EXPIRE_DATE'=>null
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('service_id', 'service name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('pin', 'pin', 'required|trim|xss_clean');
		$this->form_validation->set_rules('confirm_pin', 'confirm pin', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/create_pin', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('pin_id');
		$user_id = $this->webspice->get_user_id();
		$reseller_id = $this->webspice->get_reseller_id($user_id);
		$pin_expire_date = date("Y-m-d", strtotime("+30 days"));
		$pin = $this->webspice->enc($input->pin, 'encrypt');

		// data initialization & checking
		$errors = array();
		if( ($input->pin !== $input->confirm_pin) ) {
			$errors[] = "Your pin didn't match.";
		}

		if(count($errors)) {
			// dd($errors);
			$data['errors'] = $errors;
			$this->load->view("admin_new/settings/create_pin", $data);
			return false;
		}
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM user_pin WHERE SERVICE_ID=? AND RESELLER_ID=?", array($input->service_id, $reseller_id), 'You already setup pin for this service', 'PIN_ID', $input->pin_id, $data, 'admin_new/settings/create_pin');
		
		# remove cache
		$this->webspice->remove_cache('pin');

		# update process
		if( $input->pin_id ){

			$sql = "
			UPDATE user_pin SET SERVICE_ID=?, PIN=?, PIN_EXPIRE_DATE=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE PIN_ID=?";

			$this->db->query($sql, array($input->service_id, $pin, $pin_expire_date, $this->webspice->get_user_id(), $this->webspice->now(), $input->pin_id));

			$this->webspice->message_board('Pin has been updated!');
			$this->webspice->log_me('pin_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_pin');
			return false;
		}
		
		#insert data
		$sql = "
		INSERT INTO user_pin
		(SERVICE_ID, RESELLER_ID, USER_ID, PIN, PIN_EXPIRE_DATE, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($input->service_id, $reseller_id, $user_id, $pin, $pin_expire_date, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Service pin created successfully');
		if($this->webspice->permission_verify('manage_pin',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_pin');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_pin');
	
	}

	public function manage_pin() {

		$user_sess_id = $this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt');
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_pin');
		$this->webspice->permission_verify('manage_pin');
		$this->load->database();
		$orderby = 'ORDER BY user_pin.PIN_ID DESC';
		$groupby = null;
		$where = '';
		if(!$this->webspice->admin_verify()) {
			$where = ' WHERE USER_ID="'.$user_sess_id.'" ';
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
		SELECT  * FROM user_pin ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'user_pin',
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
				$this->webspice->edit_generator($TableName='user_pin', $KeyField='PIN_ID', $key, $RedirectController='settings_controller', $RedirectFunction='create_pin', $PermissionName='manage_pin', $StatusCheck=null, $Log='edit_pin');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_pin/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/manage_pin', $data);

	}

	public function pin_setup($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'pin_setup');
		$this->webspice->permission_verify('pin_setup');
		$pin_data = $this->db->query("SELECT * FROM user_pin WHERE USER_ID='".$this->webspice->get_user_id()."'")->row();

		/*if( !count($pin_data) ){
			$data['edit'] = array(
				'PIN_ID'=>null,
				'SERVICE_ID'=>null,
				'PIN'=>null
			);
		}
		else {
			$data['edit'] = array(
				'PIN_ID'=>$pin_data->PIN_ID,
				'PIN'=>$pin_data->PIN
			);
		}*/

		$this->load->library('form_validation');
		$this->form_validation->set_rules('old_pin', 'old pin', 'trim|xss_clean');
		if(count($pin_data)) {
			$this->form_validation->set_rules('old_pin', 'old pin', 'required|trim|xss_clean');
		}
		$this->form_validation->set_rules('pin', 'pin', 'required|trim|xss_clean');
		$this->form_validation->set_rules('confirm_pin', 'confirm pin', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/create_pin', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('pin_id');
		$user_id = $this->webspice->get_user_id();
		$pin_expire_date = date("Y-m-d", strtotime("+30 days"));
		$pin = $this->webspice->enc($input->pin, 'encrypt');

		// data initialization & checking
		$errors = array();
		if(count($pin_data)) {
			if(!$this->webspice->user_pin_verify($user_id, $input->old_pin)) {
				$errors[] = "Your current pin didn't match. ";
			}
		}

		if( ($input->pin !== $input->confirm_pin) ) {
			$errors[] = "Your pin & confirm didn't match.";
		}

		if(count($errors)) {
			// dd($errors);
			$data['errors'] = $errors;
			$this->load->view("admin_new/settings/create_pin", $data);
			return false;
		}
		# remove cache
		$this->webspice->remove_cache('pin');

		# update process
		if( count($pin_data) ){

			$sql = "
			UPDATE user_pin SET  PIN=?, PIN_EXPIRE_DATE=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE PIN_ID=?";

			$this->db->query($sql, array($pin, $pin_expire_date, $this->webspice->get_user_id(), $this->webspice->now(), $pin_data->PIN_ID));

			$this->webspice->message_board('Pin has been updated!');
			$this->webspice->log_me('pin_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'pin_setup');
			return false;
		}
		
		#insert data
		$sql = "
		INSERT INTO user_pin
		(USER_ID, PIN, PIN_EXPIRE_DATE, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($user_id, $pin, $pin_expire_date, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Service pin created successfully');
		if($this->webspice->permission_verify('pin_setup',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'pin_setup');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'pin_setup');
	
	}

	public function create_service($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_service');
		$this->webspice->permission_verify('create_service');
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
		$this->form_validation->set_rules('service_type', 'service type', 'required|trim|xss_clean');
		$this->form_validation->set_rules('service_code', 'service code', 'required|trim|xss_clean');
		$this->form_validation->set_rules('prefix', 'prefix', 'trim|xss_clean');
		$this->form_validation->set_rules('min_amount', 'min amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('max_amount', 'max amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('bulk_limit', 'bulk limit', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/create_service', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('service_id');
		// $input->service_type = "flexi_service";
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM service_settings WHERE SERVICE_NAME=? AND SERVICE_TYPE=?", array($input->service_name, $input->service_type), 'You are not allowed to enter duplicate service', 'SERVICE_ID', $input->service_id, $data, 'admin_new/settings/create_service');
		
		# remove cache
		$this->webspice->remove_cache('service');

		# verify file type
		if( $_FILES['image']['tmp_name'] ){
			$this->webspice->check_file_type(array('jpg','jpeg', 'png', 'gif'), 'image', $data, 'admin_new/settings/create_service');
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
			$this->webspice->force_redirect($url_prefix.'manage_service');
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
		if($this->webspice->permission_verify('manage_service',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_service');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_service');

	}

	public function initial_settings($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'initial_settings');
		$this->webspice->permission_verify('initial_settings');
		$settings = $this->db->query("SELECT * FROM settings WHERE SETTINGS_ID=1")->row();

		if( !count($settings) ){
			$data['edit'] = array(
				'SETTINGS_ID'=>null,
				'LEVEL_ONE'=>null,
				'LEVEL_TWO'=>null,
				'LEVEL_THREE'=>null,
				'LEVEL_FOUR'=>null,
				'LEVEL_FIVE'=>null,
				'LEVEL_SIX'=>null,
				'LEVEL_SEVEN'=>null,
				'LEVEL_EIGHT'=>null,
				'LEVEL_NINE'=>null,
				'LEVEL_TEN'=>null,
				'LEVEL_ELEVEN'=>null,
				'LEVEL_TWELVE'=>null,
				'LEVEL_THIRTEEN'=>null,
				'LEVEL_FOURTEEN'=>null,
				'WITHDRAW_CHARGE'=>null,
				'USER_BALANCE_TRANSFER'=>null
			);
		}
		else {
			$data['edit'] = array(
				'SETTINGS_ID'=>$settings->SETTINGS_ID,
				'LEVEL_ONE'=>$settings->LEVEL_ONE,
				'LEVEL_TWO'=>$settings->LEVEL_TWO,
				'LEVEL_THREE'=>$settings->LEVEL_THREE,
				'LEVEL_FOUR'=>$settings->LEVEL_FOUR,
				'LEVEL_FIVE'=>$settings->LEVEL_FIVE,
				'LEVEL_SIX'=>$settings->LEVEL_SIX,
				'LEVEL_SEVEN'=>$settings->LEVEL_SEVEN,
				'LEVEL_EIGHT'=>$settings->LEVEL_EIGHT,
				'LEVEL_NINE'=>$settings->LEVEL_NINE,
				'LEVEL_TEN'=>$settings->LEVEL_TEN,
				'LEVEL_ELEVEN'=>$settings->LEVEL_ELEVEN,
				'LEVEL_TWELVE'=>$settings->LEVEL_TWELVE,
				'LEVEL_THIRTEEN'=>$settings->LEVEL_THIRTEEN,
				'LEVEL_FOURTEEN'=>$settings->LEVEL_FOURTEEN,
				'WITHDRAW_CHARGE'=>$settings->WITHDRAW_CHARGE,
				'USER_BALANCE_TRANSFER'=>$settings->USER_BALANCE_TRANSFER
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('level_one', 'level one', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_two', 'level two', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_three', 'level three', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_four', 'level four', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_five', 'level five', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_six', 'level six', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_seven', 'level seven', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_eight', 'level eight', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_nine', 'level nine', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_ten', 'level ten', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_eleven', 'level eleven', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_twelve', 'level twelve', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_thirteen', 'level thirteen', 'required|trim|xss_clean');
		$this->form_validation->set_rules('level_fourteen', 'level fourteen', 'required|trim|xss_clean');
		$this->form_validation->set_rules('withdraw_charge', 'withdraw charge', 'required|trim|xss_clean');
		$this->form_validation->set_rules('user_balance_transfer', 'user balance transfer', 'required|trim|xss_clean');	
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/initial_settings', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('settings_id');
		

		# update process
		if( $input->settings_id ){

			$sql = "
			UPDATE settings SET LEVEL_ONE=?, LEVEL_TWO=?, LEVEL_THREE=?, LEVEL_FOUR=?, LEVEL_FIVE=?, LEVEL_SIX=?, LEVEL_SEVEN=?, LEVEL_EIGHT=?, LEVEL_NINE=?, LEVEL_TEN=?, LEVEL_ELEVEN=?, LEVEL_TWELVE=?, LEVEL_THIRTEEN=?, LEVEL_FOURTEEN=?, WITHDRAW_CHARGE=?, USER_BALANCE_TRANSFER=?, UPDATED_BY=?, UPDATED_DATE=?
			WHERE SETTINGS_ID=?";

			$this->db->query($sql, array($input->level_one, $input->level_two, $input->level_three, $input->level_four, $input->level_five, $input->level_six, $input->level_seven, $input->level_eight, $input->level_nine, $input->level_ten, $input->level_eleven, $input->level_twelve, $input->level_thirteen, $input->level_fourteen, $input->withdraw_charge, $input->user_balance_transfer, $this->webspice->get_user_id(), $this->webspice->now(), $input->settings_id));

			$this->webspice->message_board('Record has been updated!');
			$this->webspice->force_redirect($url_prefix.'initial_settings');
			return false;
		}
		
		#insert data
		$sql = "
		INSERT INTO settings
		(LEVEL_ONE, LEVEL_TWO, LEVEL_THREE, LEVEL_FOUR, LEVEL_FIVE, LEVEL_SIX, LEVEL_SEVEN, LEVEL_EIGHT, LEVEL_NINE, LEVEL_TEN, LEVEL_ELEVEN, LEVEL_TWELVE, LEVEL_THIRTEEN, LEVEL_FOURTEEN, WITHDRAW_CHARGE, USER_BALANCE_TRANSFER, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($input->level_one, $input->level_two, $input->level_three, $input->level_four, $input->level_five, $input->level_six, $input->level_seven, $input->level_eight, $input->level_nine, $input->level_ten, $input->level_eleven, $input->level_twelve, $input->level_thirteen, $input->level_fourteen, $input->withdraw_charge, $input->user_balance_transfer, $this->webspice->get_user_id(), $this->webspice->now()));

		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Software settings inserted successfully!');
		if($this->webspice->permission_verify('initial_settings',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'initial_settings');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'initial_settings');

	}

	public function manage_service() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_service');
		$this->webspice->permission_verify('manage_service');
		$this->load->database();
		$orderby = 'ORDER BY service_settings.SERVICE_ID DESC';
		$groupby = null;
		$where = '';
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
				$this->webspice->edit_generator($TableName='service_settings', $KeyField='SERVICE_ID', $key, $RedirectController='settings_controller', $RedirectFunction='create_service', $PermissionName='manage_service', $StatusCheck=null, $Log='edit_service');
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
				$this->webspice->action_executer($TableName='service_settings', $KeyField='SERVICE_ID', $key, $RedirectURL='manage_service', $PermissionName='manage_service', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='service', $Log='inactive_service');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='service_settings', $KeyField='SERVICE_ID', $key, $RedirectURL='manage_service', $PermissionName='manage_service', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='flexi', $Log='active_service');
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
					$this->webspice->force_redirect($url_prefix.'manage_service');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_service/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/manage_service', $data);

	}

	// change password
	public function change_user_password() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'change_user_password');
		$this->webspice->permission_verify('change_user_password');
		$data = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('old_password','old password','required|trim|xss_clean');
		$this->form_validation->set_rules('new_password','new password','required|trim|xss_clean');
		$this->form_validation->set_rules('repeat_password','repeat password','required|trim|xss_clean');

		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/change_user_password', $data);
			return false;
		}

		# get input post
		$input = $this->webspice->get_input('id');

		// data initialization & checking
		$user_pass = $this->db->query("SELECT USER_PASSWORD FROM user WHERE USER_ID='".$this->webspice->get_user_id()."'")->row()->USER_PASSWORD;
		$errors = array();
		if( ($input->old_password !== $this->webspice->enc($user_pass, 'decrypt')) ) {
			$errors[] = "Invalid password number. Please enter correct password.";
		}

		if($input->new_password !== $input->repeat_password) {
			$errors[] = "New password & Repeat password didn't match";
		}

		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view("admin_new/settings/change_user_password", $data);
			return false;
		}

		$user_id = $this->webspice->get_user_id();
		$enc_pass = $this->webspice->encrypt_decrypt($input->new_password, 'encrypt');

		// dd($enc_pass);

		if(isset($user_id)) {
			$sql = "
			UPDATE user SET USER_PASSWORD=?, UPDATED_BY=?, UPDATED_DATE=?
			WHERE USER_ID=?";
			$this->db->query($sql, array($enc_pass,$this->webspice->get_user_id(),$this->webspice->now(), $user_id));
			# user session destroy
			session_destroy();
			session_start();
			
			$this->webspice->message_board('Your password has been changed! Please login using your new password.');
			$this->webspice->force_redirect($url_prefix.'login');
		}

	}

	public function add_ip_blocking($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'add_ip_blocking');
		$this->webspice->permission_verify('add_ip_blocking');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'IP_ID'=>null,
				'IP_ADDRESS'=>null,
				'EXPIRE_DATE'=>null
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('ip_address', 'ip address', 'required|trim|xss_clean');
		$this->form_validation->set_rules('expire_date', 'expire date', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/add_ip_blocking', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('ip_id');
		$expire_date = date("Y-m-d", strtotime($input->expire_date));
		$ip_address = $this->webspice->enc($input->ip_address, 'encrypt');
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM ip_denied WHERE IP_ADDRESS=?", array($this->webspice->enc($input->ip_address, 'encrypt')), 'You already block this ip', 'IP_ID', $input->ip_id, $data, 'admin_new/settings/add_ip_blocking');
		
		# remove cache
		$this->webspice->remove_cache('ip');

		# update process
		if( $input->ip_id ){

			$sql = "
			UPDATE ip_denied SET IP_ADDRESS=?, EXPIRE_DATE=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE IP_ID=?";

			$this->db->query($sql, array($ip_address, $expire_date, $this->webspice->get_user_id(), $this->webspice->now(), $input->ip_id));

			$this->webspice->message_board('IP has been updated!');
			$this->webspice->log_me('ip_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_ip_blocking');
			return false;
		}
		
		#insert data
		$sql = "
		INSERT INTO ip_denied
		(IP_ADDRESS, EXPIRE_DATE, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($ip_address, $expire_date, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('IP address blocked successfully');
		if($this->webspice->permission_verify('manage_ip_blocking',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_ip_blocking');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'add_ip_blocking');
	}

	public function manage_ip_blocking() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_ip_blocking');
		$this->webspice->permission_verify('manage_ip_blocking');
		$this->load->database();
		$orderby = 'ORDER BY ip_denied.IP_ID DESC';
		$groupby = null;
		$where = '';
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
		SELECT  * FROM ip_denied ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'user_pin',
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

			case 'inactive':
				$this->webspice->action_executer($TableName='ip_denied', $KeyField='IP_ID', $key, $RedirectURL='manage_ip_blocking', $PermissionName='manage_ip_blocking', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='ip', $Log='inactive_ip');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='ip_denied', $KeyField='IP_ID', $key, $RedirectURL='manage_ip_blocking', $PermissionName='manage_ip_blocking', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='ip', $Log='active_ip');
				return false;
				break;

			case 'edit':
				$this->webspice->edit_generator($TableName='ip_denied', $KeyField='IP_ID', $key, $RedirectController='settings_controller', $RedirectFunction='add_ip_blocking', $PermissionName='manage_ip_blocking', $StatusCheck=null, $Log='edit_ip');
				return false;
			break;


			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM ip_denied WHERE IP_ID='".$id."' LIMIT 1");

				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_ip_blocking');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_ip_blocking/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/manage_ip_blocking', $data);

	}

	/*********************************************
				for complain center
	*********************************************/
	public function add_new_complain($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'add_new_complain');
		$this->webspice->permission_verify('add_new_complain');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'COMPLAIN_ID'=>null,
				'USER_ID'=>null,
				'COMPLAIN_TO'=>null,
				'SUBJECT'=>null,
				'DESCRIPTION'=>null,
				'FEEDBACK'=>null,
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('complain_to', 'complain to', 'required|trim|xss_clean');
		$this->form_validation->set_rules('subject', 'subject', 'required|trim|xss_clean');
		$this->form_validation->set_rules('description', 'description', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/add_new_complain', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('complain_id');
		$user_id = $this->webspice->get_user_id();
		$complain_to = $input->complain_to;
		$subject = $input->subject;
		$description = $input->description;

		// data initialization & checking
		$errors = array();
		if( $this->webspice->admin_verify() ) {
			$errors[] = "Admin can not complain. This panel only for rseller or user";
		}

		if(count($errors)) {
			// dd($errors);
			$data['errors'] = $errors;
			$this->load->view("admin_new/settings/add_new_complain", $data);
			return false;
		}


		# update process
		/*if( $input->pin_id ){

			$sql = "
			UPDATE user_pin SET SERVICE_ID=?, PIN=?, PIN_EXPIRE_DATE=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE PIN_ID=?";

			$this->db->query($sql, array($input->service_id, $pin, $pin_expire_date, $this->webspice->get_user_id(), $this->webspice->now(), $input->pin_id));

			$this->webspice->message_board('Pin has been updated!');
			$this->webspice->log_me('pin_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_pin');
			return false;
		}*/
		
		#insert data
		$sql = "
		INSERT INTO complain
		(USER_ID, COMPLAIN_TO, SUBJECT, DESCRIPTION, CREATED_BY, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ?, ? )";
		$this->db->query($sql, array($user_id, $complain_to, $subject, $description, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Complain submitted successfully');
		if($this->webspice->permission_verify('manage_complain',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_complain');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'add_new_complain');
	}

	public function manage_complain() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_complain');
		$this->webspice->permission_verify('manage_complain');
		$this->load->database();
		$orderby = null;
		$groupby = null;
		$where = '';
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
		SELECT  * FROM complain ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'user_pin',
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
				$this->webspice->edit_generator($TableName='user_pin', $KeyField='PIN_ID', $key, $RedirectController='settings_controller', $RedirectFunction='create_pin', $PermissionName='manage_pin', $StatusCheck=null, $Log='edit_pin');
				return false;
			break;


			case 'reply':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_complain');
				$this->webspice->permission_verify('manage_complain');

				$this->load->library('form_validation');
				$this->form_validation->set_rules('feedback', 'feedback', 'required|trim|xss_clean');
				
				if( !$this->form_validation->run() ){
					$this->load->view('admin_new/settings/reply_feedback', $data);
					return FALSE;
				}

				# get input post
				$input = $this->webspice->get_input('complain_id');
				$feedback = $input->feedback;

				# update process

				$sql = "
				UPDATE complain SET FEEDBACK=?, FEEDBACK_DATE=?	WHERE COMPLAIN_ID=?";

				$this->db->query($sql, array($feedback, $this->webspice->now(), $id));

				$this->webspice->message_board('Successfully replied to the user problem');
				$this->webspice->force_redirect($url_prefix.'manage_complain');
				return false;

			break;


			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				// dd($image_name);
				$sql = $this->db->query("DELETE FROM complain WHERE COMPLAIN_ID='".$id."' LIMIT 1");

				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_complain');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_complain/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/manage_complain', $data);

	}

	public function my_complains() {
		$data = array();
		$user_id = $this->webspice->get_user_id();

		$data['get_record'] = $this->db->query("SELECT * FROM complain WHERE CREATED_BY='".$user_id."'")->result();
		$this->load->view("admin_new/settings/my_complains", $data);
		return false;
	}


	/*********************************************
				for message center
	*********************************************/
	public function send_message($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'send_message');
		$this->webspice->permission_verify('send_message');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'MSG_ID'=>null,
				'RESELLER_USER_ID'=>null,
				'PARENT_USER_ID'=>null,
				'RECEIVER_ID'=>null,
				'MESSAGE'=>null,
				'REPLY'=>null
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean');
		$this->form_validation->set_rules('message', 'message', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/send_message', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('msg_id');

		// data initialization & checking
		$errors = array();
		$email = $input->email;
		$receiver_id = $this->db->query("SELECT USER_ID FROM user WHERE USER_EMAIL='".$email."'")->row();
		if(count($receiver_id)) {
			$receiver_id = $receiver_id->USER_ID;
		}
		else {
			$errors[] = "Invalid email address. Pleasem write a valid email to send message.";
		}

		if(count($errors)) {
			// dd($errors);
			$data['errors'] = $errors;
			$this->load->view("admin_new/settings/send_message", $data);
			return false;
		}

		# init data
		$user_id = $this->webspice->get_user_id();
		$message = $input->message;


		# update process
		/*if( $input->pin_id ){

			$sql = "
			UPDATE user_pin SET SERVICE_ID=?, PIN=?, PIN_EXPIRE_DATE=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE PIN_ID=?";

			$this->db->query($sql, array($input->service_id, $pin, $pin_expire_date, $this->webspice->get_user_id(), $this->webspice->now(), $input->pin_id));

			$this->webspice->message_board('Pin has been updated!');
			$this->webspice->log_me('pin_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_pin');
			return false;
		}*/
		
		#insert data
		$sql = "
		INSERT INTO messaging
		(USER_ID, RECEIVER_ID, MESSAGE, CREATED_BY, CREATED_DATE)
		VALUES
		( ?, ?, ?, ?, ? )";
		$this->db->query($sql, array($user_id, $receiver_id, $message, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Message send successfully');
		if($this->webspice->permission_verify('my_outbox',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'my_outbox');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'send_message');
	}

	public function my_inbox() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'my_inbox');
		$this->webspice->permission_verify('my_inbox');
		$this->load->database();
		$orderby = null;
		$groupby = null;
		$where = ' WHERE RECEIVER_ID="'.$this->webspice->get_user_id().'" ';
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
		SELECT  * FROM messaging ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'user_pin',
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
				$this->webspice->edit_generator($TableName='user_pin', $KeyField='PIN_ID', $key, $RedirectController='settings_controller', $RedirectFunction='create_pin', $PermissionName='manage_pin', $StatusCheck=null, $Log='edit_pin');
				return false;
			break;


			case 'reply':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$this->webspice->user_verify($url_prefix.'login', $url_prefix.'my_inbox');
				$this->webspice->permission_verify('my_inbox');

				$this->load->library('form_validation');
				$this->form_validation->set_rules('reply', 'reply', 'required|trim|xss_clean');
				
				if( !$this->form_validation->run() ){
					$this->load->view('admin_new/settings/reply_message', $data);
					return FALSE;
				}

				# get input post
				$input = $this->webspice->get_input('msg_id');
				$reply = $input->reply;

				# update process

				$sql = "
				UPDATE messaging SET REPLY=?, REPLY_DATE=?	WHERE MSG_ID=?";

				$this->db->query($sql, array($reply, $this->webspice->now(), $id));

				$this->webspice->message_board('Successfully replied to the user message');
				$this->webspice->force_redirect($url_prefix.'my_inbox');
				return false;

			break;


			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				// dd($image_name);
				$sql = $this->db->query("DELETE FROM messaging WHERE MSG_ID='".$id."' LIMIT 1");

				if($sql) {
					$this->webspice->force_redirect($url_prefix.'my_inbox');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'my_inbox/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/my_inbox', $data);

	}

	public function my_outbox() {
		$data = array();
		$user_id = $this->webspice->get_user_id();

		$data['get_record'] = $this->db->query("SELECT * FROM messaging WHERE CREATED_BY='".$user_id."'")->result();
		$this->load->view("admin_new/settings/my_outbox", $data);
		return false;
	}

	public function my_referral_user() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'my_referral_user');
		$this->webspice->permission_verify('my_referral_user');
		$data = array();
		$user_id = $this->webspice->get_user_id();
		$my_id = $this->webspice->my_id_via_user_id($user_id);
		$data['get_record'] = $this->db->query("SELECT * FROM user_registration WHERE REFFER_ID='".$my_id."'")->result();

		$this->load->view('admin_new/settings/my_referral_user', $data);

	}

	public function my_tree() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'my_tree');
		$this->webspice->permission_verify('my_tree');
		$data = array();
		$user_id = $this->webspice->get_user_id();
		$my_id = $this->webspice->my_id_via_user_id($user_id);
		$data['level_1'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_1='".$user_id."'")->result();
		$data['level_2'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_2='".$user_id."'")->result();
		$data['level_3'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_3='".$user_id."'")->result();
		$data['level_4'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_4='".$user_id."'")->result();
		$data['level_5'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_5='".$user_id."'")->result();
		$data['level_6'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_6='".$user_id."'")->result();
		$data['level_7'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_7='".$user_id."'")->result();
		$data['level_8'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_8='".$user_id."'")->result();
		$data['level_9'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_9='".$user_id."'")->result();
		$data['level_10'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_10='".$user_id."'")->result();
		$data['level_11'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_11='".$user_id."'")->result();
		$data['level_12'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_12='".$user_id."'")->result();
		$data['level_13'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_13='".$user_id."'")->result();
		$data['level_14'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_14='".$user_id."'")->result();
		$this->load->view('admin_new/settings/my_tree', $data);

	}

	public function my_referral() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'my_referral');
		// $this->webspice->permission_verify('my_referral');

		$criteria = $this->uri->segment(2);
		$key = $this->uri->segment(3);

		# action area
		switch ($criteria) {

			case 'level_1':
				$user_id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$data = array();
				
				$data['get_record'] = $this->db->query("SELECT * FROM user_registration WHERE LEVEL_1='".$user_id."'")->result();
				dd($data);

				$this->load->view('admin_new/settings/view_my_referral_user', $data);
				
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'my_inbox/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/my_inbox', $data);

	}

	public function my_profile($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'my_profile');
		$this->webspice->permission_verify('my_profile');
		$user_id = $this->webspice->get_user_id();
		$my_id = $this->webspice->my_id_via_user_id($user_id);
		$profile = $this->db->query("SELECT * FROM user_registration WHERE MY_ID='".$my_id."'")->row();


		if( !count($profile) ){
			$data['edit'] = array(
				'USER_REG_ID'=>null,
				'FIRST_NAME'=>null,
				'LAST_NAME'=>null,
				'EMAIL'=>null,
				'ADDRESS'=>null,
				'MOBILE'=>null,
				'MY_ID'=>null,
				'REFFER_ID'=>null,
				'NATIONAL_ID'=>null,
				'COUNTRY'=>null,
				'NID_FILE'=>null,
				'IMAGE'=>null
			);
		}
		else {
			$data['edit'] = array(
				'USER_REG_ID'=>$profile->USER_REG_ID,
				'FIRST_NAME'=>$profile->FIRST_NAME,
				'LAST_NAME'=>$profile->LAST_NAME,
				'EMAIL'=>$profile->EMAIL,
				'ADDRESS'=>$profile->ADDRESS,
				'MOBILE'=>$profile->MOBILE,
				'MY_ID'=>$profile->MY_ID,
				'REFFER_ID'=>$profile->REFFER_ID,
				'NATIONAL_ID'=>$profile->NATIONAL_ID,
				'COUNTRY'=>$profile->COUNTRY,
				'NID_FILE'=>$profile->NID_FILE,
				'IMAGE'=>$profile->IMAGE
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'first name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('last_name', 'last name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('address', 'address', 'trim|xss_clean');
		$this->form_validation->set_rules('mobile', 'mobile', 'required|trim|xss_clean');
		$this->form_validation->set_rules('national_id', 'national id', 'trim|xss_clean');
		$this->form_validation->set_rules('country', 'country', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/my_profile', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('user_reg_id');
		// dd($input);

		# verify file type
		if( $_FILES['image']['tmp_name'] && $_FILES['nid_file']['tmp_name'] ){
			$this->webspice->check_file_type(array('jpg','jpeg', 'png', 'gif'), 'image', $data, 'admin_new/settings/my_profile');
			$this->webspice->check_file_type(array('jpg','jpeg', 'png', 'gif'), 'nid_file', $data, 'admin_new/settings/my_profile');
		}
		else if( $_FILES['image']['tmp_name'] ) {
			$this->webspice->check_file_type(array('jpg','jpeg', 'png', 'gif'), 'image', $data, 'admin_new/settings/my_profile');
		}
		else if( $_FILES['nid_file']['tmp_name'] ) {
			$this->webspice->check_file_type(array('jpg','jpeg', 'png', 'gif'), 'nid_file', $data, 'admin_new/settings/my_profile');
		}
		

		# update process
		if( $input->user_reg_id ){

			if($_FILES['image']['name'] && $_FILES['nid_file']['name']) {
				$sql = "
				UPDATE user_registration SET FIRST_NAME=?, LAST_NAME=?, ADDRESS=?, MOBILE=?, NATIONAL_ID=?, COUNTRY=?, NID_FILE=?, IMAGE=?, UPDATED_BY=?, UPDATED_DATE=?
				WHERE USER_REG_ID=?";

				$this->db->query($sql, array($input->first_name, $input->last_name, $input->address, $input->mobile, $input->national_id, $input->country, $_FILES['nid_file']['name'], $_FILES['image']['name'], $this->webspice->get_user_id(), $this->webspice->now(), $input->user_reg_id));

				// upload image
				$this->webspice->process_image_single('image', $_FILES['image']['name'], 'profile_picture_full');
				$this->webspice->process_image_single('nid_file', $_FILES['nid_file']['name'], 'nid_file_full');
			}
			else if($_FILES['image']['name']) {
				$sql = "
				UPDATE user_registration SET FIRST_NAME=?, LAST_NAME=?, ADDRESS=?, MOBILE=?, NATIONAL_ID=?, COUNTRY=?, IMAGE=?, UPDATED_BY=?, UPDATED_DATE=?
				WHERE USER_REG_ID=?";

				$this->db->query($sql, array($input->first_name, $input->last_name, $input->address, $input->mobile, $input->national_id, $input->country, $_FILES['image']['name'], $this->webspice->get_user_id(), $this->webspice->now(), $input->user_reg_id));

				// upload image
				$this->webspice->process_image_single('image', $_FILES['image']['name'], 'profile_picture_full');
			}
			else if($_FILES['nid_file']['name']) {
				$sql = "
				UPDATE user_registration SET FIRST_NAME=?, LAST_NAME=?, ADDRESS=?, MOBILE=?, NATIONAL_ID=?, COUNTRY=?, NID_FILE=?, UPDATED_BY=?, UPDATED_DATE=?
				WHERE USER_REG_ID=?";

				$this->db->query($sql, array($input->first_name, $input->last_name, $input->address, $input->mobile, $input->national_id, $input->country, $_FILES['nid_file']['name'], $this->webspice->get_user_id(), $this->webspice->now(), $input->user_reg_id));

				// user image
				$this->webspice->process_image_single('nid_file', $_FILES['nid_file']['name'], 'nid_file_full');
			}
			else {
				$sql = "
				UPDATE user_registration SET FIRST_NAME=?, LAST_NAME=?, ADDRESS=?, MOBILE=?, NATIONAL_ID=?, COUNTRY=?, UPDATED_BY=?, UPDATED_DATE=?
				WHERE USER_REG_ID=?";

				$this->db->query($sql, array($input->first_name, $input->last_name, $input->address, $input->mobile, $input->national_id, $input->country, $this->webspice->get_user_id(), $this->webspice->now(), $input->user_reg_id));
			}

			$this->webspice->message_board('Record has been updated!');
			$this->webspice->force_redirect($url_prefix.'my_profile');
			return false;
		}

		$this->webspice->message_board('Software settings inserted successfully!');
		if($this->webspice->permission_verify('initial_settings',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'initial_settings');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'initial_settings');

	}


	/*********************************************
				for package setup
	*********************************************/
	public function create_package($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_package');
		$this->webspice->permission_verify('create_package');
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'PACKAGE_ID'=>null,
				'PACKAGE_NAME'=>null,
				'PACKAGE_DESC'=>null,
				// 'FACEBOOK_LINK'=>null,
				'PTC_LINK'=>null,
				// 'YOUTUBE_LINK'=>null,
				// 'GAME_PERMISSION'=>null,
				'PACKAGE_AMOUNT'=>null,
				'PACKAGE_VALIDITY'=>null
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('package_name', 'package name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('package_desc', 'package description', 'trim|xss_clean');
		// $this->form_validation->set_rules('facebook_link', 'facebook link', 'required|trim|xss_clean');
		$this->form_validation->set_rules('ptc_link', 'PTC link', 'required|trim|xss_clean');
		// $this->form_validation->set_rules('youtube_link', 'youtube link', 'required|trim|xss_clean');
		// $this->form_validation->set_rules('game_permission', 'game permission', 'required|trim|xss_clean');
		$this->form_validation->set_rules('package_amount', 'package amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('package_validity', 'package validity', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/create_package', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('package_id');
		
		// only for client requirement
		$input->facebook_link = 0;
		$input->youtube_link = 0;
		$input->game_permission = 0;
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM package_setup WHERE PACKAGE_NAME=? AND PACKAGE_AMOUNT=?", array($input->package_name, $input->package_amount), 'You are not allowed to enter duplicate package', 'PACKAGE_ID', $input->package_id, $data, 'admin_new/settings/create_package');
		
		# remove cache
		$this->webspice->remove_cache('package');

		# update process
		if( $input->package_id ){

			$sql = "
			UPDATE package_setup SET PACKAGE_NAME=?, PACKAGE_DESC=?, FACEBOOK_LINK=?, PTC_LINK=?, YOUTUBE_LINK=?, GAME_PERMISSION=?, PACKAGE_AMOUNT=?, PACKAGE_VALIDITY=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE PACKAGE_ID=?";

			$this->db->query($sql, array($input->package_name, $input->package_desc, $input->facebook_link, $input->ptc_link, $input->youtube_link, $input->game_permission, $input->package_amount, $input->package_validity, $this->webspice->get_user_id(), $this->webspice->now(), $input->package_id));

			$this->webspice->message_board('Record has been updated!');
			$this->webspice->log_me('product_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_package');
			return false;
		}
		
		#insert data

		$sql = "
		INSERT INTO package_setup
		(PACKAGE_NAME, PACKAGE_DESC, FACEBOOK_LINK, PTC_LINK, YOUTUBE_LINK, GAME_PERMISSION, PACKAGE_AMOUNT, PACKAGE_VALIDITY, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($input->package_name, $input->package_desc, $input->facebook_link, $input->ptc_link, $input->youtube_link, $input->game_permission, $input->package_amount, $input->package_validity, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Package created successfully!');
		if($this->webspice->permission_verify('manage_package',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_package');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_package');

	}

	public function manage_package() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_package');
		$this->webspice->permission_verify('manage_package');
		$this->load->database();
		$orderby = 'ORDER BY package_setup.PACKAGE_ID DESC';
		$groupby = null;
		$where = '';
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
		SELECT  * FROM package_setup ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'package_setup',
				$InputField = array(),
				$Keyword = array('PCKAGE_NAME'),
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
				$this->webspice->edit_generator($TableName='package_setup', $KeyField='PACKAGE_ID', $key, $RedirectController='settings_controller', $RedirectFunction='create_package', $PermissionName='manage_package', $StatusCheck=null, $Log='edit_package');
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
				$this->webspice->action_executer($TableName='package_setup', $KeyField='PACKAGE_ID', $key, $RedirectURL='manage_package', $PermissionName='manage_package', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='package', $Log='inactive_package');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='package_setup', $KeyField='PACKAGE_ID', $key, $RedirectURL='manage_package', $PermissionName='manage_package', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='package', $Log='active_package');
				return false;
				break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM package_setup WHERE PACKAGE_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board('Package deleted successfully!');
					$this->webspice->force_redirect($url_prefix.'manage_package');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_package/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/manage_package', $data);

	}

	public function create_facebook_add($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_facebook_add');
		$this->webspice->permission_verify('create_facebook_add');
		$data['add_data'] = $this->db->query("SELECT * FROM package_setup WHERE STATUS=7")->result();
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'ADD_ID'=>null,
				'ADD_UNIQ_ID'=>null,
				'PACKAGE_ID'=>null,
				'ADD_NAME'=>null,
				'URL_1'=>null,
				'URL_2'=>null,
				'PRICE'=>null,
				'ADD_DURATION'=>null,
				'ADD_TYPE'=>null
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('add_name', 'add name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('url_1', 'URL one', 'required|trim|xss_clean');
		foreach($data['add_data'] as $v) {
			$this->form_validation->set_rules('price_'.$v->PACKAGE_ID, $v->PACKAGE_NAME, 'required|trim|xss_clean');
		}
		$this->form_validation->set_rules('add_duration', 'add duration', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/create_facebook_add', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('add_id');
		$add_type = "facebook";
		$add_uniq_id = $this->db->query("SELECT * FROM add_setup ORDER BY ADD_UNIQ_ID DESC")->row();
		$uniq_id = 1;
		if(count($add_uniq_id)) {
			$uniq_id = $add_uniq_id->ADD_UNIQ_ID + 1;
		}

		#duplicate test
		// $this->webspice->db_field_duplicate_test("SELECT * FROM add_setup WHERE ADD_NAME=? AND URL_1=? AND ADD_UNIQ_ID=?", array($input->add_name, $input->url_1, $input->add_uniq_id), 'You are not allowed to enter duplicate add', 'ADD_ID', $input->add_id, $data, 'admin_new/settings/create_facebook_add');
		
		# remove cache
		$this->webspice->remove_cache('add');

		# update process
		if( $input->add_id ){

			$sql = "
			UPDATE add_setup SET PACKAGE_ID=?, ADD_NAME=?, URL_1=?, URL_2=?, PRICE=?, ADD_DURATION=?, ADD_TYPE=?, UPDATED_BY=?, UPDATED_DATE=? WHERE ADD_ID=?";

			$this->db->query($sql, array($input->package_id, $input->add_name, $input->url_1, $input->url_2, $input->price, $input->add_duration, $add_type, $this->webspice->get_user_id(), $this->webspice->now(), $input->add_id));

			$this->webspice->message_board('Facebook add has been updated!');
			$this->webspice->log_me('product_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_facebook_add');
			return false;
		}
		
		#insert data

		$sql = "
		INSERT INTO add_setup
		(ADD_UNIQ_ID, PACKAGE_ID, ADD_NAME, URL_1, PRICE, ADD_DURATION, ADD_TYPE, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, ?, 7 )";

		$price_val = array();
		foreach($data['add_data'] as $val) {
			// $price_val[] = $input->price_.$val->PACKAGE_ID;
			// $str = "price_".$val->PACKAGE_ID;
			$str = $input->{"price_".$val->PACKAGE_ID};
			// echo $input->$str;
			// echo $str;
			$this->db->query($sql, array($uniq_id, $val->PACKAGE_ID, $input->add_name, $input->url_1, $input->{"price_".$val->PACKAGE_ID}, $input->add_duration, $add_type, $this->webspice->get_user_id(), $this->webspice->now()));
		}
		// dd($price_val);
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Facebook add created successfully!');
		if($this->webspice->permission_verify('manage_facebook_add',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_facebook_add');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_facebook_add');

	}

	public function manage_facebook_add() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_facebook_add');
		$this->webspice->permission_verify('manage_facebook_add');
		$this->load->database();
		$orderby = 'ORDER BY add_setup.ADD_UNIQ_ID DESC';
		$groupby = ' GROUP BY ADD_UNIQ_ID ';
		$where = ' WHERE add_setup.ADD_TYPE="facebook" ';
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
		SELECT  * FROM add_setup ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'add_setup',
				$InputField = array(),
				$Keyword = array('PCKAGE_NAME'),
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
				$this->webspice->edit_generator($TableName='add_setup', $KeyField='ADD_UNIQ_ID', $key, $RedirectController='settings_controller', $RedirectFunction='create_package', $PermissionName='manage_facebook_add', $StatusCheck=null, $Log='edit_package');
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
				$this->webspice->action_executer($TableName='add_setup', $KeyField='ADD_UNIQ_ID', $key, $RedirectURL='manage_facebook_add', $PermissionName='manage_facebook_add', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='package', $Log='inactive_package');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='add_setup', $KeyField='ADD_UNIQ_ID', $key, $RedirectURL='manage_facebook_add', $PermissionName='manage_facebook_add', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='package', $Log='active_package');
				return false;
				break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM add_setup WHERE ADD_UNIQ_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board('Package deleted successfully!');
					$this->webspice->force_redirect($url_prefix.'manage_facebook_add');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_facebook_add/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/manage_facebook_add', $data);

	}

	public function create_ptc_add($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_ptc_add');
		$this->webspice->permission_verify('create_ptc_add');
		$data['add_data'] = $this->db->query("SELECT * FROM package_setup WHERE STATUS=7")->result();
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'ADD_ID'=>null,
				'ADD_UNIQ_ID'=>null,
				'PACKAGE_ID'=>null,
				'ADD_NAME'=>null,
				'URL_1'=>null,
				'URL_2'=>null,
				'PRICE'=>null,
				'ADD_DURATION'=>null,
				'ADD_TYPE'=>null
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('add_name', 'add name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('url_1', 'URL one', 'required|trim|xss_clean');
		foreach($data['add_data'] as $v) {
			$this->form_validation->set_rules('price_'.$v->PACKAGE_ID, $v->PACKAGE_NAME, 'required|trim|xss_clean');
		}
		$this->form_validation->set_rules('add_duration', 'add duration', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/create_ptc_add', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('add_id');
		$add_type = "ptc";
		$add_uniq_id = $this->db->query("SELECT * FROM add_setup ORDER BY ADD_UNIQ_ID DESC")->row();
		$uniq_id = 1;
		if(count($add_uniq_id)) {
			$uniq_id = $add_uniq_id->ADD_UNIQ_ID + 1;
		}

		#duplicate test
		// $this->webspice->db_field_duplicate_test("SELECT * FROM add_setup WHERE ADD_NAME=? AND URL_1=? AND ADD_UNIQ_ID=?", array($input->add_name, $input->url_1, $input->add_uniq_id), 'You are not allowed to enter duplicate add', 'ADD_ID', $input->add_id, $data, 'admin_new/settings/create_ptc_add');
		
		# remove cache
		$this->webspice->remove_cache('add');

		# update process
		if( $input->add_id ){

			$sql = "
			UPDATE add_setup SET PACKAGE_ID=?, ADD_NAME=?, URL_1=?, URL_2=?, PRICE=?, ADD_DURATION=?, ADD_TYPE=?, UPDATED_BY=?, UPDATED_DATE=? WHERE ADD_ID=?";

			$this->db->query($sql, array($input->package_id, $input->add_name, $input->url_1, $input->url_2, $input->price, $input->add_duration, $add_type, $this->webspice->get_user_id(), $this->webspice->now(), $input->add_id));

			$this->webspice->message_board('PTC add has been updated!');
			$this->webspice->log_me('product_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_ptc_add');
			return false;
		}
		
		#insert data

		$sql = "
		INSERT INTO add_setup
		(ADD_UNIQ_ID, PACKAGE_ID, ADD_NAME, URL_1, PRICE, ADD_DURATION, ADD_TYPE, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, ?, 7 )";

		$price_val = array();
		foreach($data['add_data'] as $val) {
			// $price_val[] = $input->price_.$val->PACKAGE_ID;
			// $str = "price_".$val->PACKAGE_ID;
			$str = $input->{"price_".$val->PACKAGE_ID};
			// echo $input->$str;
			// echo $str;
			$this->db->query($sql, array($uniq_id, $val->PACKAGE_ID, $input->add_name, $input->url_1, $input->{"price_".$val->PACKAGE_ID}, $input->add_duration, $add_type, $this->webspice->get_user_id(), $this->webspice->now()));
		}
		// dd($price_val);
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('PTC add created successfully!');
		if($this->webspice->permission_verify('manage_ptc_add',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_ptc_add');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_ptc_add');

	}

	public function manage_ptc_add() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_ptc_add');
		$this->webspice->permission_verify('manage_ptc_add');
		$this->load->database();
		$orderby = 'ORDER BY add_setup.ADD_UNIQ_ID DESC';
		$groupby = ' GROUP BY ADD_UNIQ_ID ';
		$where = ' WHERE add_setup.ADD_TYPE="ptc" ';
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
		SELECT  * FROM add_setup ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'add_setup',
				$InputField = array(),
				$Keyword = array('PCKAGE_NAME'),
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
				$id = $this->webspice->enc($key, 'decrypt');
				$data = array();
				$data['add_data'] = $this->db->query("SELECT * FROM package_setup WHERE STATUS=7")->result();
				$data['edit_data'] = $this->db->query("SELECT * FROM add_setup WHERE ADD_UNIQ_ID='{$id}' AND ADD_TYPE='ptc'")->result();
				$add_value = array();
				foreach($data['edit_data'] as $val) {
					$add_value[] = $val->PRICE;
				}
				$data['add_value'] = $add_value;

				$this->load->library('form_validation');
				$this->form_validation->set_rules('add_name', 'add name', 'required|trim|xss_clean');
				$this->form_validation->set_rules('url_1', 'URL one', 'required|trim|xss_clean');
				foreach($data['add_data'] as $v) {
					$this->form_validation->set_rules('price_'.$v->PACKAGE_ID, $v->PACKAGE_NAME, 'required|trim|xss_clean');
				}
				$this->form_validation->set_rules('add_duration', 'add duration', 'required|trim|xss_clean');
				
				if( !$this->form_validation->run() ){
					$this->load->view('admin_new/settings/edit_ptc_add', $data);
					return FALSE;
				}

				# get input post
				$input = $this->webspice->get_input('add_id');
				$add_type = "ptc";
				$add_uniq_id = $data['edit_data'][0]->ADD_UNIQ_ID;
				$package_data = $data['edit_data'][0]->PACKAGE_ID;
				$add_name = $data['edit_data'][0]->ADD_NAME;
				$url_1 = $data['edit_data'][0]->URL_1;
				$url_2 = $data['edit_data'][0]->URL_2;
				$add_duration = $data['edit_data'][0]->ADD_DURATION;

				# update process
				$sql = "
				UPDATE add_setup SET ADD_NAME=?, URL_1=?, PRICE=?, ADD_DURATION=?, ADD_TYPE=?, UPDATED_BY=?, UPDATED_DATE=? WHERE ADD_UNIQ_ID=? AND PACKAGE_ID=?";

				$price_val = array();
				foreach($data['add_data'] as $val) {
					$str = $input->{"price_".$val->PACKAGE_ID};

					$this->db->query($sql, array($input->add_name, $input->url_1, $input->{"price_".$val->PACKAGE_ID}, $input->add_duration, $add_type, $this->webspice->get_user_id(), $this->webspice->now(), $add_uniq_id, $val->PACKAGE_ID));
				}

				$this->webspice->message_board('PTC add has been updated!');
				$this->webspice->log_me('add_updated - '.$this->webspice->get_user_id()); # log activities
				$this->webspice->force_redirect($url_prefix.'manage_ptc_add');

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
				$this->webspice->action_executer($TableName='add_setup', $KeyField='ADD_UNIQ_ID', $key, $RedirectURL='manage_ptc_add', $PermissionName='manage_ptc_add', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='package', $Log='inactive_package');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='add_setup', $KeyField='ADD_UNIQ_ID', $key, $RedirectURL='manage_ptc_add', $PermissionName='manage_ptc_add', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='package', $Log='active_package');
				return false;
				break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM add_setup WHERE ADD_UNIQ_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board('Package deleted successfully!');
					$this->webspice->force_redirect($url_prefix.'manage_ptc_add');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_ptc_add/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/manage_ptc_add', $data);

	}

	public function create_youtube_add($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_youtube_add');
		$this->webspice->permission_verify('create_youtube_add');
		$data['add_data'] = $this->db->query("SELECT * FROM package_setup WHERE STATUS=7")->result();
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'ADD_ID'=>null,
				'ADD_UNIQ_ID'=>null,
				'PACKAGE_ID'=>null,
				'ADD_NAME'=>null,
				'URL_1'=>null,
				'URL_2'=>null,
				'PRICE'=>null,
				'ADD_DURATION'=>null,
				'ADD_TYPE'=>null
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('add_name', 'add name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('url_1', 'URL one', 'required|trim|xss_clean');
		foreach($data['add_data'] as $v) {
			$this->form_validation->set_rules('price_'.$v->PACKAGE_ID, $v->PACKAGE_NAME, 'required|trim|xss_clean');
		}
		$this->form_validation->set_rules('add_duration', 'add duration', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/create_youtube_add', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('add_id');
		$add_type = "youtube";
		$add_uniq_id = $this->db->query("SELECT * FROM add_setup ORDER BY ADD_UNIQ_ID DESC")->row();
		$uniq_id = 1;
		if(count($add_uniq_id)) {
			$uniq_id = $add_uniq_id->ADD_UNIQ_ID + 1;
		}

		#duplicate test
		// $this->webspice->db_field_duplicate_test("SELECT * FROM add_setup WHERE ADD_NAME=? AND URL_1=? AND ADD_UNIQ_ID=?", array($input->add_name, $input->url_1, $input->add_uniq_id), 'You are not allowed to enter duplicate add', 'ADD_ID', $input->add_id, $data, 'admin_new/settings/create_youtube_add');
		
		# remove cache
		$this->webspice->remove_cache('add');

		# update process
		if( $input->add_id ){

			$sql = "
			UPDATE add_setup SET PACKAGE_ID=?, ADD_NAME=?, URL_1=?, URL_2=?, PRICE=?, ADD_DURATION=?, ADD_TYPE=?, UPDATED_BY=?, UPDATED_DATE=? WHERE ADD_ID=?";

			$this->db->query($sql, array($input->package_id, $input->add_name, $input->url_1, $input->url_2, $input->price, $input->add_duration, $add_type, $this->webspice->get_user_id(), $this->webspice->now(), $input->add_id));

			$this->webspice->message_board('Youtube add has been updated!');
			$this->webspice->log_me('product_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_youtube_add');
			return false;
		}
		
		#insert data

		$sql = "
		INSERT INTO add_setup
		(ADD_UNIQ_ID, PACKAGE_ID, ADD_NAME, URL_1, PRICE, ADD_DURATION, ADD_TYPE, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, ?, 7 )";

		$price_val = array();
		foreach($data['add_data'] as $val) {
			// $price_val[] = $input->price_.$val->PACKAGE_ID;
			// $str = "price_".$val->PACKAGE_ID;
			$str = $input->{"price_".$val->PACKAGE_ID};
			// echo $input->$str;
			// echo $str;
			$this->db->query($sql, array($uniq_id, $val->PACKAGE_ID, $input->add_name, $input->url_1, $input->{"price_".$val->PACKAGE_ID}, $input->add_duration, $add_type, $this->webspice->get_user_id(), $this->webspice->now()));
		}
		// dd($price_val);
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Youtube add created successfully!');
		if($this->webspice->permission_verify('manage_youtube_add',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_youtube_add');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_youtube_add');

	}

	public function manage_youtube_add() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_youtube_add');
		$this->webspice->permission_verify('manage_youtube_add');
		$this->load->database();
		$orderby = 'ORDER BY add_setup.ADD_UNIQ_ID DESC';
		$groupby = ' GROUP BY ADD_UNIQ_ID ';
		$where = ' WHERE add_setup.ADD_TYPE="youtube" ';
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
		SELECT  * FROM add_setup ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'add_setup',
				$InputField = array(),
				$Keyword = array('PCKAGE_NAME'),
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
				$this->webspice->edit_generator($TableName='add_setup', $KeyField='ADD_UNIQ_ID', $key, $RedirectController='settings_controller', $RedirectFunction='create_package', $PermissionName='manage_youtube_add', $StatusCheck=null, $Log='edit_package');
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
				$this->webspice->action_executer($TableName='add_setup', $KeyField='ADD_UNIQ_ID', $key, $RedirectURL='manage_youtube_add', $PermissionName='manage_youtube_add', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='package', $Log='inactive_package');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='add_setup', $KeyField='ADD_UNIQ_ID', $key, $RedirectURL='manage_youtube_add', $PermissionName='manage_youtube_add', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='package', $Log='active_package');
				return false;
				break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM add_setup WHERE ADD_UNIQ_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board('Package deleted successfully!');
					$this->webspice->force_redirect($url_prefix.'manage_youtube_add');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_youtube_add/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/manage_youtube_add', $data);

	}


	/*********************************************
				subscription pin
	*********************************************/
	public function create_new_code($data=null) {
		$data = array();
		$errors = array();
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_new_code');
		$this->webspice->permission_verify('create_new_code');
		$data['package_data'] = $this->db->query("SELECT * FROM package_setup WHERE STATUS=7")->result();

		/*
		 * delete edit portion
		*/

		$this->load->library('form_validation');
		$this->form_validation->set_rules('package_id', 'package name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('number_of_code', 'number of code', 'required|trim|xss_clean');
		$this->form_validation->set_rules('password', 'password', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/settings/create_new_code', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('pin_id');
		$pin_value = $this->db->query("SELECT PACKAGE_AMOUNT FROM package_setup WHERE PACKAGE_ID='".$input->package_id."'")->row()->PACKAGE_AMOUNT;
		// user password check
		$input_pass = $this->webspice->enc($input->password, 'encrypt');
		$user_pass = $this->db->query("SELECT * FROM user WHERE USER_PASSWORD='".$input_pass."'")->row();
		// dd(count($user_pass));
		if(!count($user_pass)) {
			$errors[] = "Wrong password, password didn't match.";
		}

		if(!$this->webspice->admin_verify()) {
			// check user blance
			$total_amount = $pin_value * $input->number_of_code;
			$user_balance = $this->webspice->user_balance($this->webspice->get_user_id());
			
			if($user_balance < $total_amount) {
				$errors[] = "Insufficient balance, please load your balance from admin.";
			}
		}
		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view("admin_new/settings/create_new_code", $data);
			return false;
		}

		
		#insert data
		$sql = "
		INSERT INTO pin_code
		(PIN_CODE, PACKAGE_ID, GENERATOR_ID, PIN_VALUE, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, 0 )";

		for($i=0; $i<$input->number_of_code; $i++) {
			$pin_code = $this->webspice->generate_subscription_pin();
			$this->db->query($sql, array($pin_code, $input->package_id, $this->webspice->get_user_id(), $pin_value, $this->webspice->now()));
		}

		// decrease balance if user is not admin
		if(!$this->webspice->admin_verify()) {
			// insert balance to shopping_wallet
			$balance_type = "POST";
			$amount = $total_amount;
			$reason = "Buy subscription pin";
			$trans_date = $this->webspice->now();
			$trans_status = 1;
			$created_date = $trans_date;

			$sql5 = "
			INSERT INTO user_balance
			(USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, TRANS_STATUS, CREATED_DATE)
			VALUES
			( ?, ?, ?, ?, ?, ?, ? )";

			$this->db->query($sql5, array(	
				$this->webspice->get_user_id(),
				$balance_type,
				$total_amount,
				$reason,
				$trans_date,
				$trans_status,
				$created_date
			));
		}
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Pin created successfully!');
		if($this->webspice->permission_verify('available_codes',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'available_codes');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_new_code');

	}

	public function available_codes() {

		$user_sess_id = $this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt');	
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'available_codes');
		$this->webspice->permission_verify('available_codes');
		$this->load->database();
		$orderby = 'ORDER BY pc.PIN_ID DESC';
		$groupby = null;
		$where = ' AND pc.STATUS=0 ';
		if(!$this->webspice->admin_verify()) {
			$where = ' WHERE pc.GENERATOR_ID="'.$user_sess_id.'" AND pc.STATUS=0 ';
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
		SELECT  pc.*, ps.PACKAGE_NAME FROM pin_code AS pc INNER JOIN package_setup AS ps ON pc.PACKAGE_ID=ps.PACKAGE_ID";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'pin_code',
				$InputField = array(),
				$Keyword = array('PIN_CODE'),
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

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM pin_code WHERE PIN_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board('Pin deleted successfully!');
					$this->webspice->force_redirect($url_prefix.'available_codes');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'available_codes/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/available_codes', $data);

	}

	public function used_codes() {

		$user_sess_id = $this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt');	
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'used_codes');
		$this->webspice->permission_verify('used_codes');
		$this->load->database();
		$orderby = 'ORDER BY pc.PIN_ID DESC';
		$groupby = null;
		$where = ' AND pc.STATUS=1 ';
		if(!$this->webspice->admin_verify()) {
			$where = ' WHERE pc.GENERATOR_ID="'.$user_sess_id.'" AND pc.STATUS=1 ';
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
		SELECT  pc.*, ps.PACKAGE_NAME FROM pin_code AS pc INNER JOIN package_setup AS ps ON pc.PACKAGE_ID=ps.PACKAGE_ID";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'pin_code',
				$InputField = array(),
				$Keyword = array('PIN_CODE'),
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

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM pin_code WHERE PIN_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board('Pin deleted successfully!');
					$this->webspice->force_redirect($url_prefix.'used_codes');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'used_codes/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/settings/used_codes', $data);

	}

}