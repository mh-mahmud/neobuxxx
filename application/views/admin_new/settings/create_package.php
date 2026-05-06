	<?php include(APPPATH."views/admin_new/header_form.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Create Package</h3>
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
                    <h2><a class="btn btn-success btn-xs" href="<?php echo $url_prefix; ?>manage_package"><i class="fa fa-bars"></i> Manage Package</a></h2>

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
                    	<input type="hidden" name="package_id" value="<?php if( isset($edit['PACKAGE_ID']) && $edit['PACKAGE_ID'] ){echo $this->webspice->encrypt_decrypt($edit['PACKAGE_ID'], 'encrypt');} ?>" />

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Package Name<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="package_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('package_name', $edit['PACKAGE_NAME']); ?>">
                            <span class="fred"><?php echo form_error('package_name'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Package Details</label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <textarea name="package_desc" rows="5" class="form-control col-md-7 col-xs-12"><?php echo set_value('package_desc', $edit['PACKAGE_DESC']); ?></textarea>
                            <span class="fred"><?php echo form_error('package_desc'); ?></span>
                          </div>
                        </div>

                        <!-- <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Facebook Link<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="facebook_link" required="required" class="form-control col-md-7 col-xs-12" value="<?php //echo set_value('facebook_link', $edit['FACEBOOK_LINK']); ?>">
                            <span class="fred"><?php //echo form_error('facebook_link'); ?></span>
                          </div>
                        </div> -->

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">PTC Link<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="ptc_link" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('ptc_link', $edit['PTC_LINK']); ?>">
                            <span class="fred"><?php echo form_error('ptc_link'); ?></span>
                          </div>
                        </div>

                        <!-- <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Youtube Link<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="youtube_link" required="required" class="form-control col-md-7 col-xs-12" value="<?php //echo set_value('youtube_link', $edit['YOUTUBE_LINK']); ?>">
                            <span class="fred"><?php //echo form_error('youtube_link'); ?></span>
                          </div>
                        </div> -->

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Package Amount<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="package_amount" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('package_amount', $edit['PACKAGE_AMOUNT']); ?>">
                            <span class="fred"><?php echo form_error('package_amount'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Package Validity<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="package_validity" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('package_validity', $edit['PACKAGE_VALIDITY']); ?>">
                            <span class="fred"><?php echo form_error('package_validity'); ?></span>
                          </div>
                        </div>

                        <!-- <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Game Permission<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <p style="padding: 5px;">
                              <input type="radio" class="flat" name="game_permission" id="genderM" <?php //echo ($edit['GAME_PERMISSION'] == 1) ? "checked" : ""; ?> value="1" required /> ON
                              <input type="radio" class="flat" name="game_permission" id="genderF" <?php //($edit['GAME_PERMISSION'] == 0) ? "checked" : ""; ?> value="0" /> OFF
                            </p>
                            <span class="fred"><?php //echo form_error('game_permission'); ?></span>
                          </div>
                        </div> -->

            						<div class="ln_solid"></div>
            						<div class="form-group">
            							<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
            							  <a href="<?php echo $url_prefix; ?>manage_package" class="btn btn-primary">Cancel</a>
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