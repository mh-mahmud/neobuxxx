<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "web_controller";
$route['confirmation'] = "parent_controller/show_confirmation";
$route['change_pass'] = 'admin_controller/change_pass';

# web page setup
$route['home'] = 'web_controller';
$route['about'] = 'web_controller/about';
$route['contact'] = 'web_controller/contact';
$route['faq'] = 'web_controller/faq';
$route['features'] = 'web_controller/features';
$route['policy'] = 'web_controller/policy';
$route['proof'] = 'web_controller/proof';
$route['works'] = 'web_controller/works';
$route['trading_tools'] = 'web_controller/trading_tools';
$route['packages'] = 'web_controller/packages';

# admin panel setup
$route['admin'] = 'admin_controller';
$route['login'] = 'admin_controller/login';
$route['registration'] = 'admin_controller/registration';
$route['logout'] = 'admin_controller/logout';
$route['admin_confirmation'] = "admin_controller/show_confirmation";
$route['exception_found'] = "admin_controller/exception_found";
$route['user'] = 'admin_controller';


# user authentication
$route['change_password'] = 'admin_controller/change_password';
$route['change_password/:any'] = 'admin_controller/change_password';
$route['forgot_password'] = 'parent_controller/forgot_password';

# user management
$route['create_user']='master_controller/create_user';
$route['manage_user']='master_controller/manage_user';
$route['manage_user/:any']='master_controller/manage_user';
$route['create_role']='master_controller/create_role';
$route['manage_role']='master_controller/manage_role';
$route['manage_role/:any']='master_controller/manage_role';

// flexiload
$route['flexi_report']='flexi_controller/flexi_report';
$route['flexi_bulk_upload']='flexi_controller/flexi_bulk_upload';
$route['flexi_send_money']='flexi_controller/flexi_send_money';
$route['view_flexi_send_money']='flexi_controller/view_flexi_send_money';
$route['view_flexi_send_money/:any']='flexi_controller/view_flexi_send_money';

// mobile banking
$route['mobile_view_send_money']='mbanking_controller/mobile_view_send_money';
$route['mobile_view_send_money/:any']='mbanking_controller/mobile_view_send_money';
$route['mobile_banking_send_money']='mbanking_controller/mobile_banking_send_money';
$route['mobile_banking_report']='mbanking_controller/mobile_banking_report';
$route['mobile_bulk_upload']='mbanking_controller/mobile_bulk_upload';

// online banking
$route['bank_view_send_money']='ebanking_controller/bank_view_send_money';
$route['bank_view_send_money/:any']='ebanking_controller/bank_view_send_money';
$route['bank_send_money']='ebanking_controller/bank_send_money';
$route['bank_report']='ebanking_controller/bank_report';
$route['bank_bulk_upload']='ebanking_controller/bank_bulk_upload';

// reseller
$route['create_reseller']='reseller_controller/create_reseller';
$route['manage_reseller']='reseller_controller/manage_reseller';
$route['manage_reseller/:any']='reseller_controller/manage_reseller';

// settings
$route['create_pin']='settings_controller/create_pin';
$route['manage_pin']='settings_controller/manage_pin';
$route['manage_pin/:any']='settings_controller/manage_pin';
$route['create_service']='settings_controller/create_service';
$route['manage_service']='settings_controller/manage_service';
$route['manage_service/:any']='settings_controller/manage_service';
$route['change_user_password']='settings_controller/change_user_password';
$route['add_ip_blocking']='settings_controller/add_ip_blocking';
$route['manage_ip_blocking']='settings_controller/manage_ip_blocking';
$route['manage_ip_blocking/:any']='settings_controller/manage_ip_blocking';
$route['initial_settings'] = 'settings_controller/initial_settings';
$route['pin_setup']='settings_controller/pin_setup';
$route['my_profile']='settings_controller/my_profile';
$route['my_referral_user']='settings_controller/my_referral_user';
$route['my_referral/:any']='settings_controller/my_referral';
$route['my_referral']='settings_controller/my_referral';
$route['my_tree']='settings_controller/my_tree';

// card management
$route['create_card_service']='card_controller/create_card_service';
$route['manage_card_service']='card_controller/manage_card_service';
$route['manage_card_service/:any']='card_controller/manage_card_service';
$route['add_card']='card_controller/add_card';
$route['manage_card']='card_controller/manage_card';
$route['manage_card/:any']='card_controller/manage_card';
$route['available_cards']='card_controller/available_cards';
$route['sold_cards']='card_controller/sold_cards';
$route['buy_cards']='card_controller/buy_cards';
$route['purchase_history']='card_controller/purchase_history';

// group data
$route['create_group']='group_controller/create_group';
$route['manage_group']='group_controller/manage_group';
$route['manage_group/:any']='group_controller/manage_group';
$route['upload_number']='group_controller/upload_number';
$route['manage_upload_number']='group_controller/manage_upload_number';
$route['manage_upload_number/:any']='group_controller/manage_upload_number';
$route['add_new_number']='group_controller/add_new_number';
$route['group_send_money']='group_controller/group_send_money';
$route['manage_group_send_money']='group_controller/manage_group_send_money';
$route['manage_group_send_money/:any']='group_controller/manage_group_send_money';

