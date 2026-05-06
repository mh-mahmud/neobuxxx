<?php

class Webspice {
	
	var $static_url = 'http://static.domain.com';

	# starts session
	function Webspice(){
		date_default_timezone_set('Asia/Dhaka');
		#error_reporting(0);
		
		# override php.ini memory limit
		ini_set('memory_limit', '-1');
		
		if(!isset($_SESSION)){
			session_start();
		}
	}
	
	# application settings
	function settings(){
		$settings = new stdClass();
		$settings->site_title = 'NeoBuxxx: The Innovation in Paid-to-Click Services';
		$settings->domain_name = 'NeoBuxxx';
		$settings->site_slogan = '';
		$settings->site_url = 'http://nuobuxxx.com/';
		$settings->site_url_prefix = '/';
		$settings->email_from = 'hrm@nns-solution.net';
		$settings->secondary_host = '';
		$settings->secondary_username = '';
		$settings->secondary_password = '';
		$settings->secondary_database = '';
		
		return $settings;
	}
	
	function get_customer_service_contact($param=null, $class=null){
		# $class = css class
		$phone = '(+88) 12345678';
		$email = 'info@primebuxpro.com';
		$address = 'Lahor, Pakistan';
		switch($param){
			case 'phone':
				return '<span class="'.$class.'">'.$phone.'</span>';
				break;
			case 'email':
				return '<span class="'.$class.'">'.$email.'</span>';
				break;
			case 'both_br':
				return '<span class="'.$class.'">'.$phone.'<br />'.$email.'</span>';
				break;
			case 'full':
				return '<span class="'.$class.'">'.$phone.', '.$email.'</span><br />'.$address;
				break;
			default:
				return '<span class="'.$class.'">'.$phone.', '.$email.'</span>';
				break;
		}
	}
	
	function create_user_session($user){
		if( ! $user)
			return FALSE;
		
		foreach($user as $key => $value){
			if($key != 'USER_PASSWORD' && $key != 'USER_PASSWORD_HISTORY'){
				$_SESSION['user'][$key] = $this->encrypt_decrypt($value, 'encrypt');
			}
		}
		
		return TRUE;
	}

	# returns user_id of current user or FALSE if user is NOT logged in
	function get_user_id(){
		$CI = &get_instance();
		if( ! isset($_SESSION['user']['USER_ID'])){
			return FALSE;
		}
		
		return $this->encrypt_decrypt($_SESSION['user']['USER_ID'], 'decrypt');
	}

	# returns all user fields or a particular field - retuns FALSE if user is NOT logged in
	function get_user($field=null){
		if( ! isset($_SESSION['user']) ){
			return FALSE;
		}
		
		if($field){
			if(isset($_SESSION['user'][$field])){
				return $this->encrypt_decrypt($_SESSION['user'][$field], 'decrypt');
			}else{
				return FALSE;
			}
			
		}
		
		return $_SESSION['user'];
	}

	# update user session data
	function set_user($field=null, $value=null){
		if( ! isset($_SESSION['user']) ){
			return FALSE;
		}

		if($field){
			$_SESSION['user'][$field] = $this->encrypt_decrypt($value, 'encrypt');
			return TRUE;
		}
		
		return FALSE;
	}

	function now($param=null){
		# date_default_timezone_set('Asia/Dhaka');
		switch($param){
			case 'time':
				return date('h:i:s A');
				break;
				
			case 'date':
				return date('Y-m-d');
				break;
				
			default:
				return date('Y-m-d h:i:s');
				break;
		}
	}
	function formatted_date($param=null, $format=null, $type=null){
		if( !$param || $param=='' || substr($param,0,4)=='0000' ) return false;
		if($format){
			return date($format, strtotime($param));
			
		}elseif( $type=='full' ){
			return date("F d, Y h:i a", strtotime($param));
			
		}elseif( $type=='time' ){
			return date("h:i a", strtotime($param));
			
		}else{
			return date("F d, Y", strtotime($param));	
		}
	}
	
	function getLastInserted($table, $field) {
	 $CI = &get_instance();
	 $result = $CI->db->query("SELECT MAX(".$field.") AS MAXIMUM_VALUE FROM ".$table)->result();

	 if( !$result ){
	 	return 0;
	 }
	 return $result[0]->MAXIMUM_VALUE;
	}
	/*
	# default status maker
	there are several generic status you have, so default value will shown against a given status(tinyint value)
	*/
	function static_status($status){
		# 1=Pending, 2=Approved, 3=Resolved, 4=Forwarded, 5=Deployed, 6=New, 7=Active, 8=Initiated, 9=On Progress, 10=Delivered, -2=Declined, -3=Canceled, -5=Taking out, -6=Renewed/Replaced, -7=Inactive
		$text = null;
		switch($status){
			case 1:
				$text = '<span class="label label-danger">Pending</span>'; break;
			case 2:
				$text = '<span class="label label-success">Approved</span>'; break;
			case 3:
				$text = '<span class="label label-success">Resolved</span>'; break;
			case 4:
				$text = '<span class="label label-success">Forwarded</span>'; break;
			case 5:
				$text = '<span class="label label-success">Deployed</span>'; break;
			case 6:
				$text = '<span class="label label-info">New</span>'; break;
			case 7:
				$text = '<span class="label label-success">Active</span>'; break;
			case 8:
				$text = '<span class="label label-info">Initiated</span>'; break;
			case 9:
				$text = '<span class="label label-info">On Progress</span>'; break;
			case 10:
				$text = '<span class="label label-success">Delivered</span>'; break;
			case 11:
				$text = '<span class="label label-warning">Bill Applied</span>'; break;

			case 12:
				$text = '<span class="label label-primary">Bill Paid</span>'; break;

			case -2:
				$text = '<span class="label label-danger">Declined</span>'; break;
			case -3:
				$text = '<span class="label label-danger">Canceled</span>'; break;
			case -5:
				$text = '<span class="label label-danger">Taking out</span>'; break;
			case -6:
				$text = '<span class="label label-danger">Renewed</span>'; break;
			case -7:
				$text = '<span class="label label-danger">Inactive</span>'; break;
			default:
				$text = '<span class="label label-default">Unknown</span>'; break;
		}
		
		return $text;
	}
	
	/*
	get_browser option value (db=table has several group name with a single field value), 
	so you can convert option id into option value
	*/
	function option_maker($OptionID){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'option_maker';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		
		if( !$html = $CI->cache->get($cache_name, 'option') ){
			$html = array();
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM tbl_option WHERE Status=1 ORDER BY GroupName");
			$get_record = $get_record->result();
			
			foreach( $get_record as $k=>$v ){
				$html[] = $v->OPTIONID.'|'.$v->OPTIONVALUE;
			}

			$CI->cache->save($cache_name, $html, 'option', 604800);		
		}
		
		foreach($html as $k=>$v){
			$Value = explode('|', $v);
			if( $Value[0]==$OptionID ){
				return $Value[1];
			}
		}

		return false;
	}
	

	function first_of_month($year='Y', $month='m'){
		return date($year."-".$month."-d", strtotime(date($month).'/01/'.date($year).' 00:00:00'));
	}
	
	function last_of_month($year='Y', $month='m'){
		return date($year."-".$month."-d", strtotime('-1 second',strtotime('+1 month',strtotime(date($month).'/01/'.date($year).' 00:00:00'))));
	}
	
