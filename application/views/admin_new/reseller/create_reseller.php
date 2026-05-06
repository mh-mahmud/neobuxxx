	<?php include(APPPATH."views/admin_new/header_form.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Create Reseller</h3>
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
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Create Reseller</h2>
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


    					<div class="form-group">
    						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Reseller Name<span class="required">*</span>
    						</label>
    						<div class="col-md-6 col-sm-6 col-xs-12">
    						  <input type="text" id="" name="user_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('user_name'); ?>">
    						  <span class="fred"><?php echo form_error('user_name'); ?></span>
    						</div>
    					</div>

    					<div class="form-group">
    						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Email<span class="required">*</span>
    						</label>
    						<div class="col-md-6 col-sm-6 col-xs-12">
    						  <input type="text" name="user_email" id="" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('user_email'); ?>">
    						  <span class="fred"><?php echo form_error('user_email'); ?></span>
    						</div>
    					</div>

    					<div class="form-group">
    						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Mobile Number<span class="required">*</span>
    						</label>
    						<div class="col-md-6 col-sm-6 col-xs-12">
    						  <input type="text" name="user_phone" id="" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('user_phone'); ?>">
    						  <span class="fred"><?php echo form_error('user_phone'); ?></span>
    						</div>
    					</div>

    					<div class="form-group">
    						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">PIN<span class="required">*</span>
    						</label>
    						<div class="col-md-6 col-sm-6 col-xs-12">
    						  <input type="password" name="pin" id="" required="required" class="form-control col-md-7 col-xs-12" value="">
    						  <span class="fred"><?php echo form_error('pin'); ?></span>
    						</div>
    					</div>

    					<div class="form-group">
    						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Confirm PIN<span class="required">*</span>
    						</label>
    						<div class="col-md-6 col-sm-6 col-xs-12">
    						  <input type="password" name="confirm_pin" id="" required="required" class="form-control col-md-7 col-xs-12" value="">
    						  <span class="fred"><?php echo form_error('confirm_pin'); ?></span>
    						</div>
    					</div>

						<div class="form-group">
						<label class="control-label col-md-3 col-sm-3 col-xs-12">Reseller Permission<span class="required">*</span></label>
						<div class="col-md-6 col-sm-6 col-xs-12">
						  <select name="service_permission[]" class="select2_multiple form-control" multiple tabindex="-1">
								<?php
									$options = $this->db->query("SELECT * FROM service_settings WHERE STATUS = 7")->result();
								?>
								<?php foreach($options as $option) : ?>
									<option value="<?php echo strtolower(str_replace(" ", "_", $option->SERVICE_NAME)); ?>"><?php echo $option->SERVICE_NAME ?></option>
								<?php endforeach; ?>
						  </select>
						  <span class="fred"><?php echo form_error('service_permission'); ?></span>
						</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12">Type<span class="required">*</span></label>
							<div class="col-md-6 col-sm-6 col-xs-12">
							  <select name="user_type" class="form-control" tabindex="-1">
								<option value="">--- Select One ---</option>
                                <option value="reseller">Reseller</option>
                                <option value="user">User</option>
							  </select>
							  <span class="fred"><?php echo form_error('user_type'); ?></span>
							</div>
						</div>

						<div class="ln_solid"></div>
						<div class="form-group">
							<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
							  <a href="<?php echo $url_prefix; ?>manage_reseller" class="btn btn-primary">Cancel</a>
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