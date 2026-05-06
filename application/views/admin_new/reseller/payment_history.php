<?php include(APPPATH."views/admin_new/header_table.php"); ?>
<?php include(APPPATH."views/admin_new/menu.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Payment History</h3>
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
                    <h2>Payment History</h2>
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
                    <table id="" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                            <th>Reseller Name</th>
                            <th>Sender Name</th>
                            <th>Pay Amount</th>
                            <th>Return Amount</th>
                            <th>Balance</th>
                            <th>Sent Date</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php
                          $pay = array();
                          $return = array();
                          $last_balance = 0;
                          foreach($get_record as $v) :

                          ?>
                        <tr class="odd gradeX">
                            
                            <td><?php echo $this->webspice->admin_name($v->USER_ID); ?></td>
                            <td><?php echo $this->webspice->admin_name($v->SENDER_ID); ?></td>
                            <td>
                              <?php
                                if($v->TRANS_TYPE == 'transaction') {
                                  echo $v->AMOUNT;
                                  $pay[] = $v->AMOUNT;
                                }
                              ?>
                            </td>
                            <td>
                              <?php
                                if($v->TRANS_TYPE == 'return') {
                                  echo $v->AMOUNT;
                                  $return[] = $v->AMOUNT;
                                }
                              ?>
                            </td>
                            <td>
                              <?php
                                echo $v->BALANCE;
                                $last_balance = $v->BALANCE;
                              ?>
                            </td>
                            <td><?php echo date("D jS F Y g:iA", strtotime($v->SEND_DATE)); ?></td>
                        </tr>
                      <?php endforeach; ?>
                        <tr>
                          <td></td>
                          <td><b>Total:</b></td>
                          <td><b><?php echo array_sum($pay); ?>/-</b></td>
                          <td><b><?php echo array_sum($return); ?>/-</b></td>
                          <td><b><?php echo $last_balance; ?>/-</b></td>
                          <td></td>
                        </tr>
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