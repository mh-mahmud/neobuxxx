<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group_controller extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}

	public function create_group($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_group');
		$this->webspice->permission_verify('create_group');

		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'GROUP_ID'=>null,
				'GROUP_NAME'=>null
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('group_name', 'group name', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/group/create_group', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('group_id');
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM group_data WHERE GROUP_NAME=?", array($input->group_name), 'You are not allowed to insert dublicate group name', 'GROUP_ID', $input->group_id, $data, 'admin_new/group/create_group');
		
		# remove cache
		$this->webspice->remove_cache('group');

		# update process
		if( $input->group_id ){

			$sql = "
			UPDATE group_data SET GROUP_NAME=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE GROUP_ID=?";

			$this->db->query($sql, array($input->group_name, $this->webspice->get_user_id(), $this->webspice->now(), $input->group_id));

			$this->webspice->message_board('Group name has been updated!');
			$this->webspice->log_me('group_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_group');
			return false;
		}
		
		#insert data
		$sql = "
		INSERT INTO group_data
		(GROUP_NAME, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, 7 )";
		$this->db->query($sql, array($input->group_name, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Group has been created');
		if($this->webspice->permission_verify('manage_group',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_group');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_group');
	}

	public function manage_group() {

		$user_sess_id = $this->webspice->enc($_SESSION['user']['USER_ID'], 'decrypt');
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_group');
		$this->webspice->permission_verify('manage_group');
		$this->load->database();
		$orderby = 'ORDER BY group_data.GROUP_ID DESC';
		$groupby = null;
		$where = null;
		if(!$this->webspice->admin_verify()) {
			$where = ' WHERE CREATED_BY="'.$user_sess_id.'" ';
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
		SELECT  * FROM group_data ";


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
				$this->webspice->action_executer($TableName='group_data', $KeyField='GROUP_ID', $key, $RedirectURL='manage_group', $PermissionName='manage_group', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='group', $Log='inactive_group');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='group_data', $KeyField='GROUP_ID', $key, $RedirectURL='manage_group', $PermissionName='manage_group', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='group', $Log='active_group');
				return false;
				break;

			case 'edit':
				$this->webspice->edit_generator($TableName='group_data', $KeyField='GROUP_ID', $key, $RedirectController='group_controller', $RedirectFunction='create_group', $PermissionName='manage_group', $StatusCheck=null, $Log='edit_group');
				return false;
			break;


			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM group_data WHERE GROUP_ID='".$id."' LIMIT 1");

				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_group');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_group/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/group/manage_group', $data);

	}

	public function upload_number() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'upload_number');
		$this->webspice->permission_verify('upload_number');
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'GROUP_NUM_ID'=>null,
				'GROUP_ID'=>null,
				'NUMBER'=>null,
				'AMOUNT'=>null,
				'NUMBER_TYPE'=>null,
			);
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('group_id', 'group name', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/group/upload_number', $data);
			return FALSE;
		}

		if( !$_FILES || !$_FILES['upload_data_file']['tmp_name'] ){
			$this->load->view('admin_new/group/upload_number', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('group_num_id');

		if( $_FILES['upload_data_file'] ) {

			$file_name = $_FILES['upload_data_file']['name'];
			$chk_ext = explode(".", $file_name);
			if( (strtolower(end($chk_ext)) !== "csv") ) {
				$this->webspice->message_board("Your file type must be in csv format");
				$this->load->view('admin/group/upload_number', $data);
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

			#insert csv data
			$sql = "
			INSERT INTO group_number
			(`GROUP_ID`, `NUMBER`, `AMOUNT`, `NUMBER_TYPE`, `CREATED_BY`, `CREATED_DATE`, `STATUS`)
			VALUES
			( ?, ?, ?, ?, ?, ?, 7 )";

			for($i=0; $i<count($my_data); $i++) :
				$number = 0 . $my_data[$i][0];

				$this->db->query($sql, array($input->group_id, $number, $my_data[$i][1], $my_data[$i][2], $this->webspice->get_user_id(),$this->webspice->now()));
			endfor;

			if( !$this->db->insert_id() ){
				$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
				$this->webspice->force_redirect($url_prefix . 'admin');
				return false;
			}

			$this->webspice->message_board('Number uploaded successfully!');
			if($this->webspice->permission_verify('manage_group',TRUE)){
				$this->webspice->force_redirect($url_prefix . 'manage_group');
				return FALSE;
			}
			$this->webspice->force_redirect($url_prefix.'upload_number');


		}
		
	}

	public function manage_upload_number() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_upload_number');
		$this->webspice->permission_verify('manage_upload_number');
		$this->load->database();
		$orderby = 'ORDER BY group_number.GROUP_ID DESC';
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
			$group_id = $this->db->query("SELECT GROUP_ID FROM group_number WHERE GROUP_NUM_ID='".$this->webspice->enc($key, 'decrypt')."'")->row()->GROUP_ID;
			$group_id = $this->webspice->enc($group_id, 'encrypt');
		}

		$initialSQL = "
		SELECT  * FROM group_number ";


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
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$id = explode("|", $id);
				$data['number'] = $id[0];
				$data['amount'] = $id[1];
				$data['service_id'] = $id[2];
				$data['service_name'] = $this->db->query("SELECT SERVICE_NAME FROM service_settings WHERE SERVICE_ID='".$data['service_id']."'")->row()->SERVICE_NAME;
				$data['type'] = $id[3];

				// dd($data);
				
				$this->load->library('form_validation');
				$this->form_validation->set_rules('pin_number', 'pin number', 'required|trim|xss_clean');
				
				if( !$this->form_validation->run() ){
					$this->load->view('admin/flexi/confirm_send_money', $data);
					return FALSE;
				}

				# get input post
				$input = $this->webspice->get_input('flexi_trans_id');
				$pin_number = $input->pin_number;

				// dd($data);

				$sql = "
				INSERT INTO flexi_trans
				(SERVICE_ID, MOBILE_NUMBER, AMOUNT, TRANS_TYPE, TRANS_STATUS, CREATED_BY, CREATED_DATE)
				VALUES
				( ?, ?, ?, ?, ?, ?, ? )";
				$this->db->query($sql, array($data['service_id'], $data['number'], $data['amount'], $data['type'], 1, $this->webspice->get_user_id(), $this->webspice->now()));
			
				if( !$this->db->insert_id() ){
					$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
					$this->webspice->force_redirect($url_prefix . 'admin');
					return false;
				}

				$this->webspice->message_board('Money send successfully!');
				if($this->webspice->permission_verify('view_flexi_send_money',TRUE)){
					$this->webspice->force_redirect($url_prefix . 'view_flexi_send_money');
					return FALSE;
				}
				$this->webspice->force_redirect($url_prefix.'flexi_send_money');

				return false;

			break;

			case 'edit':
				$this->webspice->edit_generator($TableName='group_number', $KeyField='GROUP_NUM_ID', $key, $RedirectController='group_controller', $RedirectFunction='add_new_number', $PermissionName='manage_group', $StatusCheck=null, $Log='edit_group_number');
				return false;
			break;
			
			case 'inactive':
				$this->webspice->action_executer($TableName='group_number', $KeyField='GROUP_NUM_ID', $key, $RedirectURL='manage_upload_number/view_numbers/'.$group_id, $PermissionName='manage_group', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='group', $Log='inactive_group_number');
				return false;
			break;

			case 'active':
				$this->webspice->action_executer($TableName='group_number', $KeyField='GROUP_NUM_ID', $key, $RedirectURL='manage_upload_number/view_numbers/'.$group_id, $PermissionName='manage_group', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='group', $Log='active_group_number');
				return false;
			break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM group_number WHERE GROUP_NUM_ID='".$id."' LIMIT 1");

				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_group');
				}
				return false;
			break;

			case 'view_numbers':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("SELECT * FROM group_number WHERE GROUP_ID='".$id."'")->result();

				$data['get_record'] = $sql;
				$this->load->view("admin_new/group/view_numbers", $data);
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_upload_number/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		dd("Just fun!!");
		$this->load->view('admin/group/manage_upload_number', $data);

	}

	public function add_new_number($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'add_new_number');
		$this->webspice->permission_verify('add_new_number');
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'GROUP_NUM_ID'=>null,
				'GROUP_ID'=>null,
				'NUMBER'=>null,
				'AMOUNT'=>null,
				'NUMBER_TYPE'=>null
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('group_id', 'group name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('number', 'number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('amount', 'amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('number_type', 'number type', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/group/add_new_number', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('group_num_id');
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM group_number WHERE `NUMBER`=?", array($input->number), 'You are not allowed to insert dublicate number', 'GROUP_NUM_ID', $input->group_num_id, $data, 'admin_new/group/add_new_number');

		# update process
		if( $input->group_num_id ){

			$sql = "
			UPDATE group_number SET `GROUP_ID`=?, `NUMBER`=?, `AMOUNT`=?, `NUMBER_TYPE`=?, `UPDATED_BY`=?, `UPDATED_DATE`=?
			WHERE `GROUP_NUM_ID`=?";

			$this->db->query($sql, array($input->group_id, $input->number, $input->amount, $input->number_type, $this->webspice->get_user_id(), $this->webspice->now(), $input->group_num_id));

			$this->webspice->message_board('Group number has been updated!');
			$this->webspice->log_me('card_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_group');
			return false;
		}
		
		#insert data

		$sql = "
		INSERT INTO group_number
		(`GROUP_ID`, `NUMBER`, `AMOUNT`, `NUMBER_TYPE`, `CREATED_BY`, `CREATED_DATE`, `STATUS`)
		VALUES
		( ?, ?, ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($input->group_id, $input->number, $input->amount, $input->number_type, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Number added successfully!');
		if($this->webspice->permission_verify('manage_group',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_group');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'add_new_number');

	}

}