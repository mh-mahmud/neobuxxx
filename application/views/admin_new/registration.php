<!DOCTYPE html>
<html lang="en">
  <?php

  $url_prefix = $this->webspice->settings()->site_url_prefix;
  $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");

  ?>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $this->webspice->settings()->site_title; ?></title>

    <!-- Bootstrap -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo $url_prefix; ?>global/admin_new/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <!-- <a class="hiddenanchor" id="signup"></a> -->
      <!-- <a class="hiddenanchor" id="signin"></a> -->

      <div class="login_wrapper">

        <?php if(isset($errors) && count($errors)) : ?>
          <div class="alert alert-error alert-block">
            <a class="close" data-dismiss="alert" href="#" onclick="parentNode.remove()">&times;</a>
            <h4 class="alert-heading">Error!</h4>
            <?php
              foreach($errors as $error) {
                echo $error . "<br />";
              }
            ?>
          </div>
        <?php endif; ?>

        <!-- here will goes alert message -->
        <?php if( $this->webspice->message_board(null, 'get') ): ?>
        <div id="message_board" class="alert alert-success">
            <button type="button" id="button-close" class="close" onclick="parentNode.remove()" data-dismiss="alert">&times;</button>
            <!-- <h4>Success</h4> -->
            <?php echo $this->webspice->message_board(null,'get_and_destroy'); ?>
        </div>
        <?php endif; ?>
        <!-- alert message end -->

        <div id="" class="animate form ">
          <section class="login_content">
            <form id="demo-form2"  method="post" action=""  enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            
              <h1><a style="text-decoration: none;" href="<?php echo $url_prefix; ?>">NeoBuxxx</a></h1>

              <div>
                <input type="text" autocomplete="off" class="form-control" name="first_name" placeholder="First Name" value="<?php echo set_value('first_name'); ?>" />
                <span class="new_fred"><?php echo form_error('first_name'); ?></span>
              </div>
              <div>
                <input type="text" autocomplete="off" class="form-control" name="last_name" placeholder="Last Name" value="<?php echo set_value('last_name'); ?>" />
                <span class="new_fred"><?php echo form_error('last_name'); ?></span>
              </div>
              <div>
                <input type="email" autocomplete="off" class="form-control" name="email" placeholder="Email" value="<?php echo set_value('email'); ?>" />
                <span class="new_fred"><?php echo form_error('email'); ?></span>
              </div>
              <div>
                <input type="password" class="form-control" name="password" placeholder="Password" />
                <span class="new_fred"><?php echo form_error('password'); ?></span>
              </div>
              <div>
                <input type="password" class="form-control" name="re_password" placeholder="Re-type Password" />
                <span class="new_fred"><?php echo form_error('re_password'); ?></span>
              </div>
              <div>
                <textarea rows="5" name="address" class="form-control" name="address" placeholder="Address"><?php echo set_value('address'); ?></textarea>
                <span class="new_fred"><?php echo form_error('address'); ?></span>
              </div><br />
              <div>
                <input type="text" autocomplete="off" class="form-control" name="mobile" placeholder="Mobile / Cell" value="<?php echo set_value('mobile'); ?>" />
                <span class="new_fred"><?php echo form_error('mobile'); ?></span>
              </div>
              <div>
                <input type="text" autocomplete="off" class="form-control" name="subscription_pin" placeholder="Subscription Pin" value="<?php echo set_value('subscription_pin'); ?>" />
                <span class="new_fred"><?php echo form_error('subscription_pin'); ?></span>
              </div>
              <div>
                <input type="text" autocomplete="off" class="form-control" name="reffer_id" placeholder="Reffer ID" value="<?php echo set_value('reffer_id'); ?>" />
                <span class="new_fred"><?php echo form_error('reffer_id'); ?></span>
              </div>
              <div>
                <input type="text" autocomplete="off" class="form-control" name="national_id" placeholder="Citizen Card No" value="<?php echo set_value('national_id'); ?>" />
                <span class="new_fred"><?php echo form_error('national_id'); ?></span>
              </div>
              <div>
               <select name="country" class="form-control">
                 <option value="">-- Select Country Name --</option>
                 <?php foreach($countries as $country) : ?>
                    <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                 <?php endforeach; ?>
               </select>
               <br />
               <span class="new_fred"><?php echo form_error('country'); ?></span>
              </div>
              <div style="margin-top: 20px;margin-left: -37px">
                <!-- <a class="btn btn-default submit">Submit</a> -->
                <input type="submit" name="submit" class="btn btn-success btn-md submit" value="&nbsp;&nbsp;&nbsp;&nbsp;Submit&nbsp;&nbsp;&nbsp;&nbsp;">
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Already a member ?
                  <a href="<?php echo $url_prefix; ?>login" class="to_register"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-bar-chart-o"></i> NeoBuxxx</h1>
                  <p>&copy; 2017 All Rights Reserved. NeoBuxxx.</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>

    <script>
      // $(".close").on("click", function() {
      //   console.log("Hello");
      // });

      // var my_div = document.getElementById("button-close");
    </script>

  </body>
</html>
