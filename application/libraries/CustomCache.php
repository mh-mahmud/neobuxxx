<?php
class CustomCache{
	
	var $static_url = 'http://static.domain.com';

	# starts session
	function CustomCache(){
		if(!isset($_SESSION)){
			session_start();
		}
	}

	# get user information by user id from database
	function user_maker($user_id, $output_filed){
		# $output_filed - get db field name

		$CI =&get_instance();
		$cache_name = 'user_maker';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		
		if( !$html = $CI->cache->get($cache_name, 'user') ){
			$data['html'] = array();
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM USER ORDER BY USER_ID DESC");
			$get_record = $get_record->result();
			
			foreach( $get_record as $k=>$v ){
				$html[] = $v->USER_ID.'|'.$v->ROLE_ID.'|'.$v->USER_NAME.'|'.$v->USER_EMAIL.'|'.$v->USER_PHONE.'|'.$v->CREATED_DATE.'|'.
									$v->UPDATED_DATE.'|'.$v->STATUS.'|'.$v->COMPANY_ID;
			}

			$CI->cache->save($cache_name, $html, 'user', 604800);		
		}
		
		foreach($html as $k=>$v){
			$Value = explode('|', $v);
			if( $Value[0]==$user_id ){
				switch($output_filed){
					case 'USER_ID': return $Value[0]; break;
					case 'ROLE_ID': return $Value[1]; break;
					case 'USER_NAME': return $Value[2]; break;
					case 'USER_EMAIL': return $Value[3]; break;
					case 'USER_PHONE': return $Value[4]; break;
					case 'USER_CREATED_DATE': return $Value[5]; break;
					case 'USER_UPDATED_DATE': return $Value[6]; break;
					case 'STATUS': return $Value[7]; break;
					case 'COMPANY_ID': return $Value[8]; break;
				}
				
			}

		}

		return false;
	}
	
	# get company information by company id from database
	function company_maker($key, $output_filed){
		# $output_filed - get db field name

		$CI =&get_instance();
		$group_name = 'company';
		$cache_name = 'company_maker';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		
		if( !$html = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = array();
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_COMPANY_PROFILE ORDER BY COMPANY_ID DESC");
			$get_record = $get_record->result();
			
			foreach( $get_record as $k=>$v ){
				$html[] = $v->COMPANY_ID.'|'.$v->COMPANY_NAME.'|'.$v->COMPANY_ADDRESS.'|'.$v->COMPANY_WEBSITE.'|'.$v->COMPANY_EMAIL.'|'.$v->COMPANY_FACEBOOK.'|'.
									$v->COMPANY_TWITTER.'|'.$v->COMPANY_LINKEDIN.'|'.$v->COMPANY_PHONE.'|'.$v->COMPANY_MOBILE.'|'.$v->COMPANY_FAX.'|'.
									$v->COMPANY_TYPE.'|'.$v->COMPANY_DESCRIPTION.'|'.$v->MEMBERSHIP.'|'.$v->SERVICES.'|'.$v->COMPANY_DESCRIPTION.'|'.$v->STATUS;
			}

			$CI->cache->save($cache_name, $html, $group_name, 604800);		
		}
		
		foreach($html as $k=>$v){
			$Value = explode('|', $v);
			if( $Value[0]==$key ){
				switch($output_filed){
					case 'COMPANY_ID': return $Value[0]; break;
					case 'COMPANY_NAME': return $Value[1]; break;
					case 'COMPANY_ADDRESS': return $Value[2]; break;
					case 'COMPANY_WEBSITE': return $Value[3]; break;
					case 'COMPANY_EMAIL': return $Value[4]; break;
					case 'COMPANY_FACEBOOK': return $Value[5]; break;
					case 'COMPANY_TWITTER': return $Value[6]; break;
					case 'COMPANY_LINKEDIN': return $Value[7]; break;
					case 'COMPANY_PHONE': return $Value[8]; break;
					case 'COMPANY_MOBILE': return $Value[9]; break;
					case 'COMPANY_TYPE': return $Value[10]; break;
					case 'COMPANY_DESCRIPTION': return $Value[11]; break;
					case 'MEMBERSHIP': return $Value[12]; break;
					case 'SERVICES': return $Value[13]; break;
					case 'COMPANY_DESCRIPTION': return $Value[14]; break;
					case 'STATUS': return $Value[15]; break;
				}
				
			}

		}

		return false;
	}
	
