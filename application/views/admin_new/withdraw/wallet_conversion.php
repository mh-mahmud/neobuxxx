	<?php include(APPPATH."views/admin_new/header_form.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Wallet Conversion</h3>
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
                    <h2>Wallet Conversion</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />

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

                    <form id="demo-form2"  method="post" action=""  enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

                    	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                      <!-- <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Payment Method<span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select name="payment_method" class=" form-control" tabindex="-1">
                            <option value="">--- Select One ---</option>
                            <option value=1>bKash</option>
                            <option value=2>Perfect Money</option>
                            <option value=3>Neteller</option>
                            <option value=4>Solid Trust</option>
                          </select>
                          <span class="fred"><?php //echo form_error('payment_method'); ?></span>
                        </div>
                      </div> -->

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Transfer From<span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select name="transfer_from" class=" form-control" tabindex="-1">
                            <option value="">--- Select One ---</option>
                            <option value="adds_wallet">Adds Wallet</option>
                            <option value="refer_wallet">Refer Wallet</option>
                            <option value="joining_wallet">Joining Wallet</option>
                            <option value="shopping_wallet">Shopping Wallet</option>
                          </select>
                          <span class="fred"><?php echo form_error('transfer_from'); ?></span>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Transfer To<span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" autocomplete="off" id="" name="transfer_to" required="required" class="form-control col-md-7 col-xs-12" value="Main Wallet" readonly>
                          <span class="fred"><?php echo form_error('transfer_to'); ?></span>
                        </div>
                      </div>

            					<div class="form-group">
            						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Amount<span class="required">*</span>
            						</label>
            						<div class="col-md-6 col-sm-6 col-xs-12">
            						  <input type="text" autocomplete="off" id="" name="amount" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('amount'); ?>">
            						  <span class="fred"><?php echo form_error('amount'); ?></span>
            						</div>
            					</div>

            					<div class="form-group">
            						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Note
            						</label>
            						<div class="col-md-6 col-sm-6 col-xs-12">
            						  <textarea rows="5" name="user_note" id="" required="required" class="form-control col-md-7 col-xs-12"><?php echo set_value('user_note'); ?></textarea>
            						  <span class="fred"><?php echo form_error('user_note'); ?></span>
            						</div>
            					</div>

        						<div class="ln_solid"></div>
        						<div class="form-group">
        							<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        							  <a href="<?php echo $url_prefix; ?>manage_withdraw" class="btn btn-primary">Cancel</a>
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