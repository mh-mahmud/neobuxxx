  <?php include(APPPATH."views/admin_new/header_form.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Create Subscription Pin</h3>
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
                    <h2><a class="btn btn-success btn-xs" href="<?php echo $url_prefix; ?>available_codes"><i class="fa fa-bars"></i> Available Codes</a></h2>

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
                      <input type="hidden" name="pin_id" value="" />

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Package Name<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <select name="package_id" class="form-control" tabindex="-1">
                              <option value="">--- Select One ---</option>
                              <?php foreach($package_data as $val) : ?>
                                <option value="<?php echo $val->PACKAGE_ID; ?>"><?php echo $val->PACKAGE_NAME; ?></option>
                              <?php endforeach; ?>
                            </select>
                            <span class="fred"><?php echo form_error('package_id'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Number of Pin<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" autocomplete="off" name="number_of_code" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('number_of_code'); ?>">
                            <span class="fred"><?php echo form_error('number_of_code'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Pasword<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="password" id="first-name" autocomplete="off" name="password" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('password'); ?>">
                            <span class="fred"><?php echo form_error('password'); ?></span>
                          </div>
                        </div>

                        <div class="ln_solid"></div>
                        <div class="form-group">
                          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <a href="<?php echo $url_prefix; ?>manage_facebook_add" class="btn btn-primary">Cancel</a>
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