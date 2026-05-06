<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms_controller extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}

	public function create_address_book($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_address_book');
		$this->webspice->permission_verify('create_address_book');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'ADD_BOOK_ID'=>null,
				'BOOK_NAME'=>null
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('book_name', 'address book name', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/sms/create_address_book', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('add_book_id');
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM address_book WHERE BOOK_NAME=?", array($input->book_name), 'You are not allowed to insert dublicate address book name', 'ADD_BOOK_ID', $input->add_book_id, $data, 'admin_new/sms/create_address_book');
		
		# remove cache
		$this->webspice->remove_cache('address_book');

		# update process
		if( $input->add_book_id ){

			$sql = "
			UPDATE address_book SET BOOK_NAME=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE ADD_BOOK_ID=?";

			$this->db->query($sql, array($input->book_name, $this->webspice->get_user_id(), $this->webspice->now(), $input->add_book_id));

			$this->webspice->message_board('Address book name has been updated!');
			$this->webspice->log_me('address_book - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_address_book');
			return false;
		}
		
		#insert data
		$sql = "
		INSERT INTO address_book
		(BOOK_NAME, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, 7 )";
		$this->db->query($sql, array($input->book_name, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Address book has been created');
		if($this->webspice->permission_verify('manage_address_book',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_address_book');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_address_book');
	}

	public function manage_address_book() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_address_book');
		$this->webspice->permission_verify('manage_address_book');
		$this->load->database();
		$orderby = 'ORDER BY address_book.ADD_BOOK_ID DESC';
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
		SELECT  * FROM address_book ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'address_book',
				$InputField = array(),
				$Keyword = array('BOOK_NAME'),
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
				$this->webspice->action_executer($TableName='address_book', $KeyField='ADD_BOOK_ID', $key, $RedirectURL='manage_address_book', $PermissionName='manage_address_book', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='address_book', $Log='inactive_address_book');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='address_book', $KeyField='ADD_BOOK_ID', $key, $RedirectURL='manage_address_book', $PermissionName='manage_address_book', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='address_book', $Log='active_address_book');
				return false;
				break;

			case 'edit':
				$this->webspice->edit_generator($TableName='address_book', $KeyField='ADD_BOOK_ID', $key, $RedirectController='sms_controller', $RedirectFunction='create_address_book', $PermissionName='manage_address_book', $StatusCheck=null, $Log='edit_address_book');
				return false;
			break;


			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM address_book WHERE ADD_BOOK_ID='".$id."' LIMIT 1");

				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_address_book');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_address_book/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/sms/manage_address_book', $data);

	}

	public function upload_bulk_number() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'upload_bulk_number');
		$this->webspice->permission_verify('upload_bulk_number');
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'ADD_BOOK_NUM_ID'=>null,
				'ADD_BOOK_ID'=>null,
				'NAME'=>null,
				'NUMBER'=>null,
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('add_book_id', 'address book name', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/sms/upload_bulk_number', $data);
			return FALSE;
		}

		if( !$_FILES || !$_FILES['upload_data_file']['tmp_name'] ){
			$this->load->view('admin_new/sms/upload_bulk_number', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('add_book_num_id');

		if( $_FILES['upload_data_file'] ) {

			$file_name = $_FILES['upload_data_file']['name'];
			$chk_ext = explode(".", $file_name);
			if( (strtolower(end($chk_ext)) !== "csv") ) {
				$this->webspice->message_board("Your file type must be in csv format");
				$this->load->view('admin_new/sms/upload_bulk_number', $data);
				return false;
			}
			
			$fname = $_FILES['upload_data_file']['tmp_name'];
			$handle = fopen($fname, "r");
			$my_data = array();
			while ( ($up_data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
				$my_data[] = $up_data;
			}
			fclose($handle);

			unset($my_data[0]);
			$my_data = array_values($my_data);
			// dd($my_data);

			#insert csv data
			$sql = "
			INSERT INTO address_book_num
			(`ADD_BOOK_ID`, `NAME`, `NUMBER`, `CREATED_BY`, `CREATED_DATE`, `STATUS`)
			VALUES
			( ?, ?, ?, ?, ?, 7 )";

			for($i=0; $i<count($my_data); $i++) :
				$number = 0 . $my_data[$i][1];

				$this->db->query($sql, array($input->add_book_id, $my_data[$i][0], $number, $this->webspice->get_user_id(),$this->webspice->now()));
			endfor;

			if( !$this->db->insert_id() ){
				$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
				$this->webspice->force_redirect($url_prefix . 'admin');
				return false;
			}

			$this->webspice->message_board('Number uploaded successfully!');
			if($this->webspice->permission_verify('manage_address_book',TRUE)){
				$this->webspice->force_redirect($url_prefix . 'manage_address_book');
				return FALSE;
			}
			$this->webspice->force_redirect($url_prefix.'upload_bulk_number');


		}
		
	}

	public function manage_upload_bulk_number() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_upload_bulk_number');
		$this->webspice->permission_verify('manage_upload_bulk_number');
		$this->load->database();
		$orderby = 'ORDER BY address_book_num.ADD_BOOK_ID DESC';
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
		if($key) {
			$group_id = $this->db->query("SELECT ADD_BOOK_ID FROM address_book_num WHERE ADD_BOOK_NUM_ID='".$this->webspice->enc($key, 'decrypt')."'")->row()->ADD_BOOK_ID;
			$group_id = $this->webspice->enc($group_id, 'encrypt');
		}

		$initialSQL = "
		SELECT  * FROM address_book_num ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'address_book_num',
				$InputField = array(),
				$Keyword = array('ADD_BOOK_ID'),
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
				$this->webspice->edit_generator($TableName='address_book_num', $KeyField='ADD_BOOK_NUM_ID', $key, $RedirectController='sms_controller', $RedirectFunction='add_phone_number', $PermissionName='manage_upload_bulk_number', $StatusCheck=null, $Log='edit_phone_number');
				return false;
			break;
			
			case 'inactive':
				$this->webspice->action_executer($TableName='address_book_num', $KeyField='ADD_BOOK_NUM_ID', $key, $RedirectURL='manage_upload_bulk_number/view_numbers/'.$group_id, $PermissionName='manage_upload_bulk_number', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='address_book', $Log='inactive_address_book');
				return false;
			break;

			case 'active':
				$this->webspice->action_executer($TableName='address_book_num', $KeyField='ADD_BOOK_NUM_ID', $key, $RedirectURL='manage_upload_bulk_number/view_numbers/'.$group_id, $PermissionName='manage_upload_bulk_number', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='address_book', $Log='active_address_book');
				return false;
			break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM address_book_num WHERE ADD_BOOK_NUM_ID='".$id."' LIMIT 1");

				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_upload_bulk_number/view_numbers/'.$group_id);
				}
				return false;
			break;

			case 'view_numbers':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("SELECT * FROM address_book_num WHERE ADD_BOOK_ID='".$id."'")->result();

				$data['get_record'] = $sql;
				$this->load->view("admin_new/sms/view_numbers", $data);
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_upload_bulk_number/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		dd("Just fun!!");
		$this->load->view('admin/sms/manage_upload_bulk_number', $data);

	}

	public function add_phone_number($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'add_phone_number');
		$this->webspice->permission_verify('add_phone_number');
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'ADD_BOOK_NUM_ID'=>null,
				'ADD_BOOK_ID'=>null,
				'NAME'=>null,
				'NUMBER'=>null
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('add_book_id', 'address book name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('name', 'name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('number', 'number', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/sms/add_phone_number', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('add_book_num_id');
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM address_book_num WHERE `NUMBER`=?", array($input->number), 'You are not allowed to insert dublicate number', 'ADD_BOOK_NUM_ID', $input->add_book_num_id, $data, 'admin_new/sms/add_phone_number');

		# update process
		if( $input->add_book_num_id ){

			$sql = "
			UPDATE address_book_num SET `ADD_BOOK_ID`=?, `NAME`=?, `NUMBER`=?, `UPDATED_BY`=?, `UPDATED_DATE`=?
			WHERE `ADD_BOOK_NUM_ID`=?";

			$this->db->query($sql, array($input->add_book_id, $input->name, $input->number, $this->webspice->get_user_id(), $this->webspice->now(), $input->add_book_num_id));

			$this->webspice->message_board('Number has been updated!');
			$this->webspice->log_me('number_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_address_book');
			return false;
		}
		
		#insert data

		$sql = "
		INSERT INTO address_book_num
		(`ADD_BOOK_ID`, `NAME`, `NUMBER`, `CREATED_BY`, `CREATED_DATE`, `STATUS`)
		VALUES
		( ?, ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($input->add_book_id, $input->name, $input->number, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Number added successfully!');
		if($this->webspice->permission_verify('manage_address_book',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_address_book');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'add_phone_number');

	}

	public function create_sms($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_sms');
		$this->webspice->permission_verify('create_sms');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'SMS_ID'=>null,
				'NUMBER'=>null,
				'SMS'=>null
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('number', 'number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('sms', 'sms', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/sms/create_sms', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('sms_id');
		
		# remove cache
		$this->webspice->remove_cache('sms');
		
		#insert data
		$sql = "
		INSERT INTO sms_data
		(`NUMBER`, `SMS`, `CREATED_BY`, `CREATED_DATE`, `STATUS`)
		VALUES
		( ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($input->number, $input->sms, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('SMS send successfully');
		if($this->webspice->permission_verify('send_sms',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'send_sms');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_sms');
	}

	public function send_sms() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'send_sms');
		$this->webspice->permission_verify('send_sms');
		$this->load->database();
		$orderby = 'ORDER BY sms_data.ADD_BOOK_ID DESC';
		$groupby = null;
		$where = ' WHERE sms_data.BULK_SMS_ID IS NULL OR sms_data.BULK_SMS_ID = ""';
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
		SELECT  * FROM sms_data ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'sms_data',
				$InputField = array(),
				$Keyword = array('SMS_ID'),
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
				$sql = $this->db->query("DELETE FROM sms_data WHERE SMS_ID='".$id."' LIMIT 1");

				if($sql) {
					$this->webspice->message_board('SMS deleted successfully');
					$this->webspice->force_redirect($url_prefix.'send_sms');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'send_sms/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/sms/send_sms', $data);

	}

	public function create_group_sms($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_group_sms');
		$this->webspice->permission_verify('create_group_sms');
		$errors = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('add_book_id', 'group name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('sms', 'sms', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/sms/create_group_sms', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('group_sms_id');
		// dd($input);
		$all_data = $this->db->query("SELECT * FROM address_book_num WHERE STATUS=7 AND ADD_BOOK_ID='".$input->add_book_id."'")->result();
		if(count($all_data) == 0) {
			$errors[] = "SMS not sent because of no record found in this address book.";
		}

		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view("admin_new/sms/create_group_sms", $data);
			return false;
		}
		
		# remove cache
		$this->webspice->remove_cache('sms');
		$bulk_id = $this->webspice->get_bulk_id();
		
		#insert data
		$sql = "
		INSERT INTO sms_data
		(`BULK_SMS_ID`, `ADD_BOOK_ID`, `NAME`, `NUMBER`, `SMS`, `CREATED_BY`, `CREATED_DATE`, `STATUS`)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, 7 )";

		foreach($all_data as $data) {
			$this->db->query($sql, array($bulk_id, $data->ADD_BOOK_ID, $data->NAME, $data->NUMBER, $input->sms, $this->webspice->get_user_id(), $this->webspice->now()));
		}
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('SMS send successfully');
		if($this->webspice->permission_verify('send_group_sms',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'send_group_sms');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_group_sms');
	}

	public function send_group_sms() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'send_group_sms');
		$this->webspice->permission_verify('send_group_sms');
		$this->load->database();
		$orderby = 'ORDER BY sms_data.SMS_ID DESC';
		$groupby = ' GROUP BY sms_data.BULK_SMS_ID ';
		$where = ' WHERE sms_data.BULK_SMS_ID <>"" ';
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
		SELECT  * FROM sms_data ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'sms_data',
				$InputField = array(),
				$Keyword = array('SMS_ID'),
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
				$sql = $this->db->query("DELETE FROM sms_data WHERE BULK_SMS_ID='".$id."'");

				if($sql) {
					$this->webspice->message_board('Group SMS deleted successfully');
					$this->webspice->force_redirect($url_prefix.'send_group_sms');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'send_group_sms/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/sms/send_group_sms', $data);

	}

}