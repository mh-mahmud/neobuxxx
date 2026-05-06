<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registration_controller extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}

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

	public function new_registration() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'new_registration');
		$this->webspice->permission_verify('new_registration');
		$this->load->database();
		$orderby = 'ORDER BY CREATED_DATE DESC';
		$groupby = null;
		$where = ' WHERE CREATED_DATE BETWEEN "'.date("Y-m-d").'" AND "'.date("Y-m-d").'" ';
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
		SELECT  * FROM user_registration ";


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

			case 'active':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$user_data = $this->db->query("SELECT * FROM user_registration WHERE USER_REG_ID='".$id."'")->row();

				# var setup
				$role_id = 2;
				$user_reg_id = $id;
				$user_type = "User";
				$user_password = $user_data->INIT;
				$user_name = $user_data->FIRST_NAME . ' ' . $user_data->LAST_NAME;
				$email = $user_data->EMAIL;
				$mobile = $user_data->MOBILE;

				// inser user data
				$sql2 = "
				INSERT INTO user
				(ROLE_ID, USER_REG_ID, USER_NAME, USER_EMAIL, USER_PHONE, USER_TYPE, USER_PASSWORD, CREATED_DATE, STATUS)
				VALUES
				( ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
				$this->db->query($sql2, array($role_id, $user_reg_id, $user_name, $email, $mobile, $user_type, $user_password, $this->webspice->now()));

				// update user_registration field status
				$this->db->query("UPDATE user_registration SET STATUS=7 WHERE USER_REG_ID='".$id."'");

				// redirect to new_registration panel
				$this->webspice->message_board("Account activated successfully");
				$this->webspice->force_redirect($url_prefix.'new_registration');
				return false;
			break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM user_registration WHERE USER_REG_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board("Account deleted successfully");
					$this->webspice->force_redirect($url_prefix.'new_registration');
				}
				else {
					die("Can not deleted. Server problem");
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'new_registration/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/registration/new_registration', $data);

	}

	public function non_premium_user() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'non_premium_user');
		$this->webspice->permission_verify('non_premium_user');
		$this->load->database();
		$orderby = 'ORDER BY CREATED_DATE DESC';
		$groupby = null;
		$where = ' WHERE ACC_STATUS=0 AND STATUS=7 ';
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
		SELECT  * FROM user_registration ";


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

			case 'active':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$user_data = $this->db->query("SELECT * FROM user_registration WHERE USER_REG_ID='".$id."'")->row();

				# var setup
				$role_id = 2;
				$user_reg_id = $id;
				$user_type = "User";
				$user_password = $user_data->INIT;
				$user_name = $user_data->FIRST_NAME . ' ' . $user_data->LAST_NAME;
				$email = $user_data->EMAIL;
				$mobile = $user_data->MOBILE;

				// inser user data
				$sql2 = "
				INSERT INTO user
				(ROLE_ID, USER_REG_ID, USER_NAME, USER_EMAIL, USER_PHONE, USER_TYPE, USER_PASSWORD, CREATED_DATE, STATUS)
				VALUES
				( ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
				$this->db->query($sql2, array($role_id, $user_reg_id, $user_name, $email, $mobile, $user_type, $user_password, $this->webspice->now()));

				// update user_registration field status
				$this->db->query("UPDATE user_registration SET STATUS=7 WHERE USER_REG_ID='".$id."'");

				// redirect to new_registration panel
				$this->webspice->message_board("Account activated successfully");
				$this->webspice->force_redirect($url_prefix.'new_registration');
				return false;
			break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM user_registration WHERE USER_REG_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board("Account deleted successfully");
					$this->webspice->force_redirect($url_prefix.'non_premium_user');
				}
				else {
					die("Can not deleted. Server problem");
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'non_premium_user/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/registration/non_premium_user', $data);

	}

	public function premium_user() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'premium_user');
		$this->webspice->permission_verify('premium_user');
		$this->load->database();
		$orderby = 'ORDER BY CREATED_DATE DESC';
		$groupby = null;
		$where = ' WHERE ACC_STATUS=1 AND STATUS=7 ';
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
		SELECT  * FROM user_registration ";


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

			case 'active':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$user_data = $this->db->query("SELECT * FROM user_registration WHERE USER_REG_ID='".$id."'")->row();

				# var setup
				$role_id = 2;
				$user_reg_id = $id;
				$user_type = "User";
				$user_password = $user_data->INIT;
				$user_name = $user_data->FIRST_NAME . ' ' . $user_data->LAST_NAME;
				$email = $user_data->EMAIL;
				$mobile = $user_data->MOBILE;

				// inser user data
				$sql2 = "
				INSERT INTO user
				(ROLE_ID, USER_REG_ID, USER_NAME, USER_EMAIL, USER_PHONE, USER_TYPE, USER_PASSWORD, CREATED_DATE, STATUS)
				VALUES
				( ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
				$this->db->query($sql2, array($role_id, $user_reg_id, $user_name, $email, $mobile, $user_type, $user_password, $this->webspice->now()));

				// update user_registration field status
				$this->db->query("UPDATE user_registration SET STATUS=7 WHERE USER_REG_ID='".$id."'");

				// redirect to new_registration panel
				$this->webspice->message_board("Account activated successfully");
				$this->webspice->force_redirect($url_prefix.'new_registration');
				return false;
			break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM user_registration WHERE USER_REG_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board("Account deleted successfully");
					$this->webspice->force_redirect($url_prefix.'premium_user');
				}
				else {
					die("Can not deleted. Server problem");
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'premium_user/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/registration/premium_user', $data);

	}

	public function pending_registration() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'pending_registration');
		$this->webspice->permission_verify('pending_registration');
		$this->load->database();
		$orderby = 'ORDER BY CREATED_DATE DESC';
		$groupby = null;
		$where = ' WHERE STATUS=0 ';
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
		SELECT  * FROM user_registration ";


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

			case 'active':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$user_data = $this->db->query("SELECT * FROM user_registration WHERE USER_REG_ID='".$id."'")->row();

				# var setup
				$role_id = 2;
				$user_reg_id = $id;
				$user_type = "User";
				$user_password = $user_data->INIT;
				$user_name = $user_data->FIRST_NAME . ' ' . $user_data->LAST_NAME;
				$email = $user_data->EMAIL;
				$mobile = $user_data->MOBILE;

				// inser user data
				$sql = "
				INSERT INTO user
				(ROLE_ID, USER_REG_ID, USER_NAME, USER_EMAIL, USER_PHONE, USER_TYPE, USER_PASSWORD, CREATED_DATE, STATUS)
				VALUES
				( ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
				$this->db->query($sql, array($role_id, $user_reg_id, $user_name, $email, $mobile, $user_type, $user_password, $this->webspice->now()));
				$user_id = $this->db->insert_id();

				// insert data to package_data
				$package_data = $this->db->query("SELECT PACKAGE_ID, PACKAGE_VALIDITY, PACKAGE_AMOUNT FROM package_setup WHERE PACKAGE_ID=(SELECT PACKAGE_ID FROM pin_code WHERE PIN_ID=(SELECT PIN_ID FROM user_registration WHERE USER_REG_ID='{$id}'))")->row();
				$package_id = count($package_data) ? $package_data->PACKAGE_ID : 0;
				$validity = count($package_data) ? $package_data->PACKAGE_VALIDITY : 30;
				$package_amount = count($package_data) ? $package_data->PACKAGE_AMOUNT : 0;
				$expire_date = date("Y-m-d", strtotime("+{$validity} days"));
				
				$sql2 = "
				INSERT INTO package_data
				(PACKAGE_ID, USER_ID, ACTIVATION_DATE, EXPIRE_DATE)
				VALUES
				( ?, ?, ?, ? )";
				$this->db->query($sql2, array($package_id, $user_id, $this->webspice->now(), $expire_date));

				// add refer balance to level user
				$all_reffer = array(
					$user_data->LEVEL_1,
					$user_data->LEVEL_2,
					$user_data->LEVEL_3,
					$user_data->LEVEL_4,
					$user_data->LEVEL_5,
					$user_data->LEVEL_6,
					$user_data->LEVEL_7,
					$user_data->LEVEL_8,
					$user_data->LEVEL_9,
					$user_data->LEVEL_10,
					$user_data->LEVEL_11,
					$user_data->LEVEL_12,
					$user_data->LEVEL_13,
					$user_data->LEVEL_14,
				);
				$all_reffer = array_values(array_filter($all_reffer));
				$count_all_reffer = count($all_reffer);
				$lvl_comission = $this->db->query("SELECT LEVEL_ONE, LEVEL_TWO, LEVEL_THREE, LEVEL_FOUR, LEVEL_FIVE, LEVEL_SIX, LEVEL_SEVEN, LEVEL_EIGHT, LEVEL_NINE, LEVEL_TEN, LEVEL_ELEVEN, LEVEL_TWELVE, LEVEL_THIRTEEN, LEVEL_FOURTEEN FROM settings")->row();
				$lvl_data = array();
				$i=0;
				foreach($lvl_comission as $lvl_val) {
					if($i<$count_all_reffer) {
						$lvl_data[] = $lvl_val;
					}
					$i++;
				}

				// insert balance
				$balance_type = "GET";
				$reason = "Got reffre bonus for joining " . $this->webspice->admin_name($user_id);
				$trans_date = $this->webspice->now();
				$trans_status = 2;
				$created_date = $trans_date;
				$sql3 = "
				INSERT INTO refer_wallet
				(USER_ID, BALANCE_TYPE, AMOUNT, REASON, TRANS_DATE, PROVIDER_ID, TRANS_STATUS, CREATED_DATE)
				VALUES
				( ?, ?, ?, ?, ?, ?, ?, ? )";
				for($j=0; $j<$count_all_reffer; $j++) {
					// ommit percent value in refer income
					// $reffre_amount = $package_amount*($lvl_data[$j]/100);
					$reffre_amount = $lvl_data[$j];

					$this->db->query($sql3, array(
						$all_reffer[$j],
						$balance_type,
						$reffre_amount,
						$reason,
						$trans_date,
						$user_id,
						$trans_status,
						$created_date
					));
				}

				// update user_registration field status
				$this->db->query("UPDATE user_registration SET STATUS=7, REFFER_PAY_STATUS=1, ACC_STATUS=1 WHERE USER_REG_ID='".$id."'");

				// redirect to new_registration panel
				$this->webspice->message_board("Account activated successfully & paid all reffer user comission");
				$this->webspice->force_redirect($url_prefix.'new_registration');
				return false;
			break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM user_registration WHERE USER_REG_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board("Account deleted successfully");
					$this->webspice->force_redirect($url_prefix.'pending_registration');
				}
				else {
					die("Can not deleted. Server problem");
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'pending_registration/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/registration/pending_registration', $data);

	}

	public function ban_user() {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->webspice->user_verify($url_prefix.'login', $url_prefix.'ban_user');
		$this->webspice->permission_verify('ban_user');
		$this->load->database();
		$orderby = 'ORDER BY CREATED_DATE DESC';
		$groupby = null;
		$where = ' WHERE STATUS=7 AND ACC_STATUS=3 ';
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
		SELECT  * FROM user_registration ";


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

			case 'active':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$user_data = $this->db->query("SELECT * FROM user_registration WHERE USER_REG_ID='".$id."'")->row();

				# var setup
				$role_id = 2;
				$user_reg_id = $id;
				$user_type = "User";
				$user_password = $user_data->INIT;
				$user_name = $user_data->FIRST_NAME . ' ' . $user_data->LAST_NAME;
				$email = $user_data->EMAIL;
				$mobile = $user_data->MOBILE;

				// inser user data
				$sql2 = "
				INSERT INTO user
				(ROLE_ID, USER_REG_ID, USER_NAME, USER_EMAIL, USER_PHONE, USER_TYPE, USER_PASSWORD, CREATED_DATE, STATUS)
				VALUES
				( ?, ?, ?, ?, ?, ?, ?, ?, 7 )";
				$this->db->query($sql2, array($role_id, $user_reg_id, $user_name, $email, $mobile, $user_type, $user_password, $this->webspice->now()));

				// update user_registration field status
				$this->db->query("UPDATE user_registration SET STATUS=7 WHERE USER_REG_ID='".$id."'");

				// redirect to new_registration panel
				$this->webspice->message_board("Account activated successfully");
				$this->webspice->force_redirect($url_prefix.'new_registration');
				return false;
			break;

			case 'delete':
				$id = $this->webspice->encrypt_decrypt($key, 'decrypt');
				$sql = $this->db->query("DELETE FROM user_registration WHERE USER_REG_ID='".$id."' LIMIT 1");
				if($sql) {
					$this->webspice->message_board("Account deleted successfully");
					$this->webspice->force_redirect($url_prefix.'ban_user');
				}
				else {
					die("Can not deleted. Server problem");
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
			$data['pager'] = $this->webspice->pager( count($count_data), $no_of_record, $page_index, $url_prefix.'ban_user/page/', 10 );	
		}

		$_SESSION['sql'] = $sql;
		$_SESSION['filter_by'] = $filter_by;
		$result = $this->db->query($sql)->result();

		$data['get_record'] = $result;
		$data['filter_by'] = $filter_by;

		$this->load->view('admin_new/registration/ban_user', $data);

	}

}