// sms
$route['create_address_book']='sms_controller/create_address_book';
$route['manage_address_book']='sms_controller/manage_address_book';
$route['manage_address_book/:any']='sms_controller/manage_address_book';
$route['upload_bulk_number']='sms_controller/upload_bulk_number';
$route['manage_upload_bulk_number']='sms_controller/manage_upload_bulk_number';
$route['manage_upload_bulk_number/:any']='sms_controller/manage_upload_bulk_number';
$route['add_phone_number']='sms_controller/add_phone_number';
$route['create_sms']='sms_controller/create_sms';
$route['send_sms']='sms_controller/send_sms';
$route['send_sms/:any']='sms_controller/send_sms';
$route['create_group_sms']='sms_controller/create_group_sms';
$route['send_group_sms']='sms_controller/send_group_sms';
$route['send_group_sms/:any']='sms_controller/send_group_sms';

// complain
$route['add_new_complain']='settings_controller/add_new_complain';
$route['my_complains']='settings_controller/my_complains';
$route['manage_complain']='settings_controller/manage_complain';
$route['manage_complain/:any']='settings_controller/manage_complain';

// messaging
$route['send_message']='settings_controller/send_message';
$route['my_outbox']='settings_controller/my_outbox';
$route['my_inbox']='settings_controller/my_inbox';
$route['my_inbox/:any']='settings_controller/my_inbox';


/**************************************************************************
**************************** CHINA DEAL ROUTES ****************************
**************************************************************************/

// registration
$route['new_registration']='registration_controller/new_registration';
$route['new_registration/:any']='registration_controller/new_registration';
$route['pending_registration']='registration_controller/pending_registration';
$route['pending_registration/:any']='registration_controller/pending_registration';
$route['ban_user']='registration_controller/ban_user';
$route['ban_user/:any']='registration_controller/ban_user';
$route['premium_user']='registration_controller/premium_user';
$route['premium_user/:any']='registration_controller/premium_user';
$route['non_premium_user']='registration_controller/non_premium_user';
$route['non_premium_user/:any']='registration_controller/non_premium_user';

// balance
$route['send_money']='balance_controller/send_money';
$route['confirm_send_money']='balance_controller/confirm_send_money';
$route['confirm_send_money/:any']='balance_controller/confirm_send_money';
$route['return_money']='balance_controller/return_money';
$route['confirm_return_money/:any']='balance_controller/confirm_return_money';
$route['transfer_balance_history']='balance_controller/transfer_balance_history';
$route['return_balance_history']='balance_controller/return_balance_history';

// income
$route['reffer_income']='balance_controller/reffer_income';
$route['generation_income']='balance_controller/generation_income';
$route['mature_share_income']='balance_controller/mature_share_income';

// share
$route['buy_share']='balance_controller/buy_share';
$route['confirm_buy_share']='balance_controller/confirm_buy_share';
$route['confirm_buy_share/:any']='balance_controller/confirm_buy_share';
$route['manage_share']='balance_controller/manage_share';
$route['manage_share/:any']='balance_controller/manage_share';
$route['mature_share']='balance_controller/mature_share';
$route['mature_share/:any']='balance_controller/mature_share';

// withdraw
$route['balance_withdraw']='withdraw_controller/balance_withdraw';
$route['manage_withdraw']='withdraw_controller/manage_withdraw';
$route['manage_withdraw/:any']='withdraw_controller/manage_withdraw';
$route['confirm_withdraw_request/:any']='withdraw_controller/confirm_withdraw_request';
$route['wallet_conversion']='withdraw_controller/wallet_conversion';
$route['wallet_conversion_history']='withdraw_controller/wallet_conversion_history';
$route['wallet_conversion_history/:any']='withdraw_controller/wallet_conversion_history';

// package setup
$route['create_package']='settings_controller/create_package';
$route['manage_package']='settings_controller/manage_package';
$route['manage_package/:any']='settings_controller/manage_package';

$route['create_facebook_add']='settings_controller/create_facebook_add';
$route['manage_facebook_add']='settings_controller/manage_facebook_add';
$route['manage_facebook_add/:any']='settings_controller/manage_facebook_add';

$route['create_ptc_add']='settings_controller/create_ptc_add';
$route['manage_ptc_add']='settings_controller/manage_ptc_add';
$route['manage_ptc_add/:any']='settings_controller/manage_ptc_add';

$route['create_youtube_add']='settings_controller/create_youtube_add';
$route['manage_youtube_add']='settings_controller/manage_youtube_add';
$route['manage_youtube_add/:any']='settings_controller/manage_youtube_add';

// subscription pin
$route['create_new_code']='settings_controller/create_new_code';
$route['available_codes']='settings_controller/available_codes';
$route['available_codes/:any']='settings_controller/available_codes';
$route['used_codes']='settings_controller/used_codes';
$route['used_codes/:any']='settings_controller/used_codes';

// earn area
$route['ptc_earn']='balance_controller/ptc_earn';
$route['ptc_earn/:any']='balance_controller/ptc_earn';
$route['facebook_earn']='balance_controller/facebook_earn';
$route['facebook_earn/:any']='balance_controller/facebook_earn';
$route['youtube_earn']='balance_controller/youtube_earn';
$route['youtube_earn/:any']='balance_controller/youtube_earn';

// earn area
$route['ptc_earn']='balance_controller/ptc_earn';
$route['ptc_earn/:any']='balance_controller/ptc_earn';

// mail verify
$route['mail_test']='admin_controller/mail_test';
$route['mail_test/:any']='admin_controller/mail_test';
$route['user_activation']='admin_controller/user_activation';
$route['user_activation/:any']='admin_controller/user_activation';