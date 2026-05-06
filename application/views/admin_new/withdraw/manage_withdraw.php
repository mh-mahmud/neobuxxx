<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Manage Withdraw Request</h3>
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

              <!-- flash message -->
              <?php include(APPPATH."views/admin_new/flash_message.php"); ?>

                <div class="x_panel">
                  <div class="x_title">
                    <h2>Manage Withdraw Request</h2>
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
                  <div class="x_content" width="100%">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                            <th>Request User Name</th>
                            <th>Payment Method</th>
                            <th>Account Number</th>
                            <th>Transaction ID</th>
                            <th>API Response</th>
                            <th>Total Amount</th>
                            <th>Charge</th>
                            <th>Net Amount</th>
                            <th>Transaction Status</th>
                            <th>Trnasaction Note</th>
                            <th>Approval Note</th>
                            <th>Request Date</th>
                            <th>Payment Date</th>
                            <?php if($this->webspice->admin_verify()) : ?>
                              <th>Action</th>
                            <?php endif; ?>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach($get_record as $v) : ?>
                          <tr class="odd gradeX">
                              
                              <td><?php echo $this->webspice->admin_name($v->USER_ID); ?></td>
                              <td>
                                <?php
                                  if($v->PAYMENT_TYPE == 1) {
                                    echo "bKash";
                                  }
                                  else if($v->PAYMENT_TYPE == 2) {
                                    echo "Perfect Money";
                                  }
                                ?>
                              </td>
                              <td><?php echo $v->ACC_NUMBER; ?></td>
                              <td><?php echo $v->TRANSACTION_ID; ?></td>
                              <td><?php echo $v->API_RESPONSE; ?></td>
                              <td><?php echo "$".$v->AMOUNT; ?></td>
                              <td><?php echo "$".$v->CHARGE; ?></td>
                              <td><?php echo "$".$v->NET_AMOUNT; ?></td>
                              <td>
                                <?php
                                  if($v->TRANS_STATUS == 0) {
                                    echo '<span class="label label-primary">Pending</span>';
                                  }
                                  else if($v->TRANS_STATUS == 1) {
                                    echo '<span class="label label-info">On Precess</span>';
                                  }
                                  else if($v->TRANS_STATUS == 2) {
                                    echo '<span class="label label-success">Success</span>';
                                  }
                                  else if($v->TRANS_STATUS == 3) {
                                    echo '<span class="label label-danger">Cancelled</span>';
                                  }
                                ?>
                              <td><?php echo $v->TRANS_NOTE; ?></td>
                              <td><?php echo $v->APPROVAL_NOTE; ?></td>
                              </td>
                              <td><?php echo date("D j S F Y", strtotime($v->REQ_DATE)); ?></td>
                              <td>
                                <?php
                                  if($v->PAYMENT_DATE == "0000-00-00") {
                                    echo "";
                                  }
                                  else {
                                    echo date("D j S F Y", strtotime($v->PAYMENT_DATE));
                                  }
                                ?>
                              </td>
                              <?php if($this->webspice->admin_verify()) : ?>
                              <td>
                                <?php if( $this->webspice->permission_verify('manage_withdraw',true) && $v->TRANS_STATUS != 2 && $v->TRANS_STATUS != 3 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_withdraw/approve/<?php echo $this->webspice->encrypt_decrypt($v->PAYMENT_ID,'encrypt'); ?>" class="btn btn-success btn-sm">Approve</a>
                                <?php endif; ?>
                                <?php if( $this->webspice->permission_verify('manage_withdraw',true) && $v->TRANS_STATUS != 2 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_withdraw/cancelled/<?php echo $this->webspice->encrypt_decrypt($v->PAYMENT_ID,'encrypt'); ?>" class="btn btn-sm btn-danger">Cancelled</a>
                                <?php endif; ?>
                                <?php if( $this->webspice->permission_verify('manage_withdraw',true) && ($v->TRANS_STATUS != 1) && $v->TRANS_STATUS != 2 ): ?>
                                    <a href="<?php echo $url_prefix; ?>manage_withdraw/onprogress/<?php echo $this->webspice->encrypt_decrypt($v->PAYMENT_ID,'encrypt'); ?>" class="btn btn-sm btn-info">On Progress</a>
                                <?php endif; ?>
                              </td>
                              <?php endif; ?>
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