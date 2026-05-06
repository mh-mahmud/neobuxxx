  <?php include(APPPATH."views/admin_new/header_form.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Software Settings</h3>
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
                    <h2>Software Settings</h2>
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
                      <input type="hidden" name="settings_id" value="<?php if( isset($edit['SETTINGS_ID']) && $edit['SETTINGS_ID'] ){echo $this->webspice->encrypt_decrypt($edit['SETTINGS_ID'], 'encrypt');} ?>" />

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level One Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_one" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_one', $edit['LEVEL_ONE']); ?>">
                            <span class="fred"><?php echo form_error('level_one'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Two Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_two" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_two', $edit['LEVEL_TWO']); ?>">
                            <span class="fred"><?php echo form_error('level_two'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Three Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_three" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_three', $edit['LEVEL_THREE']); ?>">
                            <span class="fred"><?php echo form_error('level_three'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Four Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_four" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_four', $edit['LEVEL_FOUR']); ?>">
                            <span class="fred"><?php echo form_error('level_four'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Five Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_five" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_five', $edit['LEVEL_FIVE']); ?>">
                            <span class="fred"><?php echo form_error('level_five'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Six Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_six" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_six', $edit['LEVEL_SIX']); ?>">
                            <span class="fred"><?php echo form_error('level_six'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Seven Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_seven" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_seven', $edit['LEVEL_SEVEN']); ?>">
                            <span class="fred"><?php echo form_error('level_seven'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Eight Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_eight" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_eight', $edit['LEVEL_EIGHT']); ?>">
                            <span class="fred"><?php echo form_error('level_eight'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Nine Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_nine" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_nine', $edit['LEVEL_NINE']); ?>">
                            <span class="fred"><?php echo form_error('level_nine'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Ten Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_ten" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_ten', $edit['LEVEL_TEN']); ?>">
                            <span class="fred"><?php echo form_error('level_ten'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Eleven Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_eleven" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_eleven', $edit['LEVEL_ELEVEN']); ?>">
                            <span class="fred"><?php echo form_error('level_eleven'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Twelve Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_twelve" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_twelve', $edit['LEVEL_TWELVE']); ?>">
                            <span class="fred"><?php echo form_error('level_twelve'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Thirteen Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_thirteen" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_thirteen', $edit['LEVEL_THIRTEEN']); ?>">
                            <span class="fred"><?php echo form_error('level_thirteen'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Level Fourteen Commission<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="level_fourteen" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('level_fourteen', $edit['LEVEL_FOURTEEN']); ?>">
                            <span class="fred"><?php echo form_error('level_fourteen'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Withdraw Charge In Percent (%)<span class="required">*</span>
                          </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="first-name" name="withdraw_charge" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('withdraw_charge', $edit['WITHDRAW_CHARGE']); ?>">
                            <span class="fred"><?php echo form_error('withdraw_charge'); ?></span>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">User to User Balance Transfer<span class="required">*</span></label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <p style="padding: 5px;">
                              <input type="radio" class="flat" name="user_balance_transfer" id="" <?php echo ($edit['USER_BALANCE_TRANSFER'] == 1) ? "checked" : ""; ?> value="1" required /> ON
                              <input type="radio" class="flat" name="user_balance_transfer" id="" <?php echo ($edit['USER_BALANCE_TRANSFER'] == 0) ? "checked" : ""; ?> value="0" /> OFF
                            </p>
                            <span class="fred"><?php echo form_error('user_balance_transfer'); ?></span>
                          </div>
                        </div>

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