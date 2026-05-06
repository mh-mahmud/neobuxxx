<?php
include(APPPATH."views/admin_new/header_form.php");
include(APPPATH."views/admin_new/menu.php");

$countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");

?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>My Profile</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>

            <!-- start form -->
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
              <!-- flash message -->
              <?php include(APPPATH."views/admin_new/flash_message.php"); ?>
                <div class="x_panel">
                  <div class="x_title">
                    <h2>My Profile</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <?php if(isset($errors) && count($errors)) : ?>
                      <div class="alert alert-error alert-block">
                        <a class="close" data-dismiss="alert" href="#">&times;</a>
                        <h4 class="alert-heading">Error!</h4>
                        <?php
                          foreach($errors as $error) {
                            echo $error . "<br />";
                          }
                        ?>
                      </div>
                    <?php endif; ?>
                    <br />
                    <form id="demo-form2"  method="post" action=""  enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

                      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                      <input type="hidden" name="user_reg_id" value="<?php if( isset($edit['USER_REG_ID']) && $edit['USER_REG_ID'] ){echo $this->webspice->encrypt_decrypt($edit['USER_REG_ID'], 'encrypt');} ?>" />

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">First Name<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="first_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('first_name', $edit['FIRST_NAME']); ?>">
                            <span class="fred"><?php echo form_error('first_name'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Last Name<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="last_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('last_name', $edit['LAST_NAME']); ?>">
                            <span class="fred"><?php echo form_error('last_name'); ?></span>
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">USer ID
                          </label>
                          <?php
                            $email = substr($edit['EMAIL'], 0, strpos($edit['EMAIL'], "@"));
                          ?>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="user-id" class="form-control col-md-7 col-xs-12" disabled value="<?php echo $email; ?>">
                            <span class="fred"><?php echo form_error('email'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Email<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="email" required="required" class="form-control col-md-7 col-xs-12" disabled value="<?php echo set_value('email', $edit['EMAIL']); ?>">
                            <span class="fred"><?php echo form_error('email'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Address<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <!-- <input type="text" id="first-name" name="address" required="required" class="form-control col-md-7 col-xs-12" value=""> -->
                            <textarea name="address" class="form-control" rows="5"><?php echo set_value('address', $edit['ADDRESS']); ?></textarea>
                            <span class="fred"><?php echo form_error('address'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Mobile<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="mobile" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('mobile', $edit['MOBILE']); ?>">
                            <span class="fred"><?php echo form_error('mobile'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">My ID<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="my_id" required="required" class="form-control col-md-7 col-xs-12" disabled value="<?php echo $edit['MY_ID']; ?>">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Reffer ID<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="reffer_id" required="required" class="form-control col-md-7 col-xs-12" disabled value="<?php echo $edit['REFFER_ID']; ?>">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">National ID<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="national_id" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('national_id', $edit['NATIONAL_ID']); ?>">
                            <span class="fred"><?php echo form_error('national_id'); ?></span>
                          </div>
                        </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Counrty<span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                         <select name="country" class="form-control">
                           <option value="">-- Select Country Name --</option>
                           <?php foreach($countries as $country) : ?>
                              <option <?php echo ($edit['COUNTRY'] == $country) ? "selected='selected'" : ""; ?> value="<?php echo $country; ?>"><?php echo $country; ?></option>
                           <?php endforeach; ?>
                         </select>
                         <span class="new_fred"><?php echo form_error('country'); ?></span>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Profile Picture
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="file" name="image" id="" class="input-file uniform_on col-md-7 col-xs-12">
                          <span class="fred"><?php echo form_error('image'); ?></span>
                        </div>
                      </div>
                        <?php if( $edit['IMAGE'] && file_exists($this->webspice->get_path('profile_picture_full').$edit['IMAGE']) ): ?>
                          <div class="personnel-thm-img" style="padding-top:20px;margin-left:280px;">
                            <img src="<?php echo $this->webspice->get_path('profile_picture').$edit['IMAGE']; ?>"  alt="" class="img-responsive" width="100" />
                          </div>
                        <?php endif;  ?>

                        <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">NID File
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="file" name="nid_file" id="" class="input-file uniform_on col-md-7 col-xs-12">
                          <span class="fred"><?php echo form_error('nid_file'); ?></span>
                        </div>
                      </div>
                        <?php if( $edit['NID_FILE'] && file_exists($this->webspice->get_path('nid_file_full').$edit['NID_FILE']) ): ?>
                          <div class="personnel-thm-img" style="padding-top:20px;margin-left:280px;">
                            <img src="<?php echo $this->webspice->get_path('nid_file').$edit['NID_FILE']; ?>"  alt="" class="img-responsive" width="100" />
                          </div>
                        <?php endif;  ?>

                        <div class="ln_solid"></div>
                        <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <input type="submit" name="submit" value="Submit" class="btn btn-success" />
                          </div>
                        </div>

                    </form>
                  </div>
                </div>
              </div>
            </div>
            <!-- end form -->

          </div>
        </div>
        <!-- /page content -->

<?php include(APPPATH."views/admin_new/footer_form.php"); ?>