	# get user role
	function get_user_role($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'user_role_option';
		$group_name	=	'role';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = 'user_role_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = 'user_role_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_NNS_ROLE WHERE STATUS=7  ORDER BY ROLE_NAME");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->ROLE_ID.'">'.ucwords(str_replace('_',' ',$v->ROLE_NAME)).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->ROLE_ID.'|'.$v->ROLE_NAME.'">'.ucwords(str_replace('_',' ',$v->ROLE_NAME)).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->ROLE_ID.'">'.ucwords(str_replace('_',' ',$v->ROLE_NAME)).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}


	
	# get division list
	function get_division_option($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'division';
		$group_name = 'division';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_DIVISION WHERE STATUS=7 ORDER BY DIVISION_NAME");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->DIVISION_ID.'">'.ucwords(str_replace('_',' ',$v->DIVISION_NAME)).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->DIVISION_ID.'|'.$v->DIVISION_NAME.'">'.ucwords(str_replace('_',' ',$v->DIVISION_NAME)).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->DIVISION_ID.'">'.ucwords(str_replace('_',' ',$v->DIVISION_NAME)).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}
	
	# get district list
	function get_district_option($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'district';
		$group_name = 'district';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_DISTRICT WHERE STATUS=7 ORDER BY DISTRICT_NAME");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->DISTRICT_ID.'" data-div="'.$v->DIVISION_ID.'">'.ucwords(str_replace('_',' ',$v->DISTRICT_NAME)).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->DISTRICT_ID.'|'.$v->DISTRICT_NAME.'" data-div="'.$v->DIVISION_ID.'">'.ucwords(str_replace('_',' ',$v->DISTRICT_NAME)).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->DISTRICT_ID.'">'.ucwords(str_replace('_',' ',$v->DISTRICT_NAME)).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}
	
	#get upazila list of a district
	function get_upazila_option($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'upazila';
		$group_name = 'upazila';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_UPAZILA WHERE STATUS=7 ORDER BY UPAZILA_NAME");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->UPAZILA_ID.'" data-district="'.$v->DISTRICT_ID.'">'.ucwords(str_replace('_',' ',$v->UPAZILA_NAME)).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->UPAZILA_ID.'|'.$v->UPAZILA_NAME.'" data-district="'.$v->DISTRICT_ID.'">'.ucwords(str_replace('_',' ',$v->UPAZILA_NAME)).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->UPAZILA_ID.'">'.ucwords(str_replace('_',' ',$v->UPAZILA_NAME)).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}
	
	#get union list 
	function get_union_option($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'union';
		$group_name = 'union';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_UNION WHERE STATUS=7 ORDER BY UNION_NAME");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->UNION_ID.'" data-upazila="'.$v->UPAZILA_ID.'">'.ucwords(str_replace('_',' ',$v->UNION_NAME)).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->UNION_ID.'|'.$v->UNION_NAME.'" data-upazila="'.$v->UPAZILA_ID.'">'.ucwords(str_replace('_',' ',$v->UNION_NAME)).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->UNION_ID.'">'.ucwords(str_replace('_',' ',$v->UNION_NAME)).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}

	#get company list
	function get_available_company($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'company';
		$group_name = 'company';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT TBL_COMPANY_PROFILE.* FROM TBL_COMPANY_PROFILE WHERE STATUS=7 ORDER BY COMPANY_NAME");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->COMPANY_ID.'">'.ucwords(str_replace('_',' ',$v->COMPANY_NAME)).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->COMPANY_ID.'|'.$v->COMPANY_NAME.'">'.ucwords(str_replace('_',' ',$v->COMPANY_NAME)).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->COMPANY_ID.'">'.ucwords(str_replace('_',' ',$v->COMPANY_NAME)).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}
	
	#get caravan list
	function get_available_caravan($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'caravan';
		$group_name = 'caravan';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT TBL_CARAVAN_PROFILE.* FROM TBL_CARAVAN_PROFILE WHERE TBL_CARAVAN_PROFILE.STATUS=7 ");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->CARAVAN_ID.'">'.ucwords(str_replace('_',' ',$v->REGISTRATION_NO)).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->CARAVAN_ID.'|'.$v->REGISTRATION_NO.'">'.ucwords(str_replace('_',' ',$v->REGISTRATION_NO)).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->CARAVAN_ID.'">'.ucwords(str_replace('_',' ',$v->REGISTRATION_NO)).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}
	
	#get available courses
	function get_available_course_name($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'course_option';
		$group_name = 'course';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT * FROM TBL_COURSE WHERE STATUS = 7 ORDER BY COURSE_TITLE");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->COURSE_ID.'">'.ucwords(str_replace('_',' ',$v->COURSE_TITLE)).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->COURSE_ID.'|'.$v->COURSE_TITLE.'">'.ucwords(str_replace('_',' ',$v->COURSE_TITLE)).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->COURSE_ID.'">'.ucwords(str_replace('_',' ',$v->COURSE_TITLE)).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}
	
	#get available courses corresponding to slot
	function get_available_slot_course($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'course_slot';
		$group_name = 'course_slot';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		//$CI->cache->remove_group($group_name);
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT TBL_SLOT.*,TBL_COURSE.COURSE_TITLE FROM TBL_SLOT LEFT JOIN TBL_COURSE ON TBL_SLOT.COURSE_ID=TBL_COURSE.COURSE_ID WHERE TBL_SLOT.STATUS = 2 ");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->SLOT_ID.'">'.ucwords(str_replace('_',' ',$v->COURSE_TITLE .' ('.$v->SLOT_YEAR.','.$CI->webspice->month_convert($v->SLOT_MONTH).')')).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->SLOT_ID.'|'.$v->COURSE_TITLE.'('.$v->SLOT_YEAR.','.$CI->webspice->month_convert($v->SLOT_MONTH).')'.'">'.ucwords(str_replace('_',' ',$v->COURSE_TITLE .' ('.$v->SLOT_YEAR.','.$CI->webspice->month_convert($v->SLOT_MONTH).')')).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->SLOT_ID.'">'.ucwords(str_replace('_',' ',$v->COURSE_TITLE .' ('.$v->SLOT_YEAR.','.$CI->webspice->month_convert($v->SLOT_MONTH).')')).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}
	
	#get assigned course list 
	function get_available_assigned_course($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'assigned_course_area';
		$group_name = 'assigned_course_area';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT TBL_SLOT.*,TBL_COURSE.COURSE_TITLE FROM TBL_SLOT LEFT JOIN TBL_COURSE ON TBL_SLOT.COURSE_ID=TBL_COURSE.COURSE_ID WHERE TBL_SLOT.STATUS = 2 ");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->SLOT_ID.'">'.ucwords(str_replace('_',' ',$v->COURSE_TITLE .' ('.$v->SLOT_YEAR.','.$CI->webspice->month_convert($v->SLOT_MONTH).')')).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->SLOT_ID.'|'.$v->COURSE_TITLE.'">'.ucwords(str_replace('_',' ',$v->COURSE_TITLE .' ('.$v->SLOT_YEAR.','.$CI->webspice->month_convert($v->SLOT_MONTH).')')).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->SLOT_ID.'">'.ucwords(str_replace('_',' ',$v->COURSE_TITLE .' ('.$v->SLOT_YEAR.','.$CI->webspice->month_convert($v->SLOT_MONTH).')')).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}
	
	#get available courses corresponding to slot
	function get_available_batch($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'batch';
		$group_name = 'batch';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		
		$CI->load->library(array('cache','webspice'));
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT TBL_BATCH.BATCH_ID,TBL_BATCH.BATCH_NO,TBL_SLOT.SLOT_ID 
																		FROM TBL_BATCH LEFT JOIN TBL_SLOT ON TBL_SLOT.SLOT_ID=TBL_BATCH.SLOT_ID 
																		WHERE TBL_BATCH.STATUS = 2 OR TBL_BATCH.STATUS = 11 OR TBL_BATCH.STATUS = 12");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->BATCH_ID.'" slot-id="'.$v->SLOT_ID.'">'.$CI->webspice->ordinal($v->BATCH_NO ).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->BATCH_ID.'|'.$CI->webspice->ordinal($v->BATCH_NO ).'"	slot-id="'.$v->SLOT_ID.'">'.$CI->webspice->ordinal($v->BATCH_NO ).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->BATCH_ID.'"	slot-id="'.$v->SLOT_ID.'">'.$CI->webspice->ordinal($v->BATCH_NO ).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}
	
	function get_available_upazila_district($type='option'){
		# type = option/option_mix/list
		$CI =&get_instance();
		$cache_name = 'upazila_district';
		$group_name = 'upazila';
		
		# to delete cache use: $this->cache->remove_group('group_name');
		$CI->load->library('cache');
		$type == 'option_mix' ? $cache_name = $cache_name.'_option_mix' : $cache_name = $cache_name;
		$type == 'list' ? $cache_name = $cache_name.'_list' : $cache_name = $cache_name;
		
		if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
			$data['html'] = null;
			
			$CI->load->database();
			$get_record = $CI->db->query("SELECT TBL_UPAZILA.*,TBL_DISTRICT.DISTRICT_NAME FROM TBL_UPAZILA LEFT JOIN TBL_DISTRICT ON TBL_UPAZILA.DISTRICT_ID = TBL_DISTRICT.DISTRICT_ID WHERE TBL_UPAZILA.STATUS=7 ORDER BY TBL_UPAZILA.UPAZILA_NAME");
			$get_record = $get_record->result();
		
			foreach( $get_record as $k=>$v ){
				switch($type){
					case 'option':
						$data['html'] .= '<option value="'.$v->UPAZILA_ID.'" data-district="'.$v->DISTRICT_ID.'">'.ucwords(str_replace('_',' ',$v->UPAZILA_NAME .' ('.$v->DISTRICT_NAME.')')).'</option>';
						break;
					case 'option_mix':
						$data['html'] .= '<option value="'.$v->UPAZILA_ID.'|'.$v->UPAZILA_NAME.'">'.ucwords(str_replace('_',' ',$v->UPAZILA_NAME .' ('.$v->DISTRICT_NAME.')')).'</option>';
						break;
					case 'list':
						$data['html'] .= '<li class="list_item" data-id="'.$v->UPAZILA_ID.'">'.ucwords(str_replace('_',' ',$v->UPAZILA_NAME .' ('.$v->DISTRICT_NAME.')')).'</li>';
						break;
				}
			}

			$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
		}
		
		return $data['html'];
	}


	// cache for specific division
	// function get_specific_division($id){
	// 	# type = option/option_mix/list
	// 	$CI =&get_instance();
	// 	$cache_name = 'sp_division';
	// 	$group_name = 'sp_division';
		
	// 	# to delete cache use: $this->cache->remove_group('group_name');
	// 	$CI->load->library('cache');
		
		
	// 	if( !$data['html'] = $CI->cache->get($cache_name, $group_name) ){
	// 		$data['html'] = null;
			
	// 		$CI->load->database();
	// 		$get_record = $CI->db->query("SELECT * FROM TBL_DIVISION WHERE DIVISION_ID=".$id);
	// 		$get_record = $get_record->row();

	// 		$data['html'] .= $get_record->DIVISION_NAME;

	// 		$CI->cache->save($cache_name, $data['html'], $group_name, 604800);		
	// 	}
		
	// 	return $data['html'];
	// }
	

}