	function encrypt_decrypt($key, $type){	
		# type = encrypt/decrypt
		$secret = "XxOx*4e!hQqG5b~9a";
		if( !$key ){ return false; }
		
		if($type=='decrypt'){
			$key = strtr(urldecode($key),'-_,','+/=');
			$original = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($secret), base64_decode($key), MCRYPT_MODE_CBC, md5(md5($secret))), "\0");
			return $original;
			
		}elseif($type=='encrypt'){
			$verification_key = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($secret), $key, MCRYPT_MODE_CBC, md5(md5($secret))));
			return urlencode(strtr($verification_key,'+/=','-_,'));
		}
		
		return FALSE;	# if function is not used properly
	}
	
	function special_char_escape($val){
		return preg_replace('/[^a-zA-Z0-9._-]+/',' ', $val);
	}
	
	function get_seo_title($value=null){
		return htmlspecialchars($this->special_char_escape($value));
	}

	function get_item_url($id=null, $category=null, $title=null, $param=null){
		$url_prefix = $this->settings()->site_url_prefix;
		$item_url = null;
		$safe_category = trim(preg_replace('/[^a-zA-Z0-9]+/','-',strtolower($category)),'-');
		$safe_title = trim(preg_replace('/[^a-zA-Z0-9]+/','-',strtolower($title)),'-');
		switch($param){
			case 'category':
				$item_url = $url_prefix."product_catalog/category/{$safe_category}/{$id}.html";
				break;
			case 'product':
				$item_url = $url_prefix."product_details/{$safe_category}/{$id}-{$safe_title}.html";
				break;
			case 'keyword':
				$item_url = $url_prefix."product_catalog/keyword/";
				break;
			default:
				$item_url = $url_prefix."product_details/{$safe_category}/{$id}-{$safe_title}.html";
		}
		
		return $item_url;
	}

	function get_safe_url($value){
		$safe_title = trim(preg_replace('/[^a-zA-Z0-9.]+/','-',strtolower($value)),'-');
		return "{$safe_title}";
	}

	public function enc($data, $type) {
		return $this->encrypt_decrypt($data, $type);
	}
	
	# retuns WWW root path or full path where item images are stored
	function get_path($param=null){
		$url_prefix = $this->settings()->site_url_prefix;
		$site_url=$this->settings()->site_url;
		switch($param){
			case 'img':
				return FCPATH.'global/img/';
				break;
			case 'img_full':
				return FCPATH.'global/img/';
				break;
			case 'csv':
				return $url_prefix.'global/custom_files/csv/';
				break;
			case 'csv_full':
				return FCPATH.'global/custom_files/csv/';
				break;
			case 'custom':
				return $url_prefix.'global/custom_files/';
				break;
			case 'custom_full':
				return FCPATH.'global/custom_files/';
				break;
			case 'log':
				return $url_prefix.'global/custom_files/log/';
				break;
			case 'log_full':
				return FCPATH.'global/custom_files/log/';
				break;
			case 'news':
				return $url_prefix.'global/custom_files/news/';
				break;
			case 'news_full':
				return FCPATH.'global/custom_files/news/';
				break;
			case 'notice':
				return $url_prefix.'global/custom_files/notice/';
				break;
			case 'notice_full':
				return FCPATH.'global/custom_files/notice/';
				break;
			case 'personnel':
				return $url_prefix.'global/custom_files/personnel/';
				break;
			case 'personnel_full':
				return FCPATH.'global/custom_files/personnel/';
				break;
			case 'staff':
			    return $url_prefix.'global/custom_files/staff/';
			    break;
			case 'staff_full':
			    return FCPATH.'global/custom_files/staff/';
			    break;
			case 'member':
			    return $url_prefix.'global/custom_files/member/';
			    break;
			case 'member_full':
			    return FCPATH.'global/custom_files/member/';
			    break;
			case 'teacher':
			    return $url_prefix.'global/custom_files/teacher/';
			    break;
			case 'teacher_full':
			    return FCPATH.'global/custom_files/teacher/';
			    break;

			case 'parent':
			    return $url_prefix.'global/custom_files/parent/';
			    break;
			case 'parent_full':
			    return FCPATH.'global/custom_files/parent/';
			    break;
			case 'product':
				return $url_prefix.'global/custom_files/product/';
				break;
			case 'product_full':
				return FCPATH.'global/custom_files/product/';
				break;
			case 'person':
				return $url_prefix.'global/custom_files/person/';
				break;
			case 'person_full':
				return FCPATH.'global/custom_files/person/';
				break;
			case 'slider':
				return $url_prefix.'global/custom_files/slider/';
				break;
			case 'slider_full':
				return FCPATH.'global/custom_files/slider/';
				break;
			case 'gallery':
				return $url_prefix.'global/custom_files/gallery/';
				break;
			case 'gallery_full':
				return FCPATH.'global/custom_files/gallery/';
				break;
			case 'file':
				return $url_prefix.'global/custom_files/files/';
				break;
			case 'file_full':
				return FCPATH.'global/custom_files/files/';
				break;
			case 'course_material':
				return $url_prefix.'global/custom_files/course_material/';
				break;
			case 'course_material_full':
				return FCPATH.'global/custom_files/course_material/';
				break;
			case 'success_story':
				return $url_prefix.'global/custom_files/success_story/';
				break;
			case 'success_story_full':
				return FCPATH.'global/custom_files/success_story/';
				break;
				case 'batch':
				return $url_prefix.'global/custom_files/batch/';
				break;
			case 'batch_full':
				return FCPATH.'global/custom_files/batch/';
				break;
			case 'user':
				return $url_prefix.'global/custom_files/user/';
				break;
			case 'user_full':
				return FCPATH.'global/custom_files/user/';
				break;
			case 'student':
				return $url_prefix.'global/custom_files/student/';
				break;
			case 'student_full':
				return FCPATH.'global/custom_files/student/';
				break;
			case 'service':
				return $url_prefix.'global/custom_files/services/';
				break;
			case 'service_full':
				return FCPATH.'global/custom_files/services/';
				break;
			case 'card_service':
				return $url_prefix.'global/custom_files/card_services/';
				break;
			case 'card_service_full':
				return FCPATH.'global/custom_files/card_services/';
				break;
			case 'profile_picture':
				return $url_prefix.'global/custom_files/profile_picture/';
				break;
			case 'profile_picture_full':
				return FCPATH.'global/custom_files/profile_picture/';
				break;
			case 'nid_file':
				return $url_prefix.'global/custom_files/nid_file/';
				break;
			case 'nid_file_full':
				return FCPATH.'global/custom_files/nid_file/';
				break;
			default:
				return $url_prefix.'global/img/';
				break;
		}
	}

	# call this function to upload images
	# Maximum 5 images are allowed, one large and one small image is created along with the original image
	function process_image_multiple($content_name, $image_name, $image_path, $max_image, $process_function='thumb'){
		$image_path = $this->get_path($image_path);
		$width_small 	= 100;
		$height_small = 100;
		$width_mid 		= 260;
		$height_mid 	= 260;
		$width_large 	= 460;
		$height_large = 460;


		foreach($_FILES[$content_name]['tmp_name'] as $k => $v){
			if($v && $k < $max_image){
				/*image does not replaced on exist image*/
				$img_count = 0;
				for($key=0; $key < $max_image; $key++){
					$file = $image_path."{$image_name}_{$key}_small.jpg";
					if( !file_exists($file) ){break;}
					$img_count ++;
				}
				if($img_count>=$max_image){return FALSE;}
				
				$img_original = $image_path."{$image_name}_{$key}_original.jpg";
				$img_small 		=	$image_path."{$image_name}_{$key}_small.jpg";
				$img_mid 			=	$image_path."{$image_name}_{$key}_mid.jpg";
				$img_large 		=	$image_path."{$image_name}_{$key}_large.jpg";
				
				if( ! $this->image_upload($v, $img_original) ){
					return FALSE;
				}
				
				$this->image_resize($img_original, $img_small, $width_small, $height_small, $process_function);
				$this->image_resize($img_original, $img_mid, $width_mid, $height_mid, $process_function);
				$this->image_resize($img_original, $img_large, $width_large, $height_large, $process_function);
				unlink($img_original);
			}
		}
		return TRUE;
	}
	
	/*single image process*/
	function process_image_single($content_name, $image_name, $image_path_prefix, $image_width=null, $image_height=null, $process_function='thumb', $master_dim=null){
		if( $_FILES[$content_name]['tmp_name'] ){
			$info = getimagesize($_FILES[$content_name]['tmp_name']);
			if( $info['mime']!='image/gif' && $info['mime']!='image/jpeg' && $info['mime']!='image/png' && $info['mime']!='image/bmp'){
				return FALSE;
			}
			
			$new_file_name = $this->get_path($image_path_prefix).$image_name;
			$this->image_upload($_FILES[$content_name]['tmp_name'], $new_file_name);
			$this->image_resize($new_file_name, $new_file_name, $image_width, $image_height, $process_function, $master_dim);
			return true;
		}
		
		return false;
	}
	
	# upload a single image
	function image_upload($source, $save_as) {
		$info = getimagesize($source);

		if( ! copy($source, $save_as)){
			move_uploaded_file($source, $save_as);
		}
		
	  return TRUE;
	}
	
	# upload file(s) with default file name
	function upload_file($SourceContainer, $Destination, $NamePrefix){
		#$SourceContainer = input file name
		#$NamePrefix - added with file name as a prefix
		
		if( !isset($_FILES[$SourceContainer]['name']) || !$_FILES[$SourceContainer]['name'] ){
			return false;
		}
		
		if( is_array($_FILES[$SourceContainer]['name']) && $_FILES[$SourceContainer]['name'][0] ){
			foreach($_FILES[$SourceContainer]['name'] as $k=>$v){
				$FileName = $NamePrefix.$_FILES[$SourceContainer]['name'][$k];
				$FileName = $this->get_safe_url($FileName);
				move_uploaded_file($_FILES[$SourceContainer]['tmp_name'][$k], $Destination.$FileName);
			}
			return true;
		}
		
		$FileName = $NamePrefix.$_FILES[$SourceContainer]['name'];
		$FileName = $this->get_safe_url($FileName);
		move_uploaded_file($_FILES[$SourceContainer]['tmp_name'], $Destination.$FileName);
		return true;
	}
	
	# rezie and crop a single image
	function image_resize($oldFile, $newFile, $width, $height, $process_function='thumb', $master_dim='width'){
		$CI =& get_instance();
		
		//load library if not previously loaded
		if( !isset($CI->image_lib) ){
			$CI->load->library('image_lib');
		}
		
		if($process_function =='crop'){
			/*crop image*/
			$img = getimagesize($oldFile);
			$size = Array('width'=>$img['0'], 'height'=>$img['1']);	
			unset($config);
			
			//Crop image  in weight, height
			$config['image_library'] = 'gd2';
			$config['source_image'] = $newFile;
			$config['maintain_ratio'] = FALSE;
			$config['width'] = $width;
			$config['height'] = $height;
			$config['y_axis'] = round(($size['height'] - $height) / 2);
			$config['x_axis'] = 0;
			$CI->image_lib->clear();
			$CI->image_lib->initialize($config);
			if ( ! $CI->image_lib->crop()){
			    return FALSE;
			}
			return true;
			
		}elseif($process_function =='both'){
			/*resize and crop image*/
			$config['image_library'] = 'gd2';
			$config['source_image'] = $oldFile;
			$config['new_image'] = $newFile;
			$config['maintain_ratio'] = TRUE;
			$config['master_dim'] = 'width';
			$config['width'] = $width;
			$config['height'] = $height;
			$CI->image_lib->clear();
			$CI->image_lib->initialize($config);
			$CI->image_lib->resize();
			
			$img = getimagesize($newFile);
			$size = Array('width'=>$img['0'], 'height'=>$img['1']);	
			unset($config);
			
			//Crop image  in weight, height
			$config['image_library'] = 'gd2';
			$config['source_image'] = $newFile;
			$config['maintain_ratio'] = FALSE;
			$config['width'] = $width;
			$config['height'] = $height;
			$config['y_axis'] = round(($size['height'] - $height) / 2);
			$config['x_axis'] = 0;
			$CI->image_lib->clear();
			$CI->image_lib->initialize($config);
			if ( ! $CI->image_lib->crop()){
			    return FALSE;
			}
			return true;
		}

		//By default: Image will be resized in thumb format
		$config['image_library'] = 'gd2';
		$config['source_image'] = $oldFile;
		$config['new_image'] = $newFile;
		$config['maintain_ratio'] = TRUE;
		//$config['master_dim'] = $master_dim; /*commented for thumbnail style*/
		$config['width'] = $width;
		$config['height'] = $height;
		$CI->image_lib->clear();
		$CI->image_lib->initialize($config);
		$CI->image_lib->resize();

		return TRUE;
	}
	
	# check uploaded file format
	function check_file_type(array $file_type, $source_container, $AdditionalData, $ViewName){
		$is_ok = FALSE;
		$CI =&get_instance();
		
		if( !$_FILES || !isset($_FILES[$source_container]) ){
			echo 'No file posted!';
			exit;
		}
		
		# multiple image uploader
		if( is_array($_FILES[$source_container]['name']) && $_FILES[$source_container]['name'][0] ){
			foreach( $_FILES[$source_container]['type'] as $k=>$v ){
				$type = $_FILES[$source_container]['type'][$k];
				foreach($file_type as $k=>$v){
					switch($v){
						case 'doc':
							if($type=='application/msword'){
								$is_ok = TRUE;
							}
							break;
						case 'docx':
							if($type=='application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
								$is_ok = TRUE;
							}
							break;
						case 'zip':
							if($type=='application/zip'){
								$is_ok = TRUE;
							}
							break;
						case 'pdf':
							if($type=='application/pdf'){
								$is_ok = TRUE;
							}
							break;
						case 'xls':
							if($type=='application/vnd.ms-excel'){
								$is_ok = TRUE;
							}
							break;
						case 'xlsx':
							if($type=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
								$is_ok = TRUE;
							}
							break;
						case 'jpg':
							if($type=='image/jpeg'){
								$is_ok = TRUE;
							}
							break;
						case 'jpeg':
							if($type=='image/jpeg'){
								$is_ok = TRUE;
							}
							break;
						case 'png':
							if($type=='image/png'){
								$is_ok = TRUE;
							}
							break;
						case 'bmp':
							if($type=='image/bmp'){
								$is_ok = TRUE;
							}
							break;
						case 'gif':
							if($type=='image/gif'){
								$is_ok = TRUE;
							}
							break;
						case 'sql':
							if($type=='text/x-sql'){
								$is_ok = TRUE;
							}
							break;
						case 'csv':
							if($type=='text/csv'){
								$is_ok = TRUE;
							}
							break;
						case 'txt':
							if($type=='text/plain'){
								$is_ok = TRUE;
							}
							break;
						case 'xml':
							if($type=='text/xml'){
								$is_ok = TRUE;
							}
							break;
					}
				}
			}
			
			if( !$is_ok ){
				$this->message_board('File Type Invalid or File is too large. Please change your attachment file.');
				$string = $CI->load->view($ViewName, $AdditionalData, true);
				echo $string;
				exit;
			}
		}
		
		# single image uploader
		$type = $_FILES[$source_container]['type'];
		foreach($file_type as $k=>$v){
			switch($v){
				case 'doc':
					if($type=='application/msword'){
						$is_ok = TRUE;
					}
					break;
				case 'docx':
					if($type=='application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
						$is_ok = TRUE;
					}
					break;
				case 'zip':
					if($type=='application/zip'){
						$is_ok = TRUE;
					}
					break;
				case 'pdf':
					if($type=='application/pdf'){
						$is_ok = TRUE;
					}
					break;
				case 'xls':
					if($type=='application/vnd.ms-excel'){
						$is_ok = TRUE;
					}
					break;
				case 'xlsx':
					if($type=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
						$is_ok = TRUE;
					}
					break;
				case 'jpg':
					if($type=='image/jpeg' || $type=='image/pjpeg'){
						$is_ok = TRUE;
					}
					break;
				case 'jpeg':
					if($type=='image/jpeg' || $type=='image/pjpeg'){
						$is_ok = TRUE;
					}
					break;
				case 'png':
					if($type=='image/png' || $type=='image/x-png'){
						$is_ok = TRUE;
					}
					break;
				case 'bmp':
					if($type=='image/bmp' || $type=='image/x-windows-bmp'){
						$is_ok = TRUE;
					}
					break;
				case 'gif':
					if($type=='image/gif'){
						$is_ok = TRUE;
					}
					break;
				case 'sql':
					if($type=='text/x-sql'){
						$is_ok = TRUE;
					}
					break;
				case 'csv':
					if($type=='text/csv'){
						$is_ok = TRUE;
					}
					break;
				case 'txt':
					if($type=='text/plain'){
						$is_ok = TRUE;
					}
					break;
				case 'xml':
					if($type=='text/xml'){
						$is_ok = TRUE;
					}
					break;
			}
		}

		if( !$is_ok ){
			$this->message_board('File Type Invalid or File is too large. Please change your attachment file.');
			$string = $CI->load->view($ViewName, $AdditionalData, true);
			echo $string;
			exit;
		}
	}
	
	# excell reader and uploader
	function excel_reader($FileName, $SheetIndex=0, array $FieldCaption){
		# $ExcutionType = return/execute
		# $FileName - need to add \\ before file name if we enter file name manually - Ex: FCPATH."application\libraries\php-excel\\test.xls"
		
		error_reporting(E_ALL ^ E_NOTICE);
		
		# load plugin
		require_once 'php-excel/excel_reader2.php';
		
		$data = new Spreadsheet_Excel_Reader($FileName, false);
		if( !$data ){
			return false;
		}
		
		$GetCaption = $data->sheets[(int)$SheetIndex]['cells'][1];
		foreach($GetCaption as $k=>$v){
			if( count($FieldCaption) != count($GetCaption) ){
				return false;
			}elseif( $FieldCaption[$k] != $GetCaption[$k+1] ){
				return false;
			}
		}
		
		unset($data->sheets[(int)$SheetIndex]['cells'][1]);
		return $data->sheets[(int)$SheetIndex]['cells'];
	}
	
	# get time difference
	function time_since($start_date, $end_date) {
		if( $start_date > $end_date ){ return false; }
	
	  $start_date = strtotime($start_date);
	  $end_date = strtotime($end_date);
	  $seconds = $end_date - $start_date;
	  
	  $minutes = 0;
	  $hours = 0;
	  $days = 0;
	  $weeks = 0;
	  $months = 0;
	  $years = 0;
	  
	  $additional = null;
	  if ( $seconds == 0 ){
	  	$seconds = 1;
	  }
	  
	  if ( $seconds > 60 ) {
	  	$minutes =  $seconds/60;
	  	if( ($seconds % 60) > 0 ){ $additional[] = ($seconds % 60).' second(s)'; }
	  	
	  }else{
	  	return $this->time_since_extended($seconds,'second');
	  }
	
	  if( $minutes >= 60 ){
	  	$hours = $minutes/60;
	    if( ($minutes % 60) > 0 ){ $additional[] = ($minutes % 60).' minute(s)'; }
	      
	  }else{
	  	return $this->time_since_extended($minutes,'minute(s)');
	  }
	
	  if( $hours >= 24){
	  	$days = $hours/24;
	  	if( ($hours % 24) > 0 ){ $additional[] = ($hours % 24).' hour(s)'; }
	      
	  }else{
	  	return $this->time_since_extended($hours,'hour(s)', $additional);
	  }
	
	  if( $days >= 7 ){
	  	$weeks = $days/7;
	  }else{
	  	return $this->time_since_extended($days,'day(s)', $additional);
	  }
	
	  if( $weeks >= 4 ){
	  	$months = $weeks/4;
	  }else{
	  	return $this->time_since_extended($weeks,'week(s)', $additional);
	  }
	
	  if( $months >= 12 ){
	    $years = $months/12;
	    return $this->time_since_extended($years,'year(s)', $additional);
	    
	  }else{
	  	return $this->time_since_extended($months,'month(s)', $additional);
	  }
	
	}
	# connected with time_since function
	function time_since_extended($num, $word, $additional=null) {
		if($additional){ krsort($additional); $additional = '&nbsp;'.implode('&nbsp;',$additional); }
	  $num = floor($num);
	  if ( $num < 1 ) {
	      return false;
	  } else {
	      return $num.' '.$word.$additional;
	  }
	}
	
	function posted($timestamp){
		return date('M j @ g:ia', strtotime($timestamp));
	}

	function tk($number=null){
		$format = '#,##,#,#,###';
		$fraction = '';
		$negetive = null;
		if($number < 0){
			$negetive = '-';
			$number = substr($number,1);
		}
				
		$number = round($number,2);
		$len = strlen($number);
		$rt = null;
		
		if( strpos($number, '.') ){
			$fraction = substr($number, strpos($number, '.', 2));
			$number = substr($number, 0, strpos($number, '.', 2));
			$len = strlen($number);
		}

		$format = substr($format, $len*-1,$len);
		
		for($i=1; $i<=$len; $i++)
		{
			if( substr($format,$i*-1,1) != '#' ) 
				$rt = substr($format,$i*-1,1) . $rt;
				
			$rt = substr($number,$i*-1,1) . $rt;
		}
		
		if(!$fraction){
			$fraction = '.00';	
		}
		
		# taka sign in html &#2547;
		return $negetive.$rt.$fraction;
	}
	
	function currency_chooser($value){
		if( !isset($_SESSION['currency']) ){
			$_SESSION['currency'] = 'AED'; //default currency
		}
		
		if( !isset($_SESSION['currency_rate']) ){
			$_SESSION['currency_rate'] = 1;
		}
		$currency_rate = $_SESSION['currency_rate'];

		switch($_SESSION['currency']){
			case 'USD':
				return '$'.round($value*$currency_rate, 2);
				break;
				
			case 'EUR':
				return '&euro; '.round($value*$currency_rate, 2);
				break;
				
			case 'KWD':
				return 'KD '.round($value*$currency_rate, 2);
				break;
				
			case 'SAR':
				return 'SR '.round($value*$currency_rate, 2);
				break;
				
			case 'EGP':
				return 'EG&pound; '.round($value*$currency_rate, 2);
				break;
				
			default:
				$_SESSION['currency'] = 'AED';
				return 'AED '.round($value);
		}
	}

	function month_convert($number=null, $text=null){
		if($number){
			switch($number){
				case '1':
				case '01':
					$number = 'January';break;
				case '2':
				case '02':
					$number = 'February';break;
				case '3':
				case '03':
					$number = 'March';break;
				case '4':
				case '04':
					$number = 'April';break;
				case '5':
				case '05':
					$number = 'May';break;
				case '6':
				case '06':
					$number = 'June';break;
				case '7':
				case '07':
					$number = 'July';break;
				case '8':
				case '08':
					$number = 'August';break;
				case '9':
				case '09':
					$number = 'September';break;
				case '10':
					$number = 'October';break;
				case '11':
					$number = 'November';break;
				case '12':
					$number = 'December';break;
			}
			return $number;
		}
		
		if($text){
			switch($text){
				case 'January':
					$text = '01'; break;
				case 'February':
					$text = '02'; break;
				case 'March':
					$text = '03'; break;
				case 'April':
					$text = '04'; break;
				case 'May':
					$text = '05'; break;
				case 'June':
					$text = '06'; break;
				case 'July':
					$text = '07'; break;
				case 'August':
					$text = '08'; break;
				case 'September': break;
					$text = '09';
				case 'October': break;
					$text = '10';
				case 'November': break;
					$text = '11';
				case 'December': break;
					$text = '12';
			}
			return $text;
		}
		
		return false;
	}
	function convert_number_to_words($number) {
	    $hyphen      = '-';
	    $conjunction = ' and ';
	    $separator   = ', ';
	    $negative    = 'negative ';
	    $decimal     = ' point ';
	    $dictionary  = array(
	        0                   => 'zero',
	        1                   => 'one',
	        2                   => 'two',
	        3                   => 'three',
	        4                   => 'four',
	        5                   => 'five',
	        6                   => 'six',
	        7                   => 'seven',
	        8                   => 'eight',
	        9                   => 'nine',
	        10                  => 'ten',
	        11                  => 'eleven',
	        12                  => 'twelve',
	        13                  => 'thirteen',
	        14                  => 'fourteen',
	        15                  => 'fifteen',
	        16                  => 'sixteen',
	        17                  => 'seventeen',
	        18                  => 'eighteen',
	        19                  => 'nineteen',
	        20                  => 'twenty',
	        30                  => 'thirty',
	        40                  => 'fourty',
	        50                  => 'fifty',
	        60                  => 'sixty',
	        70                  => 'seventy',
	        80                  => 'eighty',
	        90                  => 'ninety',
	        100                 => 'hundred',
	        1000                => 'thousand',
	        1000000             => 'million',
	        1000000000          => 'billion',
	        1000000000000       => 'trillion',
	        1000000000000000    => 'quadrillion',
	        1000000000000000000 => 'quintillion'
	    );
  
	    if (!is_numeric($number)) {
	        return false;
	    }
	   
	    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
	        // overflow
	        trigger_error(
	            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
	            E_USER_WARNING
	        );
	        return false;
	    }

	    if ($number < 0) {
	        return $negative . $this->convert_number_to_words(abs($number));
	    }
	   
	    $string = $fraction = null;
	   
	    if (strpos($number, '.') !== false) {
	        list($number, $fraction) = explode('.', $number);
	    }
   
	    switch (true) {
	        case $number < 21:
	            $string = $dictionary[$number];
	            break;
	        case $number < 100:
	            $tens   = ((int) ($number / 10)) * 10;
	            $units  = $number % 10;
	            $string = $dictionary[$tens];
	            if ($units) {
	                $string .= $hyphen . $dictionary[$units];
	            }
	            break;
	        case $number < 1000:
	            $hundreds  = $number / 100;
	            $remainder = $number % 100;
	            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
	            if ($remainder) {
	                $string .= $conjunction . $this->convert_number_to_words($remainder);
	            }
	            break;
	        default:
	            $baseUnit = pow(1000, floor(log($number, 1000)));
	            $numBaseUnits = (int) ($number / $baseUnit);
	            $remainder = $number % $baseUnit;
	            $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
	            if ($remainder) {
	                $string .= $remainder < 100 ? $conjunction : $separator;
	                $string .= $this->convert_number_to_words($remainder);
	            }
	            break;
	    }
   
	    if (null !== $fraction && is_numeric($fraction)) {
	        $string .= $decimal;
	        $words = array();
	        foreach (str_split((string) $fraction) as $number) {
	            $words[] = $dictionary[$number];
	        }
	        $string .= implode(' ', $words);
	    }
   
    	return $string;
	}
	
	# $reply_to is optional. If not defined, $from will be used  
	# $html accepts 'html' which is optional. If not defined, plain text will be used
	function email($to, $subject, $message, $cc=NULL, $bcc=NULL, $attachment=NULL, $reply_to=NULL, $html=NULL){
		$CI =& get_instance();
		
		# to send email using google's smtp server
		$config = Array(
		    'protocol' => 'smtp',
		    #'smtp_host' => '192.168.11.128',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    #'smtp_host' => 'tls://smtp.googlemail.com',
		    #'smtp_port' => 25,
		    'smtp_port' => 465, 
		    'smtp_user' => 'hrm@nns-solution.net',
		    'smtp_pass' => 'nns@1212',
		    'mailtype'  => 'html',
		    'charset'   => 'utf-8'
		);
		$CI->load->library('email', $config);
		# end smtp config
		
		/*
		$config = array(
			'wordwrap' => FALSE
		);

		if($html && $html=='html'){
			$config = array_merge( $config, array('mailtype' => 'html') );
		}
		*/
		
		//load library if not previously loaded
		if( ! isset($CI->email)){
			$CI->load->library('email',$config);
		}
		
		
		$CI->email->clear(TRUE);
		$CI->email->set_newline("\r\n");
		$CI->email->set_crlf( "\r\n" );
		$CI->email->from($this->settings()->email_from, $this->settings()->domain_name);
		$CI->email->to($to);
		$CI->email->subject($subject);
		$CI->email->message($message);
	
		if($cc){
			$CI->email->cc($cc);
		}
		
		if($bcc){
			$CI->email->bcc($bcc);
		}
		
		if($attachment){
			if( is_array($attachment) ){
				foreach($attachment as $k222=>$v222){
					$CI->email->attach($v222);
				}
				
			}else{
				$CI->email->attach($attachment);
			}
			
		}
		
		if($reply_to){
			$CI->email->reply_to($reply_to);
		}

		if( $CI->email->send() ){
			return TRUE;
		}else{
			return FALSE;
		}
		
	}
	
	# send many emails using a single request
	# email addresses must be separated by comma
	function massmail($to,$subject,$message,$from_text=null,$from_email=null,$email_type=null){
		if(!$from_text) $from_text = $this->settings()->domain_name;
		if(!$from_email) $from_email = $this->settings()->email_from;
		
		$headers  = "MIME-Version: 1.0\r\n";
		if($email_type){
			$headers .= "Content-Type: text/plain; charset=US-ASCII\r\n";
		}else{
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		}
		//$headers .= "To: $to\r\n";
		$headers .= "From: $from_text <$from_email>";
		
		$success = mail($to,$subject,$message,$headers,"-freturn@".$this->settings()->domain_name);
		
		return $success;
	}
	
	function create_file($content=null, $file_path=null){
	    $f = fopen($file_path, "w");
	    if( !$f ){ return false; }
	    
	    # Now UTF-8 - Add byte order mark
	    fwrite($f, pack("CCC",0xef,0xbb,0xbf));
	    fwrite($f, $content);
	    fclose($f);
	        
		/*		if (!$handle = fopen($file_path, 'w')) {
			     return FALSE;
			}
			if (fwrite($handle, $output) === FALSE) {
			    return FALSE;
			}
					    
			fclose($handle);*/
			
			return TRUE;
	}
	
	function pager($totalRecords, $NumberOfRecordsToView=10, $beginIndex, $uri, $totalLinksToView=10){
		
		/* IMPORTANT: $totalLinksToView must always be even numbers */
		
		$resultNevigator="";
		$j=0;

		if ($totalRecords>$NumberOfRecordsToView){
			if( $beginIndex > 0 )
				$resultNevigator .= "<a class='ggl_btn' href='{$uri}".($beginIndex-$NumberOfRecordsToView)."'>&laquo; Prev</a>";
			else
				$resultNevigator .= "<a class='ggl_btn disabled'>&laquo; Prev</a>";

			$temp = (int) ($totalRecords/$NumberOfRecordsToView);

			if($temp>$totalLinksToView && $beginIndex>(($NumberOfRecordsToView*$totalLinksToView)/2)){
				$resultNevigator .= "<A class='ggl_btn' href='{$uri}0' title='Click to view record # 1 to ".(0+$NumberOfRecordsToView)."'>1 ...</A> ";
				$temp = $beginIndex -(int)(($NumberOfRecordsToView*$totalLinksToView)/2);
				if($temp > ($totalRecords-($NumberOfRecordsToView*$totalLinksToView)))
				{
					$temp = ((int)($totalRecords/$NumberOfRecordsToView)-$totalLinksToView)*$NumberOfRecordsToView;
					if(($totalRecords/$NumberOfRecordsToView)-(int)($totalRecords/$NumberOfRecordsToView))
						$temp = $temp + $NumberOfRecordsToView;
				}
			}else{
				$temp=0;
			}

			for ($i=$temp; $i<$totalRecords; $i = $i+$NumberOfRecordsToView){
				$j++;
				if($j<=$totalLinksToView){
					if( $i==$beginIndex )
					{
						$resultNevigator .= "<span class='ggl_btn selected'><b>" . (int)(($i/$NumberOfRecordsToView)+1) . "</b></span> &nbsp;";
					}
					else
					{
						if(($i+$NumberOfRecordsToView)>$totalRecords) $upto=$totalRecords; else $upto=($i+$NumberOfRecordsToView);

						$resultNevigator .= "<A class='ggl_btn' href='{$uri}{$i}' title='Click to view record # ".($i+1)." to ".$upto."'>" . (int)(($i/$NumberOfRecordsToView)+1) . " </A> &nbsp;";
					}
				}else{
						$last = " <A class='ggl_btn' href='{$uri}{$i}' title='Click to view record # ".($i+1)." to ".$totalRecords."'>... " . (int)(($i/$NumberOfRecordsToView)+1) . "</A> &nbsp;";
				}
			}

			if($j>$totalLinksToView){
				$resultNevigator .= $last;
			}

			if( $beginIndex != ($i-$NumberOfRecordsToView) )
				$resultNevigator .= "&nbsp;<a class='ggl_btn' href='{$uri}".($beginIndex+$NumberOfRecordsToView)."' >Next &raquo;</a>";
			else
				$resultNevigator .= "<a class='ggl_btn disabled'>Next &raquo;</a>";
		}
		
		return $resultNevigator;
	}
	
	function user_verify($call, $callback){
		# $call = must be a uri - !verify then redirect to this uri (call function)
		# $callback = must be a uri - call function executed successfully then redirect to this uri
		if( !$this->get_user_id() ){
			$this->login_callback($callback);
			$this->message_board('You have to login first to execute your request.');
			$this->force_redirect($call);
			exit;
		}
		
		return true;
	}
	
	function admin_verify($IsErrorPage=FALSE){
		if( !$this->get_user_id() || $this->get_user('STATUS')!=9 ){
			if( !$IsErrorPage ){
				return FALSE;
			}else{
				$this->page_not_found();
				exit;
			}
			
		}
		
		return true;
	}
	
	# User Role Verification
	function role_verify(array $roleID, $return=null, $callback=null, $CustomMessage=null){
		/*
		- must have to create a session using 'user_role', when an user request for log-in
		- $roleID should be an array of user's role id(s)
		- $return should be return or null - if value provided then it will return TRUE or FALSE
		- $callback should be a route or null
		- $CustomMessage will show at your message_board
		*/
		
		# Remove # from below line for go through without verify
		# return TRUE;
		
		$CI =&get_instance();
		$is_ok = null;

		if( $this->get_user_id() ){
			foreach($roleID as $k=>$v){
				if($v==$this->get_user('user_role')){
					$is_ok = TRUE;
				}
			}
		}
		
		if($CustomMessage){
			$this->message_board($CustomMessage);	
		}
		
		# routing
		if( $return=='return' ){
			return $is_ok ? TRUE: FALSE;
			
		}elseif($callback){
			# if authorized then return true otherwise return to Callback link
			return $is_ok ?  TRUE: $this->force_redirect($callback);
			
		}elseif( !$is_ok ){
			# if there is no return and no callback then send error page;
			$CI->load->view('error_404');
			return false;
		}
		
		return TRUE;
	}
	
	# user permission verify
	function permission_verify($permission_name=null, $IsReturn=FALSE){
		# multiple permission check -> permission_name must be separated by comma
		# if the user id is an admin, then he/she can access everything
		
		$data = null;
		if( $this->get_user('STATUS') == 9 ){
			# user status 9 for admin, admin can perform all operations
			return true;
		}


		if( !$permission_name || !$this->get_user_id() || !$this->get_user('PERMISSION_NAME') ){
			# return false;

			if($IsReturn){ return false; }
			
		
			$CI =& get_instance();
			// $error_page = $CI->load->view('../errors/error_404', $data, true);
			$error_page = $CI->load->view('admin_new/404', $data, true);
			echo($error_page); exit;
			// dd("Hello");
		}

		$permission_count = 0;
		$permission_name = explode(',', $permission_name);
		
		$user_permission = explode(',', $this->get_user('PERMISSION_NAME'));
		foreach($permission_name as $k=>$v){
			foreach($user_permission as $k11=>$v11){
				if( trim($v11)==trim($v) ){
					$permission_count++;
				}
			}

		}

		if($permission_count > 0){
			return TRUE;
		}
		
		# if permission_count is still 0
		# return FALSE;
		if($IsReturn){ return false; }
		
		$CI =& get_instance();
		// $error_page = $CI->load->view('../errors/error_404',$data, true);
		$error_page = $CI->load->view('admin_new/404',$data, true);
		echo($error_page); exit;
	}
	function page_not_found($data=null){
		$CI =& get_instance();
		// $string = $CI->load->view('../errors/error_404', $data, true);
		$string = $CI->load->view('admin_new/404', $data, true);
		echo $string;
		exit;
	}
	
	function force_redirect($url=null, $new_window=false, $focus_to_new_window=false, $kill_operation=false){
		# $url = URL to redirect
		# $new_window = if true then url will open in a new window/tab
		# $focus_to_new_window = if true then it will focus on new window/tab
		# $kill_operation = if true then php operation will stopped >> no more line will execute
		
		if(!$url){
			$url = $this->settings()->site_url_prefix;	
		}
		
		if($new_window==true && $focus_to_new_window==true){
			echo '
			<script>
		  var win = window.open("'.$url.'", "_blank");
		  win.focus();
		  </script>
		  ';
		  
		  if($kill_operation){
		  	exit;
		  }
		  return false;
		  
		}elseif($new_window==true){
			echo '
			<script>
		  var new_window = window.open("'.$url.'", "_blank");
		  window.opener.focus();
		  </script>
		  ';
		  if($kill_operation){
		  	exit;
		  }
		  return false;
		}

	    echo'
	    <script>
	    window.location.href = "'.$url.'";
	    </script>
	    ';
    
		if($kill_operation){
		exit;
		}
		return false;
	}
  
	function message_board($value=null, $type=null){
		//type can be get and get_and_destroy
		//if only value given then it perform set property

		if( !isset($_SESSION['message_board']) ){
			$_SESSION['message_board'] = null;	
		}

		if( $type && $type=='get' ){
			return $_SESSION['message_board'];
		}

		if( $type && $type=='get_and_destroy' ){
			$value = $_SESSION['message_board'];
			$_SESSION['message_board'] = null;
			return $value;
		}

		$_SESSION['message_board'] = $value;
		return 'ok';
	}
  
	function view_message($data){
		$CI =&get_instance();
		$string = $CI->load->view('view_message', $data, true);
		echo $string;
		exit;
	}
  
	function login_callback($value=null, $type=null){
		//type can be get and get_and_destroy
		//if only value given then it perform set property
		
		if( !isset($_SESSION['login_callback']) ){
			$_SESSION['login_callback'] = null;	
		}
		
		if( $type && $type=='get' ){
			return $_SESSION['login_callback'];
		}
		
		if( $type && $type=='get_and_destroy' ){
			$value = $_SESSION['login_callback'];
			$_SESSION['login_callback'] = null;
			return $value;
		}
		
		$_SESSION['login_callback'] = $value;
		return 'ok';
	}
  
	//facebook new edition graph API for access user facebook account information
	function get_fb_value($app_id, $app_secret, $type='user'){
		include_once FCPATH."/global/static/facebook_api/facebook.php";
		
		$facebook = new Facebook(array(  
			'appId'  => $app_id,
			'secret' => $app_secret,
			'cookie' => true, // enable optional cookie support  
		));

		switch($type){
			case 'id':
				return $facebook->getUser();
				break;
			case 'user':
				return $facebook->api('/me');
				break;
			case 'friend':
				return $facebook->api('/me/friends');
				break;
			case 'token':
				return $facebook->getAccessToken();
				break;
			default:
				return FALSE;
		}
	}
	
	function manage_special_char($val, $special_char=array()){
		foreach($val as $k=>$v){
			if(strtolower($v)=='comma'){ 
				$val = str_replace('COMMA',',', $val); 
			}elseif($v=='&'){ 
				$val = str_replace('&','And', $val); 
			}elseif(strtolower($v)=='and'){ 
				$val = str_replace('And','&', $val);
				$val = str_replace('and','&', $val); 
				$val = str_replace('AND','&', $val); 
			}
		}
		return $val;
	}
	
	# get data from databse
	function get_custom_db($sql=null, $table_name=null, $field_name=null, $where=null, $type=null, $order_by=null, $limit=null){
		# $type can be single or row
		# $sql - if sql > must be complete sql
		# $type - if type='single', must be provided field_name
		$CI =&get_instance();
		$CI->load->database();
		$new_sql = null;
		
		if( !$field_name ){$field_name = '*';}
		if($where){$where = ' WHERE '.$where;}
		if($order_by){$order_by = ' ORDER BY '.$order_by;}
		if($limit){$limit = ' LIMIT '.$limit;}
		
		if($sql){
			$new_sql = $sql;
		}elseif($table_name){
			$new_sql = 'SELECT '.$field_name.' FROM '.$table_name.$where.$order_by.$limit;
		}else{
			return FALSE;
		}
		
		$record = $CI->db->query($new_sql);
		$record = $record->result();

		if( !$record ){
			return false;
		}elseif($record && $field_name && $type=='single' ){
			return $record[0]->$field_name;
		}
		
		return $record;
	}
	# get db data using secondary connection
	function get_data_secondary($sql){
		# create secondary connection with MySQL
		# value will return as Array
		
		$conn = mysql_connect($this->settings()->secondary_host, $this->settings()->secondary_username, $this->settings()->secondary_password);
		if( !$conn ){
			# connection-failed
			mysql_close($conn);
			return false;
		}
		mysql_select_db($this->settings()->secondary_database);
		$result = mysql_query($sql);

		if( !empty($result) ) {
			mysql_close($conn);
	    while($data = mysql_fetch_assoc($result)){
	    	$row[] = $data;
	    }
			return $row; 
		}
		
		mysql_close($conn);
		return false;
	}
	
	# action execute - oracle
	/*
	function action_executer($TableName, $KeyField, $Key, $RedirectURL, $PermissionName=null, $StatusCheck=null, $ChangeStatus=null, $RemoveCache=null, $Log=null, $SecondaryUpdatedBy=null, $SecondaryUpdatedDate=null, $JoinFieldName=null, $EmailMessage=null, $SecondJoinFieldName=null){
		# $KeyField - db table primary key
		# $Key - must be encrypted
		# $RedirectURL - without url-prefix
		# $StatusCheck - field name must be Status and the value must be tinyint like 1, -1
		# $ChangeStatus - field name must be Status and the value must be tinyint like 1, -1, also must have field name UpdatedBy
		# $RemoveCache - Cache group name
		# $Log - log message
		# $SecondaryUpdatedBy - by default updated by goes to UpdatedBy field
		# $SecondaryUpdatedDate - by default the updated date goes to UpdatedDate field
		
		# $JoinFieldName=null, $EmailMessage=null -> only for send an email - $JoinFieldName=$TableName.$JoinFieldName
		
		$CI =&get_instance();
		$CI->load->database();
		$Key = $this->encrypt_decrypt($Key, 'decrypt');
		
		# permission verified
		if($PermissionName){
			$this->permission_verify($PermissionName);
		}
		
		# status verified
		if( $StatusCheck ){
			$status = $CI->db->query("SELECT * FROM ".$TableName." WHERE ".$KeyField."=?", array($Key));
			$status = $status->result();
			if( !$status || $status[0]->STATUS != $StatusCheck ){
				$this->page_not_found();
				exit;
			}
		}
		
		# remove cache
		if($RemoveCache){
			$this->remove_cache($RemoveCache);
		}
		
		# update query
		$updated_set = null;
		$updated_set[] = "STATUS=".$ChangeStatus;
		if( $SecondaryUpdatedBy ){
			$updated_set[] = $SecondaryUpdatedBy."='".$this->get_user_id()."'";
		}else{
			$updated_set[] ="UPDATED_BY='".$this->get_user_id()."'";
		}
		
		if( $SecondaryUpdatedDate ){ 
			$updated_set[] = $SecondaryUpdatedDate."=TO_DATE('".$this->now()."', 'yyyy-mm-dd hh:mi:ss')";
		}else{
			$updated_set[] ="UPDATED_DATE=TO_DATE('".$this->now()."', 'yyyy-mm-dd hh:mi:ss')";
		}
		
		if( $ChangeStatus ){
			
			$CI->db->query("UPDATE ".$TableName." SET ".implode(',', $updated_set)." WHERE ".$KeyField."=?", array($Key));
			if( !$CI->db->affected_rows() ){
				$this->page_not_found();
				exit;
			}
		}

		# log
		if( $Log ){
			$this->log_me($Log);
		}
		
		# do operation
		$this->message_board('Request has been executed successfully.');
		$this->force_redirect($this->settings()->site_url_prefix.$RedirectURL);
		return false;
	}
	*/
	
	# action execute - MySQL
	function action_executer($TableName, $KeyField, $Key, $RedirectURL, $PermissionName=null, $StatusCheck=null, $ChangeStatus=null, $RemoveCache=null, $Log=null, $SecondaryUpdatedBy=null, $SecondaryUpdatedDate=null, $JoinFieldName=null, $EmailMessage=null, $SecondJoinFieldName=null){
		# $KeyField - db table primary key
		# $Key - must be encrypted
		# $RedirectURL - without url-prefix
		# $StatusCheck - field name must be Status and the value must be tinyint like 1, -1
		# $ChangeStatus - field name must be Status and the value must be tinyint like 1, -1, also must have field name UpdatedBy
		# $RemoveCache - Cache group name
		# $Log - log message
		# $SecondaryUpdatedBy - by default updated by goes to UpdatedBy field
		# $SecondaryUpdatedDate - by default the updated date goes to UpdatedDate field
		
		# $JoinFieldName=null, $EmailMessage=null -> only for send an email - $JoinFieldName=$TableName.$JoinFieldName
		
		$CI =&get_instance();
		$CI->load->database();
		$Key = $this->encrypt_decrypt($Key, 'decrypt');
		
		# permission verified
		if($PermissionName){
			$this->permission_verify($PermissionName);
		}

		# status verified
		if( $StatusCheck ){
			$status = $CI->db->query("SELECT * FROM ".$TableName." WHERE ".$KeyField."=?", array($Key));
			$status = $status->result();
			if( !$status || $status[0]->STATUS != $StatusCheck ){
				$this->page_not_found();
				exit;
			}
		}
		
		# remove cache
		if($RemoveCache){
			$this->remove_cache($RemoveCache);
		}
		
		# update query
		$updated_set = null;
		$updated_set[] = "STATUS=".$ChangeStatus;
		if( $SecondaryUpdatedBy ){
			$updated_set[] = $SecondaryUpdatedBy."='".$this->get_user_id()."'";
		}else{
			$updated_set[] ="UPDATED_BY='".$this->get_user_id()."'";
		}
		
		if( $SecondaryUpdatedDate ){ 
			$updated_set[] = $SecondaryUpdatedDate."='".$this->now()."";
		}else{
			$updated_set[] ="UPDATED_DATE='".$this->now()."'";
		}
		
		if( $ChangeStatus ){
			$CI->db->query("UPDATE ".$TableName." SET ".implode(',', $updated_set)." WHERE ".$KeyField."=?", array($Key));
			if( !$CI->db->affected_rows() ){
				$this->page_not_found();
				exit;
			}
		}

		# log
		if( $Log ){
			$this->log_me($Log);
		}
		
		# do operation
		$this->message_board('Request has been executed successfully.');
		$this->force_redirect($this->settings()->site_url_prefix.$RedirectURL);
		return false;
	}
	
	# edit generator
	function edit_generator($TableName, $KeyField, $Key, $RedirectController, $RedirectFunction, $PermissionName=null, $StatusCheck=null, $Log=null){
		# $KeyField - db table primary key
		# $Key - must be encrypted
		
		$CI =&get_instance();
		$CI->load->database();
		$Key = $this->encrypt_decrypt($Key, 'decrypt');

		// dd($key);
		
		# permission verified
		if($PermissionName){
			$this->permission_verify($PermissionName);
		}

		# status verified
		if( $StatusCheck ){
			$status = $CI->db->query("SELECT * FROM ".$TableName." WHERE ".$KeyField."=?", array($Key));
			$status = $status->result();
			if( !$status || $status[0]->STATUS != $StatusCheck ){
				$this->page_not_found();
				exit;
			}
		}
		
		# log
		if( $Log ){
			$this->log_me($Log);
		}
		
    $get_record = $CI->db->query("SELECT * FROM ".$TableName. " WHERE ".$KeyField."=?", array($Key));
    $get_record = $get_record->result();
		if( !$get_record ){
			$this->page_not_found();
			exit;
		}
		
		if( count($get_record)==1 ){
			$get_record = $get_record[0];
		}
		$Input = array();
		foreach($get_record as $k=>$v){
			$Input[$k] = $v;
		}

		$data['edit'] = $Input;
		
    require_once(APPPATH.'controllers/'.$RedirectController.'.php'); # include controller
    $aObj = new $RedirectController(); # create object 
    $aObj->$RedirectFunction($data); # call function
	}
	
	# filter generator - oracle
	/*
	Function Filter_Generator($TableName, array $InputField, array $Keyword=null, $AdditionalWhere=null, array $DateBetween=null ){
		# $InputField - must be an array of db field name and db field name and input field name should be same
		# $Keyword - must be an array of db field name and input field name should be SearchKeyword
		# $DateBetween - must be an array and must have 3 offset, 1=DateFieldName, DateFromInputField, DateToInputField
		
		$CI =&get_instance();
    $temp_where = array();
    $temp_filter = array();
    $where = $filter_by = null;
    $DateFrom = $DateTo = null;
    
    if( $DateBetween && count($DateBetween)==3 ){
    	$DateFrom = $CI->input->post($DateBetween[1]);
    	$DateTo = $CI->input->post($DateBetween[2]);
    	$tempTableName = $TableName.'.'.$DateBetween[0];
    	if( strpos($DateBetween[0], '.') ){ $tempTableName = $DateBetween[0]; }
    	
    	if( $DateFrom && $DateTo ){
    		$temp_where[] = "(".$tempTableName." BETWEEN TO_DATE('".$DateFrom."', 'yyyy-mm-dd') AND TO_DATE('".$DateTo."', 'yyyy-mm-dd'))";
    		$temp_filter[] = "Date Between - '".$DateFrom."' and '".$DateTo."'";
    	}elseif( $DateFrom ){
    		$temp_where[] = 'TRUNC('.$tempTableName.") = TO_DATE('".$DateFrom."', 'yyyy-mm-dd')";
    		$temp_filter[] = "Date Like - '".$DateFrom."'";
    	}elseif( $DateTo ){
    		$temp_where[] = 'TRUNC('.$tempTableName.") = TO_DATE('".$DateTo."', 'yyyy-mm-dd')";
    		$temp_filter[] = "Date Like - '".$DateTo."'";
    	}
    	
    }
    
    if( $AdditionalWhere ){
    	$temp_where[] = $AdditionalWhere;
    }
    
    foreach($InputField as $k=>$v){
    	$value = $CI->input->post($v);
			
    	if($value){
    		$tempTableName = $TableName.'.'.$v;
    		if( strpos($v, '.') ){ $tempTableName = $v; }
 
    		$value = explode('|', $value);
	    	!isset($value[0]) ? $value[0] = 0 : $value[0]=$value[0];
	    	!isset($value[1]) ? $value[1] = 0 : $value[1]=$value[1];
	    	$temp_where[] = $tempTableName."='".$value[0]."'";
	    	
	    	if( $value[1] ){
	    		$temp_filter[] = ucwords($v).' - '.ucwords($value[1]);
	    	}else{
	    		$temp_filter[] = ucwords($v).' - '.ucwords($value[0]);
	    	}
	    	
    	} # end if
    	
    }

    $SearchKeyword = $CI->db->escape_like_str($CI->input->post('SearchKeyword', TRUE));

    $tmpKeyWord = array();
    foreach($Keyword as $k1=>$v1){
    	$LineClose = null;
    	$tempTableName = $TableName.'.'.$v1;
    	if( strpos($v1, '.') ){ $tempTableName = $v1; }
    		
    	if( count($Keyword)==($k1+1) ){
    		$LineClose = ')';
    	}
	
    	if($k1==0){
    		$tmpKeyWord[] = "(UPPER(".$tempTableName.") LIKE '%".strtoupper($SearchKeyword)."%'".$LineClose;
    	}else{
    		$tmpKeyWord[] = "UPPER(".$tempTableName.") LIKE '%".strtoupper($SearchKeyword)."%'".$LineClose;
    	}
    }
    
    if($SearchKeyword){
    	$temp_where[] = implode(' OR ', $tmpKeyWord);
    	$temp_filter[] = 'Keyword - '.$SearchKeyword;
  	}
    $temp_where ? $where = ' WHERE '.implode(' AND ', $temp_where) : $temp_where = null;
    $temp_filter ? $filter_by = implode(', ', $temp_filter) : $temp_filter = null;
    return array('where'=>$where, 'filter'=>$filter_by);
	}
	*/
	
	# filter generator - MySQL
	function filter_generator($TableName, array $InputField, array $Keyword=null, $AdditionalWhere=null, array $DateBetween=null ){
		# $InputField - must be an array of db field name and db field name and input field name should be same
		# $Keyword - must be an array of db field name and input field name should be SearchKeyword
		# $DateBetween - must be an array and must have 3 offset, 1=DateFieldName, DateFromInputField, DateToInputField
		
		$CI =&get_instance();
    $temp_where = array();
    $temp_filter = array();
    $where = $filter_by = null;
    $DateFrom = $DateTo = null;
    
    if( $DateBetween && count($DateBetween)==3 ){
    	$DateFrom = $CI->input->post($DateBetween[1]);
    	$DateTo = $CI->input->post($DateBetween[2]);
    	$tempTableName = $TableName.'.'.$DateBetween[0];
    	if( strpos($DateBetween[0], '.') ){ $tempTableName = $DateBetween[0]; }
    	
    	if( $DateFrom && $DateTo ){
    		$temp_where[] = "(".$tempTableName." BETWEEN '".$DateFrom."' AND '".$DateTo."') OR ".$tempTableName." LIKE '%".$DateTo."%'";
    		$temp_filter[] = "Date Between - '".$DateFrom."' and '".$DateTo."'";
    	}elseif( $DateFrom || $DateTo ){
    		$temp_where[] = $tempTableName." LIKE '%".$DateFrom."%'";
    		$temp_filter[] = "Date - ".$DateFrom."";
    	}elseif( $DateTo ){
    		$temp_where[] = $tempTableName." LIKE '%".$DateTo."%'";
    		$temp_filter[] = "Date - ".$DateTo."";
    	}
    	
    }
    
    if( $AdditionalWhere ){
    	$temp_where[] = $AdditionalWhere;
    }
    
    foreach($InputField as $k=>$v){
    	$value = $CI->input->post($v);
			
    	if($value){
    		$tempTableName = $TableName.'.'.$v;
    		if( strpos($v, '.') ){ $tempTableName = $v; }
 
    		$value = explode('|', $value);
	    	!isset($value[0]) ? $value[0] = 0 : $value[0]=$value[0];
	    	!isset($value[1]) ? $value[1] = 0 : $value[1]=$value[1];
	    	$temp_where[] = $tempTableName."='".$value[0]."'";
	    	
	    	if( $value[1] ){
	    		$temp_filter[] = ucwords($v).' - '.ucwords($value[1]);
	    	}else{
	    		$temp_filter[] = ucwords($v).' - '.ucwords($value[0]);
	    	}
	    	
    	} # end if
    	
    }

    $SearchKeyword = $CI->db->escape_like_str($CI->input->post('SearchKeyword', TRUE));

    $tmpKeyWord = array();
    foreach($Keyword as $k1=>$v1){
    	$LineClose = null;
    	$tempTableName = $TableName.'.'.$v1;
    	if( strpos($v1, '.') ){ $tempTableName = $v1; }
    		
    	if( count($Keyword)==($k1+1) ){
    		$LineClose = ')';
    	}
	
    	if($k1==0){
    		$tmpKeyWord[] = "(UPPER(".$tempTableName.") LIKE '%".strtoupper($SearchKeyword)."%'".$LineClose;
    	}else{
    		$tmpKeyWord[] = "UPPER(".$tempTableName.") LIKE '%".strtoupper($SearchKeyword)."%'".$LineClose;
    	}
    }
    
    if($SearchKeyword){
    	$temp_where[] = implode(' OR ', $tmpKeyWord);
    	$temp_filter[] = 'Keyword - '.$SearchKeyword;
  	}
    $temp_where ? $where = ' WHERE '.implode(' AND ', $temp_where) : $temp_where = null;
    $temp_filter ? $filter_by = implode(', ', $temp_filter) : $temp_filter = null;
    return array('where'=>$where, 'filter'=>$filter_by);
	}
	
	# db field data duplicate test
	function db_field_duplicate_test($SQL, array $SQLParam, $ErrorMessage, $EditIdentifierField, $EditIdentifierValue, $AdditionalData, $ViewName){
		# $SQL -> duplicate test sql (query)
		# $SQLParam -> if $SQL uses ? (parameter), then need to pass this variable value - multiple -> use comma separator
		# $DuplicateFieldName -> which field can not be duplicate - message purpose
		# $EditIdentifierField -> Primary field
		# $EditIdentifierValue -> Form (posted) Input Field Name - Edit Identifier
		# $AdditionalData -> tag with view
		# $ViewName -> Redirect to View
	
		$CI = &get_instance();
		$CI->load->database();
		if($SQLParam){
			$get_record = $CI->db->query($SQL, $SQLParam);
		}else{
			$get_record = $CI->db->query($SQL);
		}
		
		$get_record = $get_record->result();

		if ( ($get_record && !$EditIdentifierValue) || ($get_record && $get_record[0]->$EditIdentifierField != $EditIdentifierValue) ) {
			$this->message_board($ErrorMessage);	
			$string = $CI->load->view($ViewName, $AdditionalData, true);
			echo $string;	
			exit();
		}
		
		return true;
	}
	
	# get posted value
	function get_input($key=null){
		# $Input must be an array like $this->input->post()
		# $key = record identifier (encrypted) like user_id - to perform any action based on key
		
		$CI =&get_instance();

		if( !$CI->input->post() ){
			return false;
		}
	
		$Input = new stdClass();
		foreach($CI->input->post() as $k=>$v){
			$Input->$k = $this->clean_input($v);
		}
	
		if( $key ){
			$Input->$key = $this->encrypt_decrypt($CI->input->post($key),'decrypt');
		}

		return $Input;
	}
	
	# clean onput/posted value
	function clean_input($value){
		if( is_array($value) ){
			foreach($value as $k=>$v){
				#$value[$k] = str_replace("'", "''", addslashes(htmlspecialchars(strip_tags($v))));
				$value[$k] = stripslashes(htmlspecialchars(strip_tags($v)));
			}
			return $value;
		}
		
		return stripslashes(htmlspecialchars(strip_tags($value)));
	}
	
	# Remove Cache
	function remove_cache($group_name){
		$CI = &get_instance();

		$CI->load->library('cache');
		$CI->cache->remove_group($group_name);	
	}
	
	# identify viewers
	function who_is($custom_ip=null){
		$ip = $_SERVER['REMOTE_ADDR'];
		if($custom_ip){
			$ip = $custom_ip;
		}
		
	    $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
	    if( !isset($details->org) ){
	    	return false;
	    }
	    
		return 'ip: '.$details->ip.', City: '.$details->city.', Location: '.$details->loc.', Country: '.$details->country.', Org: '.$details->org;
	}
	
	# create a log (activities)
	function log_me($process_name){
		# create a log at 'log' folder
		
		if( !$this->get_path('log_full') ){
			return false;	
		}
		
		$process_name = ucwords(str_replace('~',' ',urlencode(strip_tags($process_name))));
		$user_id = $this->get_user_id();
		!$user_id ? $user_id = 'AMBIGUOUS' : $user_id = $user_id;
		
		$get_file = $this->get_path('log_full').'log_'.date("Y-m-d").'.txt';
		if( !file_exists($get_file) ){
			$get_file = fopen($get_file, "w");
			$get_file = $this->get_path('log_full').'log_'.date("Y-m-d").'.txt';
		}
		
		$current = file_get_contents($get_file);
		$current .= $process_name."~".$user_id."~".$this->now()."\n";
		file_put_contents($get_file, $current);
		
		return true;
	}

	function union($id) {

		$CI = &get_instance();
		$CI->load->database();

		$sql = $CI->db->query("SELECT un.UNION_ID, un.UNION_NAME, u.UPAZILA_ID, u.UPAZILA_NAME, d.DISTRICT_ID, d.DISTRICT_NAME, di.DIVISION_ID, di.DIVISION_NAME FROM TBL_UNION AS un INNER JOIN TBL_UPAZILA AS u ON un.UPAZILA_ID = u.UPAZILA_ID INNER JOIN TBL_DISTRICT AS d ON u.DISTRICT_ID = d.DISTRICT_ID INNER JOIN TBL_DIVISION AS di ON d.DIVISION_ID = di.DIVISION_ID WHERE un.UNION_ID =".$id);
		$union_name = $sql->row();
		return $union_name;
		
	}

	function upazila($id) {

		$CI = &get_instance();
		$CI->load->database();

		$sql = $CI->db->query("SELECT u.UPAZILA_ID, u.UPAZILA_NAME, d.DISTRICT_ID, d.DISTRICT_NAME, di.DIVISION_ID, di.DIVISION_NAME FROM TBL_UPAZILA AS u INNER JOIN TBL_DISTRICT AS d ON u.DISTRICT_ID = d.DISTRICT_ID INNER JOIN TBL_DIVISION AS di ON d.DIVISION_ID = di.DIVISION_ID WHERE u.UPAZILA_ID =".$id);
		$upazila_name = $sql->row();
		return $upazila_name;
		
	}

	function district($id) {
		$CI = &get_instance();
		$CI->load->database();

		$sql = $CI->db->query("SELECT d.DISTRICT_ID, d.DISTRICT_NAME, di.DIVISION_ID, di.DIVISION_NAME FROM TBL_DISTRICT AS d INNER JOIN TBL_DIVISION AS di ON d.DIVISION_ID = di.DIVISION_ID WHERE d.DISTRICT_ID =".$id);
		$district_name = $sql->row();
		return $district_name;
	}

	function division($id) {
		$CI = &get_instance();
		$CI->load->database();

		$sql = $CI->db->query("SELECT di.DIVISION_ID, di.DIVISION_NAME FROM TBL_DIVISION AS di WHERE di.DIVISION_ID=".$id);
		$division_name = $sql->row();
		return $division_name;
	}

	function ordinal($number) {
	    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
	    if ((($number % 100) >= 11) && (($number%100) <= 13))
	        return $number. 'th';
	    else
	        return $number. $ends[$number % 10];
	}

	function company($id) {
		$CI = &get_instance();
		$CI->load->database();

		$sql = $CI->db->query("SELECT * FROM TBL_COMPANY_PROFILE WHERE COMPANY_ID=".$id);
		$company = $sql->row();
		return $company;
	}
	
	function addOrdinalNumberSuffix($num) {
	    if (!in_array(($num % 100),array(11,12,13))){
	      switch ($num % 10) {
	        // Handle 1st, 2nd, 3rd
	        case 1:  return $num.'st';
	        case 2:  return $num.'nd';
	        case 3:  return $num.'rd';
	      }
	    }
	    return $num.'th';
	}

	public function has_product($cat_id) {
		$CI = &get_instance();
		$CI->load->database();

		$sql = $CI->db->query("SELECT COUNT(*) AS COUNT FROM products WHERE CAT_ID=".$cat_id)->result();
		if($sql[0]->COUNT >= 1) {
			return true;
		}
		else {
			return false;
		}
	}

	public function admin_name($id) {
		$CI = &get_instance();
		$CI->load->database();
		if(!$id) {
			return false;
		}
		$name = $CI->db->query("SELECT USER_NAME FROM user WHERE USER_ID=".$id)->row();
		if(count($name)) {
			return $name->USER_NAME;
		}
		else {
			return false;
		}
	}

	public function admin_user_name($id) {
		$CI = &get_instance();
		$CI->load->database();
		
		if(!$id) {
			return false;
		}
		$user_name = $CI->db->query("SELECT USER_NAME FROM user WHERE USER_ID=". $id)->row();
		$user_name = $user_name->USER_NAME;

		return $user_name;

	}

	public function get_reseller_user_id($reseller_id) {
		$CI = &get_instance();
		$CI->load->database();
		
		if(!$reseller_id) {
			return false;
		}
		$user_id = $CI->db->query("SELECT USER_ID FROM user WHERE RESELLER_ID=". $reseller_id)->row();
		$user_id = $user_id->USER_ID;

		return $user_id;
	}

	public function get_reseller_id($user_id) {
		$CI = &get_instance();
		$CI->load->database();

		if($this->admin_verify()) {
			return false;
		}
		
		if(!$user_id) {
			return false;
		}
		$reseller_id = $CI->db->query("SELECT RESELLER_ID FROM user WHERE USER_ID=". $user_id)->row();
		if(!count($reseller_id)) {
			return false;
		}

		return $reseller_id->RESELLER_ID;
	}

	public function get_parent_id($user_id) {
		$CI = &get_instance();
		$CI->load->database();

		if($this->admin_verify()) {
			return false;
		}
		
		if(!$user_id) {
			return false;
		}
		$reseller_id = $CI->db->query("SELECT PARENT_ID FROM reseller WHERE USER_ID=". $user_id)->row();
		if(!count($reseller_id)) {
			return false;
		}
		$parent_id = $this->enc($reseller_id->PARENT_ID, 'decrypt');

		return $parent_id;
	}

	public function reseller_load_balance_margin($reseller_id, $type, $amount) {
		$CI = &get_instance();
		$CI->load->database();
		$data = array();
		$error = array();

		// check row to reseller_trans table
		$balance = $CI->db->query("SELECT BALANCE FROM reseller_trans WHERE RESELLER_ID='".$reseller_id."' ORDER BY RESELLER_TRANS_ID DESC LIMIT 1")->row();
		if(count($balance)) {
			$balance = $balance->BALANCE;
		}
		else {
			$balance = 0;
		}

		// if not & give return balance then shoot an error
		if($balance==0 && $type == 'return') {
			$error[] = "This user has empty balance. you can not give return balance";
			$data['errors'] = $error;
			$err_str = $CI->load->view("admin/reseller/reseller_payment", $data, true);
			echo $err_str;
			exit();
		}

		// if has balance & return is greate than current balance, shoot error
		if($balance < $amount && $type == 'return') {
			$error[] = "Balance less then amount, value can not be minus";
			$data['errors'] = $error;
			$err_str = $CI->load->view("admin/reseller/reseller_payment", $data, true);
			echo $err_str;
			exit();
		}

		// if balance type is transaction then add balance
		if($type == 'return') {
			$balance = $balance - $amount;
		}

		// if balance is return then minus balance
		if($type == 'transaction') {
			$balance = $balance + $amount;
		}
		
		return $balance;
	}

	public function reseller_balance_margin($reseller_id, $type, $amount) {
		$CI = &get_instance();
		$CI->load->database();
		$data = array();
		$error = array();

		// check row to reseller_trans table
		$balance = $CI->db->query("SELECT BALANCE FROM reseller_trans WHERE RESELLER_ID='".$reseller_id."' ORDER BY RESELLER_TRANS_ID DESC LIMIT 1")->row();
		if(count($balance)) {
			$balance = $balance->BALANCE;
		}
		else {
			$balance = 0;
		}

		// if not & give return balance then shoot an error
		if($balance==0 && $type == 'return') {
			$error[] = "This user has empty balance. you can not give return balance";
			$data['errors'] = $error;
			$err_str = $CI->load->view("admin/reseller/reseller_payment", $data, true);
			echo $err_str;
			exit();
		}

		// if has balance & return is greate than current balance, shoot error
		if($balance < $amount && $type == 'return') {
			$error[] = "Balance less then amount, value can not be minus";
			$data['errors'] = $error;
			$err_str = $CI->load->view("admin/reseller/reseller_payment", $data, true);
			echo $err_str;
			exit();
		}

		// if balance type is transaction then add balance
		if($type == 'return') {
			$balance = $balance - $amount;
		}

		// if balance is return then minus balance
		if($type == 'transaction') {
			$balance = $balance + $amount;
		}
		
		return $balance;
	}

	public function service_name($service_id) {
		$CI = &get_instance();
		$CI->load->database();
		
		if(!$service_id) {
			return false;
		}
		$service_name = $CI->db->query("SELECT SERVICE_NAME FROM service_settings WHERE SERVICE_ID=". $service_id)->row();
		$service_name = $service_name->SERVICE_NAME;

		return $service_name;
	}

	public function get_service_id($service_code) {
		$CI = &get_instance();
		$CI->load->database();
		
		if(!$service_code) {
			return false;
		}
		$service_id = $CI->db->query("SELECT SERVICE_ID FROM service_settings WHERE SERVICE_CODE='".$service_code."'")->row();
		$service_id = $service_id->SERVICE_ID;

		return $service_id;
	}

	public function operator_details($service_id) {
		$CI = &get_instance();
		$CI->load->database();
		
		if(!$service_id) {
			return false;
		}
		$service_name = $CI->db->query("SELECT * FROM card_service WHERE CARD_SERVICE_ID=". $service_id)->row();

		return $service_name;
	}

	public function reseller_name($reseller_id) {
		$CI = &get_instance();
		$CI->load->database();
		
		if(!$reseller_id) {
			return false;
		}
		$reseller_name = $CI->db->query("SELECT USER_NAME FROM user WHERE RESELLER_ID=". $reseller_id)->row();
		$reseller_name = $reseller_name->USER_NAME;

		return $reseller_name;
	}



	public function getOS() { 

	    $user_agent = $_SERVER['HTTP_USER_AGENT'];
	    $os_platform = "Unknown OS Platform";

	    $os_array = array(
                        '/windows nt 10/i'     =>  'Windows 10',
                        '/windows nt 6.3/i'     =>  'Windows 8.1',
                        '/windows nt 6.2/i'     =>  'Windows 8',
                        '/windows nt 6.1/i'     =>  'Windows 7',
                        '/windows nt 6.0/i'     =>  'Windows Vista',
                        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                        '/windows nt 5.1/i'     =>  'Windows XP',
                        '/windows xp/i'         =>  'Windows XP',
                        '/windows nt 5.0/i'     =>  'Windows 2000',
                        '/windows me/i'         =>  'Windows ME',
                        '/win98/i'              =>  'Windows 98',
                        '/win95/i'              =>  'Windows 95',
                        '/win16/i'              =>  'Windows 3.11',
                        '/macintosh|mac os x/i' =>  'Mac OS X',
                        '/mac_powerpc/i'        =>  'Mac OS 9',
                        '/linux/i'              =>  'Linux',
                        '/ubuntu/i'             =>  'Ubuntu',
                        '/iphone/i'             =>  'iPhone',
                        '/ipod/i'               =>  'iPod',
                        '/ipad/i'               =>  'iPad',
                        '/android/i'            =>  'Android',
                        '/blackberry/i'         =>  'BlackBerry',
                        '/webos/i'              =>  'Mobile'
	                );

	    foreach($os_array as $regex => $value) { 
	        if (preg_match($regex, $user_agent)) {
	            $os_platform = $value;
	        }
	    }   

	    return $os_platform;

	}


	public function getBrowser() {

	    $user_agent = $_SERVER['HTTP_USER_AGENT'];
	    $browser = "Unknown Browser";

	    $browser_array = array(
                            '/msie/i'       =>  'Internet Explorer',
                            '/firefox/i'    =>  'Firefox',
                            '/safari/i'     =>  'Safari',
                            '/chrome/i'     =>  'Chrome',
                            '/edge/i'       =>  'Edge',
                            '/opera/i'      =>  'Opera',
                            '/netscape/i'   =>  'Netscape',
                            '/maxthon/i'    =>  'Maxthon',
                            '/konqueror/i'  =>  'Konqueror',
                            '/mobile/i'     =>  'Handheld Browser'
	                    );

	    foreach ($browser_array as $regex => $value) { 
	        if (preg_match($regex, $user_agent)) {
	            $browser    =   $value;
	        }
	    }
	    return $browser;

	}

	public function device_type(){
		$device = "";
	    $aMobileUA = array(
	        '/iphone/i' => 'iPhone', 
	        '/ipod/i' => 'iPod', 
	        '/ipad/i' => 'iPad', 
	        '/android/i' => 'Android', 
	        '/blackberry/i' => 'BlackBerry', 
	        '/webos/i' => 'Mobile'
	    );

	    //Return true if Mobile User Agent is detected
	    foreach($aMobileUA as $sMobileKey => $sMobileOS){
	        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
	            $device = $sMobileOS;
	        }
	        else {
	        	$device = "Desktop";
	        }
	    }
	    return $device;
	}

	public function get_ip_address() {
	    // check for shared internet/ISP IP
	    if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
	        return $_SERVER['HTTP_CLIENT_IP'];
	    }

	    // check for IPs passing through proxies
	    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        // check if multiple ips exist in var
	        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
	            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	            foreach ($iplist as $ip) {
	                if (validate_ip($ip))
	                    return $ip;
	            }
	        } else {
	            if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
	                return $_SERVER['HTTP_X_FORWARDED_FOR'];
	        }
	    }
	    if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
	        return $_SERVER['HTTP_X_FORWARDED'];
	    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
	        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
	    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
	        return $_SERVER['HTTP_FORWARDED_FOR'];
	    if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
	        return $_SERVER['HTTP_FORWARDED'];

	    // return unreliable ip since all else failed
	    return $_SERVER['REMOTE_ADDR'];
	}

	public function get_bulk_id() {

		$CI = &get_instance();
		$CI->load->database();
		$data = array();
		$error = array();

		$bulk_id = $CI->db->query("SELECT BULK_SMS_ID FROM sms_data ORDER BY BULK_SMS_ID DESC LIMIT 1")->row();
		if(count($bulk_id)) {
			$bulk_id = $bulk_id->BULK_SMS_ID + 1;
		}
		else {
			$bulk_id = 1;
		}

		return $bulk_id;

	}

	public function get_reseller_cur_balance($reseller_id) {

		$CI = &get_instance();
		$CI->load->database();
		$data = array();
		$error = array();

		// check row to reseller_trans table
		$balance = $CI->db->query("SELECT BALANCE FROM reseller_trans WHERE RESELLER_ID='".$reseller_id."' ORDER BY RESELLER_TRANS_ID DESC LIMIT 1")->row();
		if(count($balance)) {
			$balance = $balance->BALANCE;
		}
		else {
			$balance = 0;
		}
		return $balance;

	}

	public function user_log($user_email, $type) {
		$ci = &get_instance();
		$ci->load->database();
		/*if($this->admin_verify()) {
			$user_data = $ci->db->query("SELECT USER_ID FROM user WHERE USER_EMAIL='".$user_email."'")->row();
		}
		else {
			$user_data = $ci->db->query("SELECT  r.*, u.* FROM reseller AS r INNER JOIN user AS u ON r.USER_ID=u.USER_ID WHERE u.USER_EMAIL='".$user_email."'")->row();
		}*/
		$user_data = $ci->db->query("SELECT USER_ID, USER_TYPE FROM user WHERE USER_EMAIL='".$user_email."'")->row();
		
		$user_id = $user_data->USER_ID;
		$user_type = ($this->admin_verify()) ? "Admin" : $user_data->USER_TYPE;
		$log_time = $this->now();
		$screen_name = 0;
		$screen_value = 0;
		$browser = $this->getBrowser();
		$platform = $this->getOS();
		$log_type = $this->device_type();
		$ip = $this->get_ip_address();
		
		if($type == "login") {

			#insert data
			$sql = "
			INSERT INTO user_log
			(USER_ID, USER_TYPE, LOG_TIME, SCREEN_NAME, SCREEN_VALUE, BROWSER, PLATFORM, LOG_TYPE, IP)
			VALUES
			(?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$ci->db->query($sql, array( $user_id, $user_type, $log_time, $screen_name, $screen_value, $browser, $platform, $log_type, $ip ));

			if( !$ci->db->insert_id() ){
				die("Again try to login");
			}
			return true;

		}
		else if($type == "logout") {

			$log_id = $ci->db->query("SELECT LOG_ID FROM user_log WHERE USER_ID='".$user_id."' ORDER BY LOG_ID DESC LIMIT 1")->row()->LOG_ID;
			$update = $ci->db->query("UPDATE user_log SET LOGOUT_TIME='".$this->now()."' WHERE LOG_ID='".$log_id."' LIMIT 1");
			if(!$update) {
				die("Logout time could not setup");
			}
			return true;

		}
		else {
			die("user_log type is invalid");
		}
	}

	public function user_last_login($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$last_login = $ci->db->query("SELECT LOG_TIME FROM user_log WHERE USER_ID='".$user_id."' ORDER BY LOG_ID DESC LIMIT 1")->row();
		if(!count($last_login)) {
			return false;
		}
		return $last_login->LOG_TIME;
	}

	public function reseller_rate_mod($reseller_id) {
		$ci = &get_instance();
		$ci->load->database();

		$bool = $ci->db->query("SELECT * FROM rate_mod WHERE RESELLER_ID='".$reseller_id."'")->result();
		if(count($bool)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function service_permisison_check($service_name, $user_id) {

		$ci = &get_instance();
		$ci->load->database();
		$data = null;
		$url_prefix = $this->settings()->site_url_prefix;

		if($this->admin_verify()) {
			return true;
		}

		$services = $ci->db->query("SELECT SERVICE_PERMISSION FROM reseller WHERE USER_ID='".$user_id."'")->row();
		if(!count($services)) {
			$this->message_board('Your service did not setup yet');
			$this->force_redirect($url_prefix . 'exception_found');
		}
		$services = explode(",", $services->SERVICE_PERMISSION);
		if(in_array($service_name, $services)) {
			return true;
		}
		else {
			$this->message_board('You were in the wrong destination');
			$this->force_redirect($url_prefix . 'exception_found');
			return false;
		}

	}

	public function rate_mod_data($service_id, $reseller_id) {
		$ci = &get_instance();
		$ci->load->database();

		if($this->admin_verify()) {
			return true;
		}

		$rate_data = $ci->db->query("SELECT COMISSION, CHARGE FROM rate_mod WHERE SERVICE_ID='".$service_id."' AND RESELLER_ID='".$reseller_id."'")->row();
		if(!count($rate_data)) {
			return false;
		}
		return $rate_data;
	}

	public function pin_verification($service_id, $reseller_id, $pin) {
		$ci = &get_instance();
		$ci->load->database();

		if($this->admin_verify()) {
			return true;
		}

		$pin = $this->enc($pin, 'encrypt');

		$pin_data = $ci->db->query("SELECT * FROM user_pin WHERE RESELLER_ID='".$reseller_id."'")->result();
		if(count($pin_data)) {
			$pin_check = $ci->db->query("SELECT * FROM user_pin WHERE SERVICE_ID='".$service_id."' AND RESELLER_ID='".$reseller_id."' AND PIN='".$pin."'")->row();
			if(count($pin_check)) {
				return true;
			}
			else {
				return false;
			}
		}

		$reseller_pin = $ci->db->query("SELECT * FROM reseller WHERE RESELLER_ID='".$reseller_id."' AND PIN='".$pin."'")->row();
		if(count($reseller_pin)) {
			return true;
		}
		return false;
	}

	public function reseller_pin_verify($reseller_id, $pin) {
		$ci = &get_instance();
		$ci->load->database();

		if($this->admin_verify()) {
			return true;
		}

		$pin = $this->enc($pin, 'encrypt');

		$reseller_pin = $ci->db->query("SELECT * FROM reseller WHERE RESELLER_ID='".$reseller_id."' AND PIN='".$pin."'")->row();
		if(count($reseller_pin)) {
			return true;
		}
		return false;
	}

	public function get_perent_id($reseller_id) {

		$ci = &get_instance();
		$ci->load->database();
		$parent_id = null;

		if($this->admin_verify()) {
			$parent_id = 0;
		}

		$parent = $ci->db->query("SELECT PARENT_ID FROM reseller WHERE RESELLER_ID='".$reseller_id."'")->row();
		if(!count($parent)) {
			$parent_id = 0;
		}
		$parent_id = $parent->PARENT_ID;
		return $parent_id;

	}

	public function service_check($srv_code, $srv_id) {
		$ci = &get_instance();
		$ci->load->database();

		$chk = $ci->db->query("SELECT PREFIX FROM service_settings WHERE SERVICE_ID='".$srv_id."'")->row();
		
		if($chk->PREFIX == $srv_code) {
			return true;
		}
		else {
			return false;
		}
	}

	public function servive_wise_cost($reseller_id, $amount, $service_id) {

		$ci = &get_instance();
		$ci->load->database();

		if($reseller_id) {
			$rate_data = $this->rate_mod_data($service_id, $reseller_id);
			if(count($rate_data)) {
				$comission = ($rate_data->COMISSION * $amount)/100;
				$charge = ($rate_data->CHARGE * $amount)/100;

				$comission_amt = $comission - $charge;
				$cost = $amount - $comission_amt;
			}
			else {
				$cost = $amount;
			}
		}
		else if ($this->admin_verify()) {
			$cost = $amount;
		}
		else {
			$cost = $amount;
		}
		return $cost;

	}


	/***********************************************************************
	********************* CHINA DEAL PROJECT FUNCTIONS *********************
	***********************************************************************/
	
	public function reffer_id_verify($id) {

		$ci = &get_instance();
		$ci->load->database();

		// $id = $this->enc($id, 'encrypt');

		$reffer = $ci->db->query("SELECT * FROM user_registration WHERE MY_ID='".$id."'")->result();
		if(count($reffer) > 0) {
			return true;
		}
		return false;

	}

	public function my_rand() {
		return strtoupper(substr(md5(rand()), 0, 6));
	}

	public function rand_pin() {
		return strtoupper(substr( md5(uniqid(rand(), true)), 0, 16));
	}

	public function generate_subscription_pin() {

		$ci = &get_instance();
		$ci->load->database();
		$rand = $this->rand_pin();
		
		$chk_rand = $ci->db->query("SELECT * FROM pin_code WHERE PIN_CODE='".$rand."'")->row();
		if(count($chk_rand) > 0) {
			$this->generate_subscription_pin();
		}
		else {
			return $rand;
		}
	}

	public function check_rand() {

		$ci = &get_instance();
		$ci->load->database();
		$rand = $this->my_rand();
		
		$chk_rand = $ci->db->query("SELECT * FROM user_registration WHERE MY_ID='".$rand."'")->row();
		if(count($chk_rand) > 0) {
			$this->check_rand();
		}
		else {
			return $rand;
		}
	}

	public function generate_level($reffer_id) {

		$ci = &get_instance();
		$ci->load->database();
		$level_data = array();

		$rfr_my_id = $ci->db->query("SELECT * FROM user_registration WHERE MY_ID='".$reffer_id."'")->row();
		if(!count($rfr_my_id)) {
			return false;
		}

		$reg_id = $rfr_my_id->USER_REG_ID;
		$user_id = $ci->db->query("SELECT USER_ID FROM user WHERE USER_REG_ID='".$reg_id."'")->row();
		if(!count($user_id)) {
			return false;
		}

		$level_data['lvl_1'] = $user_id->USER_ID;
		$level_data['lvl_2'] = $rfr_my_id->LEVEL_1;
		$level_data['lvl_3'] = $rfr_my_id->LEVEL_2;
		$level_data['lvl_4'] = $rfr_my_id->LEVEL_3;
		$level_data['lvl_5'] = $rfr_my_id->LEVEL_4;
		$level_data['lvl_6'] = $rfr_my_id->LEVEL_5;
		$level_data['lvl_7'] = $rfr_my_id->LEVEL_6;

		return $level_data;
	}

	public function user_pin_verify($user_id, $pin) {
		$ci = &get_instance();
		$ci->load->database();

		/*if($this->admin_verify()) {
			return true;
		}*/

		$pin = $this->enc($pin, 'encrypt');

		$user_pin = $ci->db->query("SELECT * FROM user_pin WHERE USER_ID='".$user_id."' AND PIN='".$pin."'")->row();
		if(count($user_pin)) {
			return true;
		}
		return false;
	}

	public function verify_public_id_and_data($public_id) {
		$ci = &get_instance();
		$ci->load->database();

		$user_data = $ci->db->query("SELECT ur.*, u.USER_ID FROM user_registration AS ur INNER JOIN user AS u ON ur.USER_REG_ID=u.USER_REG_ID WHERE ur.MY_ID='".$public_id."'")->row();
		if(!count($user_data)) {
			return false;
		}

		return $user_data;
	}

	public function settings_data() {
		$ci = &get_instance();
		$ci->load->database();

		$data = $ci->db->query("SELECT * FROM settings WHERE SETTINGS_ID=1")->row();
		if(!count($data)) {
			return false;
		}

		return $data;
	}

	public function verify_first_balance($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$chk = $ci->db->query("SELECT * FROM user_balance WHERE USER_ID='".$user_id."'")->row();
		if(count($chk)) {
			return false;
		}
		return true;
	}

	public function verify_non_premium($public_id) {
		$ci = &get_instance();
		$ci->load->database();

		$chk = $ci->db->query("SELECT * FROM user_registration WHERE ACC_STATUS=0 AND MY_ID='".$public_id."'")->row();
		
		if(count($chk)) {
			return true;
		}
		return false;
	}

	public function user_balance($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$balance = $ci->db->query("SELECT SUM(AMOUNT * CASE BALANCE_TYPE WHEN 'GET' THEN 1 ELSE -1 END) AS BALANCE FROM user_balance WHERE USER_ID='".$user_id."'")->row();
		if(!count($balance)) {
			return false;
		}
		
		$balance = $balance->BALANCE;
		return $balance;
	}
	
	public function wallet_balance($user_id, $wallet_name) {
		$ci = &get_instance();
		$ci->load->database();

		$balance = $ci->db->query("SELECT SUM(AMOUNT * CASE BALANCE_TYPE WHEN 'GET' THEN 1 ELSE -1 END) AS BALANCE FROM $wallet_name WHERE USER_ID='".$user_id."'")->row();
		if(!count($balance)) {
			return false;
		}
		
		$balance = $balance->BALANCE;
		return $balance;
	}
	
	public function shopping_balance($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$balance = $ci->db->query("SELECT SUM(AMOUNT * CASE BALANCE_TYPE WHEN 'GET' THEN 1 ELSE -1 END) AS BALANCE FROM shopping_wallet WHERE USER_ID='".$user_id."'")->row();
		if(!count($balance)) {
			return false;
		}
		
		$balance = $balance->BALANCE;
		return $balance;
	}

	// joining wallet
	public function user_joining_balance($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$balance = $ci->db->query("SELECT SUM(AMOUNT * CASE BALANCE_TYPE WHEN 'GET' THEN 1 ELSE -1 END) AS BALANCE FROM joining_wallet WHERE USER_ID='".$user_id."'")->row();
		if(!count($balance)) {
			return false;
		}
		
		$balance = $balance->BALANCE;
		return $balance;
	}

	// shopping wallet
	public function user_shopping_balance($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$balance = $ci->db->query("SELECT SUM(AMOUNT * CASE BALANCE_TYPE WHEN 'GET' THEN 1 ELSE -1 END) AS BALANCE FROM shopping_wallet WHERE USER_ID='".$user_id."'")->row();
		if(!count($balance)) {
			return false;
		}
		
		$balance = $balance->BALANCE;
		return $balance;
	}

	// adds wallet
	public function user_adds_balance($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$balance = $ci->db->query("SELECT SUM(AMOUNT * CASE BALANCE_TYPE WHEN 'GET' THEN 1 ELSE -1 END) AS BALANCE FROM adds_wallet WHERE USER_ID='".$user_id."'")->row();
		if(!count($balance)) {
			return false;
		}
		
		$balance = $balance->BALANCE;
		return $balance;
	}

	// refer wallet
	public function user_refer_balance($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$balance = $ci->db->query("SELECT SUM(AMOUNT * CASE BALANCE_TYPE WHEN 'GET' THEN 1 ELSE -1 END) AS BALANCE FROM refer_wallet WHERE USER_ID='".$user_id."'")->row();
		if(!count($balance)) {
			return false;
		}
		
		$balance = $balance->BALANCE;
		return $balance;
	}

	public function user_id_via_reffer_id($reffer_id) {
		$ci = &get_instance();
		$ci->load->database();

		$user_id = $ci->db->query("SELECT u.USER_ID FROM user_registration AS ur INNER JOIN user AS u ON u.USER_REG_ID=ur.USER_REG_ID WHERE ur.MY_ID='".$reffer_id."'")->row();
		if(!count($user_id)) {
			return false;
		}

		$user_id = $user_id->USER_ID;
		return $user_id;
	}

	public function user_id_via_my_id($my_id) {
		$ci = &get_instance();
		$ci->load->database();

		$user_id = $ci->db->query("SELECT u.USER_ID FROM user_registration AS ur INNER JOIN user AS u ON u.USER_REG_ID=ur.USER_REG_ID WHERE ur.MY_ID='".$my_id."'")->row();
		if(!count($user_id)) {
			return false;
		}

		$user_id = $user_id->USER_ID;
		return $user_id;
	}

	public function reffer_id_via_user_id($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$reffer_id = $ci->db->query("SELECT ur.REFFER_ID FROM user_registration AS ur INNER JOIN user AS u ON u.USER_REG_ID=ur.USER_REG_ID WHERE u.USER_ID='".$user_id."'")->row();
		if(!count($reffer_id)) {
			return false;
		}

		$reffer_id = $reffer_id->REFFER_ID;
		return $reffer_id;
	}

	public function my_id_via_user_id($user_id) {
		$ci = &get_instance();
		$ci->load->database();

		$my_id = $ci->db->query("SELECT ur.MY_ID FROM user_registration AS ur INNER JOIN user AS u ON u.USER_REG_ID=ur.USER_REG_ID WHERE u.USER_ID='".$user_id."'")->row();
		if(!count($my_id)) {
			return false;
		}

		$my_id = $my_id->MY_ID;
		return $my_id;
	}

	public function user_package_data() {
		$ci = &get_instance();
		$ci->load->database();
		$user_id = $this->enc($_SESSION['user']['USER_ID'], 'decrypt');

		$package_data = $ci->db->query("SELECT pd.PACKAGE_ID, pd.ACTIVATION_DATE, pd.EXPIRE_DATE, ps.PACKAGE_NAME, ps.FACEBOOK_LINK, ps.PTC_LINK, ps.YOUTUBE_LINK, ps.GAME_PERMISSION, ps.PACKAGE_AMOUNT, ps.PACKAGE_VALIDITY FROM package_data AS pd INNER JOIN package_setup AS ps ON pd.PACKAGE_ID=ps.PACKAGE_ID WHERE pd.USER_ID='{$user_id}'")->row();
		return $package_data;
	}


}
