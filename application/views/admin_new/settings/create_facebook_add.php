  <?php include(APPPATH."views/admin_new/header_form.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Create Facebook Add</h3>
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
                    <h2><a class="btn btn-success btn-xs" href="<?php echo $url_prefix; ?>manage_facebook_add"><i class="fa fa-bars"></i> Manage Facebook Add</a></h2>

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
                      <input type="hidden" name="package_id" value="<?php if( isset($edit['ADD_ID']) && $edit['ADD_ID'] ){echo $this->webspice->encrypt_decrypt($edit['ADD_ID'], 'encrypt');} ?>" />

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Add Name<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="add_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('add_name', $edit['ADD_NAME']); ?>">
                            <span class="fred"><?php echo form_error('add_name'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">URL<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="url_1" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('url_1', $edit['URL_1']); ?>">
                            <span class="fred"><?php echo form_error('url_1'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Add Duration<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="add_duration" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('add_duration', $edit['ADD_DURATION']); ?>">
                            <span class="fred"><?php echo form_error('add_duration'); ?></span>
                          </div>
                        </div>

                        <?php foreach($add_data as $val) : ?>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name"><?php echo $val->PACKAGE_NAME ?> price<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input type="text" id="" name="price_<?php echo $val->PACKAGE_ID; ?>" required="required" class="form-control col-md-7 col-xs-12" value="<?php //echo set_value('add_duration', $edit['ADD_DURATION']); ?>">
                              <span class="fred"><?php echo form_error('price_'.$val->PACKAGE_ID); ?></span>
                            </div>
                          </div>
                        <?php endforeach; ?>

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