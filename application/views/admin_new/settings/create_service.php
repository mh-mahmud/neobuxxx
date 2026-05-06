	<?php include(APPPATH."views/admin_new/header_form.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Service Setup</h3>
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
                    <h2>Service Setup</h2>
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
                    	<input type="hidden" name="service_id" value="<?php if( isset($edit['SERVICE_ID']) && $edit['SERVICE_ID'] ){echo $this->webspice->encrypt_decrypt($edit['SERVICE_ID'], 'encrypt');} ?>" />

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Service Name<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="service_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('service_name', $edit['SERVICE_NAME']); ?>">
                            <span class="fred"><?php echo form_error('service_name'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Service Type</label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <select name="service_type" class=" form-control" tabindex="-1">
                              <option value="">--- Select One ---</option>
                              <option value="flexi_service" <?php echo (isset($edit['SERVICE_TYPE']) && $edit['SERVICE_TYPE'] == "flexi_service") ? "selected" : ""; ?> >Flexi Service</option>
                              <option value="mobile_banking" <?php echo (isset($edit['SERVICE_TYPE']) && $edit['SERVICE_TYPE'] == "mobile_banking") ? "selected" : ""; ?> >Mobile Banking</option>
                              <option value="online_banking" <?php echo (isset($edit['SERVICE_TYPE']) && $edit['SERVICE_TYPE'] == "online_banking") ? "selected" : ""; ?> >Online Banking</option>
                            </select>
                            <span class="fred"><?php echo form_error('service_type'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Service Code<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="service_code" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('service_code', $edit['SERVICE_CODE']); ?>">
                            <span class="fred"><?php echo form_error('service_code'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Prefix<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="prefix" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('prefix', $edit['PREFIX']); ?>">
                            <span class="fred"><?php echo form_error('prefix'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Minimum Amount<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="min_amount" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('min_amount', $edit['MIN_AMOUNT']); ?>">
                            <span class="fred"><?php echo form_error('min_amount'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Maximum Amount<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="max_amount" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('max_amount', $edit['MAX_AMOUNT']); ?>">
                            <span class="fred"><?php echo form_error('max_amount'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Bulk Limit<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="bulk_limit" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('bulk_limit', $edit['BULK_LIMIT']); ?>">
                            <span class="fred"><?php echo form_error('bulk_limit'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Logo
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="file" name="image" id="" class="input-file uniform_on col-md-7 col-xs-12">
                            <span class="fred"><?php echo form_error('image'); ?></span>
                          </div>
                        </div>
                        <?php if( isset($edit['LOGO']) && file_exists($this->webspice->get_path('service_full').$edit['LOGO']) ): ?>
                          <div class="personnel-thm-img" style="padding-top:20px;margin-left:280px;">
                            <img src="<?php echo $this->webspice->get_path('service').$edit['LOGO']; ?>"  alt="" class="img-responsive" width="100" />
                          </div>
                        <?php endif;  ?>

            						<div class="ln_solid"></div>
            						<div class="form-group">
            							<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
            							  <a href="<?php echo $url_prefix; ?>manage_service" class="btn btn-primary">Cancel</a>
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