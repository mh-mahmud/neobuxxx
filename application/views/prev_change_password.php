<!DOCTYPE html>
<html class="no-js">
    <!-- global declaration -->
    <?php $url_prefix = $this->webspice->settings()->site_url_prefix; ?>
    <head>
        <title>Base4 School Management</title>
        <!-- Bootstrap -->
        <link href="<?php echo $url_prefix; ?>global/admin/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $url_prefix; ?>global/admin/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="<?php echo $url_prefix; ?>global/admin/vendors/easypiechart/jquery.easy-pie-chart.css" rel="stylesheet" media="screen">
        <link href="<?php echo $url_prefix; ?>global/admin/assets/styles.css" rel="stylesheet" media="screen">
        <link href="<?php echo $url_prefix; ?>global/admin/assets/DT_bootstrap.css" rel="stylesheet" media="screen">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="<?php echo $url_prefix; ?>global/admin/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>

    <body>

        <!-- <div class="overloaded"></div> -->

        <div class="container">
            <div class="row-fluid">
                        <!-- here will goes alert message -->
                        <?php if( $this->webspice->message_board(null, 'get') ): ?>
                        <div id="message_board" class="alert alert-info">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <!-- <h4>Success</h4> -->
                            <?php echo $this->webspice->message_board(null,'get_and_destroy'); ?>
                        </div>
                        <?php endif; ?>
                        <!-- alert message end -->
            </div>
        </div>









        <div class="container">
            <div class="row-fluid">
                <div class="span12" id="content">
                    <div class="row-fluid">

                        <!-- here will goes alert message -->
                        <!-- <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h4>Success</h4>
                            The operation completed successfully
                        </div> -->
                        <!-- alert message end -->

                          <div class="navbar">
                              <div class="navbar-inner">
                                  <ul class="breadcrumb">
                                      <li>
                                          <b>Change Password</b>  
                                      </li>
                                  </ul>
                              </div>
                          </div>
                      </div>


                         <!-- validation -->
                        <div class="row-fluid">
                             <!-- block -->
                            <div class="block">
                                <div class="navbar navbar-inner block-header">
                                    <div class="muted pull-left">Change Password</div>
                                </div>
                                <div class="block-content collapse in">
                                    <div class="span12">
                                                <!-- BEGIN FORM-->

                                                <form id="" action="<?php echo $url_prefix; ?>change_password/<?php echo $this->uri->segment(2); ?>" class="form-horizontal" method="post" data-parsley-validate>
                                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                                                    <fieldset>
                                                            <div class="control-group">
                                                                <label class="control-label">New Password*<span class="required">*</span></label>
                                                                <div class="controls">
                                                                    <input type="password" name="new_password" data-required="1" class="span6 m-wrap" value="" />
                                                                    <span class="fred"><?php echo form_error('new_password'); ?></span>
                                                                </div>
                                                            </div>

                                                            <div class="control-group">
                                                                <label class="control-label">Repeat Password*<span class="required">*</span></label>
                                                                <div class="controls">
                                                                    <input type="password" name="repeat_password" data-required="1" class="span6 m-wrap" value="" />
                                                                    <span class="fred"><?php echo form_error('repeat_password'); ?></span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="form-actions">
                                                                <input type="submit" name="submit" class="btn btn-primary" value="Submit Data"  />
                                                            </div>
                                                    </fieldset>
                                                </form>
                                                <!-- END FORM-->
                                            </div>
                                      </div>
                                   </div>
                              <!-- /block -->
                        </div>
                         <!-- /validation -->
                    
                    
                    
                </div>
        
            </div>



























<?php include(APPPATH."views/admin/admin_footer.php"); ?>