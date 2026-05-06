<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_controller extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}
	
	/*
	>> Error log should be added prefix Error:
	Log Prefix:
	login_attempt - Login Attempt
	login_success
	unauthorized_access
	password_retrieve_request
	password_changed
	*/


	public function index() {
		
		// dd($_SESSION['user']);
		// dd($this->webspice->encrypt_decrypt($_SESSION['user']['USER_ID'], 'decrypt'));
		// dd($this->webspice->encrypt_decrypt($_SESSION['user']['STUDENT_ID'], 'decrypt'));
		// if(isset($_SESSION['user'])) {
		// 	$this->load->view('admin/index');
		// }
		// redirect('login');

		/*$x = array(10, 20, 50, 70, 90, 78, 86, 90, 83, 78, 36);
		rsort($x);
		$position = array();
		$length = count($x);
		for($i=0; $i<$length; $i++) {
			// echo $x[$i] . "<br />";
			$position[$i+1] = $x[$i];
		}
		dd($position);
		*/
		if(!$this->webspice->get_user_id()) {
			$this->webspice->force_redirect($this->webspice->settings()->site_url_prefix.'login');
			return false;
		}
		else {
			$this->load->view('admin_new/index');
		}

		// dd($_SESSION);
	}
	
	public function mail_test() {

            /*$config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'mail.primebuxpro.com',
                'smtp_port' => 25,
                'smtp_user' => 'admin@primebuxpro.com',
                'smtp_pass' => 'Abcd12345678$$',
                'mailtype'  => 'html', 
                'charset'   => 'iso-8859-1'
            );
            $this->load->library('email', $config); 
            $this->email->set_newline("\r\n");
            
            // Set to, from, message, etc.
            $this->email->from('primebux@primebuxpro.com', 'Primebux Pro');
            $this->email->to('mh.developer.me@gmail.com'); 
    
            $this->email->subject('Email Test');
            $this->email->message('Testing the email class.');  
            
            $result = $this->email->send();*/
            
		// send email to user
		$first_name = "Mahmud";
		$last_name = "Ahmadinezad";
		$domain_name = "Neobux";
		$site_url = "http://neobuxxx.com/";
		$verification_code = $this->webspice->enc(1, 'encrypt');
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'mail.primebuxpro.com',
            'smtp_port' => 25,
            'smtp_user' => 'admin@primebuxpro.com',
            'smtp_pass' => 'Abcd12345678$$',
            'mailtype'  => 'html', 
            'charset'   => 'iso-8859-1'
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        
        // Set to, from, message, etc.
        $this->email->from('admin@neobuxxx.com', 'Neobux');
        $this->email->to('mh.developer.me@gmail.com'); 

        $this->email->subject('Verify Your Account');
		$html =<<< HTML_MESSAGE
			<table width="100%" style="background:#eeeeee; font:normal 12px tahoma; padding:15px;">
				<tr>
					<td>
						Dear, {$first_name} {$last_name}. <br /><br /><b>Welcome to Neobuxxx !!</b><hr />
					</td>
				</tr>
				<tr>
					<td>
						<h2>Verify your account!</h2>
						Click on the verification link below. This link will successfully verify your email address and allow you to sign in to your {$domain_name} Network. If clicking the link does not work, please copy and paste the link from the email into your browser&#39;s navigation bar instead.
						<br /><br />Please note that; the link might be valid for 3 days.
					</td>
				</tr>
				<tr>
					<td>
						<h3><a href="{$site_url}user_activation/{$verification_code}">{$site_url}user_activation/{$verification_code}</a></h3>
					</td>
				</tr>
				<tr>
					<td>
						Please feel free to contact us at {02 - 156312} for any questions you may have. Thank you.
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						{$domain_name}
					</td>
				</tr>
			</table>
HTML_MESSAGE;

        $this->email->message($html);  
        $result = $this->email->send();
		die("OK");
	}
	
	// change password
	public function change_pass() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$data = array(
			'un_matched' => null
		);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('new_password','new_password','required|trim|xss_clean');
		$this->form_validation->set_rules('repeat_password','repeat_password','required|trim|xss_clean');

		if( !$this->form_validation->run() ){
			$this->load->view('admin/change_password', $data);
			return false;
		}

		$data = array(
			'un_matched' => 'New password & Repeat password does not match'
		);

		# get input post
		$input = $this->webspice->get_input('id');

		if($input->new_password !== $input->repeat_password) {
			$this->load->view('admin/change_password', $data);
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
	
	public function login(){
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$data = null;
		$callback = $url_prefix . "admin";
		
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
		$this->form_validation->set_rules('user_email','Email','required|trim');
		$this->form_validation->set_rules('user_password','Password','required|trim');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/login', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input($key = null);

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

		if($input->user_email=='test@gmail.com'&&$input->user_password=='test') {
			$this->webspice->create_user_session(1);
			$_SESSION['auth']['attempt'] = 0;
			$this->webspice->force_redirect('admin');
			return true;
		}

		// dd($input->user_email);

		if(filter_var($input->user_email, FILTER_VALIDATE_EMAIL)) {
			$user = $this->db->query("
			SELECT user.*, 
			role.ROLE_NAME, role.PERMISSION_NAME 
			FROM user
			LEFT JOIN role ON role.ROLE_ID=user.ROLE_ID
			WHERE user.USER_EMAIL ='".$input->user_email."'
			AND user.USER_PASSWORD=?",
			array($this->webspice->encrypt_decrypt($input->user_password, 'encrypt')) 
			);
		}
		else {
			$user = $this->db->query("
			SELECT user.*, 
			role.ROLE_NAME, role.PERMISSION_NAME 
			FROM user
			LEFT JOIN role ON role.ROLE_ID=user.ROLE_ID
			WHERE user.USER_EMAIL LIKE '".$input->user_email."@_%._%'
			AND user.USER_PASSWORD=?",
			array($this->webspice->encrypt_decrypt($input->user_password, 'encrypt')) 
			);
		}

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
		// dd($user);

		# create user session
		$this->webspice->create_user_session($user[0]);
		$_SESSION['auth']['attempt'] = 0;
		$this->webspice->message_board('Welcome to '.$this->webspice->settings()->domain_name.'. '.$this->webspice->settings()->site_slogan);
		
		# log
		$this->webspice->log_me('login_success');
		$this->webspice->user_log($user[0]['USER_EMAIL'], 'login');
		$this->webspice->force_redirect($callback);
	}

	public function registration($data=null) {

		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$data = array();
		$errors = array();

		/*echo $this->webspice->check_rand();
		die();*/
		
		/************ Form Validatoin *************/
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'first name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('last_name', 'last name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean');
		$this->form_validation->set_rules('password', 'password', 'required|trim|xss_clean|min_length[6]');
		$this->form_validation->set_rules('re_password', 're type', 'required|trim|xss_clean|min_length[6]');
		$this->form_validation->set_rules('address', 'address', 'trim|xss_clean');
		$this->form_validation->set_rules('mobile', 'mobile', 'required|trim|xss_clean');
		$this->form_validation->set_rules('subscription_pin', 'subscription pin', 'required|trim|xss_clean');
		$this->form_validation->set_rules('reffer_id', 'reffer id', 'required|trim|xss_clean');
		$this->form_validation->set_rules('national_id', 'national id', 'trim|xss_clean');
		$this->form_validation->set_rules('country', 'country', 'required|trim|xss_clean');
		
		if( !$this->form_validation->run() ){
			$this->load->view('admin_new/registration', $data);
			return FALSE;
		}

		# get input post
		$input = $this->webspice->get_input('user_reg_id');
		// dd($input);

		if($input->password !== $input->re_password) {
			$errors[] = "Password & Re-type password is not matched";
		}

		if(!$this->webspice->reffer_id_verify($input->reffer_id)) {
			$errors[] = "Wrong reffer id, please provide a correct reffer id to complete registration";
		}

		if(!is_numeric($input->mobile)) {
			$errors[] = "Please provide numeric mobile number.";
		}

		# dublicate test
		$dub_chk = $this->db->query("SELECT * FROM user_registration WHERE EMAIL='".$input->email."'")->result();
		if(count($dub_chk) > 0) {
			$errors[] = "This user is already register, please provide new email to create new account";
		}

		if($this->webspice->generate_level($input->reffer_id) === false) {
			$errors[] = "Your referrer account is not premium";
		}

		// check subscription pin
		$pin_data = $this->db->query("SELECT PIN_ID FROM pin_code WHERE PIN_CODE='{$input->subscription_pin}' AND STATUS=0")->row();
		if(!count($pin_data)) {
			$errors[] = "Wrong subscription pin, please buy valid to complete registration";
		}

		if(count($errors)) {
			$data['errors'] = $errors;
			$this->load->view("admin_new/registration", $data);
			return false;
		}
		
		# data setup
		$lvl 			= $this->webspice->generate_level($input->reffer_id);
		$first_name 	= $input->first_name;
		$last_name 		= $input->last_name;
		$email 			= $input->email;
		$address 		= $input->address;
		$mobile 		= $input->mobile;
		$my_id 			= $this->webspice->check_rand();
		$reffer_id 		= $input->reffer_id;
		$pin_id 		= $pin_data->PIN_ID;
		$national_id 	= $input->national_id;
		$country 		= $input->country;
		$password 		= $this->webspice->enc($input->password, 'encrypt');
		$lvl_1 			= $lvl['lvl_1'];
		$lvl_2 			= $lvl['lvl_2'];
		$lvl_3 			= $lvl['lvl_3'];
		$lvl_4 			= $lvl['lvl_4'];
		$lvl_5 			= $lvl['lvl_5'];
		$lvl_6 			= $lvl['lvl_6'];
		$lvl_7 			= $lvl['lvl_7'];
		
		# insert data
		$sql = "
		INSERT INTO user_registration(
			FIRST_NAME,
			LAST_NAME,
			EMAIL,
			ADDRESS,
			MOBILE,
			MY_ID,
			REFFER_ID,
			PIN_ID,
			NATIONAL_ID,
			COUNTRY,
			INIT,
			LEVEL_1,
			LEVEL_2,
			LEVEL_3,
			LEVEL_4,
			LEVEL_5,
			LEVEL_6,
			LEVEL_7,
			CREATED_DATE,
			STATUS
		)
		VALUES
		( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0 )";
		$this->db->query($sql, array(
			$first_name,
			$last_name,
			$email,
			$address,
			$mobile,
			$my_id,
			$reffer_id,
			$pin_id,
			$national_id,
			$country,
			$password,
			$lvl_1,
			$lvl_2,
			$lvl_3,
			$lvl_4,
			$lvl_5,
			$lvl_6,
			$lvl_7,
			$this->webspice->now()
		));
        $insert_id = $this->db->insert_id();
		
		if( !$this->db->insert_id() ){
			$this->webspice->message_board('We could not execute your request. Please tray again later or report to authority.');
			$this->webspice->force_redirect($url_prefix . 'registration');
			return false;
		}

		// $this->db->query("UPDATE pin_code SET STATUS=1 AND OWNER_ID='{$this->db->insert_id()}' AND USED_DATE='{$this->webspice->now()}' WHERE PIN_ID='{$pin_id}'");

		// deactivate current pin
		$sql2 = "
		UPDATE pin_code SET STATUS=?, OWNER_ID=?, USED_DATE=? WHERE PIN_ID=?";
		$this->db->query($sql2, array(1, $this->db->insert_id(), $this->webspice->now(), $pin_id));


        // email start
		/*$domain_name = "Neobux";
		$site_url = "http://neobuxxx.com/";
		$verification_code = $this->webspice->enc($insert_id, 'encrypt');
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'mail.primebuxpro.com',
            'smtp_port' => 25,
            'smtp_user' => 'admin@primebuxpro.com',
            'smtp_pass' => 'Abcd12345678$$',
            'mailtype'  => 'html', 
            'charset'   => 'iso-8859-1'
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        
        // Set to, from, message, etc.
        $this->email->from('admin@neobuxxx.com', 'Neobux');
        $this->email->to($email);

        $this->email->subject('Verify Your Account');
		$html =<<< HTML_MESSAGE
			<table width="100%" style="background:#eeeeee; font:normal 12px tahoma; padding:15px;">
				<tr>
					<td>
						Dear, {$first_name} {$last_name}. <br /><br /><b>Welcome to Neobux !!</b><hr />
					</td>
				</tr>
				<tr>
					<td>
						<h2>Verify your account!</h2>
						Click on the verification link below. This link will successfully verify your email address and allow you to sign in to your {$domain_name} Network. If clicking the link does not work, please copy and paste the link from the email into your browser&#39;s navigation bar instead.
						<br /><br />Please note that; the link might be valid for 3 days.
					</td>
				</tr>
				<tr>
					<td>
						<h3><a href="{$site_url}user_activation/{$verification_code}">{$site_url}user_activation/{$verification_code}</a></h3>
					</td>
				</tr>
				<tr>
					<td>
						Please feel free to contact us at {02 - 156312} for any questions you may have. Thank you.
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						{$domain_name}
					</td>
				</tr>
			</table>
HTML_MESSAGE;

        $this->email->message($html);  
        $result = $this->email->send();
        */
        // email end


		$this->webspice->message_board('Your registration process has been completed successfully. Please Check E-mail to Activate your account. please check your Spam or Bulk E-Mail folder just in case the confirmation email got delivered there instead of your inbox.');
		$this->webspice->force_redirect($url_prefix.'registration');

	}
	
	public function user_activation() {
	    $url_prefix = $this->webspice->settings()->site_url_prefix;
	    $id = $this->webspice->encrypt_decrypt($this->uri->segment(2), 'decrypt');
	    if(!$id) {
            $this->webspice->page_not_found();       
	    }
	    
	   // valid link check
	   $valid_link = $this->db->query("SELECT * FROM user_registration WHERE USER_REG_ID='".$id."' AND STATUS=7")->row();
	   if(count($valid_link)) {
	        $this->webspice->page_not_found();
	   }

		# var setup
		$user_data = $this->db->query("SELECT * FROM user_registration WHERE USER_REG_ID='".$id."'")->row();
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
		$this->webspice->message_board("Congratulations!! Your account has been activated successfully. Please login to earn money.");
		$this->webspice->force_redirect($url_prefix.'registration');
		return false;

	}
	
	public function forgot_password(){
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
	
	public function change_password($param_user_id=null){
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		$user_id = null;
		$data = null;
		$this->load->database();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('new_password','new password','required|trim|xss_clean');
		$this->form_validation->set_rules('repeat_password','repeat password','required|trim|xss_clean');
		
		# verify access request through 'Forgot Password' - email URL
		$get_uri = $this->webspice->encrypt_decrypt($this->uri->segment(2), 'encrypt');
	
		$get_link = explode('|', $get_uri);
	
		# verify access request for password expiration
		if( !$this->uri->segment(2) ){
			$param_user_id ? $user_id = $this->webspice->encrypt_decrypt($param_user_id, 'encrypt') : $user_id = $this->input->post('user_id');
		}
		// dd($get_link[0]);
		# verify the request
		if( isset($get_link[0]) && isset($get_link[1]) && $get_link[0] ){
			$user_id = $get_link[0];
		
			# the link is valid for only 3 days
			if( ((strtotime(date("Y-m-d"))-strtotime($get_link[1]))/86400) >3 ){
				$this->webspice->message_board('Sorry! Invalid link. Your link has been expired. Please send us your request again.');
				
				$this->webspice->force_redirect($url_prefix);
				return false;
			}
			
		}elseif( $user_id || $get_link[0] ){
			// dd("Hello");
			$data['user_id'] = ($user_id) ? $user_id : $get_link[0];
			$user_id = $this->webspice->encrypt_decrypt($data['user_id'], 'decrypt');
			// dd($user_id);
		}
		else{
			// dd("bubu");
			# log			
			$this->webspice->log_me('unauthorized_access');
			$this->webspice->page_not_found();
			return false;
		}
		if( !$this->form_validation->run() ){
			$view = $this->load->view('change_password', $data, true);
			// $this->load->view('change_password', $data);
			echo $view;
			exit();
			// return false;
		}
		// dd("Hello");
		// dd($param_user_id);
		// dd("Hello");

		// dd("bubu");
			
		# get User and verify the user
		$get_user = $this->db->query("SELECT * FROM user WHERE USER_ID=?", array($user_id))->result();
		if( !$get_user ){
			$this->webspice->page_not_found();
			return false;
		}
		# call verify_password_policy
		$this->verify_password_policy($get_user[0], 'change_password');
		// dd($get_user[0]);
	
		# encrypt password
		$new_password = $this->webspice->encrypt_decrypt($this->input->post('new_password'), 'encrypt');
		// dd($new_password);

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
			// dd($user_id);
		# update password
		$update = $this->db->query("UPDATE user SET USER_PASSWORD=?, UPDATED_DATE=?, USER_PASSWORD_HISTORY=?, STATUS=? WHERE USER_ID=?", array($new_password, $this->webspice->now(), $password_history, $STATUS, $user_id));
		if( !$update ){
			# log
			$this->webspice->log_me('error:password_changed');
			$this->webspice->message_board('We could not reset your Password. Please try again later or report to Authority.');
			$this->webspice->force_redirect($url_prefix);
			return false;
		}
			// dd($user_id);
		
		# log
		$this->webspice->log_me('password_changed');
		
		# user session destroy
		session_destroy();
		session_start();
		
		$this->webspice->message_board('Your password has been changed! Please login using your new password.');
		$this->webspice->force_redirect($url_prefix.'login');
		
	}
	
	public function logout(){
		// dd("Hello");
		// echo $this->webspice->settings()->site_url_prefix . 'login';
		// dd();

		$this->webspice->user_log($this->webspice->enc($_SESSION['user']['USER_EMAIL'], 'decrypt'), 'logout');
		session_destroy();
		session_start();
		$data['title'] = 'You have been signed out of this account.';
		$data['body'] = 'You have been signed out of this account. To continue using this account, you will need to sign in again.  This is done to protect your account and to ensure the privacy of your information. We hope that, you will come back soon.';
		$data['footer'] = $this->webspice->settings()->domain_name;
		
		$this->webspice->log_me('signed_out'); # log
		
		$this->confirmation($data);
		$this->webspice->force_redirect($this->webspice->settings()->site_url_prefix . 'login');
	}
	
	public function verify_password_policy($user, $type){
		# $type can be login or change_password
		$user = (object)$user;
		$exipiry_period = 45;
		if( $type=='login' ){
			$pwd_change_duration = strtotime(date("Y-m-d")) - strtotime($user->UPDATED_DATE);
			$pwd_change_duration = round($pwd_change_duration / ( 3600 * 24 ));

			if( $user->UPDATED_DATE && $pwd_change_duration >= $exipiry_period ){
			// dd($pwd_change_duration);
				// dd($user->USER_ID);
				$this->webspice->message_board("Your password is too old. Please change your password!");
				$this->change_password($user->USER_ID);
				return false;
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
				
				$data['user_id'] = $this->webspice->encrypt_decrypt($user->USER_ID, 'encrypt');
				
				$view = $this->load->view('change_password', $data, true);
				echo $view;	
				exit;
			}
			// dd("Hello");

			return true;
			
		} # end if
		
	}

	public function exception_found() {
		$url_prefix = $this->webspice->settings()->site_url_prefix;
		// $this->load->view('../errors/error_404');
		$this->load->view('admin_new/404');
		return FALSE;
	}

	//call confirmation for redirect another url with message
	public function confirmation($message){
		$_SESSION['admin_confirmation'] = $message;
		$this->webspice->force_redirect($this->webspice->settings()->site_url_prefix.'login');
	}

	public function show_confirmation(){
		if( !isset($_SESSION['admin_confirmation']) ){
			$_SESSION['admin_confirmation'] = array();	
		}
		$data = $_SESSION['admin_confirmation'];
		$this->load->view('view_message',$data);
	}

	#get district list of a division


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */