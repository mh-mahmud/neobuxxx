//<!--
var url_prefix = '/'; /*should be start and end with / (slash)*/
$(document).ready(function(){
	init();
});



init = function(){
	common_functions();
	
	if( $("#header_container").length ){
		header_footer();
	}
	
	var page = $('#wrapper').find('.page_identifier').attr('id');
	console.log(page);
	switch(page){
		case 'page_index': page_index(); break;
	}
	
	$("input[type=submit]").click(function(){
		$('.chzn-select,#nic_editor').hide();
	});
};


common_functions = function(){
	/*action confirmation*/
	$('.confirmation').on('click', function(){
		if(confirm('Do you really want to perform this action?')){
			return true;
		}else{
			return false;	
		}
	});
	
	/*chosen select box*/
	if(typeof chosen == 'function'){
		$('.chzn-select').chosen();
	}
	
	/*date picker*/
/*	$('.date_time_picker_24').datetimepicker({
		format:'Y-m-d H:i',
		 allowTimes:[
		  '00:00', '00:30', '01:00', '01:30',
		  '02:00', '02:30', '03:00', '03:30',
		  '04:00', '04:30', '05:00', '05:30',
		  '06:00', '06:30', '07:00', '07:30',
		  '08:00', '08:30', '09:00', '09:30',
		  '10:00', '10:30', '11:00', '11:30',
		  '12:00', '12:30', '13:00', '13:30',
		  '14:00', '14:30', '15:00', '15:30',
		  '16:00', '16:30', '17:00', '17:30',
		  '18:00', '18:30', '19:00', '19:30',
		  '20:00', '20:30', '21:00', '21:30',
		  '22:00', '22:30', '23:00', '23:30'
 		]
	});*/
	
	// $('.date_picker').datetimepicker({timepicker:false,format:'Y-m-d'});
	
	
	/*meassge show and hide after few milli-second*/
	if( $("#message_board").length ){
		$('#message_board').delay(150000).hide('slow');	
	}
	
	$('.refresh').on('click', function(){
		location.href = location.href;
	});
	
	/*razuspopup - js script*/
	$('.razuspopup').each(function(){
		var posDirection = $('.razuspopupbtn', this).attr('data_pos');
		var objWindow = $('.razuspopupwindow', this);
	
		if(posDirection=='left'){
			objWindow.css('left','0px');
		}else if(posDirection=='right'){
			objWindow.css('right','0px');
		}else{
			objWindow.css('left', -(objWindow.width()/2)+'px' );
		}
	});
	$('.razuspopupbtn').click(function(){
		$(this).next().fadeIn('slow');
		return false;
	});
	$('.razuspopupwindow .btn_close').click(function(){
		$(this).parent().parent().fadeOut('slow');
		return false;
	});
	
	/*drop-down box -> for small screen*/
	$('.list_for_small_screen').change(function(){
		var get_title = $(this).val();
		if(get_title){
			location.href = get_title;
		}
	});
	
	/*custom place holder for input box*/
	$('.placeholder .caption').each(function(){
		var pre_obj = $(this).prev();
		var pos = pre_obj.position();
		$(this).css('top', (pos.top+4) );
		$(this).css('padding-top', pre_obj.css('padding-top'));
		$(this).css('padding-right', pre_obj.css('padding-right'));
		$(this).css('padding-bottom', pre_obj.css('padding-bottom'));
		$(this).css('padding-left', pre_obj.css('padding-left'));
	});



};

header_footer = function(){

};

page_index = function(){

};

//To select country name
function selectCountry(val) {
	console.log("data selected");
	$("#search-box").val(val);
	$("#suggesstion-box").hide();
}

page_signin = function() {
  $('.forgotPassword').click(function() {
    $('#login_panel').hide('slow');
    $('#forgotPassword_panel').show('slow');

    return false;
  });

  $('.backtologin').click(function() {
    $('#forgotPassword_panel').hide('slow');
    $('#login_panel').show('slow');

    return false;
  });

  $('.link_register').click(function() {
    $('#forgot_password_panel').hide('slow');
    $('#login_panel').hide('slow');
    $('#change_password').hide('slow');
    $('#register_panel').show('slow');

    return false;
  });
};

page_create_user=function(){
	$('.user-role').removeClass('hide');
	$('#user_type').on('change',function(){
		var user_type=$('#user_type :selected').val();
			if(user_type=='organizational')
			{	
				$('.user-role').addClass('hide');
				/*find value for role name company_role*/
				var selected_role_value=$('#user_role option').filter(function () { return $(this).html() == "Company Role"; }).val();
				$('#user_role option[value="'+selected_role_value+'"]').attr('selected','selected');
				var selected_company_value=$('#company_id option').filter(function () { return $(this).html() == "Root Company"; }).val();
				$('#company_id option[value="'+selected_company_value+'"]').attr('selected',false);
				$('#company_id option[value="'+selected_company_value+'"]').hide();
			}
			else if(user_type=='authority'){
				var selected_company_value=$('#company_id option').filter(function () { return $(this).html() == "Root Company"; }).val();
				$('#company_id option[value="'+selected_company_value+'"]').attr('selected','selected');
				var selected_role_value=$('#user_role option').filter(function () { return $(this).html() == "Company Role"; }).val();
				$('#user_role option[value="'+selected_role_value+'"]').attr('selected',false);
				$('#user_role option[value="'+selected_role_value+'"]').hide();
				$('.user-role').removeClass('hide');
			}
			else{
				$('.user-role').removeClass('hide');
			}
		});
	
	
}

//check the parameter value is number or not
IsNumeric = function(num){
	var ValidChars = "0123456789";
	var IsNumber = true;
	var Char;
	
	for(i = 0; i < num.length && IsNumber == true; i++) { 
		Char = num.charAt(i); 
		if( ValidChars.indexOf(Char) == -1){
			IsNumber = false;
		}
	}
	return IsNumber;
};

IsFloat = function(num){
	var ValidChars = "0123456789.";
	var IsNumber = true;
	var Char;
	
	for(i = 0; i < num.length && IsNumber == true; i++) { 
		Char = num.charAt(i); 
		if( ValidChars.indexOf(Char) == -1){
			IsNumber = false;
		}
	}
	
	if( IsNumber && !isNaN ( parseFloat ( num )) ) {
		return true;
	}
	
	return false;
};

//check email address is valid or not
checkemail = function(val){
	//return (val.indexOf(".") > 2) && (val.indexOf("@") > 0);
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var address = val;
	if(reg.test(address) == false) {
	  return false;
	}

	return true;
};



//-->