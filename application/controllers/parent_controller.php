<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parent_controller extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
	
	/*
	>> Error log should be added prefix Error:
	Log Prefix:
	login_attempt - Login Ateempt
	login_success
	unauthorized_access
	password_retrieve_request
	password_changed
	*/


	function index(){
		#dd($_SESSION['user']);
		#vms user: echo md5('moin@islam#123');
		
		// $this->load->helper('url');
		// if(isset($_SESSION['user']['USER_TYPE']) && $this->webspice->encrypt_decrypt($_SESSION['user']['USER_TYPE'], 'decrypt') == 'organizational') {
		// 	redirect('dashboard');
		// }
		// else if(isset($_SESSION['user']['USER_TYPE']) && $this->webspice->encrypt_decrypt($_SESSION['user']['USER_TYPE'], 'decrypt') == 'authority') {
		// 	redirect('authority');
		// }
		// else {
		// 	$this->load->view('index');
		// }
		
		$this->load->view('index');
	}

	function login(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$data = null;
		$callback = $url_prefix;
		
		# verify user logged or not
		if( $this->webspice->get_user_id() ){
			$this->webspice->message_board('Dear '.$this->webspice->get_user("USER_NAME").', you are already Logged In. Thank you.');
			$this->webspice->force_redirect($url_prefix);
			return false;
		}
 
		if( $this->webspice->login_callback(null,'get') ){ 
			$callback = $this->webspice->login_callback(null,'get');
		}
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('user_email','user_email','required|trim');
		$this->form_validation->set_rules('user_password','user_password','required|trim');
		
		if( !$this->form_validation->run() ){
			$this->load->view('login', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input($key = null);
		// dd($input);
		
		# more than 5 attempts - lock the last email address with remarks
		if( !isset($_SESSION['auth']['attempt']) ){
			$_SESSION['auth']['attempt'] = 1;
			
		}else{
			$_SESSION['auth']['attempt']++;
			
			if( $_SESSION['auth']['attempt'] >50 ){
				$data['title'] = 'Warning!';
				$data['body'] = 'We have identified that; you are trying to access this application illegally. Please stop the process immediately. We like to remind you that; we are tracing your IP address. So, if you try again, we will bound to take a legal action against you.';
				$data['footer'] = $this->webspice->settings()->site_title.' Authority';
				
				# $this->db->query("UPDATE user SET STATUS=-3, remarks=? WHERE user_email=? AND user_role!=1 LIMIT 1", array('Illegal Attempt ('.$this->webspice->now().'): '.$this->webspice->who_is() , $login_email));
				
				# log
				$this->webspice->log_me('illegal_attempt~'.$this->webspice->who_is().'~'.$input->user_email);
				$this->confirmation($data);
				return false;
			}
		}

		$user = $this->db->query("
		SELECT user.*, 
		role.ROLE_NAME, role.PERMISSION_NAME 
		FROM user
		LEFT JOIN role ON role.ROLE_ID=user.ROLE_ID
		WHERE user.USER_EMAIL ='".$input->user_email."'
		AND user.USER_PASSWORD=?",
		array($this->webspice->encrypt_decrypt($input->user_password, 'encrypt')) 
		);
		$user = $user->result_array();
		
		if( !$user ){
			$this->webspice->log_me('unauthorized_access'); # log
		
			$this->webspice->message_board('User ID or password is incorrect. Please try again.');
			$this->webspice->force_redirect($url_prefix.'login');
			return false;
		}

		#check new user
		if( $user[0]['STATUS'] < 1 ){
			$this->webspice->message_board('Your account is temporarily inactive! Please contact with authority.');
			$this->webspice->force_redirect($url_prefix);
			return false;
			
		}else if( $user[0]['STATUS'] == 6 ){
			$this->webspice->message_board('You must verify your Email Address. We sent you a verification email. Please check your email inbox/spam folder.');
			$this->webspice->force_redirect($url_prefix);
			return false;
			 
		}else if( $user[0]['STATUS'] == 8 ){
			$verification_code = $this->webspice->encrypt_decrypt($user[0]['USER_EMAIL'].'|'.date("Y-m-d"), 'encrypt');
			$this->webspice->message_board('You must change your password.');
			$this->webspice->force_redirect($url_prefix.'change_password/'.$verification_code);
			return false;
		}
		
		# verify password policy
		$this->verify_password_policy($user[0], 'login');

		# create user session
		$this->webspice->create_user_session($user[0]);
		$_SESSION['auth']['attempt'] = 0;
		$this->webspice->message_board('Welcome to '.$this->webspice->settings()->domain_name.'. '.$this->webspice->settings()->site_slogan);
		
		# log
		$this->webspice->log_me('login_success');
		$this->webspice->force_redirect($callback);
	}


	
	function forgot_password(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$this->load->database();
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('user_email','user_email','required|valid_email|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('login', $data);
			return FALSE;
		}
		
		$input = $this->webspice->get_input();
		
		$get_record = $this->db->query("SELECT * FROM user WHERE USER_EMAIL=?", array($input->user_email));
		$get_record = $get_record->result();
		if( !$get_record ){
			$this->webspice->message_board('The email address you entered is invalid! Please enter your email address.');
			$this->load->view('login', $data);
			return false;
		}
		
		$get_record = $get_record[0];

		$this->load->library('email_template');
		$this->email_template->send_retrieve_password_email1($get_record->USER_ID, $get_record->USER_NAME, $get_record->USER_EMAIL);
		
		$data['title'] = 'Request Accepted!!';
		$data['body'] = 'Your request has been accepted! The system sent you an email with a link. Please check your email Inbox or Spam folder. Using the link, you can reset your Password. <br /><br />Please note that; the link will <strong>valid only for following 3 days</strong>. So, please use the link before it will being useless.';
		$data['footer'] = $this->webspice->settings()->site_title.' Authority';
		
		# log
		$this->webspice->log_me('password_retrieve_request - '.$get_record->USER_EMAIL);
			
		$this->confirmation($data);

	}
	
	function change_password($param_user_id=null){
		// dd("Hello World");
		# $param_user_id -> when user's password has been expired
		
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$user_id = null;
		$data = null;
		$this->load->database();

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('new_password','new_password','required|trim|xss_clean');
		$this->form_validation->set_rules('repeat_password','repeat_password','required|trim|xss_clean');
		
		# verify access request through 'Forgot Password' - email URL
		$get_uri = $this->webspice->encrypt_decrypt($this->uri->segment(2), 'decrypt');
	
		$get_link = explode('|', $get_uri);
	
		# verify access request for password expiration
		if( !$this->uri->segment(2) ){
			$param_user_id ? $user_id = $this->webspice->encrypt_decrypt($param_user_id, 'encrypt') : $user_id = $this->input->post('user_id');
		}
		
		# verify the request
		if( isset($get_link[0]) && isset($get_link[1]) && $get_link[0] ){
			$user_id = $get_link[0];
		
			# the link is valid for only 3 days
			if( ((strtotime(date("Y-m-d"))-strtotime($get_link[1]))/86400) >3 ){
				$this->webspice->message_board('Sorry! Invalid link. Your link has been expired. Please send us your request again.');
				
				$this->webspice->force_redirect($url_prefix);
				return false;
			}
			
		}elseif( $user_id ){
			$data['user_id'] = $user_id;
			$user_id = $this->webspice->encrypt_decrypt($user_id, 'decrypt');
		}else{
			# log
			$this->webspice->log_me('unauthorized_access');
			$this->webspice->page_not_found();
			return false;
		}
		
		if( !$this->form_validation->run() ){
			$view = $this->load->view('change_password', $data, true);
			echo $view;
			exit;
		}
			
		# get User and verify the user
		$get_user = $this->db->query("SELECT * FROM user WHERE USER_EMAIL=?", array($user_id))->result();
		if( !$get_user ){
			$this->webspice->page_not_found();
			return false;
		}
		# call verify_password_policy
		$this->verify_password_policy($get_user[0], 'change_password');
	
		# encrypt password
		$new_password = $this->webspice->encrypt_decrypt($this->input->post('new_password'), 'encrypt');

		# generate password history - last 2 password does not allowed as a new password
		$previous_history = array();
		if($get_user[0]->USER_PASSWORD_HISTORY){
			$previous_history = explode(',', $get_user[0]->USER_PASSWORD_HISTORY);
		}
		
		array_unshift($previous_history, $new_password);
		if(count($previous_history) > 2){
			#last 2 password does not allowed as a new password
			array_pop($previous_history);
		}
		
		$password_history = implode(',', $previous_history);
		
		#change status for New user
		$STATUS=$get_user[0]->STATUS;
		if($STATUS ==6 ){
			$STATUS = 7;
			}
			
		# update password
		$update = $this->db->query("UPDATE user SET USER_PASSWORD=?, UPDATED_DATE=?, USER_PASSWORD_HISTORY=?, STATUS=? WHERE USER_EMAIL=?", array($new_password, $this->webspice->now(), $password_history, $STATUS, $user_id));
		if( !$update ){
			# log
			$this->webspice->log_me('error:password_changed');
			$this->webspice->message_board('We could not reset your Password. Please try again later or report to Authority.');
			$this->webspice->force_redirect($url_prefix);
			return false;
		}
		
		# log
		$this->webspice->log_me('password_changed');
		
		# user session destroy
		session_destroy();
		session_start();
		
		$this->webspice->message_board('Your password has been changed! Please login using your new password.');
		$this->webspice->force_redirect($url_prefix.'login');
		
	}
	
	function logout(){
		session_destroy();
		session_start();
		$data['title'] = 'You have been signed out of this account.';
		$data['body'] = 'You have been signed out of this account. To continue using this account, you will need to sign in again.  This is done to protect your account and to ensure the privacy of your information. We hope that, you will come back soon.';
		$data['footer'] = $this->webspice->settings()->domain_name;
		
		$this->webspice->log_me('signed_out'); # log
		
		$this->confirmation($data);
		$this->webspice->force_redirect($this->webspice->settings()->site_url_prefix . 'login');
	}
	
	function verify_password_policy($user, $type){
		# $type can be login or change_password
		$user = (object)$user;
		$exipiry_period = 45;

		if( $type=='login' ){
			$pwd_change_duration = strtotime(date("Y-m-d")) - strtotime($user->UPDATED_DATE);
			$pwd_change_duration = round($pwd_change_duration / ( 3600 * 24 ));

			if( $user->UPDATED_DATE && $pwd_change_duration >= $exipiry_period ){
				$this->webspice->message_board("Your password is too old. Please change your password!");
				$this->change_password($user->USER_ID);
			}
			
		}elseif( $type=='change_password' ){
			$password = $this->input->post('new_password');
			$message = null;
			
			# minimum 8 charecters
			if( strlen($password) < 8 ){
				$message .= '- Password must be minimum 8 characters<br />';
			}
			
			# must have at least one capital letter, one small letter, one digit and one special character
			$containsCapitalLetter  = preg_match('/[A-Z]/', $password);
			$containsSmallLetter  = preg_match('/[a-z]/', $password);
			$containsDigit   = preg_match('/\d/', $password);
			$containsSpecial = preg_match('/[^a-zA-Z\d]/', $password);
			
			$containsAll = $containsCapitalLetter && $containsSmallLetter && $containsDigit && $containsSpecial;
			if( !$containsAll ){
				$message .= '- Password must have at least one Capital Letter<br />- Password must have at least one Small Letter<br />- Password must have at least one Digit<br />- Password must have at least one Special Character';
			}
			
			# password history verify - not allowed last 2 password
			$password_history = $user->USER_PASSWORD_HISTORY;
			if($password_history){
				$password_history = explode(',', $password_history);
				foreach($password_history as $k=>$v){
					if( $password == $this->webspice->encrypt_decrypt($v,'decrypt') ){ 
						$message .= '- You are not allowed to use your last 2 password'; 
					}
				}
				
			}
			
			# if policy breaks
			if( $message ){
				$this->webspice->message_board('<span class="stitle"><strong>You must maintain the following password policy(s):</strong><br />'.$message.'</span>');
				
				$data['user_id'] = $this->webspice->encrypt_decrypt($user->USER_EMAIL, 'encrypt');
				
				$view = $this->load->view('change_password', $data, true);
				echo $view;	
				exit;
			}

			return true;
			
		} # end if
		
	}





	/*********************************************
	*********start ajax data load functions*******
	**********************************************/


	public function teacher_salary_check() {

		$teacher_id = $this->input->post('teacher_id');
		$select_sql= "SELECT MONTH FROM account_transaction WHERE TEACHER_ID=? AND DEBIT=220153 ORDER BY CREATED_DATE DESC LIMIT 1";
		$last_month = $this->db->query($select_sql,array($teacher_id))->row();

        $select="";
		switch ($last_month->MONTH) {
			case 'January':
				$select="<option value='February'>February</option><option value='March'>March</option><option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'February':
				$select="<option value='March'>March</option><option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'March':
				$select="<option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'April':
				$select="<option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'May':
				$select="<option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'June':
				$select="<option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'July':
				$select="<option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'August':
				$select="<option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'September':
				$select="<option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'October':
				$select="<option value='November'>November</option><option value='December'>December</option>";
				break;
			
			case 'November':
				$select="<option value='December'>December</option>";
				break;
			case 'December':
				//$select="<option value='January'>January</option><option value='February'>February</option><option value='March'>March</option><option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			
			default:
				$select="<option value='January'>January</option><option value='February'>February</option><option value='March'>March</option><option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
		}
		
		echo $select;
		exit;


	}

	public function student_salary_check() {

		$student_id = $this->input->post('student_id');
		$select_sql= "SELECT MONTH FROM account_transaction WHERE TEACHER_ID=? AND DEBIT=220153 ORDER BY CREATED_DATE DESC LIMIT 1";
		$last_month = $this->db->query($select_sql,array($student_id))->row();

        $select="";
		switch ($last_month->MONTH) {
			case 'January':
				$select="<option value='February'>February</option><option value='March'>March</option><option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'February':
				$select="<option value='March'>March</option><option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'March':
				$select="<option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'April':
				$select="<option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'May':
				$select="<option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'June':
				$select="<option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'July':
				$select="<option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'August':
				$select="<option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'September':
				$select="<option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			case 'October':
				$select="<option value='November'>November</option><option value='December'>December</option>";
				break;
			
			case 'November':
				$select="<option value='December'>December</option>";
				break;
			case 'December':
				//$select="<option value='January'>January</option><option value='February'>February</option><option value='March'>March</option><option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
			
			default:
				$select="<option value='January'>January</option><option value='February'>February</option><option value='March'>March</option><option value='April'>April</option><option value='May'>May</option><option value='June'>June</option><option value='July'>July</option><option value='August'>August</option><option value='September'>September</option><option value='October'>October</option><option value='November'>November</option><option value='December'>December</option>";
				break;
		}
		
		echo $select;
		exit;

	}


	public function class_wise_section_list() {

		$class_id = $this->input->post('class_id');
		$select_sql= "SELECT s.SECTION_ID, s.SECTION_NAME, c.CLASS_NAME FROM section AS s INNER JOIN class AS c ON s.CLASS_ID = c.CLASS_ID WHERE c.CLASS_ID=?";
		$section_list = $this->db->query($select_sql,array($class_id))->result();

		// dd($section_list);

		$option_menu ='<option value="">--Select One--</option>';
		if(	$section_list	)
		{
			foreach($section_list as $list)
			{
				$option_menu.='<option value='.$list->SECTION_ID.'>'.$list->CLASS_NAME.': ' . $list->SECTION_NAME . '</option>';
			}

			echo $option_menu;
			exit;

		}
		
		echo $option_menu;
		exit;

	}

	public function group_wise_head_name() {
		$company_id = $this->webspice->encrypt_decrypt($this->webspice->get_user()['COMPANY_ID'], 'decrypt');
		$group_name = $this->input->post('group_name');
		$sub_list = $this->db->query("SELECT * FROM account_name WHERE GROUP_HEAD_NAME LIKE '%".$group_name."%' AND GROUP_HEAD_NAME <>'' AND COMPANY_ID='".$company_id."'")->result();
		// dd($select_sql);
		$option_menu = '<option value="">--Select One--</option>';
		if(count($sub_list)) {
			foreach($sub_list as $list) {
				// $option_menu .= '<option value='.$list->ACC_ID.'>'.$list->ACC_ID.': ' . $list->ACC_NAME . '</option>';
				$option_menu .= '<option value="'.$list->ACC_NAME.'">' . $list->ACC_NAME . '</option>';
			}
			echo $option_menu;
			exit();
		}

		echo $option_menu;
		exit();
	}

	public function house_wise_student_list() {

		$house_id = $this->input->post('house_id');
		$select_sql= "SELECT SI.NAME, SD.STUDENT_DATA_ID, SD.CLASS_ID, SD.STUDENT_ID FROM student_info AS SI INNER JOIN student_data AS SD ON SD.STUDENT_ID = SI.STUDENT_ID INNER JOIN admit_student_to_hostel AS ASTH ON ASTH.STUDENT_DATA_ID=SD.STUDENT_DATA_ID WHERE ASTH.HOUSE_ID='".$house_id."'";
		$student_list = $this->db->query($select_sql,array($house_id))->result();

		// dd($section_list);

		$option_menu ='<option value="">--Select One--</option>';
		if(	$student_list	)
		{
			foreach($student_list as $list)
			{
				$option_menu.='<option value='.$list->STUDENT_DATA_ID.'>'.$list->NAME.'</option>';
			}

			echo $option_menu;
			exit;

		}
		
		echo $option_menu;
		exit;

	}
	
	public function teacher_wise_salary_list() {

		$teacher_id = $this->input->post('teacher_id');
		$select_sql= "SELECT TS.SALARY FROM salary_settings AS TS WHERE TS.TEACHER_ID='".$teacher_id."' AND TS.YEAR='".date("Y")."'";
		$teacher_list = $this->db->query($select_sql,array($teacher_id))->result();

		//dd($teacher_list);

		//$option_menu ='<option value="">--Select One--</option>';
		if(	$teacher_list	)
		{
			foreach($teacher_list as $list)
			{
				$option_menu.='<option value='.$list->SALARY.'>'.$list->SALARY.'</option>';
			}

			echo $option_menu;
			exit;

		}
		
		echo $option_menu;
		exit;

	}

	public function class_wise_subject_list() {

		$class_id = $this->input->post('class_id');
		$select_sql= "SELECT s.SUBJECT_ID, s.SUBJECT_NAME, c.CLASS_NAME, c.CLASS_ID FROM subject AS s INNER JOIN class AS c ON s.CLASS_ID = c.CLASS_ID WHERE c.CLASS_ID=?";
		$section_list = $this->db->query($select_sql,array($class_id))->result();

		// dd($section_list);

		$option_menu ='<option value="">--Select One--</option>';
		if(	$section_list	)
		{
			foreach($section_list as $list)
			{
				$option_menu.='<option value='.$list->SUBJECT_ID.'>'.$list->CLASS_NAME.': ' . $list->SUBJECT_NAME . '</option>';
			}

			echo $option_menu;
			exit;

		}
		
		echo $option_menu;
		exit;

	}

	public function class_wise_student_list() {

		$class_id = $this->input->post('class_id');
		$select_sql= "SELECT SI.NAME, SD.STUDENT_DATA_ID, SD.CLASS_ID, SD.STUDENT_ID FROM student_info AS SI INNER JOIN student_data AS SD ON SD.STUDENT_ID = SI.STUDENT_ID WHERE SD.CLASS_ID=?";
		$section_list = $this->db->query($select_sql,array($class_id))->result();

		// dd($section_list);

		$option_menu ='<option value="">--Select One--</option>';
		if(	$section_list	)
		{
			foreach($section_list as $list)
			{
				$option_menu.='<option value='.$list->STUDENT_DATA_ID.'>'.$list->NAME.'</option>';
			}

			echo $option_menu;
			exit;

		}
		
		echo $option_menu;
		exit;

	}

	public function section_wise_student_list() {

		$section_id = $this->input->post('section_id');
		$select_sql= "SELECT SI.NAME, SI.PUBLIC_ID, SD.STUDENT_DATA_ID, SD.CLASS_ID, SD.STUDENT_ID FROM student_info AS SI INNER JOIN student_data AS SD ON SD.STUDENT_ID = SI.STUDENT_ID WHERE SD.SECTION_ID=?";
		$section_list = $this->db->query($select_sql,array($section_id))->result();

		// dd($section_list);

		$option_menu ='<option value="">--Select One--</option>';
		if(	$section_list	)
		{
			foreach($section_list as $list)
			{
				$option_menu.='<option value='.$list->STUDENT_DATA_ID.'>'.$list->NAME.' ( ' . $list->PUBLIC_ID . ' )</option>';
			}

			echo $option_menu;
			exit;

		}
		
		echo $option_menu;
		exit;

	}

	public function section_wise_student_list_acc() {

		$section_id = $this->input->post('section_id');
		$select_sql= "SELECT SI.NAME, SI.STUDENT_ID, SI.PUBLIC_ID, SD.STUDENT_DATA_ID, SD.CLASS_ID, SD.STUDENT_ID FROM student_info AS SI INNER JOIN student_data AS SD ON SD.STUDENT_ID = SI.STUDENT_ID WHERE SD.SECTION_ID=?";
		$section_list = $this->db->query($select_sql,array($section_id))->result();

		// dd($section_list);

		$option_menu ='<option value="">--Select One--</option>';
		if(	$section_list	)
		{
			foreach($section_list as $list)
			{
				$option_menu.='<option value='.$list->STUDENT_ID.'>'.$list->NAME.' ( ' . $list->PUBLIC_ID . ' )</option>';
			}

			echo $option_menu;
			exit;

		}
		
		echo $option_menu;
		exit;

	}

	public function class_wise_payment_list() {

		$class_id = $this->input->post('class_id');
		$select_sql= "SELECT p.PAYMENT_CAT_ID, p.CATEGORY_NAME, c.CLASS_NAME FROM payment_category AS p INNER JOIN class AS c ON p.CLASS_ID = c.CLASS_ID WHERE p.CLASS_ID=?";
		$section_list = $this->db->query($select_sql,array($class_id))->result();

		// dd($section_list);

		$option_menu ='<option value="">--Select One--</option>';
		if(	$section_list	)
		{
			foreach($section_list as $list)
			{
				$option_menu.='<option value='.$list->PAYMENT_CAT_ID.'>'.$list->CLASS_NAME . ': ' . $list->CATEGORY_NAME.'</option>';
			}

			echo $option_menu;
			exit;

		}
		
		echo $option_menu;
		exit;

	}


	public function student_list_search() {

		$keyword = $this->input->post('keyword');
		$student_list = $this->db->query("SELECT * FROM student_info WHERE NAME LIKE '".$keyword."%' ORDER BY NAME")->result();

		// dd($section_list);

		
		if($student_list)
		{
			$option_menu ='<ul>';
			foreach($student_list as $list)
			{
				// $option_menu.='<option value='.$list->STUDENT_ID.'>'.$list->NAME . '</option>';
				// $option_menu.='<li onclick="selectCountry("'. $list->NAME .'")">'.$list->NAME . '</li>';

				$option_menu .= "<li onclick=selectCountry('".$list->NAME."')>";
					$option_menu .= $list->NAME;
				$option_menu .= "</li>";

			}
			$option_menu .= '</ul>';

			echo $option_menu;
			exit;

		}
		
		echo $option_menu;
		exit;

	}

	public function search_account_head() {
		$keyword = $this->input->post("keyword");
		$id_name = $this->input->post("id_name");

		// dd($id_name);

		if(!empty($keyword)) {

			$account_list = $this->db->query("SELECT * FROM account_name WHERE ACC_NAME LIKE '".$keyword."%' ORDER BY ACC_NAME")->result();

			// dd($account_list);

			
			if(count($account_list))
			{
				/*$option_menu ='<ul class="journal_ul">';
				foreach($account_list as $list)
				{

					$option_menu .= '<li class="journal_li" onclick="selectAccount("abba");">';
						$option_menu .= $list->ACC_NAME;
					$option_menu .= '</li>';

				}
				$option_menu .= '</ul>';

				echo $option_menu;
				exit;
				*/

				?>
					<ul class="journal_ul">

						<?php foreach($account_list as $list) : ?>
							<?php if($id_name == "search-box") { ?>
								<li class="journal_li" onClick="selectAccount('<?php echo $list->ACC_NAME; ?>');"><?php echo $list->ACC_NAME; ?></li>
							<?php } else { ?>
								<li class="journal_li" onClick="selectAccount2('<?php echo $list->ACC_NAME; ?>');"><?php echo $list->ACC_NAME; ?></li>
							<?php } ?>
						<?php endforeach; ?>

					</ul>
				<?php
			}
			else {
				// $option_menu = null;
			}
			
			// echo $option_menu;
			exit;

		}

	}




	/*********************************************
	********* end ajax data load functions********
	**********************************************/



	//call confirmation for redirect another url with message
	function confirmation($message){
		$_SESSION['confirmation'] = $message;
		$this->webspice->force_redirect($this->webspice->settings()->site_url_prefix.'confirmation');
	}
	function show_confirmation(){
		if( !isset($_SESSION['confirmation']) ){
			$_SESSION['confirmation'] = array();	
		}
		$data = $_SESSION['confirmation'];
		$this->load->view('view_message',$data);
	}

	#get district list of a division


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */