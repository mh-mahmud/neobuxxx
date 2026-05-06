<?php include(APPPATH."views/admin_new/header_form.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Create User</h3>
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
                    <h2>Create User</h2>
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
                    <br />
                    <form id="demo-form2"  method="post" action=""  enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="user_id" value="<?php if( isset($edit['USER_ID']) && $edit['USER_ID'] ){echo $this->webspice->encrypt_decrypt($edit['USER_ID'], 'encrypt');} ?>" />
                                      <fieldset>

            					<div class="form-group">
            						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">User Name<span class="required">*</span>
            						</label>
            						<div class="col-md-6 col-sm-6 col-xs-12">
            						  <input type="text" id="" name="user_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('user_name',$edit['USER_NAME']); ?>">
            						  <span class="fred"><?php echo form_error('user_name'); ?></span>
            						</div>
            					</div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">User Email<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="" name="user_email" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('user_email',$edit['USER_EMAIL']); ?>">
                          <span class="fred"><?php echo form_error('user_email'); ?></span>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">User Phone<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="" name="user_phone" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('user_phone',$edit['USER_PHONE']); ?>">
                          <span class="fred"><?php echo form_error('user_phone'); ?></span>
                        </div>
                      </div>

                      <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12">User Role</label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <select name="user_role" class="form-control" tabindex="-1">
                          <option value="">Select...</option>
                          <?php
                          if(!empty($this->webspice->encrypt_decrypt($this->webspice->get_user()['RESELLER_ID'], 'decrypt'))) {
                            $options = $this->db->query("SELECT * FROM role WHERE STATUS = 7 AND CREATED_BY='".$this->webspice->encrypt_decrypt($this->webspice->get_user()['USER_ID'], 'decrypt')."'")->result();
                          }
                          else {
                            $options = $this->db->query("SELECT * FROM role WHERE STATUS = 7")->result();
                          }
                          
                          ?>
                          <?php foreach($options as $option) : ?>
                          <option value="<?php echo $option->ROLE_ID ?>" <?php echo (isset($edit['ROLE_ID']) && $edit['ROLE_ID'] == $option->ROLE_ID) ? "selected" : ""; ?> ><?php echo $option->ROLE_NAME; ?></option>
                          <?php endforeach; ?>
                        </select>
                        <span class="fred"><?php echo form_error('user_role'); ?></span>
                      </div>
                      </div>

                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <a href="<?php echo $url_prefix; ?>manage_user" class="btn btn-primary">Cancel</a>
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