<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Manage Reseller</h3>
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

            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Manage Reseller</h2>
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
                    <table id="datatable" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <!-- <th>Password</th> -->
                            <!-- <th>Pin</th> -->
                            <th>Mobile</th>
                            <th>Balance</th>
                            <th>Last Login</th>
                            <th>Up Reseller</th>
                            <th>Created Date</th>
                            <!-- <th>Created By</th> -->
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $v) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $v->USER_NAME; ?></td>
                              <td><?php echo $v->USER_EMAIL; ?></td>
                              <!-- <td>
                                <?php //echo $this->webspice->enc($v->USER_PASSWORD, 'decrypt'); ?>
                              </td>
                              <td>
                                <?php //echo $this->webspice->enc($v->PIN, 'decrypt'); ?>
                              </td> -->
                              <td><?php echo $v->USER_PHONE; ?></td>
                              <td>
                                <?php echo $this->webspice->get_reseller_cur_balance($v->RESELLER_ID); ?>
                              </td>
                              <td><?php echo $this->webspice->user_last_login($v->USER_ID); ?></td>
                              <td><?php echo $this->webspice->admin_name($v->CREATED_BY); ?></td>
                              <td><?php echo $v->CREATED_DATE; ?></td>
                              <td>
                                <?php
                                  if($v->STATUS == -7) {
                                    echo '<span class="label label-warning">Inactive</span>';
                                  }
                                  else if($v->STATUS == 2) {
                                    echo '<span class="label label-warning">Assigned</span>';
                                  }
                                  else if($v->STATUS == 7) {
                                    echo '<span class="label label-success">Active</span>';
                                  }
                                ?>
                              </td>
                              <td>
                                <?php if( $this->webspice->permission_verify('manage_reseller',true) && $v->STATUS!=9 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_reseller/payment/<?php echo $this->webspice->encrypt_decrypt($v->RESELLER_ID,'encrypt'); ?>" class="btn btn-primary">Payment</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_reseller',true) && $v->STATUS!=9 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_reseller/payment_history/<?php echo $this->webspice->encrypt_decrypt($v->RESELLER_ID,'encrypt'); ?>" class="btn btn-primary">History</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_reseller',true) && $v->STATUS!=9 && $this->webspice->permission_verify('create_rate_module',true) && !$this->webspice->reseller_rate_mod($v->RESELLER_ID) ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_reseller/set_rates/<?php echo $this->webspice->encrypt_decrypt($v->RESELLER_ID,'encrypt'); ?>" class="btn btn-primary">Set Rates</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_reseller',true) && $v->STATUS!=9 && $this->webspice->permission_verify('manage_rate_module',true) && $this->webspice->reseller_rate_mod($v->RESELLER_ID) ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_reseller/view_rates/<?php echo $this->webspice->encrypt_decrypt($v->RESELLER_ID,'encrypt'); ?>" class="btn btn-primary">View Rates</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_reseller',true) && $v->STATUS==7 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_reseller/inactive/<?php echo $this->webspice->encrypt_decrypt($v->RESELLER_ID,'encrypt'); ?>" class="btn btn-warning">Inactive</a>
                                <?php endif; ?>

                                <?php if( $this->webspice->permission_verify('manage_reseller',true) && $v->STATUS==-7 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_reseller/active/<?php echo $this->webspice->encrypt_decrypt($v->RESELLER_ID,'encrypt'); ?>" class="btn btn-warning">Active</a>
                                <?php endif; ?>
                              </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
        <!-- /page content -->

<?php include(APPPATH."views/admin_new/footer_table.php"); ?>