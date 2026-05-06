<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_controller extends CI_Controller {

	/**
	 *
	 * Card Status list: [ db CARD_STATUS field ]
	 * 1 - available
	 * 2 - purchased
	 * 3 - invalid card
	 * 4 - expired
	 *
	 */

	function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}

	public function create_card_service($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'create_card_service');
		$this->webspice->permission_verify('create_card_service');
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'CARD_SERVICE_ID'=>null,
				'OPERATOR_NAME'=>null,
				'SERVICE_TYPE'=>null,
				'LOGO'=>null
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('operator_name', 'operator name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('service_type', 'service type', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/card_mgmt/create_card_service', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('card_service_id');
		// $input->service_type = "flexi_service";
		
		#duplicate test
		$this->webspice->db_field_duplicate_test("SELECT * FROM card_service WHERE OPERATOR_NAME=? AND SERVICE_TYPE=?", array($input->operator_name, $input->service_type), 'You are not allowed to enter duplicate card service', 'CARD_SERVICE_ID', $input->card_service_id, $data, 'admin_new/card_mgmt/create_card_service');
		
		# remove cache
		$this->webspice->remove_cache('card_service');

		# verify file type
		if( $_FILES['image']['tmp_name'] ){
			$this->webspice->check_file_type(array('jpg','jpeg', 'png', 'gif'), 'image', $data, 'admin_new/card_mgmt/create_card_service');
		}

		# update process
		if( $input->card_service_id ){
			if($_FILES['image']['name']) {
				$sql = "
				UPDATE card_service SET OPERATOR_NAME=?, SERVICE_TYPE=?, LOGO=?, UPDATED_BY=?,UPDATED_DATE=?
				WHERE CARD_SERVICE_ID=?";

				$this->db->query($sql, array($input->operator_name, $input->service_type, $_FILES['image']['name'], $this->webspice->get_user_id(), $this->webspice->now(), $input->card_service_id));

				// image processing
				$this->webspice->process_image_single('image', $_FILES['image']['name'], 'card_service_full');
			}
			else {
				$sql = "
				UPDATE card_service SET OPERATOR_NAME=?, SERVICE_TYPE=?, UPDATED_BY=?,UPDATED_DATE=?
				WHERE CARD_SERVICE_ID=?";

				$this->db->query($sql, array($input->operator_name, $input->service_type, $this->webspice->get_user_id(), $this->webspice->now(), $input->card_service_id));
			}
			$this->webspice->message_board('Record has been updated!');
			$this->webspice->log_me('product_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_card_service');
			return false;
		}
		
		#insert data
		if($_FILES['image']['name']) {
			$sql = "
			INSERT INTO card_service
			(OPERATOR_NAME, SERVICE_TYPE, LOGO, CREATED_BY, CREATED_DATE, STATUS)
			VALUES
			( ?, ?, ?, ?, ?, 7 )";
			$this->db->query($sql, array($input->operator_name, $input->service_type, $_FILES['image']['name'], $this->webspice->get_user_id(), $this->webspice->now()));
			$this->webspice->process_image_single('image', $_FILES['image']['name'], 'card_service_full');
		}
		else {
			$sql = "
			INSERT INTO card_service
			(OPERATOR_NAME, SERVICE_TYPE, CREATED_BY, CREATED_DATE, STATUS)
			VALUES
			( ?, ?, ?, ?, 7 )";
			$this->db->query($sql, array($input->operator_name, $input->service_type, $this->webspice->get_user_id(), $this->webspice->now()));
			$this->webspice->process_image_single('image', $_FILES['image']['name'], 'card_service_full');
		}
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Card service saved successfully!');
		if($this->webspice->permission_verify('manage_card_service',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_card_service');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'create_card_service');

	}

	public function manage_card_service() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_card_service');
		$this->webspice->permission_verify('manage_card_service');
		$this->load->database();
		$orderby = 'ORDER BY card_service.CARD_SERVICE_ID DESC';
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
		SELECT  * FROM card_service ";


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
				$this->webspice->edit_generator($TableName='card_service', $KeyField='CARD_SERVICE_ID', $key, $RedirectController='card_controller', $RedirectFunction='create_card_service', $PermissionName='manage_card_service', $StatusCheck=null, $Log='edit_card_service');
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

			case 'active':
				$this->webspice->action_executer($TableName='card_service', $KeyField='CARD_SERVICE_ID', $key, $RedirectURL='manage_card_service', $PermissionName='manage_card_service', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='card_service', $Log='active_card_service');
				return false;
				break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$image_name = $this->db->query("SELECT LOGO FROM card_service WHERE CARD_SERVICE_ID=".$id)->row()->LOGO;
				// dd($image_name);
				$sql = $this->db->query("DELETE FROM card_service WHERE CARD_SERVICE_ID='".$id."' LIMIT 1");
				if(!unlink($this->webspice->get_path('card_service_full').$image_name)) {
					die($this->webspice->get_path('card_service_full').$image_name);
				}
				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_card_service');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_card_service/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/card_mgmt/manage_card_service', $data);

	}

	public function add_card($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'add_card');
		$this->webspice->permission_verify('add_card');
		if( !isset($data['edit']) ){
			$data['edit'] = array(
				'CARD_TRANS_ID'=>null,
				'CARD_SERVICE_ID'=>null,
				'SERIAL_NO'=>null,
				'PIN_NO'=>null,
				'AMOUNT'=>null,
				'EXPIRE_DATE'=>null
			);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('card_service_id', 'service name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('serial_no', 'serial no', 'required|trim|xss_clean');
		$this->form_validation->set_rules('pin_no', 'pin no', 'required|trim|xss_clean');
		$this->form_validation->set_rules('amount', 'amount', 'required|trim|xss_clean');
		$this->form_validation->set_rules('expire_date', 'expire date', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/card_mgmt/add_card', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('card_trans_id');
		$input->serial_no = $this->webspice->enc($input->serial_no, 'encrypt');
		$input->pin_no = $this->webspice->enc($input->pin_no, 'encrypt');
		$input->amount = $this->webspice->enc($input->amount, 'encrypt');
		$input->expire_date = $this->webspice->enc(date("Y-m-d", strtotime($input->expire_date)), 'encrypt');
		$card_status = 1;
		
		/*
		 * No duplicate test here
		*/

		# update process
		if( $input->card_trans_id ){

			$sql = "
			UPDATE card_transaction SET CARD_SERVICE_ID=?, SERIAL_NO=?, PIN_NO=?, EXPIRE_DATE=?, AMOUNT=?, UPDATED_BY=?,UPDATED_DATE=?
			WHERE CARD_TRANS_ID=?";

			$this->db->query($sql, array($input->card_service_id, $input->serial_no, $input->pin_no, $input->expire_date, $input->amount, $this->webspice->get_user_id(), $this->webspice->now(), $input->card_trans_id));

			$this->webspice->message_board('Card data has been updated!');
			$this->webspice->log_me('card_updated - '.$this->webspice->get_user_id()); # log activities
			$this->webspice->force_redirect($url_prefix.'manage_card');
			return false;
		}
		
		#insert data

		$sql = "
		INSERT INTO card_transaction
		(CARD_SERVICE_ID, SERIAL_NO, PIN_NO, EXPIRE_DATE, CARD_STATUS, AMOUNT, CREATED_BY, CREATED_DATE, STATUS)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
		$this->db->query($sql, array($input->card_service_id, $input->serial_no, $input->pin_no, $input->expire_date, $card_status, $input->amount, $this->webspice->get_user_id(), $this->webspice->now()));
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'admin');
			return false;
		}

		$this->webspice->message_board('Card inserted successfully!');
		if($this->webspice->permission_verify('manage_card',TRUE)){
			$this->webspice->force_redirect($url_prefix . 'manage_card');
			return FALSE;
		}
		$this->webspice->force_redirect($url_prefix.'add_card');

	}

	public function manage_card() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'manage_card');
		$this->webspice->permission_verify('manage_card');
		$this->load->database();
		$orderby = 'ORDER BY ct.CARD_TRANS_ID DESC';
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
		SELECT  ct.*, cs.OPERATOR_NAME, cs.SERVICE_TYPE FROM card_transaction AS ct INNER JOIN card_service AS cs ON ct.CARD_SERVICE_ID=cs.CARD_SERVICE_ID ";


		# filtering records
		if( $this->input->post('filter') ){
			$result = $this->webspice->filter_generator(
				$TableName = 'card_transaction',
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
				$this->webspice->edit_generator($TableName='card_transaction', $KeyField='CARD_TRANS_ID', $key, $RedirectController='card_controller', $RedirectFunction='add_card', $PermissionName='manage_card', $StatusCheck=null, $Log='edit_card');
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
				$this->webspice->action_executer($TableName='card_transaction', $KeyField='CARD_TRANS_ID', $key, $RedirectURL='manage_card', $PermissionName='manage_card', $StatusCheck=7, $ChangeStatus=-7, $RemoveCache='card', $Log='inactive_card');
				return false;
				break;

			case 'active':
				$this->webspice->action_executer($TableName='card_transaction', $KeyField='CARD_TRANS_ID', $key, $RedirectURL='manage_card', $PermissionName='manage_card', $StatusCheck=-7, $ChangeStatus=7, $RemoveCache='flexi', $Log='active_card');
				return false;
				break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM card_transaction WHERE CARD_TRANS_ID='".$id."' LIMIT 1");

				if($sql) {
					$this->webspice->force_redirect($url_prefix.'manage_card');
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'manage_card/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/card_mgmt/manage_card', $data);

	}

	public function available_cards() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'available_cards');
		$this->webspice->permission_verify('available_cards');
		$data = array();
		$data['service_list'] = $this->db->query("SELECT CARD_SERVICE_ID, OPERATOR_NAME, SERVICE_TYPE FROM card_service WHERE STATUS=7")->result();
		$data['amount_list'] = $this->db->query("SELECT DISTINCT AMOUNT FROM card_transaction WHERE STATUS NOT IN(2,3,4)")->result();

		$new_sql = "";
		$sql = "SELECT cs.CARD_SERVICE_ID, cs.OPERATOR_NAME, cs.SERVICE_TYPE, cs.LOGO, ct.CARD_TRANS_ID, ct.SERIAL_NO, ct.EXPIRE_DATE, ct.CARD_STATUS, ct.AMOUNT, ct.EXPIRE_DATE FROM card_transaction AS ct INNER JOIN card_service AS cs ON cs.CARD_SERVICE_ID=ct.CARD_SERVICE_ID";



		if($this->input->post("filter")){

			$card_service_id = $this->input->post("card_service_id");
			$amount = $this->input->post("amount");
			$card_status = $this->input->post("card_status");

			$errors = array();
			if( (empty($card_service_id)  && empty($amount) && empty($card_status)) ) {
				$errors[] = "You must select any one input field to get report";
			}

			if(count($errors)) {
				$data['errors'] = $errors;
				$this->load->view("admin_new/card_mgmt/available_cards", $data);
				return false;
			}

			if($card_service_id || $amount || $card_status) {
				$new_sql .= $sql . " WHERE";
			}

			if($card_service_id && $amount && $card_status) {
				$new_sql .= " ct.CARD_SERVICE_ID='".$card_service_id."' AND ct.AMOUNT='".$amount."' AND ct.CARD_STATUS='".$card_status."'";
			}

			if($card_service_id && $amount && empty($card_status)) {
				$new_sql .= " ct.CARD_SERVICE_ID='".$card_service_id."' AND ct.AMOUNT='".$amount."'";
			}

			if($card_service_id && $card_status && empty($amount)) {
				$new_sql .= " ct.CARD_SERVICE_ID='".$card_service_id."' AND ct.CARD_STATUS='".$card_status."'";
			}

			if($amount && $card_status && empty($card_service_id)) {
				$new_sql .= " ct.AMOUNT='".$amount."' AND ct.CARD_STATUS='".$card_status."'";
			}

			if($card_service_id && empty($amount) && empty($card_status)) {
				$new_sql .= " ct.CARD_SERVICE_ID='".$card_service_id."'";
			}
			
			if($amount && empty($card_service_id) && empty($card_status)) {
				$new_sql .= " ct.AMOUNT='".$amount."'";
			}

			if($card_status && empty($card_service_id) && empty($amount)) {
				$new_sql .= " ct.CARD_STATUS='".$card_status."'";
			}

			$data['get_record'] = $this->db->query($new_sql)->result();

			// dd($data);
			if(count($data['get_record'])) {
				if($card_service_id) {
					$data['service_name'] = $this->webspice->operator_details($card_service_id)->OPERATOR_NAME;
					$data['service_type'] = $this->webspice->operator_details($card_service_id)->SERVICE_TYPE;
				}
				if($amount) {
					$data['amount'] = $this->webspice->enc($amount, 'decrypt');
				}
			}else{
				$errors[] = "Sorry, no results found on your query";

				$data['errors'] = $errors;
				$this->load->view("admin_new/card_mgmt/available_cards", $data);
				return false;
			}

			$this->load->view('admin_new/card_mgmt/available_cards', $data);
			return false;
		}
		else {
			$data['get_record'] = $this->db->query($sql)->result();
			$this->load->view('admin_new/card_mgmt/available_cards', $data);
		}

	}

}